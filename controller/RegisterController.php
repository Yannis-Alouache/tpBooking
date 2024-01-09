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
        
        if (!isset($_POST['voyageur'])) $traveler = false;
        else $traveler = true;

        if (!isset($_POST['hote'])) $host = false;
        else $host = true;

        if ( $age < 18 ) 
        {
            return $this->render(array("error" => "Vous devez avoir au moins 18 ans."));
        }

        if ( !$this->validateEmail($email) )
        {
            return $this->render(array("error" => "Vous devez saisir une adresse mail valide."));
        }

        if ( !$this->validatePhoneNumber($phone) )
        {
            return $this->render(array("error" => "Vous devez saisir numéro de téléphone valide."));

        }

        if ( !(strlen($zipCode) == 5) || !is_numeric($zipCode) )
        {
            return $this->render(array("error" => "Vous devez saisir un code postal valide."));
        }

        if ( !$this->validatePassword($password) )
        {
            return $this->render(array("error" => "Vous devez saisir un mot de passe valide. (1 lettre, 1 chiffre, minimum 8 caractères)"));
        }

        if ( !$traveler && !$host )
        {
            return $this->render(array("error" => "Vous devez choisir entre être un hote ou un locataire."));
        }

        $user = new UserModel();
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
