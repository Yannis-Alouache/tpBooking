<?php

include_once("./controller/Controller.php");

include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/LoginPage.php");

class LoginController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public LoginPage $loginPage;

    private const ROUTES = array(
        "hey@GET" => "testGet",
        "hey@POST" => "testPost",
        "@GET" => "render",
    );

    public function __construct() {
        $this->navigation = new Navigation();
        $this->loginPage = new LoginPage();
        $this->footer = new Footer();
    }

    public function render() {
        echo $this->navigation->render() . $this->loginPage->render() . $this->footer->render();
    }
    public function testGet()
    {
        echo "Bonsoir, je suis la route /login/hey en GET :)";

        echo "<br/> Formulaire de test:
        <form action='/login/hey' method='POST'>
            <input type='text' name='test' value='salut'/>
            <input type='submit' value='envoi de test !' />
        </form>
        ";
    }

    public function testPost()
    {
        echo "Bonsoir, je suis la route /login/hey en POST :)";

        echo "<br/> var_dump de \$_POST: <br/>";
        var_dump($_POST);
    }

    public function getInnerRoutes(): array
    {
        return LoginController::ROUTES;
    }
}