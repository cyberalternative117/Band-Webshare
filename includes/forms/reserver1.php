<?php

if($this->obj_Lister->verifMusiqueStatut($this->url_idMusique)!=0)
{
	echo "<br /><p>Cette musique est indisponible, vous ne pouvez pas la réserver</p><p style=\"font-size:9px;\" >(Merci de vérifier si vous ne l'avez pas déjà réservée)</p>";
}
elseif($_SESSION['bwRights'] < 2)
{
	echo "<br /><p>Vous n'avez pas le droit de réserver cette musique</p>";
}
else
{
	$reservation = new Reservation($this->url_idMusique,$_SESSION["userID"],System::current_timestamp());
	$reservation->description = addslashes($_POST['elm1']);
	if(!$reservation->executer())
	{
		echo '<p>Musique réservée avec succès</p>';
		echo '<p>Attention à la durée maximum de réservation !</p>';
	}
	
}//Fin else
?>