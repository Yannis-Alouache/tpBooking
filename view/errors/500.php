<?php

class Main500
{

	public Navigation $navigation;
	public Footer $footer;

	public function __construct()
	{
		$this->navigation = new Navigation();
		$this->footer = new Footer();

		$this->render();
	}

	private function render(): void
	{
		echo
			$this->navigation->render([]) .
			'<section class="bg-gray-900 min-h-screen w-full flex items-center justify-center flex-col">
				<h1 class="text-center py-12 text-white text-9xl font-bold">500</h1>
				<p class="text-center text-gray-300 text-4xl">Internal server error. Please retry.</p>
				<a href="/" class="my-8 bg-blue-700 text-white hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Accueil</a>
			</section>'
			.
			$this->footer->render([]);
	}
}

new Main500();
