<? /* vi: set sw=4 ts=4: */
include "/htdocs/phplib/xnode.php";
include "/htdocs/phplib/trace.php";

function startcmd($cmd) {fwrite(a,$_GLOBALS["START"], $cmd."\n");}
function stopcmd($cmd)  {fwrite(a,$_GLOBALS["STOP"], $cmd."\n");}
function error($errno)  {startcmd("exit ".$errno); stopcmd("exit ".$errno);}

fwrite("w", $START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

startcmd(
	'XML_DOC=/tmp/wish_state.xml\n'.
	/* Prep the XML doc */
	'echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" > $XML_DOC\n'.
	'echo "<?xml-stylesheet type=\"text/xsl\" href=\"wish_state_style.xsl\"?>" >> $XML_DOC\n'.
	'echo "<wish_state>"  >> $XML_DOC\n'.
	/* Dump all cstat files to the XML doc */
	'cat /dev/ubicom_wish >> $XML_DOC\n'.
	/* End the XML doc */
	'echo "</wish_state>" >> $XML_DOC\n'.
);

error($ret);
?>
