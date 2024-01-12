<?php

include_once("Template.php");

class AnnouncesPage extends Template {
    public function render($context) : string {

        $html = '
            <section class="bg-gray-50 dark:bg-gray-900">
                <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                    <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                        <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
                        Flowbite    
                    </a>
                    <div class="flex flex-row bg-white rounded-lg shadow dark:border md:mt-0 sm:max-h-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                        '.$this->renderAnnouncesList($context).'
                    </div>
                </div>
            </section>
        ';
    
        return $html;
    }

    public function renderAnnouncesList($context) {
        $html = '';

        foreach($context["announces"] as $announce){
            $html .= '
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <a href="#" class="block max-w-sm p-6 h-full bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">

                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">'.$announce->emplacement.'</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">'.$announce->description.'</p>
                    <div class="flex justify-end items-end">
                        <p class="font-normal font-bold tracking-tight dark:text-white text-center">'.$announce->prix.'€/nuit</p>
                    </div>
                    </a>
                </div>
            ';
        }

        return $html;
    }
}