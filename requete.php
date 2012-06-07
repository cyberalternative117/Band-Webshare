<?php

require_once("./classes/System.php");

if(!empty($_GET['token']))
{
	System::BDD_connect();
	
	$sql = "UPDATE musique SET statut = 3 WHERE id = 10";
	$req = mysql_query($sql) or die (mysql_error());
	
	echo "<h3>REquête SQL OK</h3>";
	
	System::BDD_close();
}
else
{}

?>