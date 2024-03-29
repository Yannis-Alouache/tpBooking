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

    public function unsetUserData()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        unset($_SESSION['userId']);
        unset($_SESSION['userFullName']);
        unset($_SESSION['userEmail']);
        unset($_SESSION['userAdmin']);
        unset($_SESSION["recipientID"]);
    }

    public function validateEmail($email) {
        return preg_match('/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/', $email) === 1;
    }

    public function validatePhoneNumber($phone) {
        return preg_match('/^(?:(?:(?:\+|00)33[ ]?(?:\(0\)[ ]?)?)|0){1}[1-9]{1}([ .-]?)(?:\d{2}\1?){3}\d{2}$/', $phone) === 1;
    }

    // Minimum eight characters, at least one letter and one number:
    public function validatePassword($password) {
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password) === 1;
    }

    public function deleteUser($userId)
    {
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
        $reservations->reset();

        $announcesToDelete = $announces
            ->where("idUtilisateur", $userId, "=")
            ->get();
        $announces->reset();

        if (gettype($announcesToDelete) == 'object')
        {
            $reservations
                ->where("idAnnonce", $announcesToDelete->idAnnonce, "=")
                ->delete()
                ->get();
            $reservations->reset();
        }
        else 
        {
            for ($i=0; $i < count($announcesToDelete); $i++) { 
                $reservations
                    ->where("idAnnonce", $announcesToDelete[$i]->idAnnonce, "=")
                    ->delete()
                    ->get();

                $reservations->reset();
            }
        }

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
