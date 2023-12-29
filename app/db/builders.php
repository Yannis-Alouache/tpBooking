<?php

trait builders
{
	/**
	 * Construit en string les clauses 'WHERE'
	 * @param array $whereArr
	 * @return string
	 */
	public function buildWhere(array $whereArr): string
	{
		$finalWhere = "";

		foreach ($whereArr as $where)
		{
			$insertWhere = "";

			$cond = $where["cond"] ?? null;
			$clause = $where["clause"];

			if(!empty($cond))
			{
				$insertWhere .= $cond. " ";
			}

			$insertWhere .= $clause;

			$finalWhere .= (!empty($finalWhere) ? " " : "") . $insertWhere;
		}

		return $finalWhere;
	}

	/**
	 * Construit en string les valeurs de la clause INSERT
	 * @param array $insertArr
	 * @return string
	 */
	public function buildInsert(array $insertArr): string
	{
		$finalInsert = "";

		//Les colonnes concernées
		$allCols = implode(",",array_unique(array_keys($insertArr)));
		//Les valeurs
		$allVals = implode(",", array_values($insertArr));

		$finalInsert .= "(" . $allCols . ") VALUES (" . $allVals . ")";

		return $finalInsert;
	}

	/**
	 * Construit en string les valeurs de la clause UPDATE
	 * @param array $updatesArr
	 * @return string
	 */
	public function buildUpdates(array $updatesArr): string
	{
		$finalUpdate = "";

		foreach ($updatesArr as $col => $val)
		{
			$finalUpdate .= (!empty($finalUpdate) ? ", " : "") . $col . "=" .$val;
		}

		return $finalUpdate;

	}

	/**
	 * Construit en string les jointures demandées
	 * @param array $joints
	 * @return string
	 */
	public function buildJoints(array $joints): string
	{
		return implode(" ", $joints);
	}

	public function buildOrderBy(array $ordersArr): string
	{
		$finalOrder = array();

		foreach ($ordersArr as $order)
		{
			$finalOrder[] = "" . $order["col"] . " " . $order["mode"];
		}

		return implode(", ",$finalOrder);
	}

	public function buildGroupBy(array $groupArr): string
	{
		return implode(", ",$groupArr);
	}
}
