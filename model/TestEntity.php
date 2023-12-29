<?php

include("./model/Model.php");

class TestEntity extends Model
{
    protected string $tableName="test";
	protected string $primaryKey = "id";

    function __construct()
    {
        parent::__construct($this->tableName, $this->primaryKey);
    }
}
