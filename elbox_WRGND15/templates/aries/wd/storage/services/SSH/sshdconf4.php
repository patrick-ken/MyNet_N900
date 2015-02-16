Port 22
Protocol 2,1

HostKey /etc/ssh/ssh_host_key
HostKey /etc/ssh/ssh_host_rsa_key
HostKey /etc/ssh/ssh_host_dsa_key

KeyRegenerationInterval 1h


LoginGraceTime 10
PermitRootLogin yes
StrictModes yes


RSAAuthentication yes
PubkeyAuthentication yes


PasswordAuthentication yes
PermitEmptyPasswords yes


TCPKeepAlive no
UseLogin no
UsePrivilegeSeparation no
ClientAliveCountMax 0
MaxStartups 1

<?
include "/htdocs/phplib/trace.php";
include "/htdocs/phplib/xnode.php";

  $j = 0;     
   foreach ("/sshd/entry")    
   {
      $j++;
      $num = query($path."/entry#");
      if (query("uid")=="")
      {
        break;
      }
   } 
  $timeout = query("/sshd/entry:".$j."/timeout");
  if($timeout=="")
  {
  	echo "ClientAliveInterval 0 \n";
  }
  else
  {
  //	echo "ClientAliveInterval ".$timeout."\n";
  	echo "ClientAliveInterval 30 \n";
  }  
  
 	echo "User1 orion\n";
	echo "Passwd1 orion\n";
  
   $shell = query("/sshd/entry:".$j."/shell");
   echo "shell ".$shell."\n";
/* 
   $connections=query("/sshd/entry/".$j. "/connections");
  if($connect=="")
  {
  	echo "MaxConnections 1 \n";
  }
  else
  {
  	echo "MaxConnections ".$connect."\n";
  }
*/
?>
