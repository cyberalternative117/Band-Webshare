<?php

require_once("classes/System.php");




     //Rétourne le nom de la musique à partir de lu lien généré
    function selectMusicName($idUpload)
	{
		//Requête SQL sur la base principale
		System::BDD_connect();		
		
		$sql = "SELECT musique.nom AS asNom,musique.version AS asVer, upload.date AS asDate FROM musique,upload WHERE upload.id = ".$idUpload." AND musique.id = upload.idMusique";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
		
		System::BDD_close();
		
		$data = mysql_fetch_assoc($req);
		
		//Génération du nom et de l'extension
		$name = date('[j.m.2010]',$data['asDate']);
		$name .= " ".$data['asNom'];
		$name .= " (v.".$data['asVer'].")";
		$name .= ".gp5";
		
		return $name;
	}//Fin fonction orpheline





if(!isset($_GET['file']))
{
	echo "<h3>".System::$error."</h3>";
	exit;
}
else
{

	$file = addslashes($_GET['file']);
	$link = System::path("upload").$file;
	$nom = selectMusicName(addslashes($_GET['idUpload']));
	
	if (file_exists($link)) {
		
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header("Content-Disposition: attachment; filename=\"".basename($nom)."\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($link));
		ob_clean();
		flush();	
		readfile($link);
		exit;
	}
	else
	{
		echo "<h3>".System::$error."</h3>";
	}
}//Fin vérif GET
?>
