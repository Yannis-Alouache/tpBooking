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
        "@GET" => "getAnnounceByID",
        "@POST" => "postAComment"
    );

    public function __construct() {
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->AnnouncePage = new AnnouncePage();
        if(isset($_GET['id']) && is_numeric($_GET["id"])) { $this->announceID = $_GET['id']; }
		else {
			header("Location: /home");
			exit;
		}
    }

    public function getAnnounceByID() {
        $uniqueAnnounce = new AnnouncesModel();

        $announceData = $uniqueAnnounce
            ->find($this->announceID)
            ->get();

        $comments = new CommentModel();

        $getComments = $comments->getCommentsByAnnounce($this->announceID);

        $comments->reset();
        $uniqueAnnounce->reset();

        $owner = $uniqueAnnounce->getOwnerOfThisAnnounce($this->announceID);

        $averageRating = $comments->averageRating($this->announceID);

        // $equipments = $uniqueAnnounce->getEquipmentByAnnounce($this->announceID);

        return $this->render(array("announce" => $announceData, "comments" => $getComments, "owner" => $owner, "rating" => $averageRating));
    }

    public function postAComment() {
        $comments = new CommentModel();

        if(isset($_SESSION['userId'])){
            $userID = $_SESSION['userId'];
        } else {
            $userID = 0;
        }

        $comments->create([
			'idAnnonce' => $_GET['id'],
			'idUtilisateur'=> $userID,
			'Commentaires' => $_POST['comment'],
			'Note' => $_POST['rate'],
			'dateAvis' => "NOW()",
		]);
    }



    public function render($context = []) {
        echo $this->navigation->render($context) . $this->AnnouncePage->render($context) . $this->footer->render($context);
    }

    public function getInnerRoutes(): array
    {
        return AnnounceController::ROUTES;
    }
}
