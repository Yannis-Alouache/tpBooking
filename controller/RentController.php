<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/RentPage.php");

include_once("model/AnnouncesModel.php");

class RentController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public RentPage $doRent;

    private const ROUTES = array(
        "@GET" => "initializeCreation",
        "@POST" => "doRent",
    );

    public function __construct() {
        if(!isset($_SESSION["userId"]))
		{
			header("Location: /login");
			exit;
		}
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->doRent = new RentPage();
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->doRent->render($context) . $this->footer->render($context);
    }

    public function initializeCreation() {
        $equipment = new ListeEquipementModel();
        $getAllEquipment = $equipment->getAllEquipment();
        $this->render(array("equipment" => $getAllEquipment));
    }

    public function doRent() {
        $equipment = new ListeEquipementModel();
        $getAllEquipment = $equipment->getAllEquipment();

        $disponibility_start = $_POST["disponibilite_debut"];
        $disponibility_end = $_POST["disponibilite_fin"];
        $emplacement = $_POST["emplacement"];
        $prix = $_POST["prix"];
        $description = $_POST["description"];

        // TODO IMPORT IMAGE
        // $image = $_POST["image"];
        // $extension = pathinfo($image, PATHINFO_EXTENSION);
        // $new_name = $disponibility_start."_".$disponibilite_end."_".$prix;
        // $chemin_stockage = '../assets/images/' . $new_name . "." . $extension;
        // $temporary_path = $_POST['image'];
        // move_uploaded_file($temporary_path,$chemin_stockage);

        isset($_POST["animaux"]) ? $animals = $_POST["animaux"] : $animals = 0;
        isset($_POST["enfants"]) ? $children = $_POST["enfants"] : $children = 0;
        isset($_POST["accessibilite"]) ? $accessibility = $_POST["accessibilite"] : $accessibility = 0;
        isset($_POST["equipement"]) ? $equipment = $_POST["equipement"] : $equipment = [];
        $rules = $_POST["regles"];

        $currentDateTime = new DateTime('now');

        // TODO ERREUR DATES
        // if ( $disponibilite_end<$currentDateTime || $disponibility_start<$currentDateTime )
        // {
        //     return $this->render(array("error" => "Vous devez saisir une date de disponibilité supérieure à la date du jour.","equipment" => $getAllEquipment));
        // }

        

        $announce = new AnnouncesModel();
        $announce->create([
            "idUtilisateur" => $_SESSION['userId'],
            "disponibilite_debut" => date_create_from_format("Y-m-d", $disponibility_start),
            "disponibilite_fin " => date_create_from_format("Y-m-d", $disponibility_end),
            "emplacement" => $emplacement,
            "prix" => intval($prix),
            "description" => $description,
            "image" => "",
            "animaux" => intval($animals),
            "enfants" => intval($children),
            "accessibilite" => intval($accessibility),
            // "CodeEquipement" => intval($equipment),
            // "regle" => $rules,
        ])
        ->get();

        $latest = $announce->latest();

        return $this->render(array("success" => "Votre annonce a bien été créé !", "equipment" => $getAllEquipment, "latest" => $latest));
    }

    public function getInnerRoutes(): array
    {
        return RentController::ROUTES;
    }
}
