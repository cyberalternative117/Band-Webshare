<?php
//Contexte : Classe "Lister"
echo '<div class="report_haut"></div>';
					echo '<div class="report_content">';
					
?>

<form  action="./index.php?villa=validation&amp;action=valider&amp;idReservation=<?php echo $this->temp1; ?>&amp;idMusique=<?php echo $this->temp2; ?> " method="post" >
  <table>
  	<tr>
  		<td><label><input name="fValidation" type="radio" id="RadioGroup1_0" value="1" checked="checked" />
        Valider</label>
        </td>
        <td><label><input type="radio" name="fValidation" value="0" id="RadioGroup1_1" />
        Refuser</label>
        </td>
        <td><label>
       &nbsp;  -- &gt;
        
        <input name="sub" type="submit" value="Continuer" /></label>
        </td>
    </tr>
    
  </table>
   
</form>
<?php
echo '</div>';
					echo '<div class="report_bas"></div>';	
?>