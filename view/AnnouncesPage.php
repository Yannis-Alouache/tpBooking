<?php

include_once("Template.php");

class AnnouncesPage extends Template {
    public function render($context) : string {


		$status =  $this->renderStatus();

        $html = '
            <section class="bg-gray-50 dark:bg-gray-900 py-5">
            	'. $status .'
            	
				<div class="p-4 dark:bg-gray-800 rounded shadow-md mb-4 max-w-screen-xl w-full text-white mx-auto">
				
				'.$this->renderFilters($context["filters"], $context["equip"]).'
				</div>
            	
                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-row flex-wrap justify-center bg-white rounded-lg shadow dark:border md:mt-0 sm:max-h-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                        '.$this->renderAnnouncesList($context).'
                    </div>
                </div>
            </section>
            
            
			<script src="/assets/js/filters.js"></script>
            
        ';
    
        return $html;
    }

	public function renderAnnouncesList($context) {
		$html = '';

		if($context["announces"] instanceof stdClass)
		{
			$html .= '
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <a href="/announce/?id='.$context["announces"]->idAnnonce.'" class="block max-w-sm p-6 h-full bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
						<img src="../assets/images/'.$context["announces"]->idAnnonce.'_'.$context["announces"]->disponibilite_debut.'.png"/>
						<h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">'.$context["announces"]->emplacement.'</h5>
						<p class="font-normal text-gray-700 dark:text-gray-400">'.$context["announces"]->description.'</p>
						<div class="flex justify-end items-end pt-5">
							<p class="font-normal font-bold tracking-tight dark:text-white text-center">'.$context["announces"]->prix.'€/nuit</p>
						</div>
                    </a>
                </div>
            ';
		} else {
			foreach ($context["announces"] as $announce) {


				$html .= '
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8 ">
                    <a href="/announce/?id=' . $announce->idAnnonce . '" class="block max-w-sm p-6 h-full bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
						<img src="../assets/images/' . $announce->idAnnonce . '_' . $announce->disponibilite_debut . '.png"/>
						<h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">' . $announce->emplacement . '</h5>
						<p class="font-normal text-gray-700 dark:text-gray-400">' . $announce->description . '</p>
						<div class="flex justify-end items-end pt-5">
							<p class="font-normal font-bold tracking-tight dark:text-white text-center">' . $announce->prix . '€/nuit</p>
						</div>
                    </a>
                </div>
            ';
			}
		}

		return $html;
	}

	private function renderAllEquipements(stdClass|array $equip = array()): string
	{
		$html ='';


		if($equip instanceof stdClass)
		{


			$html .= '
			<div class="flex items-center ps-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 break-all overflow-hidden">
				<input
					id="checkbox-item-0"
					type="checkbox"
					value="'. $equip->CodeEquipement .'"
					name="equipList[]"
					class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
				<label
					for="checkbox-item-0"
					class="w-full py-2 ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">
					'. $equip->LibelleEquipement .'
				</label>
			</div>
			';
		}
		else {
			foreach ($equip as $index => $eq)
			{
				$checked = '';

				if(!empty($_GET["equipList"]))
				{
					if(in_array($eq->CodeEquipement, $_GET["equipList"]))
					{
						$checked = 'checked';
					}
				}

				$html .= '
				<div class="flex items-center ps-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 break-all overflow-hidden">
					<input
						id="checkbox-item-'.$index.'"
						type="checkbox"
						'. $checked .'
						value="'. $eq->CodeEquipement .'"
						name="equipList[]"
						class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
					<label
						for="checkbox-item-'.$index.'"
						class="w-full py-2 ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">
						'. $eq->LibelleEquipement .'
					</label>
				</div>
			';
			}
		}

		return $html;
	}

	private function renderFilters($filtres, $equipements): string
	{

		$isAnimaux = $filtres->isAnimaux ? 'checked' : '';
		$isEnfant = $filtres->isEnfant ? 'checked' : '';
		$isAccessible = $filtres->isAccessible ? 'checked' : '';

		$preEnd = DateTime::createFromFormat("Y-m-d", $filtres->end)->format('m-d-Y');

		$start = DateTime::createFromFormat("Y-m-d", $filtres->start)->format('m-d-Y');

		$end = ($preEnd === '01-01-2100'
			? ""
			: $preEnd
		);

		return '
		<input
			id="filterToggle"
			class="peer hidden cursor-pointer"
			type="checkbox">
		<label
			class="text-lg font-semibold mb-2 text-center py-3"
			for="filterToggle">
			Filtres
		</label>
		<span class="iconify iconify-inline inline peer-checked:rotate-180" data-icon="mdi:chevron-down" data-inline="true"></span>
		
		<form
		id="filterForm"
		class="my-3 hidden peer-checked:block">
			<!-- OK -->
			<div class="grid grid-cols-2 grid-rows-1 items-center">
				<!-- OK -->
				<div class="grid row-start-1 row-end-1 col-start-2 col-end-3 grid-rows-2 grid-cols-2">
				
					<!-- Enfants -->
					<div class="flex items-center">
						<input id="enfants" name="isEnfant" type="checkbox" '.$isEnfant.' value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
						<label for="enfants" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Enfants</label>
					</div>
					
					<!-- Handicapés -->
					<div class="flex items-center">
						<input id="animaux" name="isAnimaux" type="checkbox" '.$isAnimaux.' value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
						<label for="animaux" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Animaux</label>
					</div>
					
					<!-- Animaux -->
					<div class="flex items-center">
						<input id="handicap" name="isAccessible" type="checkbox" '.$isAccessible.' value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
						<label for="handicap" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Accessible</label>
					</div>
				</div>
				
				
				<!-- Date Range Picker -->
				<!-- OK -->
				<div date-rangepicker class="flex items-center row-start-1 row-end-1 col-start-1 col-end-2">
					<!-- Start Date Picker -->
					<div class="relative">
						<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
							<span class="iconify" data-icon="feather:calendar" data-inline="false"></span>
						</div>
						<input value="'. $start .'" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Début">
					</div>

					<span class="mx-4 text-white">à</span>

					<!-- End Date Picker -->
					<div class="relative">
						<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
							<span class="iconify" data-icon="feather:calendar" data-inline="false"></span>
						</div>
						<input value="'. $end .'" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Fin">
					</div>
				</div>
			</div>
			
			<hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

			<!-- OK -->
			<div>
				<!-- OK -->
				 <div class="mt-4">
					<label class="block text-white text-sm font-semibold mb-2">Addresse</label>
					<input value="'. $filtres->address .'" name="address" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Addresse">
				</div>
			
				<!-- OK -->
				<div class="mt-4">
					<label class="block text-white text-sm font-semibold mb-2">Description</label>
					<input value="'. $filtres->description .'" name="description" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Description">
				</div>
			</div>
			
			<hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
			
			<!-- Price Range Filters -->
			<!-- OK -->
			<div>
				<label class="block text-white text-sm font-semibold mb-2">
					Prix par jour
				</label>
				<div class="flex items-center space-x-4">
					<!-- Minimum Price Input Field -->
					<div class="w-1/2 h-full">
						<label class="block text-white text-sm mb-2">Minimum</label>
						
						<div class="flex flex-1">
							<div class="w-1/5 mr-5">
								<input
									id="min-input"
									type="number"
									value="'. (int)$filtres->minPrice .'"
									min="0"
									max="1000"
									name="minPrice"
									class="w-full h-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
								>
							</div>
							
							<div class="w-4/5 relative h-full">
								<input
									id="min-slider"
									type="range"
									value="'. (int)$filtres->minPrice .'"
									min="0"
									max="200"
									name="minPrice"
									class="w-full h-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
								>
									
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">0€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-1/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">50€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-2/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">100€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-3/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">150€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">200€</span>
							</div>
							
						</div>
						
					</div>
			
					<!-- Maximum Price Input Field
					https://flowbite.com/docs/forms/number-input/#number-input-with-slider
					-->
					<div class="w-1/2 h-full">
						<label class="block text-white text-sm mb-2">Maximum</label>
						
						<div class="flex flex-1">
							<div class="w-1/5 mr-5">
								<input
									id="max-input"
									type="number"
									value="'. (int)$filtres->maxPrice .'"
									min="0"
									max="1000"
									name="maxPrice"
									class="w-full h-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
								>
							</div>
							
							<div class="w-4/5 relative h-full">
								<input
									id="max-slider"
									type="range"
									value="'. (int)$filtres->maxPrice .'"
									min="0"
									max="200"
									name="maxPrice"
									class="w-full h-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
								>
									
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">0€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-1/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">50€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-2/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">100€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute start-3/4 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">150€</span>
								<span class="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">200€</span>
							</div>
							
						</div>
						
					</div>
				</div>
			</div>
			
			<hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
			
			<div class="w-full">
			
				<input
					id="checker-cat"
					class="hidden peer"
					type="checkbox"
				/>
				
				<label
					class=""
					for="checker-cat">
					Equipements
					<span class="iconify iconify-inline inline peer-checked:rotate-180" data-icon="mdi:chevron-down" data-inline="true"></span>
				</label>
				
				<div class="grid-cols-7 hidden peer-checked:grid">
					'. $this->renderAllEquipements($equipements) .'
				</div>
			</div>
			
			<hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
			
			<!-- Submit Button -->
			<p class="text-center w-full">
				<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded m-4">Appliquer les filtres</button>
				<a href="/home" class="button border border-blue-500 text-white px-4 py-2 rounded m-4 ">Réinitialiser les filtres</a>
			</p>
		</form>
		';
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
			<div class="mx-auto w-1/3 '. $barColor .' text-white p-5 my-5 rounded rounded-5">
				'. $message .'
			</div>
			';

			unset($_SESSION["messageBook"]);
		}

		return $html;
	}
}
