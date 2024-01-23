<?php

class PayementScreen
{
	private function getRentedDays(string $start, string $end): int
	{
		$rentStart = DateTime::createFromFormat('Y-m-d', $start);
		$rentEnd = DateTime::createFromFormat('Y-m-d', $end);

		return ($rentStart->diff($rentEnd)->d) + 1;
	}


	public function render(array $context = array()): string
	{

		$rendedDays = $this->getRentedDays($context["start"], $context["end"]);
		$total = round($rendedDays * $context["annonce"]->prix, 2);

		return '
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
			
			<section class="bg-gray-900 w-full text-white min-h-screen flex items-center justify-center">
                <div class="container mx-auto mt-8 flex justify-center dark:bg-gray-900 text-white">
		
					<!-- Step 1: Payment Options -->
					<div class="w-2/3 p-8 bg-dark-900 text-white">
						<h2 class="text-2xl font-bold mb-4">Paiement</h2>
						
						<div>
							<!-- Credit Card Payment Option -->
							<div class="flex items-center space-x-2">
								<input type="radio" id="ccPayment" name="paymentOption" class="mr-2">
								<label for="ccPayment">Carte bancaire</label>
							</div>
		
							<!-- Bank Transfer Payment Option -->
							<div class="flex items-center space-x-2">
								<input type="radio" id="bankTransfer" name="paymentOption" class="mr-2">
								<label for="bankTransfer">Virement</label>
							</div>
						</div>
						
						
						<!--Container inputs-->
						<div class="w-full">
						
							<!--Container CC-->
							<form
							    method="post"
							    action="/announce/book/cc"
							    class="w-full p-8 payment-form" id="ccForm">
								<h2 class="text-2xl font-bold mb-4">Carte bancaire</h2>
						
								<!-- Flowbite Input for Card Number -->
								<div class="my-4">
                                    <label
                                        for="cardNumber"
                                        class="block text-white text-sm font-semibold mb-2">
                                        Numéro de carte
                                    </label>
                                    <input
                                        type="text"
                                        name="numCarte"
                                        id="cardNumber"
                                        placeholder="Numéro de carte"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </div>
			
								<!-- Flowbite Input for Expiry Date -->
								<div class="flex my-4 space-x-4">
									<!-- Flowbite Input for CCV -->
									<div class="flex-1 w-1/2">
                                        <label
                                            for="ccv" 
                                            class="block text-white text-sm font-semibold mb-2">
                                            CCV
                                        </label>
                                        <input
                                            type="text" 
                                            name="ccv"
                                            id="ccv" 
                                            placeholder="CCV" 
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
									</div>
		
									<!-- Flowbite Input for Expiry Date -->
									<div class="flex-1 w-1/2">
                                        <label
                                            for="expiryDate" 
                                            class="block text-white text-sm font-semibold mb-2">
                                            Date d\'expiration
                                        </label>
                                        <input
                                            type="text" 
                                            name="expiDate"
                                            id="expiryDate" 
                                            placeholder="MM/AAAA" 
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
									</div>
								</div>
								
								<button type="submit" class="w-full text-white items-start bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
								    Réserver !
                                </button>
							</form>
							
							<!--Container transfert banque-->
							<form
							    method="post"
							    action="/announce/book/transfer"
							    class="w-full p-8 text-white mt-4 payment-form" id="bankForm">
							    
								<h2 class="text-2xl font-bold mb-4">Virement</h2>
		
								<div class="flowbite-input mb-4">
                                    <label
                                        for="accountHolder" 
                                        class="block text-white text-sm font-semibold mb-2">
                                        Nom du titulaire du compte
                                    </label>
                                    <input
                                        type="text" 
                                        name="accHodler"
                                        id="accountHolder" 
                                        placeholder="NOM Prénom"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
								</div>
		
								<!-- Flowbite Input for IBAN -->
								<div class="flowbite-input mb-4">
                                    <label
                                        for="iban" 
                                        class="block text-white text-sm font-semibold mb-2">
                                        IBAN
                                    </label>
                                    <input
                                        type="text" 
                                        id="iban" 
                                        name="iban"
                                        placeholder="FRXX XXXX XXXX XXXX XXXX XXXX XXX"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
								</div>
								
								<!-- Flowbite Input for BIC -->
								<div class="flowbite-input mb-4">
								    <label
                                        for="bic" 
                                        class="block text-white text-sm font-semibold mb-2">
                                        BIC
                                    </label>
                                    <input
                                        type="text" 
                                        id="bic" 
                                        name="bic"
                                        placeholder="XXXXXXX"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
								</div>
								
								<button type="submit" class="w-full text-white items-start bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
								    Réserver !
                                </button>
							</form>
						</div>
						
                    </div>
				
					<!-- Step 2: Order Summary -->
					<div class="w-1/3 flex-1 p-8 rounded-md shadow-md bg-dark-900">
						<h2 class="text-2xl font-bold mb-4">Résumé</h2>
		
						<!-- House Information (Randomly generated) -->
						<p class="mb-2"><strong>Annonce: </strong> '. $context["annonce"]->emplacement .' </p>
		
						<!-- House Description (Randomly generated) -->
						<p class="mb-2"><strong>Description: </strong><i>'. $context["annonce"]->description .'</i></p>
		
						<!-- Price per Night -->
						<p class="mb-2 inline" style="display: block ruby !important;">
							<strong class="inline">'. $context["annonce"]->prix .'</strong>
							<span class="iconify iconify-inline" data-icon="material-symbols:euro"></span> / nuit
						</p>
		
						<!-- Number of Days Rented (Randomly generated between 1 and 7) -->
						<p class="mb-2">
						    Du '.
			                    str_replace(
									"-",
									"/",
			                        DateTime::createFromFormat('Y-m-d', $context["start"])->format('d-m-Y')
			                    )
                            .' au '.
                                str_replace(
                                    "-",
                                    "/",
                                    DateTime::createFromFormat('Y-m-d', $context["end"])->format('d-m-Y')
                                )
                            .'
                        </p>
                        
						<p class="mb-2"><strong>Jours loués :</strong> '. $rendedDays .' </p>
		
						<hr class="border-t my-4">
		
						<!-- Display Total Cost -->
						<p class="mb-2"><strong>Total :</strong> '. $total .' €</p>
					</div>
			    </div>
			</section>
			 
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    let ccPayment = document.getElementById("ccPayment");
                    let bankTransfer = document.getElementById("bankTransfer");
                    let ccForm = document.getElementById("ccForm");
                    let bankForm = document.getElementById("bankForm");
    
                    //Mise de levent listener
                    ccPayment.addEventListener("change", ()=>{
                        togglePaymentForm()
                    });
                    
                    bankTransfer.addEventListener("change", ()=>{
                        togglePaymentForm()
                    });
                    
                    function togglePaymentForm() {
                        ccForm.style.display = ccPayment.checked ? "block" : "none";
                        bankForm.style.display = bankTransfer.checked ? "block" : "none";
                    }
                });
            </script>
		';
	}
}
