<?php

include_once("./controller/Controller.php");

include_once("view/messages/MainMessages.php");
include_once("view/navigation.php");
include_once("view/footer.php");

include_once("model/MessagesModel.php");

class MessagesController extends Controller
{
	public Navigation $navigation;
	public Footer $footer;
	public MainMessages $mainMessages;

	private const ROUTES = array(
		"@GET" => "getMsg",
		"send@POST" => "sendMsg",
	);

	public function __construct()
	{

		if(!isset($_SESSION["userId"]))
		{
			header("Location: /login");
			exit;
		}

		if(isset($_GET["userID"]) && $_GET["userID"] === $_SESSION["userId"])
		{
			header("Location: /messages");
			exit;
		}

		$this->navigation = new Navigation();
		$this->footer = new Footer();
		$this->mainMessages = new MainMessages();
	}

	public function render($context = []) {
		echo
			$this->navigation->render([]) .
			$this->mainMessages->render($context) .
			$this->footer->render([])
		;
	}

	/**
	 * @throws Exception
	 */
	public function getMsg(): void
	{
		$allMsg = null;
		$recipient = $_SESSION["recipientID"] = ($_GET["userID"] ?? null);

		$msgModel = new MessagesModel();

		$allConvos = $msgModel
			->getAllContacts($_SESSION["userId"]);

		$contact = null;


		if(isset($recipient))
		{
			$msgModel->reset();
			$allMsg = $msgModel
				->getAllMessages($recipient, $_SESSION["userId"]);

			$msgModel->reset();

			$contact = $msgModel
				->getContact($recipient);
		}


		$this->render([
			"convos" => $allConvos,
			"messages" => $allMsg ?? null,
			"contact" => $contact ?? null,
		]);
	}

	/**
	 * @throws Exception
	 */
	public function sendMsg(): void
	{
		try {
			$msgModel = new MessagesModel();

			if(!empty($_POST["message"]))
			{
				$msgModel->create([
					"idUtilisateur" => $_SESSION["userId"],
					"idReceveur" => $_SESSION["recipientID"],
					"message" => $_POST["message"],
					"date" => date("Y-m-d H:i:s"),
				])
					->get();
			}
		} catch (Exception $e) {

		} finally {
			header("Location: /messages?userID=" . $_SESSION["recipientID"]);
			exit;
		}



	}

	public function getInnerRoutes(): array
	{
		return MessagesController::ROUTES;
	}
}
