<?php

include_once("controller/Controller.php");
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/dashboard/ReservationHistory.php");

class ReservationHistoryController extends Controller {
    public Navigation $navigation;
    public Footer $footer;
    public ReservationHistory $reservationHistory;

    public function __construct() {
        $this->navigation = new Navigation();
        $this->reservationHistory = new ReservationHistory();
        $this->footer = new Footer();
    }

    public function listBooking() {
        return $this->render();
    }

    public function render() {
        echo $this->navigation->render() . $this->reservationHistory->render() . $this->footer->render();
    } 
}