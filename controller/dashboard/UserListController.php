<?php

include_once("controller/Controller.php");
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/dashboard/UsersList.php");

class UserListController extends Controller {
    public Navigation $navigation;
    public Footer $footer;
    public UserList $userList;

    private const ROUTES = array(
        "@GET" => "listUsers",
    );

	public function getInnerRoutes(): array
	{
		return UserListController::ROUTES;
	}

    public function __construct() {
        $this->navigation = new Navigation();
        $this->userList = new UserList();
        $this->footer = new Footer();
    }

    public function listUsers() {
        if (!isset($_SESSION["userId"]) || !$_SESSION["userAdmin"]) {
            header("Location: /home");
        }

        $users = new UserModel();
        $usersData = $users->get();

        return $this->render(array("usersData" => $usersData));
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->userList->render($context) . $this->footer->render($context);
    } 
}