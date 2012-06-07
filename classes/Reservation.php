<?php

class Reservation
{
	//Attributs
	var $id;
	var $idMusique;
	var $idUser;
	var $vDate;
	var $description;
	var $rendue;

	//Constructeur
	function Reservation($idMusique,$idUser,$vDate)
	{
		$this->id = -1; //Défaut
		$this->rendue = 0; //Défaut
		$this->idMusique = $idMusique;
		$this->idUser = $idUser;
		$this->vDate = $vDate;		
	}//Fin constructeur
	
	function chercheID($BDD_connect)
	{
			
		if($BDD_connect){System::BDD_connect();}
		//On sélectionne la réservation associée à ce rendu : c'est à dire pour la musique, et non-rendue.
		$sqli = "SELECT id FROM reservation WHERE idMusique = ".$this->idMusique." AND rendue = 0";
		$reqi = mysql_query($sqli) or die(mysql_error());
		$select = mysql_fetch_assoc($reqi);
		
		$this->id = $select['id'];
		
		if($BDD_connect){System::BDD_close();}
	}//Fin M(chercheID)
	
	//Demande de connaître l'id réservation
	function getBDD_rendue()
	{
			
		System::BDD_connect();
		
		$sqli = "SELECT rendue FROM reservation WHERE id = ".$this->id." ";
		$reqi = mysql_query($sqli) or die(mysql_error());
		$select = mysql_fetch_assoc($reqi);
		
		$this->rendue = $select['rendue'];
		
		System::BDD_close();
		
	}//Fin M(chercheID)
	
	function info()
	{
		echo '<h2>[Info : Reservation]</h2>';
		echo '<p>id : '.$this->id.'</p>';
		echo '<p>idMusique : '.$this->idMusique.'</p>';
		echo '<p>vDate (timestamp) : '.$this->vDate.'</p>';
		echo '<div>description :<br /><p>------------------------------</p> '.stripslashes($this->description).'<br /><p>------------------------------</p></div>';
		echo '<p>Rendue : '.$this->rendue.'</p>';
		echo '<h2>[/Fin Info]</h2>';
	}//Fin M(info)
	
	function isValide()
	{
		System::BDD_connect();
		$sql = "SELECT t1.etat AS asResult ".
		"FROM validation AS t1 ".
		"INNER JOIN reservation AS t2 ON t2.idValidation = t1.id ".
		"WHERE t2.id = ".$this->id." ";
		$req = mysql_query($sql);
		$select = mysql_fetch_assoc($req);
		System::BDD_close();
		
		return $select['asResult'];
		
	}//Fin M(isValide)
	
	//Renvoie un objet "Upload" correspondant, sinon renvoie -1
	function getUpload_Object()
	{
		$uplObject = -1;
		
		System::BDD_connect();
		
		$sql = "SELECT t1.id AS asIdUpload, t1.idUser AS asIdUser, t1.date AS asDate, t1.idMusique AS asIdMusique ".
		"FROM upload AS t1 ".
		"INNER JOIN reservation AS t2 ON t2.id = t1.idReservation ".
		"WHERE t1.idReservation = ".$this->id." ";
		
		$req = mysql_query($sql) or die(mysql_error());	
		
		System::BDD_close();
		$data = mysql_fetch_assoc($req);
		
		
		
		if(isset($data['asIdUpload']))
		{
			$uplObject = new Upload($data['asIdUser'],$data['asIdMusique'], $data['asDate']);
			$uplObject->id = $data['asIdUpload'];
		}
		else
		{
			$uplObject = new Upload(-1,-1,-1);			
		}//Fin else
			
		
		return $uplObject;
		
	}//Fin M(getUpload_Object()
							  
							  
							  
//********************************************************
	function executer()
	{
		$erreur = false;
		System::BDD_connect();
		
			//Création d'un enregistrement de type "validation" (de réservation)			 
			$sqli = "INSERT INTO validation (idUser, etat, date) VALUES ('-1','0','-1') ";			
			$reqi = mysql_query($sqli) or die ($erreur = true);
			
				//Récupération de l'IdValidation créé :
				$idValidation = mysql_insert_id();
			
			
			//Création d'un enregistrement de type "réservation" --> insertion de l'id de validation précédemment créé
			$sql = "INSERT INTO reservation (idMusique,idUser,description,date,rendue,idValidation) VALUES ('".$this->idMusique."','".$this->idUser."','".$this->description."','".$this->vDate."','".$this->rendue."','".$idValidation."')";
			$req = mysql_query($sql) or die ($erreur = true);
			
			
			
			//Update de l'état de la musique
			$sql2 = "UPDATE musique SET statut = 1, date = ".$this->vDate." WHERE id = ".$this->idMusique." ";
			$req2 = mysql_query($sql2) or die ($erreur = true);
						
			//Si une erreur survient : affiche et log
			if($erreur)
			{
				echo System::error_sql(mysql_error());
				$this->logErreur(mysql_error());
			}
			
		System::BDD_close();
		
		return $erreur;
		
	}//Fin M(executer)
	
	function logErreur($details)
	{
		$erreur = false;
		System::BDD_connect();
			$sql="INSERT INTO syslog (date,type,localisation,details) VALUES ('".$this->vDate."','0','reservation','".$details."'";
			$req = mysql_query($sql) or die ($erreur = true);
			if($erreur)
			{
				//Envoyer mail ?
			}
		System::BDD_close();
	}//Fin M(logErreur)
	
	
	//Vérifie si l'idUser renseigné dans l'objet réservation courant est bien le même que dans la base à la même date de réservation
	function verifProprietaire($BDD_connect)
	{
		
		$erreur = false;
		if($BDD_connect){System::BDD_connect();}
		
		$sqli="SELECT idUser FROM reservation WHERE id = ".$this->id."";
		$reqi = mysql_query($sqli) or die ($this->logErreur(mysql_error()));
		$datai = mysql_fetch_assoc($reqi);
		
		
		if($BDD_connect){System::BDD_close();}
		
						
		if($datai['idUser'] == $this->idUser)
		{
			$result = 1;
		}
		else
		{
			$result = 0;
		}
		
		return $result;	
		
	}//Fim M(verifProprietaire)
	
	function verifProprietaire_onID()
	{
		$erreur = false;
		System::BDD_connect();
		
		$sqli="SELECT idUser FROM reservation WHERE id= '".$this->id."' ";
		$reqi = mysql_query($sqli) or die ($erreur =true);
		$datai = mysql_fetch_assoc($reqi);
		
		System::BDD_close();
		
		//Si une erreur survient : affiche et log
		if($erreur)
		{
				echo System::error_sql(mysql_error());
				$this->logErreur(mysql_error());
		}
		
		if($datai['idUser'] == $this->idUser)
		{
			
			$result = 1;
		}
		else
		{			
			$result = 0;
		}
		
		return $result;	
	}//Fin M(verifProprietaire_onID)
	
	function BDD_setDescriptionRendu($texte)
	{
		$erreur = false;
		
		System::BDD_connect();
		
		$texte = addslashes($texte);
		
		$sql = "UPDATE reservation SET descriptionRendu = '".$texte."' WHERE id = ".$this->id."";
		$req = mysql_query($sql) or die($erreur = true);
		
		System::BDD_close();
		
		if($erreur)
		{
			echo System::error_sql(mysql_error());
			$this->logErreur(mysql_error());
		}
		else
		{		
			echo '<br /><br /><p style="color:green;" >Commentaire ajouté avec succès !</p>';
		}
		
	}//Fin M(Bdd_setDescriptionRendu)
	
	function valider($commentaire)
	{
		$erreur = false;
		
		System::BDD_connect();
		
		$sql1 = "UPDATE validation AS t1 ".
		"INNER JOIN reservation AS t2 ON t2.idValidation = t1.id ".
		"SET t1.etat = 1, t1.date = ".$this->vDate.", t1.idUser = ".$this->idUser.", t1.commentaire = '".$commentaire."' ".		
		"WHERE t2.id = ".$this->id." ";
		
		$req1 = mysql_query($sql1) or die ($erreur = true);
		if($erreur)
		{
			System::error_sql(mysql_error());
		}
		else
		{		
		
			$sql2 = "UPDATE musique AS t1 ".
			"SET t1.statut = 0, t1.date = ".$this->vDate.", t1.version = t1.version + 1  ".
			"WHERE t1.id = ".$this->idMusique." ";
			
			$req2 = mysql_query($sql2) or die ($erreur = true);
			
			if($erreur)
			{
				System::error_sql(mysql_error());
			}
			else
			{
				echo '<p style ="color:green;" >Musique validée</p>';
			}
			
		}//Fin else
		
		
		System::BDD_close();
		
		
		
	}//Fin M(Valider)
	
	function refuser($commentaire)
	{
		$erreur = false;
		
		System::BDD_connect();
		
		
		$sql1 = "UPDATE validation AS t1 ".
		"INNER JOIN reservation AS t2 ON t2.idValidation = t1.id ".
		"SET t1.etat = 2, t1.date = ".$this->vDate.", t1.idUser = ".$this->idUser.", t1.commentaire = '".$commentaire."' ".		
		"WHERE t2.id = ".$this->id." ";
		
		$req1 = mysql_query($sql1) or die ($erreur = true);
		
		if($erreur)
		{
			System::error_sql(mysql_error());
		}
		else
		{
			$sql2 = "UPDATE musique AS t1 ".
			"SET t1.statut = 0, t1.date = ".$this->vDate." ".
			"WHERE t1.id = ".$this->idMusique." ";
		
			$req2 = mysql_query($sql2) or die (mysql_error());
			
			if($erreur)
			{
				System::error_sql(mysql_error());
			}
			else
			{
				echo '<p style="color:green;" >Musique refusée avec succès</p>';
			}
		}//Fin else
		
		
		System::BDD_close();	
		
		
	}//Fin M(Refuser)
	
	
	
}//Fin C(Reservation)

?>