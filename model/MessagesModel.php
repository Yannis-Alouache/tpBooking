<?php


include_once("./model/Model.php");


class MessagesModel extends Model
{
	protected string $tableName="messagerie";
	protected string $primaryKey = "idMessage";

	public function __construct()
	{
		parent::__construct($this->tableName, $this->primaryKey);
	}

	public function getAllMessages(int $recipientID, int $userID): array
	{
		$preRep = $this
			->select([
				"messagerie.message as msg",
				"messagerie.date as date",
				"messagerie.idReceveur as idRecip",
			])
			->where("messagerie.idUtilisateur",$userID)
			->andWhere("messagerie.idReceveur", $userID)
			->orWhere("messagerie.idUtilisateur", $recipientID)
			->orWhere("messagerie.idReceveur", $recipientID)
			->orderBy("messagerie.date")
			->get();

		$rep = array();

		if($preRep instanceof stdClass)
		{
			$rep[] = $preRep;
		}
		else {
			$rep = $preRep;
		}

		return $rep;
	}

	/**
	 * @param int $userID
	 * @return array
	 * @throws Exception
	 */
	public function getAllContacts(int $userID): array
	{
		$preRep = $this
			->select([
				"messagerie.message as msg",
				"messagerie.date as date",
				"utilisateur.idUtilisateur as idDesti",
				"utilisateur.nom as nom",
				"utilisateur.prenom as prenom",
				"ua.idUtilisateur as idRecip",
			])
			->join("messagerie","idUtilisateur","utilisateur","idUtilisateur","JOIN")
			->join("messagerie","idReceveur","utilisateur","idUtilisateur","JOIN", "ua")
			->where("messagerie.idUtilisateur",$userID)
			->orWhere("messagerie.idReceveur", $userID)
			->orderBy("messagerie.date", "DESC")
			->groupBy([
				"utilisateur.idUtilisateur"
			])
			->get();

		$rep = array();

		if($preRep instanceof stdClass)
		{
			$rep[] = $preRep;
		}
		else {
			$rep = $preRep;
		}

		return $rep;
	}

	/**
	 * @throws Exception
	 */
	public function getContact(int $recipient): bool|array|stdClass
	{
		$user = new UserModel();

		return $user
			->find($recipient)
			->first();
	}
}
