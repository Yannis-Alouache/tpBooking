<?php

class ReservationModel extends Model 
{
    protected string $tablename = "reservation";
    protected string $primaryKey = "idReservation";

    function __construct()
    {
        parent::__construct($this->tablename, $this->primaryKey);
    }

	/**
	 * @throws Exception
	 */
	function getReservationsByUserId($userId): bool|array|stdClass|null
	{
        return $this
			->where("idUtilisateur", $_GET["userId"], "=")
			->get();
    }

	public function verifyBookFields(array $fields): bool
	{
		$toReturn = true;

		foreach ($fields as $field => $regex)
		{
			if(preg_match($regex, $_POST[$field]) !== 1) $toReturn = false;
		}

		return $toReturn;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function makeBook(): void
	{
		$this->reset();

		$this
			->create([
				"idAnnonce" => $_SESSION["currAnnonce"],
				"idUtilisateur" => $_SESSION["userId"],
				"dateDebut" => $_SESSION["bookStart"],
				"dateFin" => $_SESSION["bookEnd"],
			])
			->get();

		$_SESSION["messageBook"] = array(
			"status" => true,
			"message" => "La réservation s'est bien enregistrée, profitez bien de votre séjour et merci d'avoir choisi TpBooking.",
		);
	}
}
