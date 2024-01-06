<?php

/**
 * Data access layer.
 * Aucune idée si ce que je fais est bien un DAL. Au pire un parseur et adapteur de données.
 * Ce n'est pas un DAL
 */
class responseParser
{
	private mixed $result;

	/** @var mixed Les données qui vont être retournées */
	private mixed $data;

	/** @var mixed Le query SQL */
	private string $sql;

	public function __construct(mixed $data, string $sql, bool $parseResult = false)
	{
		$this->registerData($data, $sql);

		if($parseResult)
		{
			$this->parseReponse();
		}

		return $this;
	}

	public function parseReponse(): void
	{
		//Si c'est bien un select
		$isSelect = (preg_match('/SELECT/',trim($this->getSql())) !== 0);

		$data = $this->getData();

		//Si le résultat est false
		if($data === false)
		{
			//Si le query était un select, alors activer une Exception
			if($isSelect)
			{
				throw new DBexception("Une erreur est survenue lors de l'opération dans la base de données.");
			}
			else {
				$this->setResult(false);
			}
		}
		else if($data === array())
		{
			$this->setResult(true);
		}
		else {
			//Si le type des données reçues est un array
			if(gettype($data) === 'array')
			{
				//Si l'array n'a qu'un élément et que ce dernier existe
				if(sizeof($data) <= 1)
				{
					//Si ce qui vas être retourné est déjà une stdClass, alors le retourner
					if($data[0] instanceof stdClass)
					{
						$this->setResult($data[0]);
					}
					//Sinon, en faire un object (stdClass)
					else {
						$this->setResult(
							$this->makeObject(
								$data[0] ?? array()
							)
						);
					}
				}
				//Si l'array contient plus d'un seul élément
				else {
					//Si c'est un select
					if($isSelect)
					{
						//Ce qui va être retourné
						$toRes =
							array_map(function ($val) {
								return (object)json_decode(json_encode($val));
							},$data);

						//Retour des données
						$this->setResult(
							$toRes
						);
					}
					//Si c'est un insert, delete ou update
					else {
						//Alors on retourne true. Si l'opération a eu une erreur, une PDOException sera relevée
						$this->setResult(true);
					}
				}
			}
			//Si c'est un autre type de données (un string sûrement)
			else {
				//Si le string est 'true', 'false' ou que ce dernier est vide
				if($data === 'true' || $data === 'false')
				{
					//On retourne si le string est vide ou non, car si l'opération a eu une erreur, une PDOException sera relevée.
					$this->setResult($this->getData() === 'true');
				}
				//Sinon, on retourne true
				else {
					$this->setResult(true);
				}
			}
		}
	}

	/**
	 * //array map pour y transformer le tableau associatif retourné de la BDD en objet
	 * $res = array_map(function ($val) {
	 * return (object)json_decode(json_encode($val));
	 * },$resAll);
	 */

	private function registerData(mixed $data, string $sql): void
	{
		$this->setData($data);
		$this->setSql($sql);
	}

	private function makeObject(array $data): stdClass
	{
		return
			(object)
			json_decode(json_encode($data));
	}

	// ----------------------- assessors -----------------------

	public function getResult(): mixed
	{
		return $this->result ?? null;
	}

	private function setResult(mixed $res): void
	{
		$this->result = $res;
	}

	private function setData(mixed $data): void
	{
		$this->data = $data;
	}

	public function getData(): mixed
	{
		return $this->data ?? null;
	}

	private function setSql(string $sql): void
	{
		$this->sql = $sql;
	}

	public function getSql(): string|null
	{
		return $this->sql ?? null;
	}
}
