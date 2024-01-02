<?php

include_once("./app/interfaces/ORMInterface.php");
include_once("./app/db/Connect.php");
include_once("./app/db/builders.php");

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
			"sql" => "JOIN",
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
	);

	private array $arraySQLMethods = array(
		"where",
		"join",
		"insert",
		"update",
		"orderby",
		"groupby",
	);

	/** @var Connect Singleton de connection à la BDD */
	private Connect $conn;

	/** @var string Le query qui a été construit */
	private string $query;

	/** @var stdClass Classe retenant les bouts de la requête @deprecated */
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
	 * Instancie la variable queryBits et ses bouts de base qui permettent de faire une requête simple
	 * @return void
	 */
	private function registerQueryBits(): void
	{

		//Query simple
		$this->queryBits = new stdClass();

		//Instructions pre-sql
		$this->queryBits->before = null;

		//Instructions de base
		$this->queryBits->method = "SELECT";
		$this->queryBits->columns = "*";
		$this->queryBits->from = "FROM";
		$this->queryBits->table = $this->getTableName();

		//Query optionnels
		//Avec array
		$this->queryBits->where = array();
		$this->queryBits->join = array();
		$this->queryBits->orderby = array();
		$this->queryBits->groupby = array();

		$this->queryBits->insert = array();
		$this->queryBits->update = array();

		//Sans array
		$this->queryBits->distinct = null;
		$this->queryBits->limit = null;

		//Instructions post-SQL
		$this->queryBits->after = null;
	}

	// ------------- Base queries (all methods in the interface) -------------

	public function where(string $colonne, mixed $value, string $operator = "=", string|null $cond=null): BaseModel
	{
		$clause = $colonne;
		$clause .= $operator ?? "=";
		$clause .= $value;

		$finalWhere = array(
			"cond" => $cond ?? null,
			"clause" => $clause,
		);

		$this->insertQBit("where",$finalWhere);

		return $this;
	}

	public function orWhere(string $colonne, mixed $value, string $operator = "="): BaseModel
	{
		$this->where($colonne, $value, $operator, "OR");
		return $this;
	}

	public function andWhere(string $colonne, mixed $value, string $operator = "="): BaseModel
	{
		$this->where($colonne, $value, $operator, "AND");
		return $this;
	}

	public function find(int $primaryKey): BaseModel
	{
		$this->where($this->getTablePrimaryKey(),$primaryKey);
		$this->limit(1);
		return $this;
	}

	public function findBy(string $colonne, mixed $value): BaseModel
	{
		$this->where($colonne, $value);
		$this->limit(1);
		return $this;
	}

	public function create(array $attributs): bool
	{
		$this->queryBits->method = "INSERT";
		$this->queryBits->from = "INTO";

		$this->queryBits->insert = $attributs;
		return true;
	}

	public function update(array $attributs): BaseModel
	{
		$this->queryBits->method = "UPDATE";
		$this->queryBits->from = "SET";

		$this->queryBits->update = $attributs;
		return $this;
	}

	public function delete(): BaseModel
	{
		$this->queryBits->method = "DELETE";

		return $this;
	}

	public function join(string $table, string $tableCol, string $joinedTable , string $joinedCol, string $joinType="INNER JOIN"): BaseModel
	{
		$joint = $joinType . " ";

		$joint .= $table;
		$joint .= " ON ";
		$joint .= $joinedTable . "." . $joinedCol;
		$joint .= "=".$table . "." . $tableCol;

		$this->insertQBit("join", $joint);

		return $this;
	}

	public function orderBy(string $colonne, string $mode="ASC"): BaseModel
	{
		$toInsert = array(
			"col" => $colonne,
			"mode" => $mode,
		);

		$this->insertQBit("orderby",$toInsert);
		return $this;
	}

	public function limit(int|string $limit): BaseModel
	{
		$this->insertQBit("limit", $limit);
		return $this;
	}

	// --------- Executors ---------

	public function all(): array
	{
		$this->queryBits->limit = null;
		return $this->exec();
	}

	public function get(): array
	{
		return $this->exec();
	}

	public function first(): array
	{
		$this->limit(1);
		return $this->exec();
	}

	public function last(): array
	{
		$res = $this->exec();

		if(empty($res))
		{
			return array();
		}
		else {
			return array_slice($res,-1,1);
		}
	}

	public function latest(string $column = null): array
	{
		array_unshift($this->queryBits->orderby, array(
			"col" => $column ?? "created_at",
			"mode" => "ASC",
		));

		$this->limit(1);

		return $this->exec();
	}

	public function oldest(string $column = null): array
	{
		array_unshift($this->queryBits->orderby, array(
			"col" => $column ?? $this->getTablePrimaryKey() ?? "id",
			"mode" => "DESC",
		));

		$this->limit(1);

		return $this->exec();
	}

	public function truncate(string $tableName = null): bool
	{
		$this->queryBits->method = "TRUNCATE";
		$this->queryBits->from = "TABLE";

		if(isset($tableName))
		{
			$this->queryBits->table = $tableName;
		}

		$this->exec();

		return true;
	}

	public function select(array $colonnes): BaseModel
	{
		$this->queryBits->columns = implode(", ", $colonnes);

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

	public function max(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("MAX", $colonne, ($as ?? null));
		return $this;
	}

	public function min(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("MIN", $colonne, ($as ?? null));
		return $this;
	}

	public function avg(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("AVG", $colonne, ($as ?? null));
		return $this;
	}

	public function count(string $colonne, string $as = null): BaseModel
	{
		$this->funcSQL("COUNT", $colonne, ($as ?? null));
		return $this;
	}

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

	public function distinct(): BaseModel
	{
		$this->queryBits->distinct = "DISTINCT";
		return $this;
	}

	// ------------- Combinaisons -------------

	public function createGetRecord(array $attributs): array
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

		return $this->exec();
	}

	public function createGetColumn(array $attributs, string $colonne): array
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

		return $this->exec();
	}

	// ------------- ORM-only funcs -------------

	/**
	 * Vas exécuter le query, en construisant la requête
	 * @return array|null
	 */
	public function exec(): array|null
	{
		$this->buildQuery();

		//Connection à la BDD en singleton
		$this->conn = Connect::getInstance();

		try {
			//Connection à la BDD
			$this->conn->connect();
			$res = $this->conn->excecute($this->query);
		} catch(Exception $e) {

		} finally {
			//Déconnexion dans tous les cas
			$this->conn->disconnect();

			//Retour du résultat
			return $res ?? null;
		}
	}

	/**
	 * Construit le string qui va query la DB
	 * @return void
	 */
	private function buildQuery(): void
	{
		//Si une requête SQL doit être faite avant la requête demandée
		if(isset($this->queryBits->before))
		{
			$this->query = $this->queryBits->before;
		}

		//Prise de l'ordre des bouts de query
		//Cette étape va prendre les bouts de query correspondants afin d'initier la requête SQL.
		//Puisque le tableau $methodOrder contient l'ordre des bouts en fonction de l'opération demandé
		//(SELECT, INSERT, UPDATE ou DELETE), je peux itérer dans ces orders pour construire le début de la requête SQL
		//avec les informations qu'il me faut.
		$orders = $this->methodOrder[$this->getQBits("method")];

		//Itération dans les bouts du type de requête
		foreach ($orders as $index => $order)
		{
			//Prise du bout de requête SQL
			$toInsert = $this->getQBits($order);

			if(isset($toInsert))
			{
				$this->insertQ($this->getQBits($order));
			}
		}

		foreach ($this->getSqlOrder() as $bit => $types)
		{
			//Si la partie de SQL n'est pas vide
			if(!empty($this->queryBits->{$bit}))
			{
				//Si le type de la partie SQL est un array
				if(isset($types["builder"]))
				{
					/* NOTE:
					Pour une raison inconnue, PHP décide de transformer l'array des paramètres à construire de ce format
					array(1) { [0]=> array(2) { ["cond"]=> NULL ["clause"]=> string(4) "id=1" } } (normal, ce que je veux)
					à ça (quand un seul autre array est donné dans l'array):
					array(1) { [0]=> array(2) { ["cond"]=> NULL ["clause"]=> string(4) "id=1" } }
					Voici donc pourquoi il ne faut pas utiliser call_user_fund_array ici. (Merci PHP :) )
					$builtPart = call_user_func_array(array($this,$types["builder"]),$this->queryBits->{$bit});
					*/

					//Alors on passe cette partie d'instruction aux builders
					$builtPart = $this->{$types["builder"]}($this->queryBits->{$bit});
					$this->insertQ($builtPart);
				}
				//Sinon, on met directement la partie de requête SQL dans la query
				else {
					$this->insertQ($this->queryBits->{$bit});
				}
			}
		}

		//Comma pour séparer les instructions.
		$this->query .= ";";

		if(isset($this->queryBits->after))
		{
			$this->query .= $this->queryBits->after;
		}
	}

	protected function getQBits(string $clef): string|null
	{
		return $this->queryBits->{$clef} ?? null;
	}

	protected function insertQ(string $q): void
	{
		if(!isset($this->query))
		{
			$this->query = $q;
		}

		else {
			$this->query .= " ".$q;
		}
	}

	protected function insertQBit(string $clef, mixed $value): void
	{
		//Méthodes SQL où plusieurs closes sont possibles
		if(in_array($clef,$this->arraySQLMethods))
		{
			$this->queryBits->{$clef}[] = $value;
		} else {
			if($clef === "before" || $clef === "after")
			{
				$this->queryBits->{$clef} .= $value;
			} else {
				if(!isset($this->queryBits->{$clef}))
				{
					$this->queryBits->{$clef} = $value;
				} else {
					$this->queryBits->{$clef} .= ", " . $value;
				}
			}

		}
	}

	// --------------------------------------- Assesers ---------------------------------------
	public function getConn(): Connect
	{
		return $this->conn;
	}

	protected function getTableName(): string
	{
		return $this->tableName;
	}

	protected function setTableName(string $tableName): void
	{
		$this->tableName = $tableName;
	}

	protected function getTablePrimaryKey(): string
	{
		return $this->primaryKey;
	}

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
	 * @return array|array[]|string[]
	 */
	public function getSqlOrder(string $clef = null): array
	{
		if(isset($clef))
		{
			return $this->sqlOrder[$clef];
		}

		return $this->sqlOrder;
	}
}
