<?php

include ("view/messages/RecepteurMsg.php");
include ("view/messages/DestinatireMsg.php");

class MainMessages extends Template
{
	public function __construct()
	{

	}

	private function renderConvos(array $convos): string
	{
		$allConvos = "";

		foreach ($convos as $index => $convo)
		{
			if($convo->idDesti !== $_SESSION["userId"] || $convo->idRecip === $_SESSION["userId"])
			{
				$dateMsg = DateTime::createFromFormat('Y-m-d H:i:s', $convo->date);
				$date = str_replace("-","/",$dateMsg->format('d-m-Y'));
				$time = $dateMsg->format("H:i:s");

				$allConvos .= '
					<li
					class="mb-2 rounded border border-2 border-none border-sky-700 hover:border-solid p-1">
						<a
						class=""
						href="/messages?userID='.$convo->idDesti.'">
						
							'.$convo->nom.' '. $convo->prenom .'
							
							<div class="text-gray-400 text-sm truncate flex items-center">
								<span class="iconify mr-1" data-icon="mingcute:time-line"></span>
								'. $date .' à '. $time .'
							</div>
							
							<div class="text-sm truncate flex items-center">
              					<span class="iconify mr-1" data-icon="tabler:message"></span>
              					'. ($convo->msg ?? '(aucun message)') .'
            				</div>
						</a>
					</li>
				';
			}
		}

		return $allConvos;
	}

	private function renderMessages(array $messages): string
	{
		$allMsg = "";

		foreach ($messages as $index => $message)
		{
			$toRender = "";

			$dateMsg = date_create_from_format(
				'Y-m-d H:i:s',
				$message->date ?? date('Y-m-d H:i:s')
			)->format('d-m-Y H:i:s');


			if($message->idRecip === $_SESSION["userId"])
			{
				$toRender =
					(new RecepteurMsg($message->msg,$dateMsg))
						->render([]);
			}
			else {
				$toRender =
					(new DestinatireMsg($message->msg,$dateMsg))
						->render([]);
			}

			$allMsg .= $toRender;
		}

		return $allMsg;
	}

	public function render($context): string
	{
		$allConvos = $this->renderConvos($context["convos"] ?? array());
		$allMsg = $this->renderMessages($context["messages"] ?? array());
		$navTitle = "Messages";

		if(isset($_GET["userID"]))
		{
			$navTitle = "Conversation avec ". $context["contact"]->nom . " " . $context["contact"]->prenom;
		}

		$html = '
		<section class="font-sans h-full w-full top-5 dark:bg-gray-900 text-white">
			<!--Convos & messages-->
			<div class="flex h-screen overflow-hidden position-relative z-10">
				<!-- Sidebar convos -->
				<div class="sticky top-0 left-0 h-full bg-gray-800 p-4 w-1/5">
					<ul class="text-white">'.
						$allConvos
					.'</ul>
				</div>
				
				<!-- Vertical Line Separator -->
    			<div class="w-1 bg-white"></div>
				
				<!-- Main Chat Area -->
				<div class="flex-1 p-4 overflow-y-scroll h-full max-h-screen">';

					if(isset($_GET["userID"]))
					{
						$html .='
						<!-- Navigation Bar -->
						<div class="sticky top-0 left-0 bg-gray-800 text-white p-4 z-20 mb-5">
							<div class="flex items-center justify-between">
								<!-- Logo or application name -->
								<div class="text-white text-lg font-bold">
									'. $navTitle .'
								</div>
							</div>
						</div>
						';
					}
					else {
						$html .= '
							<div class="w-full p-5 h-96 flex flex-col items-center">
								<span class="iconify w-1/3 h-full" data-icon="tabler:messages"></span>
								<h2 class="text-white text-xl text-center my-5">Sélectionnez une conversation.</h2>
							</div>
						';
					}

					$html .='
					<div class="mb-4 h-100"> '.
						$allMsg
					.'</div>';


		if(isset($_GET["userID"]))
		{
			$html .= '
				<!-- Chat Bar -->
				<div class="sticky bottom-0 left-0 p-4 w-100 grid grid-cols-4 dark:bg-gray-900">
					<p class="col-start-1 col-end-2"></p>
					
					<form
					action="/messages/send"
					method="POST"
					class="flex col-start-2 col-end-4">
						<input
						type="text"
						name="message"
						class="flex-1 border text-black rounded-l p-2">
						
						<button
						type="submit"
						class="bg-blue-500 text-white rounded-r p-2">
							<span class="iconify w-full h-full" data-icon="material-symbols:send-outline"></span>
						</button>
					</form>
					
					<p class="col-start-4 col-end-5"></p>
				</div>
			';
		}

		$html .='
				</div>
			</div>
		</section>
		';
		return $html;
	}
}
