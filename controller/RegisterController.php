<?php
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/RegisterPage.php");

class RegisterController {
    public Navigation $navigation;
    public Footer $footer;
    public RegisterPage $registerPage;

    public function __construct() {
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->registerPage = new RegisterPage();
    }

    public function render() {
        echo $this->navigation->render() . $this->registerPage->render() . $this->footer->render();
    }
}