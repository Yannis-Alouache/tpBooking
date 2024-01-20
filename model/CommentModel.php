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
}