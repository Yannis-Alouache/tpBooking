<?php

class CommentModel extends Model
{
    protected string $tableName="avis";
	protected string $primaryKey = "idAvis";

	public function __construct()
	{
		parent::__construct($this->tableName, $this->primaryKey);
	}

	public function deleteComment($idAvis) {
        $this
            ->where("idAvis", $idAvis, "=")
            ->delete()
            ->get();
	}

	public function getCommentsByAnnounce(int $id){

		$allComments = $this
			->select(['nom','prenom','Note','Commentaires','dateAvis'])
			->join( "avis", "idUtilisateur", "utilisateur", "idUtilisateur")
			->where("idAnnonce", $id)
			->get();

		return $allComments;
	}

	public function averageRating(int $id) {
		$comment = new $this;

		$average = $comment
			->avg("Note","moyenne")
			->get();

		return $average;
	}
}