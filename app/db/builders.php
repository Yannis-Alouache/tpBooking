<?php

trait builders
{
	/**
	 * Construit en string les clauses 'WHERE'
	 * @param array $whereArr
	 * @return string
	 */
	public function buildWhere(array $whereArr): array
	{
		$sql = "WHERE";
		$params = array();

		foreach ($whereArr as $where)
		{
			$column = $where["col"];
			$value = $where["value"];
			$operator = $where["operator"];
			$cond = $where["cond"];

			$insertWhere = "";

			//Si une précondition (OR, AND) est présente, alors l'insérer avec un espace
			if(isset($cond))
			{
				$insertWhere .= $cond. " ";
			}

			//Préparation du query SQL, avec le paramètre
			$insertWhere .= $column;
			$insertWhere .= " " . $operator . " ";
			$insertWhere .= "?";

			//Mise dans les paramètres le paramètre
			$params[] = $value;

			//Mise dans la query du SQL
			$sql .= (!empty($sql) ? " " : "") . $insertWhere;
		}

		return [
			"sql" => $sql,
			"params" => $params,
		];
	}

	/**
	 * Construit en string les valeurs de la clause INSERT
	 * @param array $insertArr
	 * @return string
	 */
	public function buildInsert(array $insertArr): array
	{
		$sql = "";
		$params = array();

		//Les colonnes concernées
		$allCols = implode(",",array_unique(array_keys($insertArr)));

		$sql .= "(" . $allCols . ")";
		$sql .= " VALUES (";

		//Itérations dans les données
		foreach ($insertArr as $col => $value)
		{
			//Si les paramètres sont vides
			if(empty($params))
			{
				//Alors, on met juste le ?, car il n'existe qu'un paramètre
				$sql .= "?";
			} else {
				//Sinon, on insère une virgule et un espace
				$sql .= ", ?";
			}

			//Mise dans les paramètres pour l'exec du PDO
			$params[] = $value;
		}

		$sql .= ")";

		return [
			"sql" => $sql,
			"params" => $params,
		];
	}

	/**
	 * Construit en string les valeurs de la clause UPDATE
	 * @param array $updatesArr
	 * @return array
	 */
	public function buildUpdates(array $updatesArr): array
	{
		$sql = "";
		$params = array();

		//Itérations dans les updates
		foreach ($updatesArr as $col => $val)
		{
			//Si la query SQL n'est pas vide, alors on insère une virgule
			$sql .= (!empty($sql) ? ", " : "");
			$sql .= $col;
			$sql .= " = ";
			$sql .= "?";

			//Mise dans les paramètres les valeurs
			$params[] = $val;
		}

		return [
	 		"sql" => $sql,
			"params" => $params,
	 	];
	}

	/**
	 * Construit en string les jointures demandées
	 * @param array $joints
	 * @return string
	 */
	public function buildJoints(array $joints): string
	{
		//Construction des jointures
		return implode(" ", $joints);
	}

	/**
	 * Construit la clause ORDER BY
	 * @param array $ordersArr
	 * @return string
	 */
	public function buildOrderBy(array $ordersArr): string
	{
		$sql = "";

		//Touts les ORDER BY
		$finalOrder = array();

		//Itérations dans les order by
		foreach ($ordersArr as $order)
		{
			//Ajout dans le tableau la colonne à trier et le mode de try
			$finalOrder[] = "" . $order["col"] . " " . $order["mode"] ?? "ASC";
		}

		$sql = "ORDER BY " . implode(", ",$finalOrder);

		return $sql;
	}

	/**
	 * Construit une clause GROUP BY
	 * @param array $groupArr
	 * @return string
	 */
	public function buildGroupBy(array $groupArr): string
	{
		return "GROUP BY " . implode(", ",$groupArr);
	}

	/**
	 * Construit la clause LIMIT
	 * @param string|int $limit
	 * @return array
	 */
	public function buildLimit(string|int $limit): array
	{
		$sql = "";
		$params = array();

		$sql .="LIMIT ";
		$sql .= intval($limit);


		return [
			"sql" => $sql,
			"params" => $params,
		];
	}

	public function buildAfter(string $after): string
	{
		return "; ".$after;
	}

	public function buildBefore(string $before): string
	{
		return $before."; ";
	}

	public function buildColumns(array $cols): string
	{
		return implode(", ", $cols);
	}
}
