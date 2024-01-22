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
        "@GET" => "render",
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

    public function doRent() {
        $disponibility_start = $_POST["disponibilite_debut"];
        $disponibilite_end = $_POST["disponibilite_fin"];
        $emplacement = $_POST["emplacement"];
        $prix = $_POST["prix"];
        $description = $_POST["description"];
        $image = $_POST["image"];
        $_POST["animaux"] ? $animals = true : $animals = false;
        $_POST["enfants"] ? $children = true : $children = false;
        $_POST["accessibilite"] ? $accessibility = true : $accessibility = false;
        $equipment = $_POST["equipement"]; //TODO
        $rules = $_POST["regles"]; //TODO

        $currentDateTime = new DateTime('now');

        if ( $disponibilite_end<$currentDateTime )
        {
            return $this->render(array("error" => "Vous devez saisir une date de fin de disponibilité supérieure à la date du jour."));

        }

        // $announce = new AnnouncesModel();
        // $announce->create([
        //     "nom" => $lastName,
        //     "prenom " => $firstName,
        //     "adresse" => $adress,
        //     "email" => $email,
        //     "age" => $age,
        //     "code_postal" => $zipCode,
        //     "ville" => $city,
        //     "telephone" => $phone,
        //     "hote" => intval($host),
        //     "voyageur" => intval($traveler),
        //     "admin" => intval(false),
        //     "motdepasse" => password_hash( $password, PASSWORD_DEFAULT )
        // ])
        // ->get();

        return $this->render(array("success" => "Votre annonce a bien été créé !"));
    }

    public function getInnerRoutes(): array
    {
        return RentController::ROUTES;
    }
}
