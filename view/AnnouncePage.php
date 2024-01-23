<?php

include_once("Template.php");

class AnnouncePage extends Template {
    public function render($context) : string {

        $html = '
            <section class="bg-gray-50 dark:bg-gray-900">
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
                        <div class="flex justify-end items-end pt-5">
                        <p class="font-normal font-bold tracking-tight dark:text-white text-center">'.$context['announce']->prix.'€/nuit</p>
                    </div>
                    <a href="/home" class="text-white items-start bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Retour</a>
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h3 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Commentaires</h3>
                    <div class="font-bold text-sm dark:text-gray-400">
                        Note Moyenne de cette location : ***<span class="iconify inline text-yellow-300" data-icon="material-symbols:award-star-outline"></span>
                    </div>
                        '.$this->comments($context).'
                    </div>
                    <hr>
                    <form class="space-y-4 md:space-y-6" action="/announce/?id='.$_GET['id'].'" method="POST">
                    <div class="d-flex flex-col">
                            '.$this->postingComments($context).'
                    </div>
                    </form>
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

    public function postingComments() {
        $html = '';
        if(isset($_SESSION['userId'])) {
            $html .= '
            <div>
            <label for="comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Poster un commentaire</label>
            <textarea name="comment" id="comment" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Très jolie maison..." required></textarea>
            <label for="rate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Donner une note</label>
            <select id="rate" name="rate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            </select>
            </div>
            <button type="submit" class="w-100 mt-3 text-white bg-blue-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800">Publier le Commentaire</button>';
        } else {
            $html .= '
            <label for="comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Poster un commentaire</label>
            <textarea name="comment" id="comment" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Ne pas crier le soir..." disabled></textarea>
            <label for="rate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Donner une note</label>
            <select id="rate" name="rate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" disabled>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            </select>
            <p class="text-red-600">Vous devez être connecté pour poster un commentaire.</p>
            <button type="submit" class="w-100 mt-3 text-white bg-blue-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800" disabled>Publier le Commentaire</button>';
        }

        return $html;
    }

    // TODO LISTE EQUIPEMENT
    // public function getEquipments($context) {
    //     $html = '';
    //     if($context['equipment'] instanceof stdClass){
    //         foreach ($context['equipment'] as $equip) {
    //             $html .= '
    //             <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-red-400 border border-gray-500 ">
    //                 '.$equip.'
    //             </span>';
    //         }
    //     } else if ($context['equipment']) {
    //         $html .= '';
    //     } else {
    //         $html .= '';
    //     }
    //     return $html;
    // }
}
