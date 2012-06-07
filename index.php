<?php
	//Includes
	
	require_once("./classes/System.php");
	require_once("./classes/Upload.php");
	require_once("./classes/Afficher.php");	
	require_once("./classes/Lister.php");	
	require_once("./classes/Reservation.php");	
	require_once("./classes/Verrou.php");
	
	//Initialisation de la session et de la page
	System::init();
	System::phpBB_init();
	System::start_head();
	System::head_tinyMCE();
	System::end_head();
	
	//Instanciation objets
	$Affichage = new Afficher();
	$Liste = new Lister();
	
	$Affichage->init($Liste);
	
	
	
	//Affichage	
	$Affichage->bodyHeader();
	$Affichage->menuPage();
	$Affichage->contentPage($Liste);
	$Affichage->footerPage();
	
?>

