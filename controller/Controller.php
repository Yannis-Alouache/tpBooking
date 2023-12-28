<?php

//include("./app/interfaces/RoutesInterfaces.php");

include("./controller/LoginController.php");
include("./controller/RegisterController.php");
include("./controller/TestController.php");

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
		"login" => LoginController::class,
		"register" => RegisterController::class,
		"test" => TestController::class,
	);

	public static function getRoutes(): array
	{
		return Controller::$routes;
	}
}
