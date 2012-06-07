<?php

if($this->obj_Lister->verifMusiqueStatut($this->url_idMusique)!=0)
{
	echo "<br /><p>Cette musique est indisponible, vous ne pouvez pas la réserver</p>";
}
elseif($_SESSION['bwRights'] < 2)
{
	echo "<br /><p>Vous n'avez pas le droit de réserver cette musique</p>";
}
else
{

?>

<form action="./index.php?villa=reservation&amp;action=reserver1&amp;idMusique=<?php echo $this->url_idMusique; ?>" method="post" >
<table style="width:600px; padding-top:20px;" >
  <tr>
    <td style="width:100px;" ><p>Musique : <?php $this->obj_Lister->musiqueNom($this->url_idMusique); ?></p></td>
    </tr>
  <tr>
    <td><p>&nbsp;</p>
      <p>Explications et commentaires (obligatoire pour la réservation) :</p></td>
  </tr>
  <tr>
    <td><label>
      <textarea name="elm1" id="elm1" cols="45" rows="5"  ></textarea>
    </label></td>
    </tr>
  <tr>
    <td style="text-align:center;" ><label>
      <input type="submit" name="button" id="button" value="Réserver et télécharger" />
    </label></td>
    </tr>
</table>
</form>

<?php
}//Fin else
?>