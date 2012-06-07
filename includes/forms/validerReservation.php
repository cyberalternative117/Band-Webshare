<?php

if(!isset($_POST['fValidation']))
{
	echo System::error("<br /><br /><p>Erreur lors de la transmission du formulaire </p>");	
}
else
{	
	$valide = $_POST['fValidation'];
	if($valide)
	{
		echo '<span>Validation de la modification : </span>';
	}
	else
	{
		echo '<span>Refus de la modification : </span>';
	}
?>

        <form action="./index.php?villa=validation&amp;action=valider1&amp;idReservation=<?php echo $this->url_idReservation; ?>&amp;idMusique=<?php echo $this->url_idMusique; ?>" method="post" >
        <table style="width:600px; padding-top:20px;" >
          <tr>
            <td style="width:100px;" ><p>Musique : <?php $this->obj_Lister->musiqueNom($this->url_idMusique); ?></p></td>
            </tr>
          <tr>
            <td><p>Merci de laisser un commentaire / explication :</p></td>
          </tr>
          <tr>
            <td><label>
              <textarea name="elm1" id="elm1" cols="45" rows="5"></textarea>
            </label></td>
            </tr>
          <tr>
            <td style="text-align:center;" ><label>
              <input type="submit" name="button" id="button" value="Enregistrer le commentaire" />
            </label></td>
            </tr>
        </table>
          <input type="hidden" name="event" value="<?php echo $valide ?>" />
        </form>

<?php
	
}//Fin else
?>