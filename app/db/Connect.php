<?php

//Singleton
class Connect
{
    private static Connect $instance;

    private PDO $connect;


    function __construct()
    {
        $this->setConnect(
            new PDO("mysql:host=".$_ENV["DB_HOST"].":".$_ENV["DB_PORT"].";dbname=".$_ENV["DB_NAME"]."",$_ENV["DB_USER"],$_ENV["DB_PWD"])
        );
    }

    public static function getInstance(): Connect
    {
        if(!isset(self::$instance))
        {
            self::$instance = new Connect();
        }

        return self::$instance;
    }

    public function getConnect(): PDO
    {
        return $this->connect;
    }

    public function setConnect(PDO $connect): Connect
    {
        $this->connect = $connect;
        return $this;
    }
}
