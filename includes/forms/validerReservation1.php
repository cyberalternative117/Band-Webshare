<?php

$reservation = new Reservation($this->url_idMusique,$_SESSION['userID'],System::current_timestamp());
$reservation->id = addslashes($_GET['idReservation']);

if($reservation->isValide() || !isset($_POST['elm1']))
{
	System::error("<p>Merci de ne pas naviguer depuis la barre d'URL.</p>");
}
else
{
	if($_POST['event'])
	{
		$reservation->valider(addslashes($_POST['elm1']));
	}
	else
	{
		$reservation->refuser(addslashes($_POST['elm1']));
	}
	
	
}//FIn verif
			

?>

