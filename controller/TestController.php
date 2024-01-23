<?php

include_once("./controller/Controller.php");

include_once("view/navigation.php");
include_once("view/footer.php");

class TestController extends Controller
{

	public Navigation $navigation;
	public Footer $footer;


	public const ROUTES = array(
		"filtre@GET" => "filtres",
		"payV1@GET" => "payerV1",
		"payer@GET" => "payerV2",
	);

	public function getInnerRoutes(): array
	{
		return TestController::ROUTES;
	}

	public function __construct()
	{
		$this->navigation = new Navigation();
		$this->footer = new Footer();
	}

	public function filtres(): void
	{
		echo $this->navigation->render([]);
		echo '<section class="bg-white dark:bg-gray-900 w-full text-white min-h-screen flex items-center justify-center">';

		echo '
		<!--https://flowbite.com/docs/components/badge/-->
		<!-- Filter Section -->
        <div class="p-4 rounded shadow-md mb-4 max-w-screen-xl w-full text-white">
            <h2 class="text-lg font-semibold mb-2">Filtres</h2>
            
            <form id="filterForm">
                <!-- OK -->
                <div class="grid grid-cols-2 grid-rows-1 items-center">
                	<!-- OK -->
					<div class="grid row-start-1 row-end-1 col-start-2 col-end-3 grid-rows-2 grid-cols-2">
					
						<!-- Enfants -->
						<div class="flex items-center">
							<input id="enfants" name="isEnfant" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
							<label for="enfants" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Enfants</label>
						</div>
						
						<!-- Handicapés -->
						<div class="flex items-center">
							<input id="animaux" name="isAnimaux" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
							<label for="animaux" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Animaux</label>
						</div>
						
						<!-- Animaux -->
						<div class="flex items-center">
							<input id="handicap" name="isHandicap" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
							<label for="handicap" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Handicapés</label>
						</div>
					</div>
					
					
                	<!-- Date Range Picker -->
					<!-- OK -->
					<div date-rangepicker class="flex items-center row-start-1 row-end-1 col-start-1 col-end-2">
						<!-- Start Date Picker -->
						<div class="relative">
							<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
								<!-- Replace with Flowbite date picker icon -->
								<span class="iconify" data-icon="feather:calendar" data-inline="false"></span>
							</div>
							<input name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Début">
						</div>
	
						<!-- "to" Text -->
						<span class="mx-4 text-white">à</span>
	
						<!-- End Date Picker -->
						<div class="relative">
							<div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
								<!-- Replace with Flowbite date picker icon -->
								<span class="iconify" data-icon="feather:calendar" data-inline="false"></span>
							</div>
							<input name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Fin">
						</div>
					</div>
				</div>
                
                <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

				<!-- OK -->
				<div>
					<!-- OK -->
					 <div class="mt-4">
						<label class="block text-white text-sm font-semibold mb-2">Addresse</label>
						<input name="address" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter address">
					</div>
                
					<!-- OK -->
					<div class="mt-4">
						<label class="block text-white text-sm font-semibold mb-2">Description</label>
						<input name="address" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Description">
					</div>
				</div>
                
                <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                
                <!-- Price Range Filters -->
                <!-- OK -->
				<div>
					<label class="block text-white text-sm font-semibold mb-2">Price Range</label>
					<div class="flex items-center space-x-4">
						<!-- Minimum Price Input Field -->
						<div class="w-1/2">
							<label class="block text-white text-sm mb-2">Minimum Price</label>
							<input name="minPrice" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter minimum price">
						</div>
				
						<!-- Maximum Price Input Field -->
						<div class="w-1/2">
							<label class="block text-white text-sm mb-2">Maximum Price</label>
							<input name="maxPrice" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter maximum price">
						</div>
					</div>
				</div>
                
                
                <!--OK-->
                <button id="dropdownDefault" data-dropdown-toggle="dropdown"
					class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
					type="button">
					
					Filter by category
					<svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"
					  xmlns="http://www.w3.org/2000/svg">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
					</svg>
				</button>
								
				<!-- Dropdown menu -->
				<div id="dropdown" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
					<h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
				  		Category
					</h6>
					
					<ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
					</ul>
			  	</div>
                
                <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                
                <!-- Submit Button -->
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Apply Filters</button>
            </form>
            
        </div>
		';

		echo ' </section> ';
		echo $this->footer->render([]);
	}

	public function payerV1(): void
	{
		//SVP ne pas supprimer, c'est en construction
		echo $this->navigation->render([]);
		echo '<section class="bg-white dark:bg-gray-900 w-full text-white min-h-screen flex items-center justify-center">';
		echo '
			<div class="max-w-screen-xl w-full">
			
				<div class="flex mx-auto">
		
					<!-- Carte de gauche (Paiement par carte de crédit) -->
					<div class="flex-1 pr-4">
						<div class="flowbite-card dark:bg-gray-800 hover:dark:bg-gray-700 p-4 rounded-md shadow-md">
							<form>
								<div class="mb-5">
									<label for="numeroCarte" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Numéro de Carte</label>
									<div class="flowbite-input dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
										<input type="text" id="numeroCarte" class="dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
									</div>
								</div>
								<div class="flex space-x-4">
									<div class="flex-1">
										<div class="mb-5">
											<label for="dateExpiration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date d\'Expiration</label>
											<div class="flowbite-input dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
												<input type="text" id="dateExpiration" class="dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
											</div>
										</div>
									</div>
									<div class="flex-1">
										<div class="mb-5">
											<label for="cvv" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CVV</label>
											<div class="flowbite-input dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
												<input type="text" id="cvv" class="dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
											</div>
										</div>
									</div>
								</div>
								<div class="mt-4">
									<button class="flowbite-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Payer Maintenant</button>
								</div>
							</form>
						</div>
					</div>
		
					<!-- Carte de droite (Virement Bancaire Direct) -->
					<div class="flex-1 pl-4">
						<div class="flowbite-card dark:bg-gray-800 hover:dark:bg-gray-700 p-4 rounded-md shadow-md">
							<form>
								<h3 class="text-lg font-semibold mb-4">Virement Bancaire Direct</h3>
								<div class="mb-5">
									<label for="nomBanque" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nom de la Banque</label>
									<div class="flowbite-input dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
										<input type="text" id="nomBanque" class="dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
									</div>
								</div>
								<div class="mb-5">
									<label for="numCompte" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Numéro de Compte</label>
									<div class="flowbite-input dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
										<input type="text" id="numCompte" class="dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
									</div>
								</div>
								<div class="mb-5">
									<label for="reference" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Référence</label>
									<div class="flowbite-input dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
										<input type="text" id="reference" class="dark:bg-gray-800 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
									</div>
								</div>
								<div class="mt-4">
									<button class="flowbite-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Payer Maintenant</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		';

   		echo ' </section> ';
		echo $this->footer->render([]);
	}

	public function payerV2(): void
	{

		echo $this->navigation->render([]);
		echo "
		<style>
  /* Hide all payment forms by default */
  .payment-form {
    display: none;
  }
  
  #ccPayment:checked :root .payment-form  {
    display: block;
  }

  /* Show the bank transfer form when the bank transfer radio button is checked */
  #bankTransfer:checked ~ .payment-form {
    display: block;
  }
</style>
		";
		echo '<section class="bg-gray-900 w-full text-white min-h-screen flex items-center justify-center">';
		
		echo '
		<div class="container mx-auto mt-8 flex justify-center bg-gray-900 text-white">
		
			<!-- Step 1: Payment Options -->
		  	<div class="w-3/4 p-8 bg-dark-900 text-white">
    			<h2 class="text-2xl font-bold mb-4">Payment Options</h2>
    			
				<div>
					<!-- Credit Card Payment Option -->
					<div class="flex items-center space-x-2">
        				<input type="radio" id="ccPayment" name="paymentOption" class="mr-2">
        				<label for="ccPayment">Credit Card</label>
      				</div>

     				<!-- Bank Transfer Payment Option -->
      				<div class="flex items-center space-x-2">
        				<input type="radio" id="bankTransfer" name="paymentOption" class="mr-2">
        				<label for="bankTransfer">Bank Transfer</label>
      				</div>
    			</div>
    			
    			
    			<!--Container inputs-->
    			<div class="w-full">
    			
    				<!--Container CC-->
					<div class="w-full p-8 payment-form" id="ccForm">
    					<h2 class="text-2xl font-bold mb-4">Carte de crédit</h2>
    			
						<!-- Flowbite Input for Card Number -->
						<div class="flowbite-input mb-4">
							<label for="cardNumber">Card Number</label>
							<input type="text" id="cardNumber" placeholder="Enter card number">
						</div>
	
						<!-- Flowbite Input for Expiry Date -->
						<div class="flex space-x-4">
							<!-- Flowbite Input for CCV -->
							<div class="flex-1">
						    	<label for="ccv">CCV</label>
						    	<input type="text" id="ccv" placeholder="CCV">
    						</div>

							<!-- Flowbite Input for Expiry Date -->
							<div class="flex-1">
								<label for="expiryDate">Expiry Date</label>
							  	<input type="text" id="expiryDate" placeholder="MM/YYYY">
    						</div>
  						</div>
					</div>
					
					<!--Container transfert banque-->
					<div class="w-3/4 p-8 text-white mt-4 payment-form" id="bankForm">
  						<h2 class="text-2xl font-bold mb-4">Virement</h2>

  						<div class="flowbite-input mb-4">
    						<label for="accountHolder">Nom du titulaire du compte</label>
    						<input type="text" id="accountHolder" placeholder="Enter account holders name">
  						</div>

						<!-- Flowbite Input for IBAN -->
						<div class="flowbite-input mb-4">
							<label for="iban">IBAN</label>
							<input type="text" id="iban" placeholder="Enter IBAN">
						</div>
						
						<!-- Flowbite Input for BIC -->
						<div class="flowbite-input mb-4">
							<label for="bic">BIC</label>
							<input type="text" id="bic" placeholder="Enter BIC">
						</div>
					</div>
				</div>
    			
  			</div>
		
 			<!-- Step 2: Order Summary -->
  			<div class="w-1/4 flex-1 p-8 rounded-md shadow-md bg-dark-900">
    			<h2 class="text-2xl font-bold mb-4">Order Summary</h2>

				<!-- House Information (Randomly generated) -->
				<p class="mb-2"><strong>House Name:</strong> Sunny Retreat</p>

				<!-- House Description (Randomly generated) -->
				<p class="mb-2"><strong>Description:</strong> A cozy retreat with breathtaking views.</p>

				<!-- Price per Night -->
				<p class="mb-2"><strong>Price per Night:</strong> $81</p>

				<!-- Number of Days Rented (Randomly generated between 1 and 7) -->
				<p class="mb-2"><strong>Number of Days Rented:</strong> 41 </p>

    			<hr class="border-t my-4">

				<!-- Display Total Cost -->
				<p class="mb-2"><strong>Total:</strong> 3 321 €</p>
  			</div>

		 	
		</div>
		';

		echo ' </section> ';
		echo "<script>
			document.addEventListener('DOMContentLoaded', () => {
				let ccPayment = document.getElementById('ccPayment');
				let bankTransfer = document.getElementById('bankTransfer');
				let ccForm = document.getElementById('ccForm');
				let bankForm = document.getElementById('bankForm');

				//Mise de l'event listener
				ccPayment.addEventListener('change', ()=>{
					togglePaymentForm()
				});
				
				bankTransfer.addEventListener('change', ()=>{
					togglePaymentForm()
				});
				
				function togglePaymentForm() {
					ccForm.style.display = ccPayment.checked ? 'block' : 'none';
					bankForm.style.display = bankTransfer.checked ? 'block' : 'none';
				}
			});
		</script>";
		echo $this->footer->render([]);
	}

	public function render()
	{
		//no
	}
}
