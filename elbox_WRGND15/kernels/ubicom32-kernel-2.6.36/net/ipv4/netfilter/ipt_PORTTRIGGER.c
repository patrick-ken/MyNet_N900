//#include <linux/config.h>
#include <linux/types.h>
#include <linux/icmp.h>
#include <linux/ip.h>
#include <linux/timer.h>
#include <linux/netfilter.h>
#include <linux/netfilter_ipv4.h>
#include <linux/module.h>
#include <linux/skbuff.h>
#include <linux/proc_fs.h>
#include <linux/vmalloc.h>
#include <net/checksum.h>
#include <net/ip.h>
#include <linux/stddef.h>
#include <linux/sysctl.h>
#include <linux/slab.h>
#include <linux/random.h>
#include <linux/jhash.h>
#include <linux/err.h>
#include <linux/percpu.h>
#include <linux/moduleparam.h>
#include <linux/notifier.h>
#include <linux/tcp.h>
#include <linux/udp.h>

/* nf_conntrack_lock protects the main hash table, protocol/helper/expected
   registrations, conntrack timers*/
#define ASSERT_READ_LOCK(x)
#define ASSERT_WRITE_LOCK(x)

#include <net/netfilter/nf_conntrack.h>
#include <net/netfilter/nf_conntrack_core.h>
#include <net/netfilter/nf_nat_rule.h>
#include <linux/netfilter/x_tables.h>
#include <linux/netfilter_ipv4/ipt_PORTTRIGGER.h>

MODULE_LICENSE("GPL");


#if 0
#define DEBUGP printk
#else
#define DEBUGP(format, args...)
#endif

#define ASSERT_READ_LOCK(x) //MUST_BE_READ_LOCKED(&nf_conntrack_lock)
#define ASSERT_WRITE_LOCK(x) //MUST_BE_WRITE_LOCKED(&nf_conntrack_lock)

static struct ipt_porttrigger {
	struct list_head list;
	struct timer_list timeout;
	unsigned int src_ip;
	unsigned int dst_ip;
	unsigned short trigger_proto;
	unsigned short forward_proto;
	unsigned int timer;
	struct ipt_mport trigger_ports;
	struct ipt_mport forward_ports;
	struct nf_nat_range range;
};

static LIST_HEAD(trigger_list);

static unsigned int
del_porttrigger_rule(struct ipt_porttrigger *trigger)
{
	NF_CT_ASSERT(trigger);
	spin_lock_bh(&nf_conntrack_lock);
	DEBUGP("del rule src_ip=%d,proto=%d,dst_ip=%d,proto=%d\n",trigger->src_ip,trigger->trigger_proto,trigger->dst_ip,trigger->forward_proto);
	list_del(&trigger->list);
	spin_unlock_bh(&nf_conntrack_lock);
	kfree(trigger);
	return 0;
}


static void
refresh_timer(struct ipt_porttrigger *trigger, unsigned long extra_jiffies)
{
	NF_CT_ASSERT(trigger);
	spin_lock_bh(&nf_conntrack_lock);

	if(extra_jiffies == 0)
		extra_jiffies = TRIGGER_TIMEOUT * HZ;

	if (del_timer(&trigger->timeout)) {
		trigger->timeout.expires = jiffies + extra_jiffies;
		add_timer(&trigger->timeout);
	}
	spin_unlock_bh(&nf_conntrack_lock);
}

static void timer_timeout(unsigned long in_trigger)
{
	struct ipt_porttrigger *trigger= (void *) in_trigger;
	del_porttrigger_rule(trigger);
	DEBUGP("timer out, del trigger rule\n");
}


static inline int
ports_match(const struct ipt_mport *minfo, u_int16_t port)
{
	unsigned int i, m;
	u_int16_t s, e;
	u_int16_t pflags = minfo->pflags;

	for (i=0, m=1; i<IPT_MULTI_PORTS; i++, m<<=1) {
		if (pflags & m  && minfo->ports[i] == 65535){
			DEBUGP("port:%d don't match=%d\n",port,i);
			return 0;
		}

		s = minfo->ports[i];
		if (pflags & m) {
			e = minfo->ports[++i];
			m <<= 1;
		} else
			e = s;

		if ( port >= s && port <= e){
			//DEBUGP("s=%x,e=%x\n",s,e);
			return 1;
		}
	}
	DEBUGP("ports=%d don't match\n",port);
	return 0;
}


static inline int
packet_in_match(const struct ipt_porttrigger *trigger,
	const unsigned short proto,
	const unsigned short dport,
	const unsigned int src_ip)
{
	/*
	  Modification: for protocol type==all(any) can't work
      Modified by: ken_chiang
      Date:2007/8/21
    */
#if 0
	u_int16_t forward_proto = trigger->forward_proto;

	if (!forward_proto)
		forward_proto = proto;
	return ( (forward_proto == proto) && (ports_match(&trigger->forward_ports, dport)) );
#else
	u_int16_t forward_proto = trigger->forward_proto;
	DEBUGP("src_ip=%d,trigger->src_ip=%d in match\n",src_ip,trigger->src_ip);
	/*
	  Modification: for trigge port==incomeing port can't work
      Modified by: ken_chiang
      Date:2007/9/7
    */
	if(src_ip==trigger->src_ip){
		return 0;
	}
	DEBUGP("proto=%d,dport=%d in match\n",proto,dport);
	if (!forward_proto){
		DEBUGP("forward_proto=null\n");
		return ( ports_match(&trigger->forward_ports, dport) );
	}
	else{
		DEBUGP("forward_proto=%d, trigger->forward_ports:%d, dport:%d\n",forward_proto, trigger->forward_ports.ports[0], dport);
		return ( (trigger->forward_proto == proto) && (ports_match(&trigger->forward_ports, dport)) );
	}
#endif
}

static inline int
packet_out_match(const struct ipt_porttrigger *trigger,
	const unsigned short proto,
	unsigned short dport)
{
	/*
	  Modification: for protocol type==all(any) can't work
      Modified by: ken_chiang
      Date:2007/8/21
    */
    u_int16_t trigger_proto = trigger->trigger_proto;
    DEBUGP("proto=%d,dport=%d out match\n",proto,dport);
	if (!trigger_proto){
		DEBUGP("trigger_proto=null\n");
		return ( ports_match(&trigger->trigger_ports, dport) );
	}
	else{
		DEBUGP("trigger_proto=%d\n",trigger_proto);
		return ( (trigger->trigger_proto == proto) && (ports_match(&trigger->trigger_ports, dport)) );
	}
}


static unsigned int
add_porttrigger_rule(struct ipt_porttrigger *trigger)
{
	struct ipt_porttrigger *rule;

	spin_lock_bh(&nf_conntrack_lock);
	rule = (struct ipt_porttrigger *)kmalloc(sizeof(struct ipt_porttrigger), GFP_ATOMIC);

	if (!rule) {
		spin_unlock_bh(&nf_conntrack_lock);
		return -ENOMEM;
	}

	memset(rule, 0, sizeof(*trigger));
	INIT_LIST_HEAD(&rule->list);
	memcpy(rule, trigger, sizeof(*trigger));
	DEBUGP("add rule src_ip=%d,proto=%d,dst_ip=%d,proto=%d\n\n\n",rule->src_ip,rule->trigger_proto,rule->dst_ip,rule->forward_proto);
	list_add(&rule->list, &trigger_list);
	init_timer(&rule->timeout);
	rule->timeout.data = (unsigned long)rule;
	rule->timeout.function = timer_timeout;
	DEBUGP("rule->timer=%d\n",rule->timer);
	DEBUGP("rule->src_ip=%d\n",rule->src_ip);
	/*
	  Modification: for protocol type==all(any) sometime can't work if timer = 0
      Modified by: ken_chiang
      Date:2007/8/31
    */
	if(rule->timer<600)
		rule->timer =600;
	DEBUGP("rule->timer2=%d\n",rule->timer);
	rule->timeout.expires = jiffies + (rule->timer * HZ);
	add_timer(&rule->timeout);
	spin_unlock_bh(&nf_conntrack_lock);
	return 0;
}


static unsigned int
porttrigger_nat(struct sk_buff *skb, const struct xt_action_param *par)
{
	struct nf_conn *ct;
	enum ip_conntrack_info ctinfo;
	const struct iphdr *iph = ip_hdr(skb);
	struct tcphdr _tcph, *tcph;
	struct nf_nat_range newrange;
	struct ipt_porttrigger *found;
	struct udphdr _udph, *udph;
	unsigned short dest;

	tcph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_tcph), &_tcph);
	udph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_udph), &_udph);

	if (iph->protocol == IPPROTO_TCP) {
		dest = ntohs(tcph->dest);
	} else if (iph->protocol == IPPROTO_UDP) {
		dest = ntohs(udph->dest);
	} else {
		return XT_CONTINUE;
	}

	NF_CT_ASSERT(par->hooknum == NF_INET_PRE_ROUTING);
	/*
	  Modification: for trigger port==incoming port can't work
      Modified by: ken_chiang
      Date:2007/9/7
    */
	list_for_each_entry(found, &trigger_list, list) {
		if (packet_in_match(found, iph->protocol, dest, ntohl(iph->saddr))) {
			DEBUGP("DNAT, iph->protocol:%d, dest:%d, iph->saddr:%u.%u.%u.%u\n", iph->protocol, dest, NIPQUAD(iph->saddr));
			DEBUGP("DNAT: src IP %u.%u.%u.%u\n", NIPQUAD(found->src_ip));
			ct = nf_ct_get(skb, &ctinfo);
			newrange = ((struct nf_nat_range)
				{ IP_NAT_RANGE_MAP_IPS, found->src_ip, found->src_ip,
				found->range.min, found->range.max });

			return nf_nat_setup_info(ct, &newrange, IP_NAT_MANIP_DST);

		}
	}

	return XT_CONTINUE;
}


static unsigned int
porttrigger_forward(struct sk_buff *skb, const struct xt_action_param *par)
{
	const struct ipt_porttrigger_info *info = par->targinfo;
	const struct iphdr *iph = ip_hdr(skb);
	struct tcphdr _tcph, *tcph;
	struct ipt_porttrigger trigger, *found, match;
	struct udphdr _udph, *udph;
	unsigned short dest;

	tcph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_tcph), &_tcph);
	udph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_udph), &_udph);

	if (iph->protocol == IPPROTO_TCP) {
		dest = ntohs(tcph->dest);
	} else if (iph->protocol == IPPROTO_UDP) {
		dest = ntohs(udph->dest);
	} else {
		return XT_CONTINUE;
	}

	switch(info->mode)
	{
		case MODE_FORWARD_IN:
			/*
				Modification: for trigge port==incomeing port can't work
			Modified by: ken_chiang
			Date:2007/9/7
		*/
			list_for_each_entry(found, &trigger_list, list) {
				if (packet_in_match(found, iph->protocol, dest, ntohl(iph->saddr))) {
					refresh_timer(found, info->timer * HZ);
					DEBUGP("FORWARD_IN, iph->protocol:%d, dest:%d, iph->saddr:%u.%u.%u.%u\n", iph->protocol, dest, NIPQUAD(iph->saddr));
					return NF_ACCEPT;
				}
			}
			break;

		case MODE_FORWARD_OUT:
if (iph->protocol == IPPROTO_UDP)
	DEBUGP("UDP_OUT, dest:%d, iph->saddr:%u.%u.%u.%u\n", dest, NIPQUAD(iph->saddr));
			list_for_each_entry(found, &trigger_list, list) {
				if (packet_out_match(found, iph->protocol, dest)) {
					refresh_timer(found, info->timer * HZ);
					found->src_ip = ntohl(iph->saddr);
					//DEBUGP("FORWARD_OUT found ip=%x\n",found->src_ip);
				}
			}

			match.trigger_ports = info->trigger_ports;
			match.trigger_proto = info->trigger_proto;

			if( packet_out_match(&match, iph->protocol, dest) ) {
				DEBUGP("FORWARD_OUT_MATCH\n");
				memset(&trigger, 0, sizeof(trigger));
				trigger.src_ip = ntohl(iph->saddr);
				//DEBUGP("FORWARD_OUT trigger ip=%x\n",trigger.src_ip);
				trigger.trigger_proto = iph->protocol;
				trigger.forward_proto = info->forward_proto;
				memcpy(&trigger.trigger_ports, &info->trigger_ports, sizeof(struct ipt_mport));
				memcpy(&trigger.forward_ports, &info->forward_ports, sizeof(struct ipt_mport));
				add_porttrigger_rule(&trigger);
			}

			break;
	}

	return XT_CONTINUE;
}

static unsigned int
porttrigger_target(struct sk_buff *skb, const struct xt_action_param *par)
{
	const struct ipt_porttrigger_info *info = par->targinfo;
	const struct iphdr *iph = ip_hdr(skb);

	if ((iph->protocol != IPPROTO_TCP) && (iph->protocol != IPPROTO_UDP))
		return XT_CONTINUE;

	if (info->mode == MODE_DNAT)
		return porttrigger_nat(skb, par);
	else if (info->mode == MODE_FORWARD_OUT)
		return porttrigger_forward(skb, par);
	else if (info->mode == MODE_FORWARD_IN)
		return porttrigger_forward(skb, par);

	return XT_CONTINUE;
}

static int porttrigger_check(const struct xt_tgchk_param *par)
{
	const struct ipt_porttrigger_info *info = par->targinfo;
	struct list_head *cur, *tmp;

	if( info->mode == MODE_DNAT && strcmp(par->table, "nat") != 0) {
		DEBUGP("porttrigger_check: bad table `%s'.\n", par->table);
		return -EINVAL;
	}
/*	if (targinfosize != IPT_ALIGN(sizeof(*info))) {
		DEBUGP("porttrigger_check: size %u != %u.\n",
		       targinfosize, sizeof(*info));
		return -EINVAL;
	}
*/
	if (par->hook_mask & ~((1 << NF_INET_PRE_ROUTING) | (1 << NF_INET_FORWARD))) {
		DEBUGP("porttrigger_check: bad hooks %x.\n", par->hook_mask);
		return -EINVAL;
	}
	if ( info->forward_proto != IPPROTO_TCP && info->forward_proto != IPPROTO_UDP && info->forward_proto != 0) {
		DEBUGP("porttrigger_check: bad trigger proto.\n");
		return -EINVAL;
	}

	list_for_each_safe(cur, tmp, &trigger_list) {
		struct ipt_porttrigger *trigger = (void *)cur;
		del_timer(&trigger->timeout);
		del_porttrigger_rule(trigger);
	}

	return 0;
}



static struct xt_target porttrigger __read_mostly = {
	.name		= "PORTTRIGGER",
	.family		= NFPROTO_IPV4,
	.target		= porttrigger_target,
    .checkentry = porttrigger_check,
	.targetsize	= sizeof(struct ipt_porttrigger_info),
	//.table		= "nat",
	.hooks		= ((1 << NF_INET_PRE_ROUTING) | (1 << NF_INET_FORWARD)),
	.me			= THIS_MODULE,
};

static int __init init(void)
{
	return xt_register_target(&porttrigger);
}

static void __exit fini(void)
{
	xt_unregister_target(&porttrigger);
}

module_init(init);
module_exit(fini);

