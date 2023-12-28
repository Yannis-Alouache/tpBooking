<?php

include_once("./app/interfaces/ORMInterface.php");
include_once("./app/db/Connect.php");

/**
 * Modèle retenant toutes les méthodes de l'ORM
 */
class BaseModel implements ORMInterface
{

	/** @var Connect Singleton de connection à la BDD */
	private Connect $conn;

	/** @var string Le query qui a été construit */
	private string $query;

	/** @var stdClass Classe retenant les bouts de la requête */
	private stdClass $queryBits;

	/** @var string Le nom de la table */
	private string $tableName;

	function __construct(string $tableName)
	{
		$this->setTableName($tableName);
		$this->registerQueryBits();
		//Tableau avec les clauses SQL à insérer au non
		//Type "select"=>"SELECT * FROM ..."
	}

	/**
	 * Instancie la variable queryBits et ses bouts de base qui permettent de faire une requête simple
	 * @return void
	 */
	private function registerQueryBits(): void
	{
		$this->queryBits = new stdClass();
		$this->queryBits->method = "SELECT";
		$this->queryBits->columns = "*";
		$this->queryBits->table = $this->getTableName();

		dump($this->queryBits);
	}

	// ------------- Base queries (all methods in the interface) -------------

	public function where(string $colonne, mixed $value, string $operator = "="): BaseModel
	{

		return $this;
	}

	public function find(int $primaryKey): BaseModel
	{
		return $this;
	}

	public function findBy(string $colonne, mixed $value): BaseModel
	{
		return $this;
	}

	public function create(array $attributs): bool
	{
		return false;
	}

	public function update(array $attributs, bool $updateTimestamps = true): bool
	{
		return false;
	}

	public function delete(): bool
	{
		return false;
	}

	public function join(string $table, string $tableCol, string $joinedCol, string $joinType="INNER JOIN"): BaseModel
	{
		return $this;
	}

	public function orderBy(string $colonne, string $mode="ASC"): BaseModel
	{
		return $this;
	}

	public function raw(string $queryPart): BaseModel
	{
		return $this;
	}

	// --------- Executors ---------

	public function all(array $colonnes=array()): array
	{
		return array();
	}

	public function get(): array
	{
		return array();
	}

	public function first(): array
	{
		return array();
	}

	public function last(): array
	{
		return array();
	}

	public function latest(): array
	{
		return array();
	}

	public function oldest(): array
	{
		return array();
	}

	public function truncate(string $tableName): bool
	{
		return false;
	}

	public function select(array $colonnes): BaseModel
	{
		return $this;
	}

	// --------- Executors (sql funcs) ---------

	public function max(string $colonne): BaseModel
	{
		return $this;
	}

	public function min(string $colonne): BaseModel
	{
		return $this;
	}

	public function avg(string $colonne): BaseModel
	{
		return $this;
	}

	public function count(string $colonne): BaseModel
	{
		return $this;
	}

	public function groupBy(string|array $group): BaseModel
	{
		return $this;
	}

	public function distinct(): BaseModel
	{
		return $this;
	}

	// ------------- Combinaisons -------------

	public function firstWhere(string $colonne, mixed $value, string $operator = "="): array
	{
		return array();
	}

	public function createGetID(array $attributs): int|bool
	{
		return false;
	}

	public function createGetColumn(array $attributs, string $colonne): mixed
	{
		return "hey";
	}

	public function createMultiple(array $creates): bool
	{
		return false;
	}

	// ------------- ORM-only funcs -------------

	/**
	 * Vas exécuter le query, en construisant la requête
	 * @return array
	 */
	private function executeQuery(): array
	{
		$this->prepareQuery();

		$this->buildQuery();

		//Connection à la BDD en singleton
		$this->conn = Connect::getInstance();

		//Retour du résultat
		return array();
	}

	private function buildQuery(): void
	{

	}

	private function prepareQuery()
	{

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
}
