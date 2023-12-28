<?php

include_once("./controller/Controller.php");

include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/RegisterPage.php");

include_once("model/TestEntity.php");

class RegisterController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public RegisterPage $registerPage;

    private const ROUTES = array(
        "@GET" => "render",
    );

    public function __construct() {
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->registerPage = new RegisterPage();
    }

    public function render() {
        echo $this->navigation->render() . $this->registerPage->render() . $this->footer->render();
    }

    public function getInnerRoutes(): array
    {
        return RegisterController::ROUTES;
    }
}
