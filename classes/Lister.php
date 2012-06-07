<?php

class Lister
{
	var $temp1;
	var $temp2;
	
	//############################################################################################################################	
		//Affiche tous les upload
		//DEPRECIEEEEE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		function Upload()
		{
			System::BDD_connect();
			System::BDD_SSO_connect();
			
			$sql = "SELECT t1.date AS asDate, t2.nom asNom,t2.id asIdMusique, t3.id AS asIdUser, t3.pseudo AS asPseudo
			FROM ".System::$db2Name.".upload AS t1, ".System::$db2Name.".musique AS t2 , ".System::$dbName.".user_sso AS t3
			WHERE t2.id = t1.idMusique
			AND t3.id = t1.idUser 
			";
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()); 
			
			System::BDD_close();
			
			echo '<table style="text-align:center; width:600px; margin:auto;" >';
			echo '<tr><td><p>Musique</p></td><td><p>Date</p><td><p>Utilisateur</p><td><p>Commentaire</p><td><p>Télécharger</p></tr>';
				
			
			while($data=mysql_fetch_array($req))
			{
				$tmpUpl = new Upload($data['asIdUser'],$data['asIdMusique'],$data['asDate']);
				echo '<tr>';
					echo '<td><p>'.$data['asNom'].'</p></td>';
					echo '<td><p>'.date('j/m/2010',$tmpUpl->vDate).'</p></td>';
					echo '<td><p>'.$data['asPseudo'].'</p></td>';
					echo '<td><p>Lire</p></td>';
					echo '<td><p>'.$tmpUpl->creerDL().'</a></p></td>';
				echo '</tr>';
				
			}//Fin While
			echo '</table>';
					
			
		}//Fin M(Upload)
				
		
		//############################################################################################################################	
		//Affiche tous les upload
		function historique($idMusique)
		{
			System::BDD_connect();
			System::BDD_SSO_connect();
			
			$sql = "SELECT t1.date AS asDate, t4.date AS asDateV, t1.description AS asDesc, t4.commentaire AS asDescV, t2.date AS asUpDate, t3.pseudo AS asUser, t3.id AS asIdUser, t1.descriptionRendu AS asDescR, t1.id AS asIdRes, t4.etat AS asEtatVal, t5.pseudo AS asUserValid  ".
			"FROM ".System::$db2Name.".reservation AS t1 ".
			"INNER JOIN ".System::$dbName.".user_sso AS t3 ON t1.idUser = t3.id ".			
			"INNER JOIN ".System::$db2Name.".upload AS t2 ON t2.idReservation = t1.id ".
			"INNER JOIN ".System::$db2Name.".validation AS t4 ON t1.idValidation = t4.id ".
			//AJOUT
			"INNER JOIN ".System::$dbName.".user_sso AS t5 ON t4.idUser = t5.id ".
			//FIN AJOUT
			"WHERE t1.idMusique = ".$idMusique." ".
			"ORDER BY CONCAT(asDate, asUpDate, asDateV) DESC";
			
			$req = mysql_query($sql) or die (mysql_error());
			
			System::BDD_close();
			
			
			while($data = mysql_fetch_assoc($req))
			{
				$valide = 1; //Par défaut la musique est considérée comme valide
				//Pour chaque réservation : on recherche l'Upload associé :
				$tmpRes = new Reservation ($idMusique,$data['asIdUser'],$data['asDate']);
				$tmpRes->id = $data['asIdRes'];
				$tmpRes->getBDD_rendue();
				//Création de l'objet Upload associé (pour le DL)
				$tmpUpl = new Upload(-1,-1,-1);
				$tmpUpl = $tmpRes->getUpload_Object();
				if($tmpUpl->id == -1)
				{
					System::error("<p>Erreur lors de la création de l'objet pour le téléchargement</p>");
				}
				//$tmpUpl = new Upload($data['asIdUser'],$idMusique,$data['asDateUpl']);
				
				
				echo '<div class="report" >';
				
					
					echo '<table><tr><td><img src="./images/dossier_user.png" alt="reservation : " /> </td><td style="text-decoration:underline; font-weight:bold; " >Réservée par '.$data['asUser'].' le '.date('j/m/2010',$data['asDate']).' à '.date('H\hi:s',$data['asDate']).'</td></tr></table>';
					echo '<div class="report_haut"></div>';
					echo '<div class="report_content">';
					echo stripslashes($data['asDesc']);
					echo '</div>';
					echo '<div class="report_bas"></div>';
					
					if(!empty($data['asUpDate']))
					{
						echo '<table><tr><td><img src="./images/dossier_web.png" alt="reservation : " /> </td><td style="text-decoration:underline; font-weight:bold; ">Rendue par '.$data['asUser'].' le '.date('j/m/2010',$data['asUpDate']).' à '.date('H\hi:s',$data['asUpDate']).'</td></tr></table>';	
					echo '<div class="report_haut"></div>';
					echo '<div class="report_content">';
					echo stripslashes($data['asDescR']);
					echo '</div>';
					echo '<div class="report_bas"></div>';	
						
						//Vérif état validation + Commentaire du lien 
						switch($data['asEtatVal'])
						{
							case 0:
								$linkState = '| <span style="color:orange; font-size:12px; font-weight:normal;" > Télécharger (Attention, version non validée)</span>';
								$valide = 0;
							break;
							
							case 1:
								$linkState = '| <span style="color:green; font-size:12px; font-weight:normal;" > Télécharger</span>';
							break;
							
							case 2:
								$linkState = '| <span style="color:red; font-size:12px; font-weight:normal;" >Télécharger (Attention, version refusée)</span>';
							break;
						}
						
						if($valide)
						{
							$tmpUpl->creerLien();
							echo '<table><tr><td>Validation : '.date('j/m/2010',$data['asDateV']).' à '.date('H\hi:s',$data['asDateV']).' par '.$data['asUserValid'].'</td><td><a href="./download.php?file='.$tmpUpl->lien.'&amp;idUpload='.$tmpUpl->id.' ">'.$linkState.'</a></td></tr></table>';
						}
						else
						{
							$tmpUpl->creerLien();
							echo '<table><tr><td>Validation : en attente</td><td><a href="./download.php?file='.$tmpUpl->lien.'&amp;idUpload='.$tmpUpl->id.' ">'.$linkState.'</a></td></tr></table>';
						}
						
					echo '<div class="report_haut"></div>';
					echo '<div class="report_content">';
					echo stripslashes($data['asDescV']);
					echo '</div>';
					echo '<div class="report_bas"></div>';	
					}
					
					
				
				echo '<a href="#header"><img src="images/deco6.png" style="float:right;margin:2px 10px 0 0" alt=""/></a>';
				echo '</div>';
				
			}//Fin while
			
			
							
			
		}//Fin M(historique)
				
			
		//############################################################################################################################	
		//Liste des musiques à valider		
		function count_aValider()
		{
			System::BDD_connect();
			System::BDD_SSO_connect();
			
		$sql = "SELECT COUNT(*) ".
			"FROM ".System::$db2Name.".reservation AS t1 ".
			"INNER JOIN ".System::$dbName.".user_sso AS t3 ON t1.idUser = t3.id ".
			"INNER JOIN ".System::$db2Name.".upload AS t2 ON t2.idReservation = t1.id ".
			"INNER JOIN ".System::$db2Name.".validation AS t4 ON t1.idValidation = t4.id ".
			"INNER JOIN ".System::$db2Name.".musique AS t5 ON t1.idMusique = t5.id ".
			"WHERE t4.etat = 0 ";
			
			$req = mysql_query($sql) or die (mysql_error());
			
			System::BDD_close();
			
			
			$data = mysql_fetch_assoc($req);
			
			return  $data['COUNT(*)'];				
			
			
			
		}//FIN M(count_aValider)
		
		//############################################################################################################################	
		//Liste des musiques à valider		
		function count_aValider2()
		{
			$c=0;
			
			System::BDD_connect();
			System::BDD_SSO_connect();
			
		$sql = "SELECT t1.id ".
			"FROM ".System::$db2Name.".reservation AS t1 ".
			"INNER JOIN ".System::$dbName.".user_sso AS t3 ON t1.idUser = t3.id ".
			"INNER JOIN ".System::$db2Name.".upload AS t2 ON t2.idReservation = t1.id ".
			"INNER JOIN ".System::$db2Name.".validation AS t4 ON t1.idValidation = t4.id ".
			"INNER JOIN ".System::$db2Name.".musique AS t5 ON t1.idMusique = t5.id ".
			"WHERE t4.etat = 0 ";
			
			$req = mysql_query($sql) or die (mysql_error());
			
			System::BDD_close();
			
			
			while($data = mysql_fetch_assoc($req))
			{
				$c++;
			}
			
			return $c;			
			
			
			
		}//FIN M(count_aValider)
		
	//############################################################################################################################	
		//Liste des musiques à valider		
		function aValider()
		{
			//Force Variables temporaires
			$this->temp1 = 0;
			$this->temp2 = 0;
			
			System::BDD_connect();
			System::BDD_SSO_connect();
			
		$sql = "SELECT t1.id AS id, t1.date AS asDate, t1.description AS asDesc, t2.date AS asUpDate, t3.pseudo AS asUser, t1.descriptionRendu AS asDescR, t5.nom AS asNomMus, t5.id AS asIdMus  ".
			"FROM ".System::$db2Name.".reservation AS t1 ".
			"INNER JOIN ".System::$dbName.".user_sso AS t3 ON t1.idUser = t3.id ".
			"INNER JOIN ".System::$db2Name.".upload AS t2 ON t2.idReservation = t1.id ".
			"INNER JOIN ".System::$db2Name.".validation AS t4 ON t1.idValidation = t4.id ".
			"INNER JOIN ".System::$db2Name.".musique AS t5 ON t1.idMusique = t5.id ".
			"WHERE t4.etat = 0 ".
			"ORDER BY asDate ASC";
			
			$req = mysql_query($sql) or die (mysql_error());
			
			System::BDD_close();
			
			
			while($data = mysql_fetch_assoc($req))
			{
				echo '<div class="report" >';
					
					echo '<table><tr><td><img src="./images/dossier_user.png" alt = "musique" /></td><td><p style="color:#333; font-weight:bold;" > '.$data['asNomMus'].' ('.$data['asUser'].')</p></td></tr></table>';
					
					//Inclusion Formulaire de validation :
					if($_SESSION['bwRights'] >= 7)
					{
						$this->temp1 = $data['id'];
						$this->temp2 = $data['asIdMus'];
						include(System::path("forms")."aValider.php");
					}
					//Fin inclusion
					
					echo '<table><tr><td><p style="color:#333; font-weight:bold;" >Rendu : '.date('j/m/2010',$data['asUpDate']).' à '.date('H\hi:s',$data['asUpDate']).'</p></td></tr></table>';
					echo '<div class="report_haut"></div>';
					echo '<div class="report_content">';
					echo stripslashes($data['asDescR']);
					echo '</div>';
					echo '<div class="report_bas"></div>';	
					
					
					echo '<table><tr><td><p style="color:#333; font-weight:bold;" >Réservation : '.date('j/m/2010',$data['asDate']).' à '.date('H\hi:s',$data['asDate']).'</p></td></tr></table>';	
					
					echo '<div class="report_haut"></div>';
					echo '<div class="report_content">';
					echo stripslashes($data['asDesc']);
					echo '</div>';
					echo '<div class="report_bas"></div>';	
					
					
					
					
					
					
				
				echo '<a href="#header"><img src="images/deco6.png" style="float:right;margin:2px 10px 0 0" alt=""/></a>';
				echo '</div>';
				
			}//Fin while
			
			
		}//Fin M(aValider)
		
	//############################################################################################################################	
		//Affiche le nom de la musique via son ID
		function musiqueNom($idMusique)
		{
			//Requête SQL
			System::BDD_connect();
			$sql = "SELECT musique.nom FROM musique WHERE musique.id = ".$idMusique."";
			$req = mysql_query($sql) or die (System::error_sql(mysql_error()));
			System::BDD_close();
			
			$result = mysql_fetch_assoc($req);			
			echo $result['nom'];
			
		}//Fin M(musiqueNom)
		
	//############################################################################################################################	
		//Renvoie le statut de la musique via son ID
		function verifMusiqueStatut($idMusique)
		{
			//Requête SQL
			System::BDD_connect();
			$sql = "SELECT musique.statut FROM musique WHERE musique.id = ".$idMusique."";
			$req = mysql_query($sql) or die (System::error_sql(mysql_error()));
			System::BDD_close();
			
			$result = mysql_fetch_assoc($req);			
			$result = $result['statut'];
			
			return $result;
		}//Fin M(verifMMusiqueStatut
		
}//Fin C(Lister)

?>