<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/LoginPage.php");

include_once("model/UserModel.php");

class LogoutController extends Controller
{

    private const ROUTES = array(
        "@POST" => "render",
    );

    public function __construct() { }

    public function render() {
        $user = new UserModel();
        $user->unsetUserData();
  
        header("Location: /login");
    }

    public function getInnerRoutes(): array
    {
        return LogoutController::ROUTES;
    }
}
