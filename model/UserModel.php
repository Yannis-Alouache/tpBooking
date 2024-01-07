<?php

include("./model/Model.php");

class UserModel extends Model
{
    protected string $tableName="utilisateur";
	protected string $primaryKey = "idUtilisateur";

    function __construct()
    {
        parent::__construct($this->tableName, $this->primaryKey);
    }

    
}
