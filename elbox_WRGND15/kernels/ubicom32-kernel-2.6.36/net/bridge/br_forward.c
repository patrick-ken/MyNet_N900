/*
 *	Forwarding decision
 *	Linux ethernet bridge
 *
 *	Authors:
 *	Lennert Buytenhek		<buytenh@gnu.org>
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	as published by the Free Software Foundation; either version
 *	2 of the License, or (at your option) any later version.
 */

#include <linux/err.h>
#include <linux/slab.h>
#include <linux/kernel.h>
#include <linux/netdevice.h>
#include <linux/netpoll.h>
#include <linux/skbuff.h>
#include <linux/if_vlan.h>
#include <linux/netfilter_bridge.h>
#include "br_private.h"

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
unsigned char bcast_mac_addr[6] = { 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF };
#endif

#define BR_FORWARD_FOR_CONTROL_MULTICAST_SNOOP 1

static int deliver_clone(const struct net_bridge_port *prev,
			 struct sk_buff *skb,
			 void (*__packet_hook)(const struct net_bridge_port *p,
					       struct sk_buff *skb));

/* Don't forward packets to originating port or forwarding diasabled */
#ifdef CONFIG_BRIDGE_MULTICAST_BWCTRL
static inline int should_deliver(struct net_bridge_port *p,
	       	const struct sk_buff *skb)
#else
static inline int should_deliver(const struct net_bridge_port *p,
				 const struct sk_buff *skb)
#endif
{
#ifdef CONFIG_BRIDGE_MULTICAST_BWCTRL
	unsigned char * dest;
	struct ethhdr *eth = eth_hdr(skb);

	if (skb->dev == p->dev || p->state != BR_STATE_FORWARDING)
		return 0;

	dest = eth->h_dest;
	/* Bouble- 20100514 -
	 * Original code will limit bandwidth on broadcast and multicast
	 * Current code will limit on multicase only
	 * Another, for CPU overhead consideration, will not use memncmp(),
	 * just only partial compare with dest[0] only
	 */
	//if ((dest[0] & 1) && p->bandwidth !=0) {
	if ((dest[0] & 1) && (dest[0] != 0xFF) && p->bandwidth !=0) {
		if ((p->accumulation + skb->len) > p->bandwidth)
			return 0;
		p->accumulation += skb->len;
	}

	return ((p->flags & BR_HAIRPIN_MODE) || (skb->dev != p->dev));
#else
	return (((p->flags & BR_HAIRPIN_MODE) || skb->dev != p->dev) &&
		p->state == BR_STATE_FORWARDING);
#endif
}

static inline unsigned packet_length(const struct sk_buff *skb)
{
	return skb->len - (skb->protocol == htons(ETH_P_8021Q) ? VLAN_HLEN : 0);
}

int br_dev_queue_push_xmit(struct sk_buff *skb)
{
	/* drop mtu oversized packets except gso */
	if (packet_length(skb) > skb->dev->mtu && !skb_is_gso(skb))
		kfree_skb(skb);
	else {
		/* ip_fragment doesn't copy the MAC header */
		if (nf_bridge_maybe_copy_header(skb))
			kfree_skb(skb);
		else {
			skb_push(skb, ETH_HLEN);
			dev_queue_xmit(skb);
		}
	}

	return 0;
}

int br_forward_finish(struct sk_buff *skb)
{
	return NF_HOOK(NFPROTO_BRIDGE, NF_BR_POST_ROUTING, skb, NULL, skb->dev,
		       br_dev_queue_push_xmit);

}

static void __br_deliver(const struct net_bridge_port *to, struct sk_buff *skb)
{
	skb->dev = to->dev;

	if (unlikely(netpoll_tx_running(to->dev))) {
		if (packet_length(skb) > skb->dev->mtu && !skb_is_gso(skb))
			kfree_skb(skb);
		else {
			skb_push(skb, ETH_HLEN);
			br_netpoll_send_skb(to, skb);
		}
		return;
	}

	NF_HOOK(NFPROTO_BRIDGE, NF_BR_LOCAL_OUT, skb, NULL, skb->dev,
		br_forward_finish);
}

static void __br_forward(const struct net_bridge_port *to, struct sk_buff *skb)
{
	struct net_device *indev;

	if (skb_warn_if_lro(skb)) {
		kfree_skb(skb);
		return;
	}

	indev = skb->dev;
	skb->dev = to->dev;
	skb_forward_csum(skb);

	NF_HOOK(NFPROTO_BRIDGE, NF_BR_FORWARD, skb, indev, skb->dev,
		br_forward_finish);
}

/* called with rcu_read_lock */
void br_deliver(const struct net_bridge_port *to, struct sk_buff *skb)
{
	if (should_deliver(to, skb)) {
		__br_deliver(to, skb);
		return;
	}

	kfree_skb(skb);
}

/* called with rcu_read_lock */
void br_forward(const struct net_bridge_port *to, struct sk_buff *skb, struct sk_buff *skb0)
{
	if (should_deliver(to, skb)) {
		if (skb0)
			deliver_clone(to, skb, __br_forward);
		else
			__br_forward(to, skb);
		return;
	}

	if (!skb0)
		kfree_skb(skb);
}

static int deliver_clone(const struct net_bridge_port *prev,
			 struct sk_buff *skb,
			 void (*__packet_hook)(const struct net_bridge_port *p,
					       struct sk_buff *skb))
{
	struct net_device *dev = BR_INPUT_SKB_CB(skb)->brdev;

	skb = skb_clone(skb, GFP_ATOMIC);
	if (!skb) {
		dev->stats.tx_dropped++;
		return -ENOMEM;
	}

	__packet_hook(prev, skb);
	return 0;
}

static struct net_bridge_port *maybe_deliver(
	struct net_bridge_port *prev, struct net_bridge_port *p,
	struct sk_buff *skb,
	void (*__packet_hook)(const struct net_bridge_port *p,
			      struct sk_buff *skb))
{
	int err;

	if (!should_deliver(p, skb))
		return prev;

	if (!prev)
		goto out;

	err = deliver_clone(prev, skb, __packet_hook);
	if (err)
		return ERR_PTR(err);

out:
	return p;
}

/* called under bridge lock */
static void br_flood(struct net_bridge *br, struct sk_buff *skb,
		     struct sk_buff *skb0,
		     void (*__packet_hook)(const struct net_bridge_port *p,
					   struct sk_buff *skb))
{
	struct net_bridge_port *p;
	struct net_bridge_port *prev;

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP
	if((memcmp(eth_hdr(skb)->h_dest, bcast_mac_addr, 6) != 0) &&	// non-broadcast packet ?!
			((eth_hdr(skb)->h_proto == htons(ETH_P_IP)) || (eth_hdr(skb)->h_proto == htons(ETH_P_IPV6))))	// either IPv4 or IPv6
	{
		do_alpha_multicast(br, skb, skb0, __packet_hook);
	}
	else {
		prev = NULL;

		list_for_each_entry_rcu(p, &br->port_list, list) {
			prev = maybe_deliver(prev, p, skb, __packet_hook);
			if (IS_ERR(prev))
				goto out;
		}

		if (!prev)
			goto out;

		if (skb0)
			deliver_clone(prev, skb, __packet_hook);
		else
			__packet_hook(prev, skb);
		return;

out:
		if (!skb0)
			kfree_skb(skb);
	}
#else
	prev = NULL;

	list_for_each_entry_rcu(p, &br->port_list, list) {
		prev = maybe_deliver(prev, p, skb, __packet_hook);
		if (IS_ERR(prev))
			goto out;
	}

	if (!prev)
		goto out;

	if (skb0)
		deliver_clone(prev, skb, __packet_hook);
	else
		__packet_hook(prev, skb);
	return;

out:
	if (!skb0)
		kfree_skb(skb);
#endif
}


/* called with rcu_read_lock */
void br_flood_deliver(struct net_bridge *br, struct sk_buff *skb)
{
	br_flood(br, skb, NULL, __br_deliver);
}

/* called under bridge lock */
void br_flood_forward(struct net_bridge *br, struct sk_buff *skb,
		      struct sk_buff *skb2)
{
	br_flood(br, skb, skb2, __br_forward);
}

#ifdef CONFIG_BRIDGE_IGMP_SNOOPING
/* called with rcu_read_lock */
static void br_multicast_flood(struct net_bridge_mdb_entry *mdst,
			       struct sk_buff *skb, struct sk_buff *skb0,
			       void (*__packet_hook)(
					const struct net_bridge_port *p,
					struct sk_buff *skb))
{
	struct net_device *dev = BR_INPUT_SKB_CB(skb)->brdev;
	struct net_bridge *br = netdev_priv(dev);
	struct net_bridge_port *prev = NULL;
	struct net_bridge_port_group *p;
	struct hlist_node *rp;
#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
	struct port_group_mac *g;
#endif

	rp = rcu_dereference(br->router_list.first);
	p = mdst ? rcu_dereference(mdst->ports) : NULL;
	while (p || rp) {
		struct net_bridge_port *port, *lport, *rport;

		lport = p ? p->port : NULL;
		rport = rp ? hlist_entry(rp, struct net_bridge_port, rlist) :
			     NULL;

		port = (unsigned long)lport > (unsigned long)rport ?
		       lport : rport;

#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP

#ifdef BR_FORWARD_FOR_CONTROL_MULTICAST_SNOOP
		int wireless = atomic_read(&port->wireless_interface);
		if ( (wireless == 1) && // wireless interface
			(should_flood(skb) == 0))  //for some traffic, we always need to flood it
		{
			do_enhance(port, br, skb,__packet_hook);
		}
		else
		{
			if (wireless != 1 || accept_flooding_by_bw())
			{
				prev = maybe_deliver(prev, port, skb, __packet_hook);
				if (IS_ERR(prev))
			goto out;
			}
		}
#else
		int wireless = atomic_read(&port->wireless_interface);
		//for wifi port0 joined, prevent multicast packets to lan port unless anyone joined lan port
		if (should_flood(skb) == 0)
		{
			if (wireless == 1)
			{
				do_enhance(port, br, skb,__packet_hook);
			}
			else
			{
				list_for_each_entry(g, &port->igmp_group_list, list)
				{
					struct ethhdr * dest;
					dest = eth_hdr(skb);
					if (!memcmp(dest->h_dest, g->grpmac, 6))
					{
						prev = maybe_deliver(prev, port, skb, __packet_hook);
						if (IS_ERR(prev))
							goto out;
					}
				}
			}
		}
		else
		{
			if (wireless != 1 || accept_flooding_by_bw())
			{
				prev = maybe_deliver(prev, port, skb, __packet_hook);
				if (IS_ERR(prev))
					goto out;
			}
		}
#endif

#else
		prev = maybe_deliver(prev, port, skb, __packet_hook);
		if (IS_ERR(prev))
			goto out;
#endif

		if ((unsigned long)lport >= (unsigned long)port)
			p = rcu_dereference(p->next);
		if ((unsigned long)rport >= (unsigned long)port)
			rp = rcu_dereference(rp->next);
	}

	if (!prev)
		goto out;

	if (skb0)
		deliver_clone(prev, skb, __packet_hook);
	else
		__packet_hook(prev, skb);
	return;

out:
	if (!skb0)
		kfree_skb(skb);
}

/* called with rcu_read_lock */
void br_multicast_deliver(struct net_bridge_mdb_entry *mdst,
			  struct sk_buff *skb)
{
	br_multicast_flood(mdst, skb, NULL, __br_deliver);
}

/* called with rcu_read_lock */
void br_multicast_forward(struct net_bridge_mdb_entry *mdst,
			  struct sk_buff *skb, struct sk_buff *skb2)
{
	br_multicast_flood(mdst, skb, skb2, __br_forward);
}
#endif

#ifdef CONFIG_BRIDGE_ALPHA_MULTICAST_SNOOP

static int in_ipv4_flooding_list(struct sk_buff *skb)
{
	struct iphdr *iph;
	unsigned long prefix;

	if(!pskb_may_pull(skb , sizeof(struct iphdr)))
		return 0; //no ip header, should i flood it? i'm not sure

	iph = ip_hdr(skb);
	
	prefix = ntohl(iph->daddr) & 0xffff0000;
	
        //no wireless enhance for multicast management ip. 
  
        //flooding 224.0.0.0/16 

	if(prefix == 0xe0000000)
		return 1;

	//flooding 239.0.0.0/8
	if((prefix & 0xff000000 ) == 0xef000000)
		return 1;

	return 0;	
}

static int in_ipv6_flooding_list(struct sk_buff *skb)
{
	struct ipv6hdr *ipv6h;
	unsigned short prefix;

	if(!pskb_may_pull(skb , sizeof(struct ipv6hdr)))
		return 0; //no ipv6 header, should i flood it? i'm not sure

	ipv6h = ipv6_hdr(skb);

	prefix = ntohs(ipv6h->daddr.s6_addr16[0]);
	
	//flooding ff02::/16
	if(prefix == 0xff02)
		return 1;

	return 0;
}

#define PKT_DEFAULT_BUDGET (50)
u64 pkt_time_range[2] = {0ULL , 0ULL}; //jiffies_64
unsigned long available_pkt = PKT_DEFAULT_BUDGET;

static void housekeeping(void)
{
	u64 current_time;
	current_time = get_jiffies_64();

	if(!time_after_eq64(current_time , pkt_time_range[0]) ||
	   !time_before_eq64(current_time , pkt_time_range[1]))
	{
		//reset our available_pkt
		pkt_time_range[0] = current_time;
		pkt_time_range[1] = current_time + HZ;
		available_pkt = PKT_DEFAULT_BUDGET;
	}	
}

#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
int accept_flooding_by_bw(void)
#else
static int accept_flooding_by_bw(void)
#endif
{
	housekeeping();

	if(available_pkt == 0)
		return 0; //no budget for flooding

	--available_pkt;
	return 1;
}

#ifdef CONFIG_BRIDGE_HARDWARE_MULTICAST_SNOOP
int should_flood(struct sk_buff *skb)
#else
static int should_flood(struct sk_buff *skb)
#endif
{
	struct ethhdr *dest = eth_hdr(skb);

	if(dest->h_proto == htons(ETH_P_IP))
		return in_ipv4_flooding_list(skb);

	if(dest->h_proto == htons(ETH_P_IPV6))
		return in_ipv6_flooding_list(skb);

	return 0;
}

void do_alpha_multicast(
		struct net_bridge *br,
		struct sk_buff *skb,
		struct sk_buff *skb0,
		void (*__packet_hook)(const struct net_bridge_port *p, struct sk_buff *skb))
{
	struct net_bridge_port *p;
	struct net_bridge_port *prev;
	struct port_group_mac *g;

	prev = NULL;

#ifdef BR_FORWARD_FOR_CONTROL_MULTICAST_SNOOP
	list_for_each_entry_rcu(p, &br->port_list, list) {
		int wireless = atomic_read(&p->wireless_interface);

		if((wireless == 1) && // wireless interface
		   (should_flood(skb) == 0)) //for some traffic, we always need to flood it (tom, 20111228)
		{
			do_enhance(p, br, skb,__packet_hook);
		}
		else {
			//for wired interface, we flood the traffic
			//for wireless interface, we use bandwidth limit to flood it
			if(wireless != 1 || accept_flooding_by_bw()) {
				prev = maybe_deliver(prev, p, skb, __packet_hook);
				if (IS_ERR(prev))
					goto out;
			}
		}
	}	
#else	
	list_for_each_entry_rcu(p, &br->port_list, list) {
		int wireless = atomic_read(&p->wireless_interface);
		//for mld wifi port0 joined, prevent multicast packets to lan port unless anyone joined lan port
		if(should_flood(skb) == 0)
		{
			if (wireless ==1)
			{
				do_enhance(p, br, skb,__packet_hook);
			}
			else
			{
				list_for_each_entry(g, &p->igmp_group_list,list)
				{
					struct ethhdr * dest;
					dest = eth_hdr(skb);
					if (!memcmp(dest->h_dest, g->grpmac, 6))
					{
						prev = maybe_deliver(prev, p, skb, __packet_hook);
						if (IS_ERR(prev))
							goto out;
					}
				}
			}
		}
		else {
			//for wired interface, we flood the traffic
			//for wireless interface, we use bandwidth limit to flood it
			if(wireless != 1 || accept_flooding_by_bw()) {
				prev = maybe_deliver(prev, p, skb, __packet_hook);
				if (IS_ERR(prev))
					goto out;
			}
		}
	}
#endif

	if (!prev)
		goto out;

	if (skb0)
		deliver_clone(prev, skb, __packet_hook);
	else
		__packet_hook(prev, skb);
	return;

out:
	if (!skb0)
		kfree_skb(skb);
}

void do_enhance(struct net_bridge_port *p, struct net_bridge *br, struct sk_buff *skb,
		void (*__packet_hook)(const struct net_bridge_port *p, struct sk_buff *skb))
{
	struct port_group_mac *g;
	struct sk_buff *skb2;
	int found =0;

	/*  does group address stored in table ? */
	list_for_each_entry(g, &p->igmp_group_list, list) {
		struct ethhdr * dest;
		struct port_member_mac *m;

		dest = eth_hdr(skb);
		if (!memcmp(dest->h_dest, g->grpmac, 6)) {
			list_for_each_entry(m, &g->member_list, list) {
				if ((skb2 = skb_copy(skb, GFP_ATOMIC)) == NULL) {
					br->dev->stats.tx_dropped++;
					//kfree_skb(skb);
					return;
				}

				dest = eth_hdr(skb2);
				memcpy(dest->h_dest, m->member_mac, sizeof(uint8_t)*6);
				if (should_deliver(p, skb2)) {
					__packet_hook(p, skb2);
					found=1;
				}
				else
					kfree_skb(skb2);
			}
		}
	}
}
#endif
