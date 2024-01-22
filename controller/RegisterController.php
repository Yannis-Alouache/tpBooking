<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/RegisterPage.php");

include_once("model/UserModel.php");

class RegisterController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public RegisterPage $registerPage;

    private const ROUTES = array(
        "@GET" => "render",
        "@POST" => "doRegister",
    );

    public function __construct() {
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->registerPage = new RegisterPage();
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->registerPage->render($context) . $this->footer->render($context);
    }

    public function doRegister() {
        $firstName = $_POST["prenom"];
        $lastName = $_POST["nom"];
        $age = $_POST["age"];
        $email = $_POST["email"];
        $phone = $_POST["telephone"];
        $adress = $_POST["adresse"];
        $zipCode = $_POST["cp"];
        $city = $_POST["ville"];
        $password = $_POST["motdepasse"];

        $user = new UserModel();
        
        if (!isset($_POST['voyageur'])) $traveler = false;
        else $traveler = true;

        if (!isset($_POST['hote'])) $host = false;
        else $host = true;

        if ( $age < 18 ) 
        {
            return $this->render(array("error" => "Vous devez avoir au moins 18 ans."));
        }

        if ( !$user->validateEmail($email) )
        {
            return $this->render(array("error" => "Vous devez saisir une adresse mail valide."));
        }

        if ( !$user->validatePhoneNumber($phone) )
        {
            return $this->render(array("error" => "Vous devez saisir numéro de téléphone valide."));

        }

        if ( !(strlen($zipCode) == 5) || !is_numeric($zipCode) )
        {
            return $this->render(array("error" => "Vous devez saisir un code postal valide."));
        }

        if ( !$user->validatePassword($password) )
        {
            return $this->render(array("error" => "Vous devez saisir un mot de passe valide. (1 lettre, 1 chiffre, minimum 8 caractères)"));
        }

        if ( !$traveler && !$host )
        {
            return $this->render(array("error" => "Vous devez choisir entre être un hote ou un locataire."));
        }

        $user->create([
            "nom" => $lastName,
            "prenom " => $firstName,
            "adresse" => $adress,
            "email" => $email,
            "age" => $age,
            "code_postal" => $zipCode,
            "ville" => $city,
            "telephone" => $phone,
            "hote" => intval($host),
            "voyageur" => intval($traveler),
            "admin" => intval(false),
            "motdepasse" => password_hash( $password, PASSWORD_DEFAULT )
        ])
        ->get();

        return $this->render(array("success" => "Votre compte a bien été créé !"));
    }

    public function getInnerRoutes(): array
    {
        return RegisterController::ROUTES;
    }
}
