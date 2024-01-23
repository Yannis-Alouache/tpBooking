<?php

class ListeEquipementModel extends Model
{
	protected string $tableName="liste_equipement";
	protected string $primaryKey = "CodeEquipement";

	function __construct()
	{
		parent::__construct($this->tableName, $this->primaryKey);
	}

	public function getAllEquipment() {
		return $this->get();
	}

	public function getEquipmentByAnnounce(int $id){

		$this->reset();

		$equipmentsList = $this
		->select(["LibelleEquipement"])
		->join("liste_equipement","CodeEquipement","equipementannonce","CodeEquipement")
		->join("annonce","idAnnonce","equipementannonce","idAnnonce")
		->where("idAnnonce",$id)
		->get();

		return $equipmentsList;
	}

}
