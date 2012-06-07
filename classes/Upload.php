<?php

class Upload 
{
	//Attributs minimum
	var $idUser;
	var $idMusique;
	var $vDate; //("date" = mot-clé)
	var $id;
	
	//Attributs autres
	var $lien;
	var $idReservation;
	
	//Constructeur
	function Upload($idUser,$idMusique,$date)
	{
		$this->idUser = $idUser;
		$this->idMusique = $idMusique;
		$this->vDate = $date;
		$this->id = -1;
		
		//Cherche la réservation associée
		$this->chercheReservation();
		
	}//Fin Constructeur
	
	
	function info()
	{
		echo '<h2>[Info : Upload]</h2>';
		echo '<p>id : '.$this->id.'</p>';
		echo '<p>idUser : '.$this->idUser.'</p>';
		echo '<p>idMusique : '.$this->idMusique.'</p>';
		echo '<p>vDate (timestamp) : '.$this->vDate.'</p>';
		echo '<p>Lien : '.$this->lien.'</p>';
		echo '<h2>[/Fin Info]</h2>';
	}//Fin M(info)
	
	//Sélectionne le dernier Upload validé pour la musique, pour le téléchargement.
	function selectDernierUpload($BDD_connect)
	{
		if($BDD_connect){System::BDD_connect();}
		
		$sql = "SELECT MAX(upload.date) AS asDate, upload.id AS asId, upload.idUser AS asIdUser  FROM upload ".
		"INNER JOIN reservation ON upload.idReservation = reservation.id ".
		"INNER JOIN validation ON validation.id = reservation.idValidation ".
		"WHERE validation.etat = 1 ".
		
		"AND upload.idMusique = ".$this->idMusique." ".
		//"ORDER BY upload.date ASC LIMIT 1  "; //Correction 15/12/2010
		"AND upload.date = (SELECT MAX(date) FROM upload WHERE idMusique = ".$this->idMusique.")";
		
		$req = mysql_query($sql) or die (mysql_error());
		$select = mysql_fetch_assoc($req);
		
		if(empty($select['asId']))
		{
			$sql2 = "SELECT upload.date, upload.id, upload.idUser AS asIdUser FROM upload WHERE idReservation = -1 AND idMusique = ".$this->idMusique." ";
			$req2 = mysql_query($sql2) or die (mysql_error());
			$select2 = mysql_fetch_assoc($req2);
			
			$this->id = $select2['id'];
			$this->vDate = $select2['date'];
			$this->idUser = $select2["asIdUser"];
			
		}
		else
		{
			$this->id = $select['asId'];
			$this->vDate = $select['asDate'];
			$this->idUser = $select["asIdUser"];
		}
		
		if($BDD_connect){System::BDD_close();}
		
				
	}//Fin M (selectDernierUpload)
	
	function chercheReservation()
	{		
		System::BDD_connect();
		
		
		//On sélectionne la réservation associée à ce rendu : c'est à dire pour la musique, et non-rendue.
		$sql = "SELECT id FROM reservation WHERE idMusique = ".$this->idMusique." AND rendue = 0";
		$req = mysql_query($sql) or die(mysql_error());
		$select = mysql_fetch_assoc($req);
		
		if(!empty($select['id']))
		{
			$this->idReservation = $select['id'];
		}
		else
		{
			$this->idReservation = -1;
		}
		
		System::BDD_close();		
	}//Fin M(chercheReservation)
	
	function execute($array_files)
	{
		//UPLOAD
		$this->creerLien();
		$dest_fichier = System::path("upload").$this->lien;
		
		move_uploaded_file($_FILES['gp']['tmp_name'], $dest_fichier);
		
		echo '<p style="color:green;" >Réservation rendue avec succès !</p>';
		
		//Base
		System::BDD_connect();
		
		//Mer à jour musique (en attente de validation)
		$sqla = "UPDATE musique SET statut = 2, date = ".$this->vDate." WHERE id = ".$this->idMusique." ";		
		$reqa = mysql_query($sqla) or die(mysql_error());
		
		//Insère enregistrement UPLOAD
		$sqlb = "INSERT INTO upload (idMusique,idUser,date,idReservation) VALUES('".$this->idMusique."','".$this->idUser."','".$this->vDate."','".$this->idReservation."') ";		
		$reqb = mysql_query($sqlb) or die(mysql_error());
				
		//Met à jour la table réservation
		$sqlc = "UPDATE reservation SET rendue = 1 WHERE id = '".$this->idReservation."' ";		
		$reqc = mysql_query($sqlc) or die(mysql_error());
		
		System::BDD_close();
		
	}//Fin M(execute)
	
		
	//Crée le lien vers le fichier : prend le chemin racine, ajoute le nom complexe
	function creerLien()
	{
		$this->lien = sha1($this->idUser+$this->idMusique+$this->vDate);
		$this->lien .= ".gp5";
		//Attention : Ne pas modifier le hash (dépendances).
		
	}//Fin M(creerLien)
	
	//Crée un téléchargement via le lien précédemment créé
	function creerDL()
	{	
		$this->creerLien();
		
		return '<a href="'.System::path("download").'download.php?file='.$this->lien.'" target="_blank" ><img src="images/gp5.jpg" alt="Guitar pro" /></a>';
		
	}//Fin M(creerDL)
	
	//Destructeur		
	public function __destruct()
	{
		$this->lien = "";
		$this->idUser = 0;
		$this->idMusique = 0;
		$this->vDate = 0;
	}

	
}//Fin C(Upload)
?>