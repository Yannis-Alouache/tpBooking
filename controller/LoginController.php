<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/LoginPage.php");
include_once("model/UserModel.php");


class LoginController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public LoginPage $loginPage;

    private const ROUTES = array(
        "@GET" => "render",
        "@POST" => "doLogin",
    );

	public function getInnerRoutes(): array
	{
		return LoginController::ROUTES;
	}

    public function __construct() {
        $this->navigation = new Navigation();
        $this->loginPage = new LoginPage();
        $this->footer = new Footer();
    }

    public function validateEmail($email) {
        return preg_match('/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/', $email) === 1;
    }

    public function doLogin() {
        $email = $_POST["email"];
        $password = $_POST["password"];

        if ( !$this->validateEmail($email) )
        {
            return $this->render(array("error" => "Vous devez saisir une adresse mail valide."));
        }

        $user = new UserModel();

        $userData = $user
            ->where("email", $email, "=")
            ->get();

        // Si l'utilisateur n'existe pas 
        if (!isset($userData->motdepasse)) return $this->render(array("error" => "Information de connexion incorrect !"));

        
        if (password_verify($password, $userData->motdepasse)) {
            if (session_status() === PHP_SESSION_NONE)
                session_start();
            $_SESSION["userId"] = $userData->idUtilisateur;
            $_SESSION["userFullName"] = $userData->nom . " " . $userData->prenom;
            $_SESSION["userEmail"] = $userData->email;
            $_SESSION["userAdmin"] = $userData->admin;
            return $this->render(array("success" => "Vous êtes connecté !"));
        } else {
            return $this->render(array("error" => "Information de connexion incorrect !"));
        }


    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->loginPage->render($context) . $this->footer->render($context);
    }

}
