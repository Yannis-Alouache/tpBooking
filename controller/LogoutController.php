<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/LoginPage.php");

include_once("model/UserModel.php");

class LogoutController extends Controller
{

    private const ROUTES = array(
        "@POST" => "doLogout",
    );

    public function getInnerRoutes(): array
    {
        return LogoutController::ROUTES;
    }

    public function __construct() { }

    public function doLogout() {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        unset($_SESSION['userId']);
        unset($_SESSION['userFullName']);
        unset($_SESSION['userEmail']);
        unset($_SESSION['userAdmin']);
		unset($_SESSION["recipientID"]);
        
        header("Location: /login");
    }

    public function render() {}
}
