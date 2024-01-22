<?php

include_once("Template.php");

class AnnouncePage extends Template {
    public function render($context) : string {

		$status =  $this->renderStatus();

        $html = '
            <section class="bg-gray-50 dark:bg-gray-900">
            '. $status .'
            
                <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-0">
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <div class="flex justify-center flex-col items-center">
                            <img src="../assets/images/'.$context['announce']->image.'" class="h-auto max-w-lg rounded-lg"/>
                            <div class="flex flex-row mt-3">
                            '.$this->autorization($context).'
                            </div>
                            <p class="dark:text-gray-400 mt-3">Publié par '.$context['owner']->nom.' '.$context['owner']->prenom.'</p>
                        </div>
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">'.$context['announce']->emplacement.'</h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">'.$context['announce']->description.'</p>
                        <div class="flex justify-end items-end pt-5 flex-row">
                        	<p class="font-normal font-bold tracking-tight dark:text-white text-center">'.$context['announce']->prix.'€/nuit</p>
                    	</div>
					</div>
					
					<div class="p-4 rounded shadow-md mb-4 max-w-screen-xl w-full text-white">
						<form
							method="post"
							class="flex justify-center flex-col mb-10"
							action="/announce/book/"
							id="book-form">
							
							<div class="grid grid-cols-4 gap-4">
								<div class="relative col-start-1 col-end-4">
									<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
										<!-- Replace with Flowbite date picker icon -->
										<span class="iconify" data-icon="feather:calendar" data-inline="false"></span>
									</div>
									
									<input
										type="text"
										id="datePicker"
										name="dateRange"
										placeholder="Réservation"
										class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
								</div>
								
								<button
									type="submit"
									class="col-start-4 col-end-5 text-white mx-auto w-full bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
									Louer !
								</button>
							</div>
						</form>
					</div>
                    	
                    <a href="/home" class="text-white items-start bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Retour</a>
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h3 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Commentaires</h5>
                        '.$this->comments($context).'
                    </div>
                </div>
            </section>
            
            <script>
            
            let now = new Date()
            let today = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate()
            
            console.log(localStorage)
            
            document.addEventListener("DOMContentLoaded", function() {
				// Instantiation du date picker
				const datePicker = flatpickr("#datePicker", {
					mode: "range",
					//Mise des dates désactivées
					disable: JSON.parse(localStorage.getItem("disabled")),
					minDate: localStorage.getItem("maxDate"),
					maxDate: localStorage.getItem("minDate"),
					dateFormat: "d/m/Y",
				});
			});
			</script>
        ';
    
        return $html;
    }

    public function autorization($context) {
        $authorization = "";
        $context['announce']->animaux ? 
            $authorization .= '
                <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-green-400 border border-gray-500 ">
                    Animaux
                </span>'
            : $authorization .= '
                <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-red-400 border border-gray-500 ">
                    Animaux
                </span>';
        $context['announce']->enfants ? 
            $authorization .= '
                <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-green-400 border border-gray-500 ">
                    Enfants
                </span>'
            : $authorization .= '
                <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-red-400 border border-gray-500 ">
                    Enfants
                </span>';
        $context['announce']->accessibilite ? 
            $authorization .= '
                <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-green-400 border border-gray-500 ">
                    Accessibilite
                </span>'
            : $authorization .= '
                <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-red-400 border border-gray-500 ">
                    Accessibilite
                </span>';
        return $authorization;
    }

    public function comments($context) {
        $html = '';
        if($context['comments'] instanceof stdClass){
            $html .= '
                    <div class="text-white mb-3">
                    <h5>'.$context['comments']->nom.' '.$context['comments']->prenom.'</h5>
                    <p>'.$context['comments']->Commentaires.' - '.$context['comments']->Note.'/5</p>
                    <span>'.$context['comments']->dateAvis.'</span>
                    </div>';
        } else {
            foreach ($context['comments'] as $comment) {
                $html .= '
                    <div class="text-white mb-3">
                    <h3 class="font-bold">'.$comment->nom.' '.$comment->prenom.' - <span class="text-sm dark:text-gray-400">'.$comment->dateAvis.'</span></h5>
                    <p>'.$comment->Commentaires.' - '.$comment->Note.' <span class="iconify inline text-yellow-300" data-icon="material-symbols:award-star-outline"></span></p>
                    </div>';
            }
        }
        return $html;
    }

	private function renderStatus(): string
	{
		$html = '';

		$barColor = "bg-gray-500";

		if(isset($_SESSION["messageBook"]))
		{
			$barColor = $_SESSION["messageBook"]["status"] === true ? 'bg-green-700' : 'bg-red-500';
			$message = $_SESSION["messageBook"]["message"];

			$html = '
			<div class="mx-auto w-1/3 '. $barColor .' text-white p-5 mb-5 rounded rounded-5">
				'. $message .'
			</div>
			';

			unset($_SESSION["messageBook"]);
		}

		return $html;
	}
}
