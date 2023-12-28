<?php

include_once("./controller/Controller.php");

class TestController extends Controller
{
	private const ROUTES = array(
		"@GET" => "render",
	);

	public function getInnerRoutes(): array
	{
		return TestController::ROUTES;
	}

	public function render()
	{
	}
}
