<?php

include ("./app/BaseModel.php");

abstract class Model extends BaseModel
{
    function __construct(string $tableName, string $primaryKey)
    {
		  parent::__construct($tableName, $primaryKey);
    }
}
