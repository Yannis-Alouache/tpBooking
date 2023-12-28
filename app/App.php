<?php

include "./vendor/autoload.php";

include("./app/Router.php");
include ("./app/dotEnv.php");

use Spatie\Ignition\Ignition;

class App
{
	function __construct()
	{
		Ignition::make()->register();

		session_start();

		new dotEnv();

		new Router();
	}
}
