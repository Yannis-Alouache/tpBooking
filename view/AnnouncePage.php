<?php

include_once("Template.php");

class AnnouncePage extends Template {
    public function render($context) : string {

        $html = '
            <section class="bg-gray-50 dark:bg-gray-900">
                <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <div class="flex justify-center flex-col items-center">
                            <img src="../assets/images/'.$context['announce']->idAnnonce.'_'.$context['announce']->disponibilite_debut.'.png" class="h-auto max-w-lg rounded-lg"/>
                            <div class="flex flex-row mt-3">
                            '.$this->autorization($context).'
                            </div>
                        </div>
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">'.$context['announce']->emplacement.'</h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">'.$context['announce']->description.'</p>
                        <div class="flex justify-end items-end pt-5">
                        <p class="font-normal font-bold tracking-tight dark:text-white text-center">'.$context['announce']->prix.'€/nuit</p>
                    </div>
                    <a href="/home" class="text-white items-start bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Retour</a>
                </div>
            </section>
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
}