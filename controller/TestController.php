<?php

include_once("./controller/Controller.php");

class TestController extends Controller
{
	private const ROUTES = array(
		"@GET" => "tester",
	);

	public function getInnerRoutes(): array
	{
		return TestController::ROUTES;
	}

	public function tester(): void
	{
		$et = new TestEntity();
		$et
			->find(1)
			->get(["nom"])
			;
		dump($et);
	}

	public function render()
	{
	}
}
