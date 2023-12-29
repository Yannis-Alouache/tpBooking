<?php

interface ORMInterface
{
    // --------- Builders (query builders) ---------
    /**
     * Crée une instruction 'where' dans l'instruction SQL
     * @param string $colonne La colonne du modèle.
     * @param string $operator L'opérateur à utiliser. '=' par défault.
     * @param mixed $value La valeur à chercher.
     */
    public function where(string $colonne, mixed $value, string $operator = "=");

    /**
     * N'est pas compatible avec 'where'. Vas essayer de trouver une seule ligne avec sa clef primaire. Retourne un tableau vide si rien n'a été trouvé et retourne les valeurs dans la BDD si quelque chose a été trouvé.
     * @param int $primaryKey La clef primaire à checker
     * @return array Le tableau de la ligne à retourner. Vide si la clef primaire ne correspond à rien. 
     */
    public function find(int $primaryKey);
    /**
     * Vas essayer de trouver une seule ligne avec la colonne indiquée
     * @param string $colonne La colonne à checker.
     * @param mixed $value La valeur de la colonne.
     * @return array Le tableau de la ligne à retourner. Vide si la condition ne correspond à rien. 
     */
    public function findBy(string $colonne, mixed $value);

    /**
     * Vas insérer une ligne dans la table du modèle.
     * @param array $attributs Le tableau d'attributs à insérer. Format clef-valeur. La clef du tableau est la colonne.
     * @return bool Si l'insertion a bien réussi ou non
     */
    public function create(array $attributs): bool;

    /**
     * Vas mettre à jour une ligne dans la BDD.
     * @param array $attributs Les attributs à mettre à jour. Format clef => valeur.
     * @return bool Si la mise à jour a bien réussi ou non
     */
    public function update(array $attributs): bool;

    /**
     * Vas supprimer une ligne en utilisant les méthodes de l'interface.
     * Exemple pour supprimer une ligne avec l'ID 4 dans la table users (imaginons que la modèle User existe déjà et que la colonne ID est la clef primaire):
     * ```php
     * <?php
     *      //Nouveau modèle User
     *      $user = new User();
     *      
     *      $isDeleted = $user
     *          //Trouver la ligne l'ID n°4
     *          ->find(4)
     *          //Suppression de la ligne
     *          ->delete();
     *      //Si la suppression ne s'est pas bien passée
     *      if(!$isDeleted)
     *      {
     *          echo "La suppression n'a pas pu se faire correctement";
     *      } else {
     *          echo "La suppression s'est faite correctement.";
     *      }
     * ?>
     * ```
     * @return bool Si la supression a réussi ou non
     */
    public function delete(): bool;

	/**
	 * Effectue une jointure SQL dans la BDD.
	 * @param string $table La table de base.
	 * @param string $tableCol La colonne de la table de base.
	 * @param string $joinedTable Le nom de la table jointe
	 * @param string $joinedCol La colonne de la table à joindre
	 * @param string $joinType Le type de jointure SQL. Par défaut "INNER JOIN"
	 */
    public function join(string $table, string $tableCol, string $joinedTable, string $joinedCol, string $joinType="INNER JOIN");

    /**
     * Créer une clause ORDER BY en SQL.
     * @param string $colonne La colonne à trier par
     * @param string $mode Le mode de ORDER BY à exectuter
     */
    public function orderBy(string $colonne, string $mode="ASC");

    /**
     * Insère une section crue de requête SQL dans la requête.
     * @param string $queryPart La partie de requête SQL crue à insérer
	 * @deprecated
     */
    //public function raw(string $queryPart);

    // --------- Executors ---------
    /**
     * Retourne toutes les lignes du modèle
     * @param array $colonnes Les colonnes à retourner. Si vide, alors toutes les colonnes seront retournées
     * @return array Retourne un tableau vide s'il n'y a aucune ligne dans la table du modèle
     */
    public function all(array $colonnes=array()): array;

    /**
     * Exécute la requête SQL construite et retourne les résultats
	 * @param array $colonnes Une liste de colonnes à retourner. Une valeur vide va retourner toutes les colonnes.
     * @return array Retourne un tableau vide s'il n'y a aucune ligne dans la table du modèle
     */
    public function get(array $colonnes = array()): array;

    /**
     * Exécute la requêtre SQL construite et prend automatiquement le premier résultat
     * @return array|null
     */
    public function first(): array|null;

    /**
     * Exécute la requêtre SQL construite et prend automatiquement le dernier résultat de la requête
     * @return array|null
     */
    public function last(): array|null;

    /**
     * Exécute la requêtre SQL construite et prend automatiquement le premier résultat par rapport à la date de création (le moins vieux)
     * @return array|null
     */
    public function latest(string $column): array;

    /**
     * Exécute la requêtre SQL construite et prend automatiquement le dernier résultat par rapport à la date de création (le plus vieux)
     * @return array|null
     */
    public function oldest(string $column): array;

    /**
     * Vide entièrement la table du modèle
     * @return bool Si l'opération c'est bien passée.
     */
    public function truncate(string $tableName): bool;

    /**
     * Donne les colonnes à sélectionner lors du SELECT de SQL
     * @param array $colonnes Les colonnes à sélectionner lors du SELECT de sql
     */
    public function select(array $colonnes);


    // --------- Executors (sql funcs) ---------

    /**
     * Exécute la requête SQL et retourne le maximum (calculé par SQL) d'une colonne donnée
     */
    public function max(string $colonne, string $as);

    /**
     * Exécute la requête SQL et retourne le minimum (calculé par SQL) d'une colonne donnée
     */
    public function min(string $colonne);

    /**
     * Exécute la requête SQL et retourne la moyenne (calculé par SQL) d'une colonne donnée
     */
    public function avg(string $colonne);

    /**
     * Exécute la requête SQL et retourne le nombre d'éléments d'une colonne donnée
     */
    public function count(string $colonne);

    /**
     * Excécute la requête SQL et effectue un GROUP BY sur une colonne.
     * Il est aussi possible de faire plusieurs grouppages en y insérant un array content les colonnes à grouper
     */
    public function groupBy(string|array $group);

    /**
     * Insère une clause DISTINCT dans le SELECT
     */
    public function distinct();
}

