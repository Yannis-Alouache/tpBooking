<?php

include("./model/Model.php");
include_once("./model/CommentModel.php");
include_once("./model/ReglesModel.php");
include_once("./model/EquipementAnnonceModel.php");

class UserModel extends Model
{
    protected string $tableName="utilisateur";
	protected string $primaryKey = "idUtilisateur";

    function __construct()
    {
        parent::__construct($this->tableName, $this->primaryKey);
    }

    public function validateEmail($email) {
        return preg_match('/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/', $email) === 1;
    }

    public function deleteUser($userId) {
        $announces = new AnnouncesModel();
        $reservations = new ReservationModel();
        $avis = new CommentModel();
        $regles = new ReglesModel();
        $messages = new MessagesModel();
        $equipementAnnonce = new EquipementAnnonceModel();


        $avis
            ->where("idUtilisateur", $userId, "=")
            ->delete()
            ->get();

        $avis->reset();

        // RECUPERE TOUTE LES ANNONCES DE L'UTILISATEUR
        $allAnnounces = $announces
            ->where("idUtilisateur", $userId, "=")
            ->get();
        // ON DOIT RECUPERER LES ID DE TOUTES SES ANNONCES ET SUPPPRIMER LES AVIS DE CETTE ANNONCE
        $announces->reset();

        if (gettype($allAnnounces) == "object") { // ca veut dire qu'il n'y a qu'une annonce
            $avis
                ->where("idAnnonce", $allAnnounces->idAnnonce, "=")
                ->delete()
                ->get();
            $avis->reset();

            $regles
                ->where("idAnnonce", $allAnnounces->idAnnonce, "=")
                ->delete()
                ->get();
            $regles->reset();

            $equipementAnnonce
                ->where("idAnnonce", $allAnnounces->idAnnonce, "=")
                ->delete()
                ->get();
            $equipementAnnonce->reset();
        } else {
            
            for ($i = 0; $i < count($allAnnounces); $i++) {
                $avis
                    ->where("idAnnonce", $allAnnounces[$i]->idAnnonce, "=")
                    ->delete()
                    ->get();
                $avis->reset();

                $regles
                    ->where("idAnnonce", $allAnnounces[$i]->idAnnonce, "=")
                    ->delete()
                    ->get();
                $regles->reset();

                $equipementAnnonce
                    ->where("idAnnonce", $allAnnounces[$i]->idAnnonce, "=")
                    ->delete()
                    ->get();
                $equipementAnnonce->reset();
            }
        }

        $messages
            ->where("idUtilisateur", $userId, "=")
            ->delete()
            ->get();
        $messages->reset();

        $messages
            ->where("idReceveur", $userId, "=")
            ->delete()
            ->get();

        $reservations
            ->where("idUtilisateur", $userId, "=")
            ->delete()
            ->get();
        $announces
            ->where("idUtilisateur", $userId, "=")
            ->delete()
            ->get();
        $this
            ->where("idUtilisateur", $userId, "=")
            ->delete()
            ->get();
    }
    
}
