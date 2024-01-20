<?php

include_once("controller/Controller.php");
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/dashboard/AnnoncesList.php");

class AnnoncesListController extends Controller {
    public Navigation $navigation;
    public Footer $footer;
    public AnnoncesList $annoncesList;

    private const ROUTES = array(
        "@GET" => "listAnnonce",
    );

	public function getInnerRoutes(): array
	{
		return AnnoncesListController::ROUTES;
	}

    public function __construct() {
        $this->navigation = new Navigation();
        $this->annoncesList = new AnnoncesList();
        $this->footer = new Footer();
    }

    public function listAnnonce() {
        if (!isset($_SESSION["userId"]) || !$_SESSION["userAdmin"]) {
            header("Location: /home");
        }
        $annonces = new AnnouncesModel();
        $annoncesData = $annonces->get();

        return $this->render(array("annoncesData" => $annoncesData));
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->annoncesList->render($context) . $this->footer->render($context);
    } 
}