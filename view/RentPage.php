<?php

include_once("Template.php");

class RentPage extends Template {
    public function render($context) : string {
        $html = '
        <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-20">
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-xl xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Louer un bien
                    </h1>';
                    
                    if (isset($context["error"]))
                        $html .= 
                        '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-900 dark:text-red-400" role="alert"><strong>' . 
                            $context["error"] .
                        '</strong></div>';
                    
                    if (isset($context["success"]))
                        $html .= 
                        '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-900 dark:text-green-400" role="alert"><strong>' . 
                            $context["success"] .
                        '</strong></div>';
                    
                    $html .= '
                    
                    <form class="space-y-4 md:space-y-6" action="/sell" method="POST">
                        <div date-rangepicker class="grid md:grid-cols-2 md:gap-3 text-center">
                            <!-- Start Date Picker -->
                            <label for="disponibilite_debut" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Début de Disponibilité</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <span class="iconify text-white" data-icon="feather:calendar" data-inline="false"></span>
                                </div>
                                <input value="" name="disponibilite_debut" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Début">
                            </div>

                            <!-- End Date Picker -->
                            <label for="disponibilite_fin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fin de Disponibilité</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <span class="iconify text-white" data-icon="feather:calendar" data-inline="false"></span>
                                </div>
                                <input value="" name="disponibilite_fin" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Fin">
                            </div>
                        </div>
                        <div>
                            <label for="emplacement" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Emplacement</label>
                            <input type="text" name="emplacement" id="emplacement" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Emplacement" required>
                        </div>
                        <div>
                        <div class="flex flex-1">
                            <div class="w-full text-white">
                                <label for="prix" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Prix par nuit en €</label>
                                <input
                                    id="min-input"
                                    type="number"
                                    value="1"
                                    min="1"
                                    max="3000"
                                    name="prix"
                                    class="w-full h-auto h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                >
                            </div>
                        </div>    
                        </div>
                        <div>
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                            <input type="textarea" name="description" id="description" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Appartement bien éclairé..." required>
                        </div>
                        <div>
                            <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Image (Format acceptés : .png , .jpg , .jpeg)</label>
                            <input type="file" name="image" id="image" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                              <input id="animaux" name="animaux" aria-describedby="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                            </div>
                            <div class="ml-3 text-sm">
                              <label for="animaux" class="font-light text-gray-500 dark:text-gray-300">Mon bien accepte de recevoir des animaux</label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="enfants" name="enfants" aria-describedby="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enfants" class="font-light text-gray-500 dark:text-gray-300">Mon bien accepte de recevoir des enfants</label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="accessibilite" name="accessibilite" aria-describedby="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="accessibilite" class="font-light text-gray-500 dark:text-gray-300">Mon bien est accessible aux personnes à mobilité réduites</label>
                            </div>
                        </div>
                        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800">Publier mon annonce</button>
                    </form>
                </div>
            </div>
        </div>
      </section>
        ';



        return $html;
    } 
}

?>