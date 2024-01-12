<?php

class AnnouncesModel extends Model
{
    protected string $tableName="annonce";
	protected string $primaryKey = "idAnnonce";

    function __construct()
    {
        parent::__construct($this->tableName, $this->primaryKey);
    }

    
}