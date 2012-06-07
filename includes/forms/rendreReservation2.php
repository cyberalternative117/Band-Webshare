<?php

$reservation = new Reservation($this->url_idMusique,$_SESSION['userID'],System::current_timestamp());
$reservation->id = addslashes($_GET['idReservation']);
if($reservation->verifProprietaire_onID())
{
	$reservation->BDD_setDescriptionRendu($_POST['elm1']);//ADDSLASHES ajouté en aval
}
else
{
	echo '<br /><br /><p style="color:red;" >Vous ne pouvez pas modifier ce commentaire, la réservation ne vous appartient pas.</p>';
}
			

?>

