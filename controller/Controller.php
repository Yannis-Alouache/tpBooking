<?php

//include("./app/interfaces/RoutesInterfaces.php");

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

//Décommenter le 'implement' crée une erreur en php 8.3 (à voir si ça crashe aussi pour vous) (class controller not found) dans les autres controlleurs.
//Les mots ne peuvent pas décrire ma confusion.
//Mettez ce que vous obtenez ici quand vous décommantez svp
//Félix		PHP 8.3		ne marche pas (class Controller not found dans Login et RegisterController)
//Raph		PHP 8.?		
//Yannis	PHP 8.?		
abstract class Controller // implements RoutesInterface
{
	abstract public function render();

	private static array $routes = array(
		"home" => AnnouncesController::class,
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
		"delete-announce" => DeleteAnnounceController::class
	);

	public static function getRoutes(): array
	{
		return Controller::$routes;
	}

	abstract public function getInnerRoutes(): array;
}
