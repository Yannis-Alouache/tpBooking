<?php

class AnnouncesModel extends Model
{
    protected string $tableName="annonce";
	protected string $primaryKey = "idAnnonce";

	private string $inputStart;
	private string $inputEnd;

	private string $dateRangeRegex = '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\sto\s[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/';
	private string $dayRegex = '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/';

    function __construct()
    {
        parent::__construct($this->tableName, $this->primaryKey);
    }

	/**
	 * @throws Exception
	 */
	public function getDisabledDates(): array
	{
		$this->reset();

		$allDisabled = $this
			->select([
				"reservation.dateDebut as start",
				"reservation.dateFin as end",
				"annonce.disponibilite_debut as anStart",
				"annonce.disponibilite_fin as anEnd"
			])
			->where("reservation.idAnnonce", $_GET['id'])
			->join("annonce", "idAnnonce", "reservation", "idAnnonce")
			->all()
		;

		if($allDisabled instanceof stdClass) { $allDisabled = [$allDisabled]; }

		$this->reset();

		return $allDisabled;
	}

    function deleteAnnounce($idAnnonce) {
        $reservations = new ReservationModel();
        $avis = new CommentModel();
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
		$query = $this
			->where("annonce.idAnnonce", 0, ">=");

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
			//->join( "annonce", "idAnnonce", "equipementannonce", "idAnnonce")
			->groupBy("annonce.idAnnonce")
		;

		return $this->filterDates(
			$this->all(),
			$filters,
		);
	}

	private function filterDates(array|stdClass $dates, stdClass $filters): array
	{

		$finalDates = array();

		if($dates instanceof stdClass)
		{
			$dates = [$dates];
		}

		$dates = array_map(function ($val){
			$toPush = $val;
			$toPush->start = $val->disponibilite_debut;
			$toPush->end = $val->disponibilite_fin;

			return $toPush;
		}, $dates);

		foreach ($dates as $index => $date)
		{
			if(!empty($filters->start) && !empty($filters->end))
			{
				//Si la date est dans la range
				if(!$this->isInDateRange(
					$filters->start ?? "2010-01-01",
					$filters->end ?? "2100-01-01",
					[$date],
				)) {
					$finalDates[] = $date;
				}
			}
			else {
				//Si la date est dans la range
				if($this->isInDateRange(
					"2010-01-01",
					"2100-01-01",
					[$date],
				)) {
					$finalDates[] = $date;
				}
			}
		}

		return $finalDates;
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

		if(!empty($_GET["end"]))
		{
			$dateEnd = str_replace('/','-', $_GET["end"]);
			$allFilters->end = DateTime::createFromFormat('m-d-Y', $dateEnd)->format('Y-m-d');
		}
		else {
			$allFilters->end = '2100-01-01';
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

	public function getOwnerOfThisAnnounce(int $id) {
		$user = new UserModel();

		$owner = $user
			->select(['nom','prenom'])
			->find($id)
			->get();

		return $owner;
	}

	public function checkBookInputs(): bool
	{
		$isValid = true;
		try {

			if(empty($_POST["dateRange"]))
			{
				$isValid = false;
			}

			//date format d/m/Y, 'to', date format 'd/m/Y'
			$regexDates = '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\sto\s[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/';

			//Si les dates mises ne correspondent pas au regex des dates (une range, ici)
			if(preg_match($regexDates, $_POST["dateRange"]) !== 1)
			{
				//Une date simple ici
				if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/' ,$_POST["dateRange"]) !== 1)
				{
					$isValid = false;
				}
				else {
					//Si elles correspondent
					if(!$this->isDateValid())
					{
						$isValid = false;
					}
				}
			}
			else {
				//Si elles correspondent
				if(!$this->areDatesValid())
				{
					$isValid = false;
				}
			}

			$_SESSION["bookStart"] = $this->getInputStart();
			$_SESSION["bookEnd"] = $this->getInputEnd();

		} catch (Exception $e) {
			$isValid = false;
		} finally {
			return $isValid;
		}
	}

	/**
	 * @throws Exception
	 */
	private function book(string $start, string $end): void
	{
		$this->reset();
		$reserv = new ReservationModel();
		$reserv->create([
			"idAnnonce" => $_SESSION["currAnnonce"],
			"idUtilisateur" => $_SESSION["userId"],
			"dateDebut" => $start,
			"dateFin" => $end,
		])
		->get();
	}

	private function isDateValid(): bool
	{
		$this->reset();
		$isValid = true;

		try {
			$date = DateTime::createFromFormat("d-m-Y",
				str_replace(
					"/",
					"-",
					trim($_POST["dateRange"])
				)
			)->format('Y-m-d');

			$this->setInputStart($date);
			$this->setInputEnd($date);

			$allDates = $this
				->select([
					"reservation.dateDebut as start",
					"reservation.dateFin as end",
				])
				->where("annonce.idAnnonce", $_SESSION["currAnnonce"])
				->join("annonce","idAnnonce","reservation","idAnnonce")
				->all();

			if($allDates instanceof stdClass) {$allDates=[$allDates];}

			if(!$this->isInDateRange($date, $date, $allDates)) {
				throw new Exception("nuh uh", 1);
			}
		} catch (Exception $e) {
			$isValid = false;
		} finally {
			return $isValid;
		}
	}

	private function areDatesValid(): bool
	{

		$this->reset();

		$areValid = true;

		try {
			//Prise de la date de départ et de fin
			$exDates = explode("to", $_POST["dateRange"]);

			$start = DateTime::createFromFormat("d-m-Y",
				str_replace(
					"/",
					"-",
					trim($exDates[0])
				)
			)->format('Y-m-d');

			$end = DateTime::createFromFormat("d-m-Y",
				str_replace(
					"/",
					"-",
					trim($exDates[1])
				)
			)->format('Y-m-d');

			$this->setInputStart($start);
			$this->setInputEnd($end);

			$allDates = $this
				->select([
					"reservation.dateDebut as start",
					"reservation.dateFin as end",
				])
				->where("annonce.idAnnonce", $_SESSION["currAnnonce"])
				->join("annonce","idAnnonce","reservation","idAnnonce")
				->all();

			if($allDates instanceof stdClass) {$allDates=[$allDates];}

			if(!$this->isInDateRange($start, $end, $allDates)) {
				throw new Exception("nuh uh", 1);
			}

		} catch (Exception $e) {
			$areValid = false;
		} finally {
			$this->reset();
			return $areValid;
		}
	}

	private function isInDateRange(string $iStart, string $iEnd, array $dates): bool
	{
		$isValid = true;

		try {
			$iStart = DateTime::createFromFormat('Y-m-d', $iStart);
			$iEnd = DateTime::createFromFormat('Y-m-d', $iEnd);

			foreach ($dates as $index => $dt) {
				$dbStart = DateTime::createFromFormat('Y-m-d', $dt->start);
				$dbEnd = DateTime::createFromFormat('Y-m-d', $dt->end);

				//Si la date input est dans la range des dates de bdd
				if($iStart >= $dbStart && $iStart <= $dbEnd)
				{
					$isValid = false;
				}

				//Si la date input de fin est dans la range des dates de bdd
				if($iEnd >= $dbStart && $iEnd <= $dbEnd)
				{
					$isValid = false;
				}
			}

		} catch (Exception $e) {
			$isValid = false;
		} finally {
			return $isValid;
		}

	}

	/* ------------------------ Assesseurs ------------------------ */

	public function getInputStart(): string
	{
		return $this->inputStart;
	}

	public function setInputStart(string $inputStart): void
	{
		$this->inputStart = $inputStart;
	}

	public function getInputEnd(): string
	{
		return $this->inputEnd;
	}

	public function setInputEnd(string $inputEnd): void
	{
		$this->inputEnd = $inputEnd;
	}

	public function getDateRangeRegex(): string
	{
		return $this->dateRangeRegex;
	}

	public function getDayRegex(): string
	{
		return $this->dayRegex;
	}
}
