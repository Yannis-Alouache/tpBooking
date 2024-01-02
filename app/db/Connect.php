<?php

//Singleton
class Connect
{
    private static Connect $instance;


    private PDO $connect;


    function __construct()
    {

    }

    public static function getInstance(): Connect
    {
        if(!isset(self::$instance))
        {
            self::$instance = new Connect();
        }

        return self::$instance;
    }

	public function connect(): bool
	{
		$PDOStatus = true;

		try {
			$this->setConnect(
				new PDO("mysql:host=".$_ENV["DB_HOST"].":".$_ENV["DB_PORT"].";dbname=".$_ENV["DB_NAME"]."",$_ENV["DB_USER"],$_ENV["DB_PWD"])
			);
		} catch (Exception $e) {
			$PDOStatus = false;
		} finally {
			return $PDOStatus;
		}
	}

	public function excecute(string $sql): array
	{

		$res = array();

		try {
			$prepared = $this->getConnect()->prepare($sql);
			$prepared->execute();
			$resAll = $prepared->fetchAll(PDO::FETCH_ASSOC);

			if(sizeof($resAll) === 1) {
				$res = $resAll[0];
			}
		} catch (Exception $e) {
		} finally {
			return $res;
		}
	}

	public function disconnect(): void
	{
		$this->setConnect(null);
	}

    public function getConnect(): PDO
    {
        return $this->connect;
    }

    public function setConnect(PDO|null $connect): Connect
    {
		if(!isset($connect))
		{
			unset($this->connect);
		} else {
			$this->connect = $connect;
		}

        return $this;
    }
}
