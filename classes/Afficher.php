<?php

/******************************************************************************************************************************
Cette classe gère le contenu principal : index du site, liens, redirections, inclusions, elle est étroitement liée à la page
index.php et nécessite System.php :             ATTENTION :   !! Récupère les variables GET !!
//****************************************************************************************************************************/

class Afficher
{
	//Variables d'url : en lecture !
	var $url_villa;
	var $url_action;
	var $url_idUser;
	var $url_idMusique;
	var $url_idUpload;
	var $url_idReservation;
	var $url_temp;
	
	//Objets
	var $obj_Lister;
	

//############################################################################################################################	
	//Cette méthode gère les url --> A appeler en premier lieu
	function init($Lister)
	{			
		$this->obj_Lister = $Lister;
		
	    //Fonction de sécurisation des variables
	    function secur($var)
		{
			return addslashes($var);			
		}
		
		if(isset($_GET['villa']))
		{
			$this->url_villa = secur($_GET['villa']);
					
		}
		
		if(isset($_GET['action']))
		{
			$this->url_action = secur($_GET['action']);
		}
		if(isset($_GET['idMusique']))
		{
			$this->url_idMusique = secur($_GET['idMusique']);
		}
		else{$this->url_idMusique = -1;}
		
		if(isset($_GET['idUpload']))
		{
			$this->url_idUpload = secur($_GET['idUpload']);
		}
		else{$this->url_idUpload = -1;}
		
		if(isset($_GET['idUser']))
		{
			$this->url_idUser = secur($_GET['idUser']);
		}
		else{$this->url_idUser = -1;}
		
		if(isset($_GET['idReservation']))
		{
			$this->url_idReservation = secur($_GET['idReservation']);
		}
		else{$this->url_idReservation = -1;}
		
		if(isset($_GET['temp']))
		{
			$this->url_temp = secur($_GET['temp']);
		}
		else{$this->url_temp = -1;}
		
		
		
	}//Fin M(init)
	
//############################################################################################################################	
	//Cette méthode gère les titres de la page
	function titre()
	{
		$result ="";
		switch($this->url_villa)
		{
			case "historique":
				$result .= "Historique";
			break;
			
			case "reservation":
				$result .= "Réservation";
			break;
			
			default :
				$result.="Accueil";
			break;
			
		}//Fin switch		
		
		
		
		return $result;
	}//Fin M(titre)
	
//############################################################################################################################	
	//Header : onglets, Texte bannière
	function bodyHeader()
	{
		?>
		<body>	   

		<div id="conteneur"><!-- Global -->
		<div id="header"><!-- Header -->
		<!--Début Du Menu Horizontal --> 
		<a id="bouton1" href="./../login/index.php?deco=1"></a>
		<a id="bouton2" href="http://forum.band.geekoes.com" target="_blank" ></a>
		<a id="bouton3" href=""></a>   
		<!--Fin Du Menu Horizontal -->
		 
		<div id="texte"><!-- Slogan -->
		  <?php echo "<span style=\"text-decoration:underline; color:white; margin-left:80px; \" > .: Menu rapide :.</span>"; ?><br />
		  <!-- Slogan --> 
		  - <?php echo '<a style="color:white; " href="./index.php?villa=validation&amp;action=lister">Musiques en attente de validation ('.$this->obj_Lister->count_aValider2().')</a>';
		  ?><br />
		  <!-- Slogan --> 
		  - (A venir) <br /><!-- Slogan --> 
          
		</div><!-- Fin Slogan -->
		</div><!-- Fin header -->
        
        <?php
	}//Fin M(bodyHeader)
	
	
//############################################################################################################################
	//Gestion du menu de la page (arborescence)
	function menuPage()
	{
		?>
		<div id="contenu"><!-- Contenu --><!-- VOus pouvez rajouter ou retirer des menus -->	
		<div id="left"><!-- Colonne de Gauche --><!-- VOus pouvez rajouter ou retirer des menus -->	
		<div class="menu_haut">.: Musiques :.</div><div class="menu_fond" style="min-height:400px;">
		<table style="width:167px; text-align:left;" >
		<?php
		
		System::BDD_connect();
		$sql = "SELECT musique.nomCourt as asNom, musique.date, musique.id AS asIdMusique,musique.statut as asStatut FROM musique";
		$req = mysql_query($sql) or die(mysql_error());
		System::BDD_close();
		
		while($data=mysql_fetch_assoc($req))
		{
			$download = new Upload($_SESSION['userID'],$data['asIdMusique'],System::current_timestamp());
			$download->selectDernierUpload(true);
			$download->creerLien();
			echo '<tr><td style="padding-left:20px;width:38px;"><a href="./download.php?file='.$download->lien.'&amp;idUpload='.$download->id.' " ><img src="images/';
			switch($data['asStatut'])
			{
				case '0':
					echo "dossier.png\" ";
					if($_SESSION['bwRights'] >= 2)
					{
						$linkState = '<a href="./index.php?villa=reservation&amp;action=reserver&amp;idMusique='.$data['asIdMusique'].'" style="font-weight:normal;font-size:9px; color:green;" >Réserver</a>';
					}//Fin vérif droits
					else
					{
						$linkState = "0";
					}
				break;
				
				case '1':
					echo "dossierRes.png\" ";	
					$test = new Reservation($data['asIdMusique'],$_SESSION['userID'],$data['date']);
					$test->chercheID(true);
					if($test->verifProprietaire(true))
					{$linkState = '<a href="./index.php?villa=reservation&amp;action=rendre&amp;idReservation='.$test->id.'&amp;idMusique='.$data['asIdMusique'].'&amp;temp='.$test->vDate.'" style="color:purple; font-size:9px;font-weight:normal;">Rendre la musique</a>';}
					else{$linkState = '<span style="color:orange; font-size:9px;font-weight:normal;">Réservée</span>';}				
				break;
				
				case '2':
					echo "dossierVer.png\" ";
					$linkState = '<span style="color:orange; font-size:9px;font-weight:normal;">En attente de validation</span>';
				break;
				
				case '3':
					echo "dossierVer.png\" ";
					$linkState = '<span style="color:red; font-size:9px;font-weight:normal;">Verrouillée</span>';
				break;
			}
			
			echo 'width="38" height="44" alt="dossier" /></a></td>';
				
			
			?>
            
              <td style="width:129px;padding-left:0px;" ><p style="color:#000;font-family:Verdana, Geneva, sans-serif;font-size:10px;"><?php echo $data['asNom']; ?></p><p style="color:#333; font-size:9px;font-family:Verdana, Geneva, sans-serif;"><?php echo date('j/m/2010',$data['date']);?></p>
                <p style="font-family:Verdana, Geneva, sans-serif;"
                ><?php if($linkState != "0" ){echo $linkState;}	?></p>
                <p style="color:#333; font-size:9px;font-family:Verdana, Geneva, sans-serif;"><a href="./index.php?villa=historique&amp;idMusique=<?php echo $data['asIdMusique'];?> " style="color:#03C; font-weight:normal;"  >Historique</a></p></td>
            </tr>
            
            <?php
		}//Fin while
	
	
	?>      
  			</table>
  			<!--  Menu -->		
		   <br />
			</div>	
			<div class="menu_bas"></div><!-- Fin Menu -->	
			</div><!-- Fin Colonne de Gauche -->	
	<?php
	
	}//Fin M(menuPage)
	
	
//############################################################################################################################
	//Gestion de la page et des liens
	function contentPage()
	{
		
		?>
        <div id="right"><!-- Colonne de Droite -->
		<div class="contenu_haut">.: <?php echo $this->titre(); ?> :.</div>
		<div class="contenu_fond"> <!--  News -->
        <?php
			
			switch($this->url_villa)
			{
				case "historique":
					$this->obj_Lister->historique($this->url_idMusique);
				break;
				
				case "reservation":
					switch($this->url_action)
					{
						case "reserver" :
							echo '<span>Réserver une musique pour modification</span>';
							$this->includeForm("reserver.php");
						break;
						
						case "reserver1" :
							$this->includeForm("reserver1.php");
						break;
						
						case "rendre" :
							echo '<span>Rendre une musique réservée</span>';
							$this->includeForm("rendreReservation.php");
						break;
						case "rendre1" :
							echo '<span>Rendre une musique réservée</span>';
							$this->includeForm("rendreReservation1.php");
						break;
						case "rendre2" :
							echo '<span>Rendre une musique réservée</span>';
							$this->includeForm("rendreReservation2.php");
						break;
						
					}//Fin switch(reservation)	
										
				break;
				
				case "validation":
					if( ($_SESSION['bwRights'] < 7) && ($this->url_action != "lister") )
					{
						echo System::error_forbidden(" Vous n'avez pas les droits suffisants pour accéder à cette page, votre tentative d'accès a été enregistrée");
						//$this->Syslog(" Tentative d'accès au module de validation des musiques par l'utilisateur "); //--> à compléter
						
					}
					elseif($this->url_action == "lister")
					{
						echo '<br /><span>Musiques en attente de validation</span><br /><br />';
						$this->obj_Lister->aValider();					
					}
					else
					{
								
						switch($this->url_action)
						{
													
							case "valider":
								$this->includeForm("validerReservation.php");
							break;
							
							case "valider1":
								$this->includeForm("validerReservation1.php");
							break;		
							
						}//Fin switch
						
					}//Fin ($_SESSION['bwRights'])
					
				break;
				
				
				
				default:
					//$this->obj_Lister->historique($this->idMusique);
					//$this->obj_Lister->aValider();
				break;
				
			}//Fin switch
		
		?>
		
        <div class="clear"></div>
        </div><div class="contenu_bas"></div><!--  Fin News -->		
        <a href="#header"><img src="images/deco6.png" style="float:right;margin:2px 10px 0 0" alt=""/></a>
        </div><!--  Fin Colonne de Droite --> 
        <div class="clear"></div><!-- Ne pas supprimer -->
        </div><!--  Fin Contenu -->  
        <?php
	}//Fin M(contentPage)
	
	
//############################################################################################################################
	//Pied de page
	function footerPage()
	{
		?>				 
		<div id="pied">
		<!-- mention de copyright Ne pas retirer sans autorisation écrite -->	
		<div class="copyright">©<a href="">- GeeKoEs Webshare -</a> 2010 | Design <a href="http://www.kitgraphiquegratuit.org" title="kits gratuits" > Kitgraphiquegratuit.org</a>| Intégration par @lternative</div>
		<!-- mention de copyright Ne pas retirer sans autorisation écrite -->
		</div> 
		</div>
		
		</body>
		</html>
        <?php
	}//Fin M(footerPage)
	
	
	
	//############################################################################################################################
	//Pied de page
	function includeForm($form)
	{
		$path = System::path("forms");
		
		include($path.$form);			
				
	}//Fin M(includeForm)
	
}//Fin C(Afficher)

?>