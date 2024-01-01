<?php

include_once("controller/Controller.php");
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/dashboard/UsersList.php");

class UserManagerController extends Controller {
    public Navigation $navigation;
    public Footer $footer;
    public UserList $userList;

    public function __construct() {
        $this->navigation = new Navigation();
        $this->userList = new UserList();
        $this->footer = new Footer();
    }

    public function listBooking() {
        return $this->render();
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->userList->render($context) . $this->footer->render($context);
    } 
}