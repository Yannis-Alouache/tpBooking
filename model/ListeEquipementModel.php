<?php

class ListeEquipementModel extends Model
{
	protected string $tableName="liste_equipement";
	protected string $primaryKey = "CodeEquipement";

	function __construct()
	{
		parent::__construct($this->tableName, $this->primaryKey);
	}

}
