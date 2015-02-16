/*
 * net/ipv4/netfilter/ipt_NATTYPE.c
 *	Endpoint Independent, Address Restricted and Port-Address Restricted NAT types'
 *	kernel side implementation.
 *
 * (C) Copyright 2009, Ubicom, Inc.
 *
 * This file is part of the Ubicom32 Linux Kernel Port.
 *
 * The Ubicom32 Linux Kernel Port is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 2 of the
 * License, or (at your option) any later version.
 *
 * The Ubicom32 Linux Kernel Port is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See
 * the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the Ubicom32 Linux Kernel Port.  If not,
 * see <http://www.gnu.org/licenses/>.
 *
 * Ubicom32 implementation derived from Cameo's implementation(with many thanks):
 * 
 */
#include <linux/types.h>
#include <linux/ip.h>
#include <linux/udp.h>
#include <linux/netfilter.h>
#include <linux/netfilter_ipv4.h>
#include <linux/module.h>
#include <net/protocol.h>
#include <net/checksum.h>
#include <net/ip.h>
#include <linux/tcp.h>
#include <net/netfilter/nf_conntrack.h>
#include <net/netfilter/nf_conntrack_core.h>
#include <net/netfilter/nf_nat_rule.h>
#include <linux/netfilter/x_tables.h>
#include <linux/netfilter_ipv4/ipt_NATTYPE.h>
#include <asm/atomic.h>

//#define NATTYPE_DEBUG 1
#if defined(NATTYPE_DEBUG)
#define DEBUGP(args...) printk(KERN_INFO args)
#else
#define DEBUGP(format, args...)
#endif

struct ipt_nattype {
	struct list_head list;
	struct timer_list timeout;
	unsigned int dst_addr;
	unsigned short nat_port;	/* Router src port */
	unsigned short proto;
	struct nf_nat_range range;
};

/*
 * TODO: It might be better to have a list for UDP and another list for TCP.
 * If we did this, the performance of the checks would be better in high
 * volume traffic.
 */
static LIST_HEAD(nattype_list);
static DEFINE_SPINLOCK(nattype_lock);

#if defined(NATTYPE_DEBUG)
static const char *types[] = {"TYPE_PORT_ADDRESS_RESTRICTED", "TYPE_ENDPOINT_INDEPENDENT", "TYPE_ADDRESS_RESTRICTED"};
static const char *modes[] = {"MODE_DNAT", "MODE_FORWARD_IN", "MODE_FORWARD_OUT"};
#endif

/*
 * nattype_free()
 *	Free the object.
 */
static void nattype_free(struct ipt_nattype *nattype)
{
	DEBUGP("%p: free\n", nattype);
	kfree(nattype);
}

/*
 * nattype_refresh_timer()
 *	Refresh the timer for this object.
 */
static void nattype_refresh_timer(struct ipt_nattype *nattype)
{
	NF_CT_ASSERT(nattype);
	BUG_ON(!spin_is_locked(&nattype_lock));

	if (del_timer(&nattype->timeout)) {
		DEBUGP("%p: timer refreshed\n", nattype);
		nattype->timeout.expires = jiffies + NATTYPE_TIMEOUT * HZ;
		add_timer(&nattype->timeout);
	}
}

/*
 * nattype_timer_timeout()
 *	The timer has gone off, self-destruct
 */
static void nattype_timer_timeout(unsigned long in_nattype)
{
	struct ipt_nattype *nattype= (void *) in_nattype;
	
	/*
	 * The race with list deletion is solved by ensuring
	 * that either this code or the list deletion code 
	 * but not both will remove the oject.
	 */
	DEBUGP("%p: timed out\n", nattype);
	spin_lock_bh(&nattype_lock);
	list_del(&nattype->list);
	spin_unlock_bh(&nattype_lock);
	nattype_free(nattype);
}

/*
 * nattype_packet_in_match()
 *	Ingress packet, try to match with this nattype entry.
 */
static inline int nattype_packet_in_match(const struct ipt_nattype *nattype, struct sk_buff *skb, const struct ipt_nattype_info *info)
{
	const struct iphdr *iph = ip_hdr(skb);
	u_int16_t dst_port;
	
	/*
	 * If the protocols are not the same, no sense in looking
	 * further.
	 */
	if (nattype->proto != iph->protocol) {
		return 0;
	}
		
	/*
	 * In ADDRESS_RESTRICT, the destination must match the source
	 * of this ingress packet.
	 */
	if (info->type == TYPE_ADDRESS_RESTRICTED) {
		if (nattype->dst_addr != iph->saddr) {
			return 0;
		}
	}

	/*
	 * Obtain the destination port value for TCP or UDP.  The nattype
	 * entries are stored in native (not host).
	 */
	if (iph->protocol == IPPROTO_TCP) {
		struct tcphdr _tcph, *tcph;
		tcph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_tcph), &_tcph);
		if (!tcph) {
			return 0;
		}
		dst_port = tcph->dest;
	} else if (iph->protocol == IPPROTO_UDP) {
		struct udphdr _udph, *udph;
		udph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_udph), &_udph);
		if (!udph) {
			return 0;
		}
		dst_port = udph->dest;
	}

	/*
	 * Our NAT port must match the ingress pacekt's destination port.
	 */
	if (nattype->nat_port == dst_port) {
		DEBUGP("nattype_packet_in_match: nat port: %d\n", ntohs(nattype->nat_port));
		return 1;
	}
	
	DEBUGP("nattype_packet_in_match fail: nat port: %d, dest_port: %d\n", ntohs(nattype->nat_port), ntohs(dst_port));
	return 0;
}

/*
 * nattype_packet_out_match()
 *	Egress packet try to match with nattype entry.
 */
static inline int nattype_packet_out_match(const struct ipt_nattype *nattype, struct sk_buff *skb, const u_int16_t nat_port, const struct ipt_nattype_info *info)
{
	const struct iphdr *iph = ip_hdr(skb);
	u_int16_t src_port;
	
	/*
	 * If the new NAT port does not match the entries NAT port they are
	 * not the same.
	 */
	if (nattype->nat_port != nat_port) {
		return 0;
	}
 
	/*
	 * If the protocols are not the same, no sense in looking
	 * further.
	 */
	if (nattype->proto != iph->protocol) {
		return 0;
	}

	/*
	 * In ADDRESS_RESTRICT, the destination address of the egress packet
	 * must match the nattype entry.
	 */
	if (info->type == TYPE_ADDRESS_RESTRICTED) {
		if (nattype->dst_addr != iph->daddr) {
			return 0;
		}
	}

	/*
	 * Obtain the source port value for TCP or UDP.  The nattype
	 * entries are stored in native (not host).
	 */
	if (iph->protocol == IPPROTO_TCP) {
		struct tcphdr _tcph, *tcph;
		tcph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_tcph), &_tcph);
		if (!tcph) {
			return 0;
		}
		src_port = tcph->source;
	} else if (iph->protocol == IPPROTO_UDP) {
		struct udphdr _udph, *udph;
		udph = skb_header_pointer(skb, ip_hdrlen(skb), sizeof(_udph), &_udph);
		if (!udph) {
			return 0;
		}
		src_port = udph->source;
	}
	
	/*
	 * Protocol and private source port and public source port must match.
	 */
	if (nattype->range.min.all == src_port) {
		DEBUGP("nattype_packet_out_match: nat port : %d, r.port: %d\n", ntohs(nattype->nat_port), ntohs(nattype->range.min.all));
		return 1;
	}

	DEBUGP("nattype_packet_out_match fail: nat port : %d, src_port: %d\n", ntohs(nattype->nat_port), ntohs(src_port));
	return 0;
}

/*
 * nattype_nat()
 *	Ingress packet on PRE_ROUTING hook, find match, update conntrack to allow
 */
static unsigned int nattype_nat(struct sk_buff *skb, const struct xt_action_param *par)
{
	struct nf_conn *ct;
	enum ip_conntrack_info ctinfo;
	struct ipt_nattype *nte;

	NF_CT_ASSERT(par->hooknum == NF_INET_PRE_ROUTING);
	spin_lock_bh(&nattype_lock);
	list_for_each_entry(nte, &nattype_list, list) {
		if (nattype_packet_in_match(nte, skb, par->targinfo)) {
			struct nf_nat_range newrange = ((struct nf_nat_range)
				{ nte->range.flags | IP_NAT_RANGE_MAP_IPS,
				  nte->range.min_ip, nte->range.min_ip,
				  nte->range.min, nte->range.max });
#if defined(NATTYPE_DEBUG)
			short port = ntohs(nte->nat_port);
			short rport = ntohs(nte->range.min.all);
#endif
			spin_unlock_bh(&nattype_lock);
			DEBUGP("Expand entry: port : %d, r.port: %d\n", port, rport);
		
			/*
			 * A match is found, update the conntrack to allow
			 * this ingress packet.
			 */
			ct = nf_ct_get(skb, &ctinfo);
			return nf_nat_setup_info(ct, &newrange, IP_NAT_MANIP_DST);
		}
	}
	spin_unlock_bh(&nattype_lock);
	DEBUGP("nattype_nat: not found\n");
	return XT_CONTINUE;
}

/*
 * nattype_forward()
 *	Ingress and Egress packet forwarding hook
 */
static unsigned int nattype_forward(struct sk_buff *skb, const struct xt_action_param *par)
{
	const struct iphdr *iph = ip_hdr(skb);
	void *protoh = (void *)iph + iph->ihl * 4;
	struct ipt_nattype *nte;
	struct nf_conn *ct;
	enum ip_conntrack_info ctinfo;
	const struct ipt_nattype_info *info = par->targinfo;
	u_int16_t nat_port;

	NF_CT_ASSERT(par->hooknum == NF_INET_FORWARD);

	/*
	 * Ingress packet, refresh the timer if we find an entry.
	 */
	if (info->mode == MODE_FORWARD_IN) {
		spin_lock_bh(&nattype_lock);
		list_for_each_entry(nte, &nattype_list, list) {
			if (nattype_packet_in_match(nte, skb, info)) {
				nattype_refresh_timer(nte);
				spin_unlock_bh(&nattype_lock);
				DEBUGP("FORWARD_IN_ACCEPT\n");
				return NF_ACCEPT;
			}
		}
		spin_unlock_bh(&nattype_lock);
		DEBUGP("FORWARD_IN_FAIL\n");
		return XT_CONTINUE;
	}
	
	/*
	 * Egress packet, create a new rule in our list.  If conntrack does
	 * not have an entry, skip this packet.
	 */	
	ct = nf_ct_get(skb, &ctinfo);
	if (ct == NULL) {
		return XT_CONTINUE;
	}
	NF_CT_ASSERT((ctinfo == IP_CT_NEW || ctinfo == IP_CT_RELATED));
	nat_port = ct->tuplehash[IP_CT_DIR_REPLY].tuple.dst.u.all;

	/*
	 * Search the list for an existing entry, if found, refresh the timer.
	 */
	spin_lock_bh(&nattype_lock);
	list_for_each_entry(nte, &nattype_list, list) {
		if (nattype_packet_out_match(nte, skb, nat_port, info)) {
			nattype_refresh_timer(nte);
			spin_unlock_bh(&nattype_lock);
			return XT_CONTINUE;
		}
	}
	spin_unlock_bh(&nattype_lock);

	/*
	 * Check for LAND attack and ignore.
	 */
	if (iph->daddr == iph->saddr) {
		DEBUGP("LAND attack: dest = %pI4, src = %pI4\n", &iph->daddr, &iph->saddr);
		return XT_CONTINUE;
	}

	/*
	* Check that we have valid source and destination addresses.
	*/
	if ((iph->daddr == (__be32)0) || (iph->saddr == (__be32)0)) {
		DEBUGP("null address check failure: dest = %pI4, src = %pI4\n", &iph->daddr, &iph->saddr);
		return XT_CONTINUE;
	}

	/*
	 * Check that we have a supported protocol.  Should be done by nattype_target().
	 */
	BUG_ON((iph->protocol != IPPROTO_TCP) && (iph->protocol != IPPROTO_UDP));

	/*
	 * Allocate a new entry
	 */
	nte = (struct ipt_nattype *)kmalloc(sizeof(struct ipt_nattype), GFP_ATOMIC | __GFP_NOWARN);
	if (!nte) {
		return XT_CONTINUE;
	}

	memset(nte, 0, sizeof(struct ipt_nattype));

	INIT_LIST_HEAD(&nte->list);
	
	nte->dst_addr = iph->daddr;
	nte->proto = iph->protocol;
	nte->nat_port = nat_port;

	nte->range.flags |= IP_NAT_RANGE_PROTO_SPECIFIED;
	nte->range.min_ip = nte->range.max_ip = iph->saddr;
	
	if (iph->protocol == IPPROTO_TCP) {
		nte->range.max.tcp.port = nte->range.min.tcp.port =  ((struct tcphdr *) protoh)->source ;
		DEBUGP("ADD: TCP nat port: %d\n", ntohs(nte->nat_port));
		DEBUGP("ADD: TCP Source Port: %d\n", ntohs(nte->range.min.tcp.port));
	} else if (iph->protocol == IPPROTO_UDP) {
		nte->range.max.udp.port = nte->range.min.udp.port = ((struct udphdr *) protoh)->source;
		DEBUGP("ADD: UDP NAT Port: %d\n", ntohs(nte->nat_port));
		DEBUGP("ADD: UDP IP_CT_DIR_ORIGINAL dst port: %d\n", ntohs(ct->tuplehash[IP_CT_DIR_ORIGINAL].tuple.dst.u.udp.port));
		DEBUGP("ADD: UDP Source Port: %d\n", ntohs(nte->range.min.udp.port));
	}

	/*
	 * Initilizer our timer and create the self-destruct timer.
	 */
	init_timer(&nte->timeout);
	nte->timeout.data = (unsigned long)nte;
	nte->timeout.function = nattype_timer_timeout;

	/*
	 * Now that the object is initialized we can put it on the global list.
	 * The add_timer() must be done inside of the list lock to ensure that
	 * the timeout does not occur before we get on the list.
	 */
	spin_lock_bh(&nattype_lock);
	nte->timeout.expires = jiffies + (NATTYPE_TIMEOUT  * HZ);
	add_timer(&nte->timeout);
	list_add(&nte->list, &nattype_list);
	spin_unlock_bh(&nattype_lock);
	return XT_CONTINUE;
}

/*
 * nattype_target()
 *	One of the iptables hooks has a packet for us to analyze, do so.
 */
static unsigned int nattype_target(struct sk_buff *skb, const struct xt_action_param *par)
{
	const struct ipt_nattype_info *info = par->targinfo;
	const struct iphdr *iph = ip_hdr(skb);

	/*
	 * TODO: This was checked later on, in the original code but does not make
	 * any sense.  If info is NULL, you can not dereference it.
	 */
	BUG_ON(info == NULL);

	/*
	 * The default behavior for Linux is PORT and ADDRESS restricted.  So
	 * we do not need to create rules/entries if we are in that mode.
	 */
	if (info->type == TYPE_PORT_ADDRESS_RESTRICTED) {
		return XT_CONTINUE;
	}

	/*
	 * Check if we have enough data in the skb.
	 */
	if (skb->len < ip_hdrlen(skb)) {
		DEBUGP("skb is too short for IP header\n");
		return XT_CONTINUE;
	}

	/*
	 * We can not perform endpoint filtering on anything but UDP and TCP.
	 */
	if ((iph->protocol != IPPROTO_TCP) && (iph->protocol != IPPROTO_UDP)) {
		return XT_CONTINUE;
	}

	DEBUGP("nattype_target: type = %s, mode = %s\n", types[info->type], modes[info->mode]);

	/*
	 * TODO: why have mode at all since par->hooknum provides this information?
	 */
	switch (info->mode) {
	case MODE_DNAT:
		return nattype_nat(skb, par);
	case MODE_FORWARD_OUT:
		return nattype_forward(skb, par);
	case MODE_FORWARD_IN:
		return nattype_forward(skb, par);
	}
	return XT_CONTINUE;
}

/*
 * nattype_check()
 *	check info (mode/type) set by iptables.
 */
static int nattype_check(const struct xt_tgchk_param *par)
{
	const struct ipt_nattype_info *info = par->targinfo;
	struct list_head *cur, *tmp;

	if ((info->type != TYPE_PORT_ADDRESS_RESTRICTED) && 
		(info->type != TYPE_ENDPOINT_INDEPENDENT) && 
		(info->type != TYPE_ADDRESS_RESTRICTED)) {
		DEBUGP("nattype_check: unknown type: %d\n", info->type);
		return -EINVAL;
	}

	if (info->mode != MODE_DNAT && info->mode != MODE_FORWARD_IN && info->mode != MODE_FORWARD_OUT) {
		DEBUGP("nattype_check: unknown mode - %d.\n", info->mode);
		return -EINVAL;
	}

	DEBUGP("nattype_check: type = %s, mode = %s\n", types[info->type], modes[info->mode]);

	if (par->hook_mask & ~((1 << NF_INET_PRE_ROUTING) | (1 << NF_INET_FORWARD))) {
		DEBUGP("nattype_check: bad hooks %x.\n", par->hook_mask);
		return -EINVAL;
	}
	
	/*
	 * Remove all entries from the nattype list.
	 */
drain:
	spin_lock_bh(&nattype_lock);
	list_for_each_safe(cur, tmp, &nattype_list) {
		struct ipt_nattype *nte = (void *)cur;

		/*
		 * If the timeout is in process, it will tear
		 * us down.  Since it is waiting on the spinlock
		 * we have to give up the spinlock to give the 
		 * timeout on another CPU a chance to run.
		 */
		if (!del_timer(&nte->timeout)) {
			spin_unlock_bh(&nattype_lock);
			goto drain;
		}
		
		DEBUGP("%p: removing from list\n", nte);
		list_del(&nte->list);
		spin_unlock_bh(&nattype_lock);
		nattype_free(nte);
		goto drain;
	}
	spin_unlock_bh(&nattype_lock);
	return 0;
}

static struct ipt_target nattype = {
	.name		= "NATTYPE",
	.family		= NFPROTO_IPV4,
	.target		= nattype_target,
	.checkentry	= nattype_check,
	.targetsize	= sizeof(struct ipt_nattype_info),
	.hooks		= ((1 << NF_INET_PRE_ROUTING) | (1 << NF_INET_FORWARD)),
	.me		= THIS_MODULE,
};

static int __init init(void)
{
	return xt_register_target(&nattype);
}

static void __exit fini(void)
{
	xt_unregister_target(&nattype);
}

module_init(init);
module_exit(fini);

MODULE_LICENSE("GPL");

