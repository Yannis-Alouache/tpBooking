<?php

include_once("Template.php");

class AnnouncePage extends Template {
    public function render($context) : string {

        $html = '
            <section class="bg-gray-50 dark:bg-gray-900">
                <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                    <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                        <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
                        Flowbite    
                    </a>
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">

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
}