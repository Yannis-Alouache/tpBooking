<?php

class EquipementAnnonceModel extends Model
{
    protected string $tableName="equipementannonce";
	protected string $primaryKey = "";

	public function __construct()
	{
		parent::__construct($this->tableName, $this->primaryKey);
	}
}