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

	public function update(array $attributs): bool
	{
		$this->queryBits->method = "UPDATE";
		$this->queryBits->from = "SET";

		$this->queryBits->update = $attributs;
		return false;
	}

	public function delete(): bool
	{
		$this->queryBits->method = "DELETE";

		return false;
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

	public function all(array $colonnes=array()): array
	{
		$this->queryBits->limit = null;
		return $this->exec();
	}

	public function get(array $colonnes = array()): array
	{
		return $this->exec();
		/* WIP gl
		return array_filter($res,function($row) use ($colonnes)
		{
			return (in_array($row, $colonnes));
			//A finir
		});
		*/
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
	 *
	 * //TODO v
	 * //TO FIX
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
	 * @return array
	 */
	public function exec(): array
	{
		$this->prepareQuery();

		$this->buildQuery();

		//Connection à la BDD en singleton
		$this->conn = Connect::getInstance();

		//Retour du résultat
		return array();
	}

	/**
	 * Construit le string qui va query la DB
	 * @return void
	 */
	private function buildQuery(): void
	{

		//Prise de l'ordre des bouts de query
		$orders = $this->methodOrder[$this->getQBits("method")];

		if(isset($this->queryBits->before))
		{
			$this->query = $this->queryBits->before;
		}

		//Itération dans l'ordre des bouts
		foreach ($orders as $index => $order)
		{
			$toInsert = $this->getQBits($order);

			if(isset($toInsert))
			{
				$this->insertQ($this->getQBits($order));
			}

		}

		//Autres méthodes
		if(!empty($this->queryBits->insert))
		{
			$buildInsert = $this->buildInsert($this->queryBits->insert);
			$this->insertQ($buildInsert);
		}

		//Si c'est une update
		if(!empty($this->queryBits->update))
		{
			$buildUpdate = $this->buildUpdates($this->queryBits->update);
			$this->insertQ($buildUpdate);
		}

		//TODO: Mettre le tableau $arraySQLMethods en global et l'utiliser dans cette partie pour appeler les méthodes correspondantes.
		//Optimisation / 20 mdr
		if(!empty($this->queryBits->join))
		{
			$builtJoints = $this->buildJoints($this->queryBits->join);
			$this->insertQ($builtJoints);
		}

		if(!empty($this->queryBits->where))
		{
			$this->insertQ("WHERE");
			$builtWhere = $this->buildWhere($this->queryBits->where);

			$this->insertQ($builtWhere);
		}

		if(!empty($this->queryBits->groupby))
		{
			$this->insertQ("GROUP BY");
			$builtGroup = $this->buildGroupBy($this->queryBits->groupby);

			$this->insertQ($builtGroup);

		}

		if(!empty($this->queryBits->orderby))
		{
			$this->insertQ("ORDER BY");
			$buildOrder = $this->buildOrderBy($this->queryBits->orderby);
			$this->insertQ($buildOrder);
		}

		if(isset($this->queryBits->limit) && is_numeric($this->queryBits->limit))
		{
			$this->insertQ("LIMIT ".$this->getQBits("limit"));
		}

		$this->query .= ";";

		if(isset($this->queryBits->after))
		{
			$this->query .= $this->queryBits->after;
		}
	}

	private function prepareQuery()
	{

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
		$arraySQLMethods = array(
			"where",
			"join",
			"insert",
			"update",
			"orderby",
			"groupby",
		);

		if(in_array($clef,$arraySQLMethods))
		{
			$this->queryBits->{$clef}[] = $value;
		} else {

			if($clef === "before" || $clef === "after")
			{
				$this->queryBits->{$clef} .= $value;
			} else {
				$this->queryBits->{$clef} .= ", ".$value;
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
}
