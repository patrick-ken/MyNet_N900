<module>
	<service><?=$GETCFG_SVC?></service>
        <inf>
<?
        include "/htdocs/phplib/xnode.php";
		$wan = query("/runtime/device/activewan");
		if($wan == "") $wan = "WAN-1";
		$infp = XNODE_getpathbytarget("", "inf", "uid", $wan, 0);
		$run_infp = XNODE_getpathbytarget("/runtime", "inf", "uid", $wan, 0);
        if($infp!="") echo dump(2, $infp);
?>        
        </inf>    
        <bwc>
			<? echo dump(1, "/bwc");?>        	
    	</bwc>	            
		<runtime>
			<device>
				<layout><? echo dump(0, "/runtime/device/layout");?></layout>
				<activewan><? 
						$wan = query("/runtime/device/activewan");
						if($wan == "") 
						{
							$wan = "WAN-1";
							set("/runtime/device/activewan", $wan);
						}
						echo dump(0, "/runtime/device/activewan"); 
				?></activewan>				
			</device>
			<inf>
				<auto_detect_bw><? echo query($run_infp."/auto_detect_bw");?></auto_detect_bw>
				<BandWidthField><? echo query($run_infp."/bwc/BandWidthField");?></BandWidthField>
			</inf>	
		</runtime>
</module>
