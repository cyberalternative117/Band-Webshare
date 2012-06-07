<?php

class Verrou
{
	//Attributs
	var $id;
	var $idMusique;
	var $idUser;
	var $vDate;
	var $desc;
	
	//Constructeur
	function Verrou($idMusique, $idUser, $vDate)
	{
		$this->id = -1;
		$this->idMusique = $idMusique;
		$this->idUser = $idUser;
		$this->vDate = $vDate;
		
	}//Fin constructeur
	
}//FIN C(Verrou)

?>