<?php

include_once("controller/Controller.php");
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/LoginPage.php");

class LoginController extends Controller {
    public Navigation $navigation;
    public Footer $footer;
    public LoginPage $loginPage;

    public function __construct() {
        $this->navigation = new Navigation();
        $this->loginPage = new LoginPage();
        $this->footer = new Footer();
    }

    public function render() {
        echo $this->navigation->render() . $this->loginPage->render() . $this->footer->render();
    }
}