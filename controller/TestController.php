<?php

include_once("./controller/Controller.php");

class TestController extends Controller
{
	public const ROUTES = array(
		"@GET" => "tester",
	);

	public function getInnerRoutes(): array
	{
		return TestController::ROUTES;
	}

	/**
	 * @throws Exception
	 */
	public function tester(): void
	{
		$et = new TestEntity();

		$a = $et
			->orderBy("id")
			->where("id","1")
			->distinct()
			->get()
		;

		echo "<h6>RES:</h6>";

		dump($et->getQuery());
		dump($a);

		echo "<h1>--------------------------------------------------</h1>";
	}

	public function render()
	{

	}
}
