<?php

include_once("controller/Controller.php");
include_once("view/Navigation.php");
include_once("view/Footer.php");
include_once("view/dashboard/CommentList.php");
include_once("model/CommentModel.php");

class CommentListController extends Controller {
    public Navigation $navigation;
    public Footer $footer;
    public CommentList $commentList;

    private const ROUTES = array(
        "@GET" => "listComments",
    );

	public function getInnerRoutes(): array
	{
		return CommentListController::ROUTES;
	}

    public function __construct() {
        $this->navigation = new Navigation();
        $this->commentList = new CommentList();
        $this->footer = new Footer();
    }

    public function listComments() {
        if (!isset($_SESSION["userId"]) || !$_SESSION["userAdmin"]) {
            header("Location: /home");
        }
        $comment = new CommentModel();
        $commentData = $comment->get();

        return $this->render(array("commentData" => $commentData));
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->commentList->render($context) . $this->footer->render($context);
    } 
}