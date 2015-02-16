<?
foreach('/runtime/inf')
{
	$uid = query('uid');
	if ($uid==$TYPE."-1")
	{
		$addrtype = query('inet/addrtype');
		if ($addrtype=="ipv4") {echo query('inet/ipv4/ipaddr').'\n';}
		else {echo query('inet/ppp4/local').'\n';}
	}
}
?>
