<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/AnnouncesPage.php");

include_once("model/AnnouncesModel.php");

class AnnouncesController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public AnnouncesPage $AnnouncesPage;

    private const ROUTES = array(
        "@GET" => "render",
        "@GET" => "getAnnounces",
    );

    public function __construct() {
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->AnnouncesPage = new AnnouncesPage();
    }

    public function getAnnounces() {
        $announces = new AnnouncesModel();

        $announcesData = $announces
            ->all();

        return $this->render(array("announces" => $announcesData));
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->AnnouncesPage->render($context) . $this->footer->render($context);
    }

    public function getInnerRoutes(): array
    {
        return AnnouncesController::ROUTES;
    }
}
