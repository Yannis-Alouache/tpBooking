<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/AnnouncePage.php");


class AnnounceController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public AnnouncePage $AnnouncePage;
    public int $announceID;

    private const ROUTES = array(
        "@GET" => "render",
        "@GET" => "getAnnounceByID",
    );

    public function __construct() {
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->AnnouncePage = new AnnouncePage();
        if(isset($_GET['id'])) { $this->announceID = $_GET['id']; }
    }

    public function getAnnounceByID() {
        $uniqueAnnounce = new AnnouncesModel();

        $announceData = $uniqueAnnounce
            ->find($this->announceID)
            ->get();

        return $this->render(array("announce" => $announceData));
    }

    public function render($context = []) {
        echo $this->navigation->render($context) . $this->AnnouncePage->render($context) . $this->footer->render($context);
    }

    public function getInnerRoutes(): array
    {
        return AnnounceController::ROUTES;
    }
}
