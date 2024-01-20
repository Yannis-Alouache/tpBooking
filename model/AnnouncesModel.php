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
    
}