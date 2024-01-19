<?php

class AvisModel extends Model
{
    protected string $tableName="avis";
	protected string $primaryKey = "idAvis";

	public function __construct()
	{
		parent::__construct($this->tableName, $this->primaryKey);
	}
}