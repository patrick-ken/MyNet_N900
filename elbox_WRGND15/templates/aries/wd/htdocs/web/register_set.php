HTTP/1.1 200 OK
Content-Type: text/xml

<?
$result = "OK";
if($_POST["result"]=="success") 
{
	setattr("/runtime/register", "set", "devdata set -e register=1");
	set("/runtime/register", "1");
	del("/runtime/register");	
}	
echo '<?xml version="1.0"?>\n';
?><register_set>
	<report><?=$result?></report>
</register_set>
