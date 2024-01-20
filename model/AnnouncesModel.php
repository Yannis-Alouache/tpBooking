<?php

class AnnouncesModel extends Model
{
    protected string $tableName="annonce";
	protected string $primaryKey = "idAnnonce";

    function __construct()
    {
        parent::__construct($this->tableName, $this->primaryKey);
    }

    function deleteAnnounce($idAnnonce) {
        $reservations = new ReservationModel();
        $avis = new AvisModel();
        $regles = new ReglesModel();
        $equipementAnnonce = new EquipementAnnonceModel();

        $avis
            ->where("idAnnonce", $idAnnonce, "=")
            ->delete()
            ->get();

        $avis->reset();

        $regles
            ->where("idAnnonce", $idAnnonce, "=")
            ->delete()
            ->get();

        $regles->reset();

        $equipementAnnonce
            ->where("idAnnonce", $idAnnonce, "=")
            ->delete()
            ->get();

        $equipementAnnonce->reset();

        $reservations
            ->where("idAnnonce", $idAnnonce, "=")
            ->delete()
            ->get();
        $reservations->reset();
        
        $this
            ->where("idAnnonce", $idAnnonce, "=")
            ->delete()
            ->get();
    }

	public function getFilters(): stdClass
	{
		$allFilters = new stdClass();
		$allFilters->errors = array();

		$this->getDates($allFilters);
		$this->getCheckboxes($allFilters);
		$this->getInputs($allFilters);
		$this->getRanges($allFilters);
		$this->getEquip($allFilters);

		return $allFilters;
	}

	/**
	 * @throws Exception
	 */
	public function applyFilters(stdClass $filters): bool|array|stdClass|null
	{
		$query = $this;

		$query
			->where("annonce.disponibilite_debut", '2010-01-01', ">=")
			->andWhere("annonce.disponibilite_debut", $filters->start, "<=")
		;

		if($filters->start !== $filters->end && $filters->end !== '2100-01-01')
		{
			$query
				->andWhere("annonce.disponibilite_fin", '2100-01-01', "<=")
				->andWhere("annonce.disponibilite_fin", $filters->end, ">=")
			;
		}

		if($filters->isEnfant === true) 	$query->andWhere("annonce.enfants", 1);
		if($filters->isAnimaux === true) 	$query->andWhere("annonce.animaux", 1);
		if($filters->isAccessible === true) $query->andWhere("annonce.accessibilite", 1);

		if(!empty($filters->address))  		$query->andWhere("annonce.emplacement", '%' . $filters->address . '%', "LIKE" );
		if(!empty($filters->description)) 	$query->andWhere("annonce.description", '%' . $filters->description . '%', "LIKE" );

		if(!empty($filters->minPrice)) 		$query->andWhere('annonce.prix', $filters->minPrice,">=");

		if(!empty($filters->maxPrice)) 		$query->andWhere('annonce.prix', $filters->maxPrice,"<=");

		if(!empty($filters->allEquip))
		{
			foreach ($filters->allEquip as $eq)
			{
				$query->orWhere("equipementannonce.CodeEquipement", $eq);
			}
		}

		$query
			->distinct()
			->groupBy("annonce.idAnnonce")
			->join( "annonce", "idAnnonce", "equipementannonce", "idAnnonce")
		;

		$all = $this->all();

		dump($all);

		return $all;

		//return $this->all();
	}

	private function getDates(stdClass &$allFilters): void
	{
		if(!empty($_GET["start"]))
		{
			$dateStart = str_replace('/','-', $_GET["start"]);
			$allFilters->start = DateTime::createFromFormat('m-d-Y', $dateStart)->format('Y-m-d');
		}
		else {
			$allFilters->start = date('Y-m-d');
		}

		if(!empty($_GET["start"]))
		{
			$dateEnd = str_replace('/','-', $_GET["end"]);
			$allFilters->end = DateTime::createFromFormat('m-d-Y', $dateEnd)->format('Y-m-d');
		}
		else {
			$allFilters->end = DateTime::createFromFormat('Y-m-d', '2100-01-01')->format('Y-m-d');
		}
	}

	private function getCheckboxes(stdClass &$allFilters): void
	{
		$checkBoxes = ["isEnfant", "isAnimaux", "isAccessible"];

		foreach ($checkBoxes as $box)
		{
			if(isset($_GET[$box]))
			{
				$allFilters->{$box} = true;
			}
			else {
				$allFilters->{$box} = false;
			}
		}
	}

	private function getInputs(stdClass &$allFilters): void
	{
		$allTextInputs = ["address", "description"];

		foreach ($allTextInputs as $input)
		{
			$allFilters->{$input} = (
			!empty($_GET[$input])
				? $_GET[$input]
				: null
			);
		}
	}

	private function getRanges(stdClass &$allFilters): void
	{

		$allRanges = ["minPrice", "maxPrice"];

		foreach ($allRanges as $range)
		{
			$allFilters->{$range} = (
				!empty($_GET[$range])
					? $_GET[$range]
					: 0
			);
		}

		$min = $allFilters->minPrice;
		$max = $allFilters->maxPrice;

		if($min > $max && $max > 0)
		{
			$allFilters->maxPrice = $min;
			$allFilters->minPrice = $max;
		}
	}

	private function getEquip(stdClass &$allFilters)
	{
		if(isset($_GET["equipList"]))
		{
			$allFilters->allEquip = array();

			foreach ($_GET["equipList"] as $eq)
			{
				$allFilters->allEquip[] = $eq;
			}

		}
	}
}
