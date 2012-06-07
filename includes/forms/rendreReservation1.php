<?php

if($this->obj_Lister->verifMusiqueStatut($this->url_idMusique)!=1)
{
	echo "<br /><p>Vous n'avez pas réservé cette musique, vous ne pouvez donc pas la rendre </p>";
}
else
{	
		$extension = strrchr($_FILES['gp']['name'], '.');
		if($extension != ".gp5"){echo '<br /><br /><p style="color:red;" >Seuls les fichiers Guitar pro au format .gp5 sont autorisés !</p>';}
		else
		{
			
			$upload = new Upload($_SESSION['userID'],$this->url_idMusique,System::current_timestamp());
			echo '<br /><br />';
			$upload->execute($_FILES);
			

?>

        <form action="./index.php?villa=reservation&amp;action=rendre2&amp;idReservation=<?php echo $upload->idReservation; ?>" method="post" >
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
        </form>

<?php
		}//Fin else (extension)
	
}//Fin else
?>