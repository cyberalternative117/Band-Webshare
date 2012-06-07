<?php

if($this->obj_Lister->verifMusiqueStatut($this->url_idMusique)!=1)
{
	echo "<br /><p>Vous n'avez pas réservé cette musique, vous ne pouvez donc pas la rendre </p>";
}
else
{
	
	$reservation = new Reservation($this->url_idMusique,$_SESSION['userID'],$this->url_temp);
	$reservation->id=$this->url_idReservation;
	if(!$reservation->verifProprietaire(true)){echo "<br /><p>Vous n'avez pas réservé cette musique, vous ne pouvez donc pas la rendre </p>";}
	else
	{

?>

        <form  enctype="multipart/form-data" action="./index.php?villa=reservation&amp;action=rendre1&amp;idMusique=<?php echo $this->url_idMusique; ?>" method="post" >
        <table style="width:600px; padding-top:20px;" >
          <tr>
            <td height="21" style="width:100px;" ><p>Musique : <?php $this->obj_Lister->musiqueNom($this->url_idMusique); ?></p></td>
            </tr>
          <tr>
            <td><p>Rendre le fichier :
              <label>
                <input name="gp" type="file" id="button2" value="" size="50" />
              </label>
            </p></td>
          </tr>
          <tr>
            <td style="text-align:center;" ><label>
              <input type="submit" name="button" id="button" value="Envoyer le fichier Guitar Pro" />
            </label></td>
            </tr>
        </table>
        </form>

<?php
	}//Fin else
}//Fin else
?>