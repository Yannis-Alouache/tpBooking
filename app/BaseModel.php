<?php

include_once("./app/interfaces/ORMInterface.php");
include_once("./app/db/Connect.php");
include_once("./app/db/builders.php");

include("./app/responseParser.php");


/**
 * Modèle retenant toutes les méthodes de l'ORM
 */
class BaseModel implements ORMInterface
{
	use builders;

	/**
	 * Définit l'ordre des méthodes en fonction de l'instruction SQL
	 * @var array|array[]
	 */
	private array $methodOrder = array(
		"SELECT" => array(
			"method",
			"distinct",
			"columns",
			"from",
			"table",
		),

		"UPDATE" => array(
			"method",
			"table",
			"from",
		),

		"INSERT" => array(
			"method",
			"from",
			"table",
		),

		"DELETE" => array(
			"method",
			"from",
			"table",
		),

		"TRUNCATE" => array(
			"method",
			"from",
			"table"
		)
	);

	private array $params = array();

	/**
	 * Array qui sert d'ordre aux instructions SQL et pour construire le query SQL.
	 * Se constitue ainsi :
	 * - la clef = le nom de l'instruction.
	 * - Valeur = autre array avec les propriétés suivantes : <br/>
	 * 		- type : le type de données qui sera retenu : array si on peut y mettre plusieurs bouts (ex : WHERE est un array car on peut y mettre plusieurs conditions SQL) et string si on ne peut y mettre qu'une seule valeur (ex : LIMIT de SQL). <br/>
	 * 		- sql : quoi mettre avant le contenu de l'instruction (WHERE, ORDER BY, LIMIT, etc.) <br/>
	 * 		- builder : le nom de la fonction qui va être appellé afin de 'compiler' le contenu de la requête SQL. <br/>
	 *
	 * @var array
	 */
	private array $sqlOrder = array(
		"columns" => array(
			"type" => "array",
			"builder" => "buildColumns",
			"build" => false,
		),
		"insert" => array(
			"type" => "array",
			"builder" => "buildInsert",
		),
		"update" => array(
			"type" => "array",
			"builder" => "buildUpdates",
		),
		"join" => array(
			"type" => "array",
			"builder" => "buildJoints",
		),
		"where" => array(
			"type" => "array",
			"sql" => "WHERE",
			"builder" => "buildWhere",
		),
		"groupby" => array(
			"type" => "array",
			"sql" => "GROUP BY",
			"builder" => "buildGroupBy",
		),
		"orderby" => array(
			"type" => "array",
			"sql" => "ORDER BY",
			"builder" => "buildOrderBy",
		),
		"limit" => array(
			"type" => "string",
			"sql" => "LIMIT",
			"builder" => "buildLimit",
		),
		"distinct" => array(
			"type" => "string",
			"sql" => "DISTINCT",
			"builder" => "none",
			"build" => false,
		),
		"before" => array(
			"type" => "string",
			"builder" => "buildBefore"
		),
		"after" => array(
			"type" => "string",
			"builder" => "buildAfter"
		),
	);

	/** @var Connect Singleton de connection à la BDD */
	private Connect $conn;

	/** @var string Le query qui a été construit */
	private string $query;

	/** @var stdClass Classe retenant les bouts de la requête. */
	private stdClass $queryBits;

	/** @var string Le nom de la table */
	private string $tableName;

	/** @var string|null La clef primaire de la table */
	private ?string $primaryKey;

	function __construct(string $tableName, string $primaryKey = null)
	{
		$this->setTableName($tableName);
		$this->setTablePrimaryKey($primaryKey);

		$this->registerQueryBits();
	}

	/**
	 * Instancie la variable queryBits et ses bouts de base qui permettent de faire une requête simple.
	 * @return void
	 */
	private function registerQueryBits(): void
	{
		$this->queryBits = new stdClass();

		//Instructions optionnelles
		//Prise de toutes les parties d'instruction SQL définies
		foreach ($this->getSqlOrder() as $clef => $params)
		{
			//Prise du type de l'instruction (si c'est un array ou string).
			switch($params["type"]) {
				case "array":
					//Initialisation de l'array
					$this->setQBit($clef,array());
					break;
				case "string":
					//Initialisation du string
					$this->setQBit($clef,null);
					break;
			}
		}

		//Query simple
		//Instructions de base
		$this->setQBit("method","SELECT");
		$this->setQBit("columns",["*"]);
		$this->setQBit("from","FROM");
		$this->setQBit("table",$this->getTableName());
	}

	// ------------- Base queries (all methods in the interface) -------------

	//OK
	public function where(string $colonne, mixed $value, string $operator = "=", string|null $cond=null): BaseModel
	{
		$finalWhere = array(
			"col" => $colonne,
			"value" => $value,
			"operator" => $operator,
			"cond" => $cond ?? null,
		);

		$this->insertQBit("where",$finalWhere);

		return $this;
	}

	//OK
	public function orWhere(string $colonne, mixed $value, string $operator = "="): BaseModel
	{
		$this->where($colonne, $value, $operator, "OR");
		return $this;
	}

	//OK
	public function andWhere(string $colonne, mixed $value, string $operator = "="): BaseModel
	{
		$this->where($colonne, $value, $operator, "AND");
		return $this;
	}

	//OK
	public function find(int $primaryKey): BaseModel
	{
		$this->where($this->getTablePrimaryKey(),$primaryKey);

		$this->limit(1);
		return $this;
	}

	//OK
	public function findBy(string $colonne, mixed $value): BaseModel
	{
		$this->where($colonne, $value);
		$this->limit(1);
		return $this;
	}

	//OK
	public function create(array $attributs): BaseModel
	{
		$this->setQBit("method","INSERT");
		$this->setQBit("from","INTO");

		$this->setQBit("insert",$attributs);

		return $this;
	}

	//OK
	public function update(array $attributs): BaseModel
	{

		$this->setQBit("method","UPDATE");
		$this->setQBit("from","SET");

		$this->setQBit("update",$attributs);

		return $this;
	}

	//OK
	public function delete(): BaseModel
	{
		$this->setQBit("method", "DELETE");

		return $this;
	}


	//OK
	public function join(string $table, string $tableCol, string $joinedTable , string $joinedCol, string $joinType="INNER JOIN", string $alias=null): BaseModel
	{
		$joint = $joinType . " ";

		$joint .= $joinedTable .  " " . $alias ?? null;
		$joint .= " ON ";
		$joint .= $table . "." . $tableCol;

		$joint .= "=" . ($alias ?? $joinedTable) . "." . $joinedCol;

		$this->insertQBit("join", $joint);

		return $this;
	}

	//OK
	public function orderBy(string $colonne, string $mode="ASC"): BaseModel
	{
		$toInsert = array(
			"col" => $colonne,
			"mode" => $mode,
		);

		$this->insertQBit("orderby",$toInsert);
		return $this;
	}

	//OK
	public function limit(int|string $limit): BaseModel
	{
		$this->setQBit("limit", $limit);
		return $this;
	}

	// --------- Executors ---------

	/**
	 * @throws Exception
	 */
	//OK
	public function all(): array|bool|stdClass|null
	{
		$this->setQBit("limit",null);
		return $this->exec();
	}


	/**
	 * @throws Exception
	 */
	//OK
	public function get(): array|bool|stdClass|null
	{
		return $this->exec();
	}

	/**
	 * @throws Exception
	 */
	//OK
	public function first(): array|bool|stdClass|null
	{
		$this->limit(1);
		return $this->exec();
	}

	/**
	 * @throws Exception
	 */
	//OK
	public function last(): array|bool|stdClass|null
	{
		$res = $this->exec();

		if(empty($res))
		{
			return new stdClass();
		}
		else {
			$slice =  array_slice($res,-1,1);
			$dal = new responseParser($slice, $this->getQuery(), true);

			return $dal->getResult();
		}
	}


	/**
	 * @throws Exception
	 */
	//OK
	public function latest(string $column = null): array|bool|stdClass|null
	{
		//Je ne peux pas mettre getQBit, car array_unshift prend par référence les bouts d'orderby
		array_unshift($this->queryBits->orderby, array(
			"col" => $column ?? $this->getTablePrimaryKey() ?? "created_at",
			"mode" => "DESC",
		));

		$this->limit(1);

		return $this->exec();
	}

	/**
	 * @throws Exception
	 */
	//OK
	public function oldest(string $column = null): array|bool|stdClass|null
	{
		//Je ne peux pas mettre getQBit, car array_unshift prend par référence les bouts d'orderby
		array_unshift($this->queryBits->orderby, array(
			"col" => $column ?? $this->getTablePrimaryKey() ?? "created_at",
			"mode" => "ASC",
		));

		$this->limit(1);

		return $this->exec();
	}


	/**
	 * @throws Exception
	 */
	//OK
	public function truncate(): bool
	{
		$this->setQBit("method","TRUNCATE");
		$this->setQBit("from","TABLE");

		return $this->exec();
	}

	//OK
	public function select(array $colonnes, bool $keepOld = false): BaseModel
	{
		$toInsert = array();

		foreach ($colonnes as $col)
		{
			if($keepOld)
			{
				$this->insertQBit("columns",$col);
			}

			$toInsert[] = $col;
		}

		if(!$keepOld)
		{
			$this->setQBit("columns",$toInsert);
		}

		return $this;
	}

	// --------- Executors (sql funcs) ---------

	/**
	 * Partie commune à MAX, MIN, AVG et COUNT
	 * @param string $func Le nom de la fonction SQL
	 * @param string $col La colonne
	 * @param string|null $as Si la colonne doit avoir un nom. Optionnel
	 * @return void
	 */
	private function funcSQL(string $func, string $col, string $as = null): void
	{
		$toPush = $func;
		$toPush .= "(";
		$toPush .= $col;
		$toPush .= ")";

		//si un nom personnalisé est demandé
		if(isset($as))
		{
			$toPush .= " AS ";
			$toPush .= $as;
		}

		$this->insertQBit("columns",$toPush);
	}

	//OK
	public function max(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("MAX", $colonne, ($as ?? null));
		return $this;
	}

	//OK
	public function min(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("MIN", $colonne, ($as ?? null));
		return $this;
	}

	//OK
	public function avg(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("AVG", $colonne, ($as ?? null));
		return $this;
	}

	//OK
	public function count(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("COUNT", $colonne, ($as ?? null));
		return $this;
	}

	//OK
	public function groupBy(string|array $group): BaseModel
	{
		//si plusieurs group by
		if(is_array($group))
		{
			foreach ($group as $single)
			{
				$this->insertQBit("groupby", $single);
			}
		}
		else {
			$this->insertQBit("groupby", $group);
		}

		return $this;
	}

	//OK
	public function distinct(): BaseModel
	{
		$this->setQBit("distinct", "DISTINCT");
		//$this->queryBits->distinct = "DISTINCT";
		return $this;
	}

	// ------------- Combinaisons -------------


	/**
	 * @deprecated La configuration de PDO fait que cette fonction ne marche pas. (Voir la classe Connect pour
	 * y proposer des modifs sur la config de PDO si vous voulez)
	 * @param array $attributs
	 * @return $this
	 */
	public function createGetRecord(array $attributs): BaseModel
	{
		$this->create($attributs);

		//Après la query
		$afterQuery = "SELECT * FROM ";
		$afterQuery .= $this->getTableName();
		$afterQuery .= " WHERE ";
		$afterQuery .= ($this->getTablePrimaryKey() ?? "id");
		$afterQuery .= "=";
		$afterQuery .= "LAST_INSERT_ID();";

		$this->insertQBit("after", $afterQuery);

		$this->setQBit("columns",null);

		return $this;
	}

	/**
	 * @deprecated La configuration de PDO fait que cette fonction ne marche pas. (Voir la classe Connect pour
	 * y proposer des modifs sur la config de PDO si vous voulez)
	 * @param array $attributs
	 * @param string $colonne
	 * @return $this
	 */
	public function createGetColumn(array $attributs, string $colonne): BaseModel
	{
		$this->create($attributs);

		$afterQuery = "SELECT ";
		$afterQuery .= $colonne;
		$afterQuery .= " FROM ";
		$afterQuery .= $this->getTableName();
		$afterQuery .= " WHERE ";
		$afterQuery .= ($this->getTablePrimaryKey() ?? "id");
		$afterQuery .= "=";
		$afterQuery .= "LAST_INSERT_ID();";
		$this->insertQBit("after", $afterQuery);

		return $this;
	}

	// ------------- ORM-only funcs -------------

	/**
	 * Vas exécuter le query, en construisant la requête
	 * @return bool|array|stdClass|null
	 * @throws Exception
	 */
	private function exec(): bool|array|stdClass|null
	{
		$this->buildQuery();

		//Connection à la BDD en singleton
		$this->conn = Connect::getInstance();

		//Connection à la BDD
		$this->getConn()->connect();

		//Exécution de la BDD et prise des résultats
		$res = $this
			->getConn()
			->execute($this->getQuery(), $this->getParams());

		$parser = new responseParser($res, $this->getQuery(), true);

		//Déconnexion dans tous les cas
		$this->getConn()->disconnect();

		//Retour du résultat du parseur
		return $parser->getResult();
	}

	/**
	 * Construit le string qui va query la DB
	 * @return void
	 */
	private function buildQuery(): void
	{
		//Si une requête SQL doit être faite avant la requête demandée, l'insérer. Sinon, initialiser la query
		//$this->insertQ($this->queryBits->before ?? "");

		//Prise de l'ordre des bouts de query
		//Cette étape va prendre les bouts de query correspondants afin d'initier la requête SQL.
		//Puisque le tableau $methodOrder contient l'ordre des bouts en fonction de l'opération demandé
		//(SELECT, INSERT, UPDATE ou DELETE), je peux itérer dans ces orders pour construire le début de la requête SQL
		//avec les informations qu'il me faut.
		$orders = $this->getMethodOrder($this->getQBits("method")); //$this->methodOrder[$this->getQBits("method")];

		//Itération dans les bouts du type de requête
		foreach ($orders as $order)
		{
			//Prise du bout de requête SQL
			$preInsert = $this->getQBits($order);
			$toInsert = $preInsert;

			if(is_array($toInsert))
			{
				$toInsert = implode(", ", $preInsert);
			}

			if(isset($toInsert))
			{
				$this->insertQ($toInsert);
			}
		}

		//Itérations dans les bouts de requête SQL
		foreach ($this->getSqlOrder() as $bit => $types)
		{
			//Si la partie de SQL n'est pas vide
			if(!empty($this->getQBits($bit)))
			{
				//Si un builder y est indiqué et qu'il est différent de none
				if(isset($types["builder"]) && $types["builder"] !== 'none')
				{
					/* NOTE :
					Pour une raison inconnue, PHP décide de transformer l'array des paramètres à construire de ce format
					array(1) { [0]=> array(2) { ["cond"]=> NULL ["clause"]=> string(4) "id=1" } } (normal, ce que je veux)
					à ça (quand un seul autre array est donné dans l'array):
					array(1) { [0]=> array(2) { ["cond"]=> NULL ["clause"]=> string(4) "id=1" } }
					Voici donc pourquoi il ne faut pas utiliser call_user_fund_array ici. (Merci PHP :) )
					$builtPart = call_user_func_array(array($this,$types["builder"]),$this->queryBits->{$bit});
					*/

					//S'il est indiqué (ou non) de build, alors build la partie de query
					if(
						!isset($types["build"]) ||
						(isset($types["build"]) && $types["build"] === true))
					{

						//Alors on passe cette partie d'instruction aux builders
						//TODO: getBuilder()
						$build = $this->{$types["builder"]}($this->getQBits($bit)); //$this->queryBits->{$bit});

						if(gettype($build) === 'string')
						{
							if(!isset($bitsParams["build"]) || $bitsParams["build"] === true)
							{
								//Insertion de la partie SQL finale
								$this->insertQ($build);
							}
						} else {
							//Prise du query SQL
							$builtSql = $build["sql"] ?? "";
							//Prise des paramètres
							$buildParams = $build["params"] ?? null;

							if(isset($buildParams))
							{
								//Itérations dans les paramètres
								foreach ($buildParams as $param)
								{
									//Ajout du paramètre
									$this->insertParam($param);
								}
							}

							//Insertion de la partie SQL finale
							$this->insertQ($builtSql);
						}
					}

				}
			}
		}
	}

	/**
	 * OK
	 * Vas reset l'objet, à utiliser obligatoirement s'il faut le réutiliser.
	 * @return void
	 */
	public function reset(): void
	{
		$this->__construct($this->getTableName(),$this->getTablePrimaryKey());
		$this->query = "";
		$this->params = array();
	}


	// --------------------------------------- Assesseurs ---------------------------------------

	/**
	 * Insère un bout de query SQL, pour la requête à effectuer
	 * @param string|null $q Le string à insérer (peut être null : va être ignoré)
	 * @return void
	 */
	private function insertQ(string|null $q): void
	{
		//Si le string de query n'est pas encore défini
		if(!isset($this->query))
		{
			if(isset($q))
			{
				$this->query = $q;
			}

		}
		//Si le string de query est défini
		else {
			//On insère un espace après le dernier bout, pour séparer les instructions
			if(isset($q))
			{
				$this->query .= " ".$q;
			}
		}
	}

	/**
	 * Retourne la query SQL qui va être executé
	 * @return string|null
	 */
	public function getQuery(): string|null
	{
		return $this->query ?? null;
	}

	/**
	 * Donne les bouts de query de la clef correspondante si indiquée. Si la clef n'est pas indiquée, alors tout l'objet sera retourné
	 * @param string|null $clef
	 * @return stdClass|string|array|null
	 */
	private function getQBits(string $clef = null): stdClass|string|array|null
	{
		if(isset($clef))
		{
			return $this->queryBits->{$clef} ?? null;
		}
		else {
			return $this->queryBits ?? null;
		}
	}

	/**
	 * Insère dans le tableau des bits de query une valeur.
	 * @param string $clef Quel endroit / bout d'instruction à insérer
	 * @param mixed $value La valeur
	 * @return void
	 */
	protected function insertQBit(string $clef, mixed $value): void
	{
		$orderMethod = $this->getSqlOrder($clef) ?? null;

		//Si le type de bit de query est bien un array
		if($orderMethod["type"] === 'array')
		{
			//Si la clef n'existe pas
			if($this->getQBits($clef) === null)
			{
				$this->queryBits->{$clef} = array($value);
			}

			//Insertion de la valeur dans la query
			$this->queryBits->{$clef}[] = $value;
		}
		//Si le type est autre (sûrement un string)
		else {
			//Si la clef n'existe pas, alors l'initialiser
			if($this->getQBits($clef) === null)
			{
				$this->queryBits->{$clef} = "";
			}

			//Si le bout de SQL n'est pas une instruction à faire avant après la query
			if($clef === 'after')
			{
				$this->queryBits->{$clef} .= $value;
			}
			//Sinon, on insère une virgule après le bout d'instruction
			else {
				$this->queryBits->{$clef} .= ", " . $value;
			}
		}
	}

	/**
	 * Vas set pour la clef une valeur dans les bouts de query
	 * @param string $clef La clef
	 * @param mixed $value La valeur
	 * @return void
	 */
	protected function setQBit(string $clef, mixed $value): void
	{
		$this->queryBits->{$clef} = $value;
	}

	/**
	 * Retourne l'instance de connection à la BDD
	 * @return Connect|null Instance de connection à la BDD.
	 */
	public function getConn(): Connect|null
	{
		return $this->conn ?? null;
	}

	/**
	 * Retourne le nom de la table du modèle.
	 * @return string
	 */
	protected function getTableName(): string
	{
		return $this->tableName;
	}

	/**
	 * Vas donner une valeur à la variable du nom de table du modèle
	 * @param string $tableName Nom de la table
	 * @return void
	 */
	protected function setTableName(string $tableName): void
	{
		$this->tableName = $tableName;
	}

	/**
	 * Vas retourner la clef primaire si indiquée.
	 * Si la clef primaire n'a pas été indiquée, alors cette méthode vas retourner null
	 * @return string|null
	 */
	protected function getTablePrimaryKey(): string|null
	{
		return $this->primaryKey ?? null;
	}

	/**
	 * Vas donner une valeur (ou pas) à la variable de la clef primaire
	 * @param string|null $key Colonne clef primaire
	 * @return void
	 */
	protected function setTablePrimaryKey(string|null $key): void
	{
		$this->primaryKey = $key ?? null;
	}

	/**
	 * Donne le tableau SQLOrder.
	 * Si la clef n'est pas indiqué, alors tout le tableau sera retourné.
	 * Si la clef est indiqué, alors seule la clef et son contenu sera retourné
	 *
	 * @param string|null $clef
	 * @return array|null
	 */
	public function getSqlOrder(string $clef = null): array|null
	{
		if(isset($clef))
		{
			return $this->sqlOrder[$clef] ?? null;
		}

		return $this->sqlOrder;
	}

	/**
	 * Retourne le tableau des paramètres de la requête SQL
	 * @return array
	 */
	protected function getParams(): array
	{
		return $this->params;
	}

	/**
	 * Insère une valeur dans le tableau des paramètres
	 * @param mixed $param La valeur du paramètre à insérer
	 * @return void
	 */
	protected function insertParam(mixed $param): void
	{
		$this->params[] = $param;
	}

	/**
	 * Retourne la partie correspondante à la clef (si indiquée) du tableau d'ordre des opérations SQL (INSERT, DELETE, etc.).
	 * Sinon, retourne le tableau complet
	 * @param string|null $clef La clef
	 * @return array
	 */
	protected function getMethodOrder(string $clef = null): array
	{
		if(isset($clef))
		{
			return $this->methodOrder[$clef] ?? array();
		}
		else {
			return $this->methodOrder;
		}
	}
}
