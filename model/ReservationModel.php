<?php

class ReservationModel extends Model 
{
    protected string $tablename = "reservation";
    protected string $primaryKey = "idReservation";

    function __construct()
    {
        parent::__construct($this->tablename, $this->primaryKey);
    }

    function getReservationsByUserId($userId) {
        return $this
        ->where("idUtilisateur", $_GET["userId"], "=")
        ->get();
    }
}