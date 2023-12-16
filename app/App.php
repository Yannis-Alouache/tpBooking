<?php

include ("./app/Routeur.php");
class App
{
	function __construct()
	{
		session_start();

		new Routeur();
	}
}


?>