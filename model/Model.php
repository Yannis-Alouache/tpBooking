<?php

include ("./app/BaseModel.php");

abstract class Model extends BaseModel
{
    function __construct(string $tableName)
    {
		parent::__construct($tableName);
    }
}
