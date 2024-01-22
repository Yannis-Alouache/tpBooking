<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");
include_once("view/AnnouncePage.php");
include_once("view/payment/PayementScreen.php");

class AnnounceController extends Controller
{
    public Navigation $navigation;
    public Footer $footer;
    public AnnouncePage $AnnouncePage;
	public PayementScreen $payementScreen;
    public int $announceID;

    private const ROUTES = array(
        "@GET" => "getAnnounceByID",
		"book/cc@POST" => "bookByCC",
		"book/transfer@POST" => "bookByTransfer",
		"book/@POST" => "bookScreen",
    );

    public function __construct() {
        $this->navigation = new Navigation();
        $this->footer = new Footer();
        $this->AnnouncePage = new AnnouncePage();
		$this->payementScreen = new PayementScreen();

    }

	/**
	 * @throws Exception
	 */
	public function getAnnounceByID() {
		if(isset($_GET['id']) && is_numeric($_GET["id"])) { $this->announceID = $_GET['id']; }
		else {
			header("Location: /home");
			exit;
		}

        $uniqueAnnounce = new AnnouncesModel();

		//Pour l'écran de réservation
		$_SESSION["currAnnonce"] = $this->announceID;

		$disabledDates = $uniqueAnnounce->getDisabledDates();

		$announceData = $uniqueAnnounce
            ->find($this->announceID)
            ->get();

        $comments = new CommentModel();

        $getComments = $comments->getCommentsByAnnounce($this->announceID);

        $comments->reset();
        $uniqueAnnounce->reset();

        $owner = $uniqueAnnounce->getOwnerOfThisAnnounce($this->announceID);

        return $this->render(array("announce" => $announceData, "comments" => $getComments, "owner" => $owner, "disabled" => $disabledDates));
    }



	public function render($context = []): void
	{
		$allDisabled = array_map(function($val) {
			return (object)array(
				"from" => $val->start,
				"to" => $val->end,
			);
		}, $context["disabled"] ?? array());


		$toFront = '<script>
			localStorage.setItem("disabled", `'. json_encode($allDisabled) .'`);
			localStorage.setItem("maxDate", `'.$context["announce"]->disponibilite_debut.'`);
			localStorage.setItem("minDate", `'.$context["announce"]->disponibilite_fin.'`);
		</script> ';


		echo
			$this->navigation->render($context) .
			$toFront .
			$this->AnnouncePage->render($context) .
			$this->footer->render($context)
		;
	}

	/**
	 * @throws Exception
	 */
	public function bookScreen(): void
	{
		$annonce = new AnnouncesModel();

		if(!$annonce->checkBookInputs())
		{
			header("Location: /announce/?id=".$_SESSION["currAnnonce"]);
			exit;
		}

		$annonce->reset();

		$annonceBook = $annonce
			->find($_SESSION["currAnnonce"])
			->get();

		$this->renderBookScreen([
			"annonce" => (object)$annonceBook,
			"start" => $_SESSION["bookStart"],
			"end" => $_SESSION["bookEnd"],
		]);
	}

	private function renderBookScreen(array $context = array()): void
	{
		echo
			$this->navigation->render([]) .
			$this->payementScreen->render($context) .
			$this->footer->render([]);
	}

	public function bookByCC(): void
	{
		//Validation des inputs
		try {
			$reservation = new ReservationModel();

			$fields = array(
				"numCarte" => '/^[0-9]{4}(\s|-|)[0-9]{4}(\s|-|)[0-9]{4}(\s|-|)[0-9]{4}$/',
				"ccv" => '/^[0-9]{3}$/',
				"expiDate" => '/^[0-9]{1,2}(\/|-|\s)([0-9]{4}|[0-9]{2})$/',
			);

			if(!$reservation->verifyBookFields($fields)) throw new Exception("nuh uh");

			$cleanDate = DateTime::createFromFormat('Y-m-d', (
				str_replace("/","-", $_POST["expiDate"])
			));

			$now = DateTime::createFromFormat('Y-m-d', (
				date('Y-m-d')
			));

			if($now <= $cleanDate) {
				throw new Exception("nuh uh" ,1);
			}

			$reservation->makeBook();

			header("Location: /announce?id=".$_SESSION["currAnnonce"]);
			exit;

		} catch(Exception $e) {
			$_SESSION["messageBook"] = array(
				"status" => false,
				"message" => "Une erreur inconnue viens de se passer, veuillez réessayer."
			);
			header("Location: /announce?id=".$_SESSION["currAnnonce"]);
			exit;
		}
	}

	public function bookByTransfer()
	{
		try {
			$reservation = new ReservationModel();

			$fields = array(
				"accHodler" => '/^[A-Z]{1,}(\s|-| )[a-zA-Z]{1,}/',
				"iban" => '/^[A-Z]{2}[0-9]{2}(\s| )[A-Z0-9]{4}(\s| )[A-Z0-9]{4}(\s| )[A-Z0-9]{4}(\s| )[A-Z0-9]{4}(\s| )[A-Z0-9]{4}(\s| )[A-Z0-9]{1,4}$/',
				"bic" => '/^[a-zA-Z]{8}$/',
			);

			if(!$reservation->verifyBookFields($fields)) throw new Exception("nuh uh");

			$reservation->makeBook();

			header("Location: /announce?id=".$_SESSION["currAnnonce"]);
			exit;

		} catch(Exception $e) {
			$_SESSION["messageBook"] = array(
				"status" => false,
				"message" => "Une erreur inconnue viens de se passer, veuillez réessayer."
			);
			header("Location: /announce?id=".$_SESSION["currAnnonce"]);
			exit;
		}
	}

    public function getInnerRoutes(): array
    {
        return AnnounceController::ROUTES;
    }
}
