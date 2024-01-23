<?php

include("./app/interfaces/RoutesInterfaces.php");

include("./controller/LoginController.php");
include("./controller/RegisterController.php");
include("./controller/LogoutController.php");
include("./controller/TestController.php");
include("./controller/MessagesController.php");
include("./controller/AnnouncesController.php");
include("./controller/AnnounceController.php");
include("./controller/dashboard/ReservationHistoryController.php");
include("./controller/dashboard/UserListController.php");
include("./controller/DeleteUserController.php");
include("./controller/dashboard/AnnoncesListController.php");
include("./controller/dashboard/DeleteAnnounceController.php");
include("./controller/dashboard/CommentListController.php");
include("./controller/RentController.php");
include_once("./controller/dashboard/DeleteCommentController.php");


abstract class Controller //implements RoutesInterface
{
	abstract public function render();

	private static array $routes = array(
		"announce" => AnnounceController::class,
		"login" => LoginController::class,
		"register" => RegisterController::class,
		"logout" => LogoutController::class,
		"test" => TestController::class,
		"messages" => MessagesController::class,
		"reservation-history" =>  ReservationHistoryController::class,
		"user-list" => UserListController::class,
		"delete-user" => DeleteUserController::class,
		"annonce-list" => AnnoncesListController::class,
		"delete-announce" => DeleteAnnounceController::class,
		"comment-list" => CommentListController::class,
		"delete-comment" => DeleteCommentController::class,
		"sell" => RentController::class,
		"home" => AnnouncesController::class,
		"" => AnnouncesController::class,
	);

	public static function getRoutes(): array
	{
		return Controller::$routes;
	}

	abstract public function getInnerRoutes(): array;
}
