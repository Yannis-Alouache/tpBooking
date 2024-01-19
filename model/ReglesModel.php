<?php

class ReglesModel extends Model 
{
    protected string $tablename = "regles";
    protected string $primaryKey = "idRegles";

    function __construct()
    {
        parent::__construct($this->tablename, $this->primaryKey);
    }
}