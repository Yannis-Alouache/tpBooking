<?php

include "app/exceptions/DBexception.php";

//Singleton

/**
 * Singleton de connection à la BDD.
 * DAL
 */
class Connect
{
    private static Connect $instance;


    private PDO $connect;


    private function __construct()
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
				new PDO(
					"mysql:host=".$_ENV["DB_HOST"].":".$_ENV["DB_PORT"].";dbname=".$_ENV["DB_NAME"],
					$_ENV["DB_USER"],
					$_ENV["DB_PWD"],
					array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
				)
			);
		} catch (Exception $e) {
			$PDOStatus = false;
		} finally {
			return $PDOStatus;
		}
	}

	/**
	 * @throws Exception
	 */
	public function execute(string $sql, array $params): array|bool
	{
		//Prise de la connexion en singleton
		//Vas se connecter automatiquement à la BDD
		$conn =
			self::getInstance()
				->getConnect();
		// @deprecated Servait pour createGetRecord et createGetRecord, mais ne sert plus à rien
		//$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

		//Préparation de la query
		$prepared = $conn->prepare($sql);


		//Si le tableau des paramètres est vide, donner null pour ce qui va exécuter. Sinon, donner le tableau
		$execParams = (empty($params) ? null : $params);
		//Exécution de la query avec les paramètres
		$prepared->execute($execParams);

		//Prise des résultats
		$resAll = $prepared->fetchAll(PDO::FETCH_ASSOC);


		$res = $resAll;

		//Retour du résultat
		return $res;
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
