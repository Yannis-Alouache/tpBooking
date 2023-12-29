<?php

include("./model/Model.php");

class TestEntity extends Model
{
    protected string $tableName="test";

    function __construct()
    {
        parent::__construct($this->tableName);
    }




}
