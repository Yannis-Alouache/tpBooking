<?php

include_once("controller/Controller.php");
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/dashboard/ReservationHistory.php");
include_once("model/ReservationModel.php");

class ReservationHistoryController extends Controller {
    public Navigation $navigation;
    public Footer $footer;
    public ReservationHistory $reservationHistory;

    private const ROUTES = array(
        "@GET" => "listBooking",
    );

    public function getInnerRoutes(): array {
        return ReservationHistoryController::ROUTES;
    }

    public function __construct() {
        $this->navigation = new Navigation();
        $this->reservationHistory = new ReservationHistory();
        $this->footer = new Footer();
    }

    public function listBooking() {
        $reservations = new ReservationModel();

        $reservationsData = $reservations
            ->where("idUtilisateur", $_GET["userId"], "=")
            ->get();

        return $this->render(array("reservationData" => $reservationsData));
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->reservationHistory->render($context) . $this->footer->render($context);
    } 
}