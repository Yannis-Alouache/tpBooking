<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
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

	public function getInnerRoutes(): array
	{
		return LoginController::ROUTES;
	}

    public function __construct() {
        $this->navigation = new Navigation();
        $this->loginPage = new LoginPage();
        $this->footer = new Footer();
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->loginPage->render($context) . $this->footer->render($context);
    }

}
