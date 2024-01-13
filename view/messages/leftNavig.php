<?php

include_once "view/Template.php";

class leftNavig extends Template
{
    public function render($context): string {
		$html = '
		<section class="font-sans bg-dark-900 text-white">
			<div class="bg-gray-800 p-4"> ';

				if($context instanceof stdClass) {
					$html .= '<a href="/messages?userID='. $context->idUtilisateur . '" >
						<span>'.$context->nom.'</span>
						<span>'.$context->prenom.'</span>
					</a>';
				} else {
					foreach ($context as $index => $contact) {
						$html .= '<a href="/messages?userID='. $contact->idUtilisateur . '" >
							<span>'.$contact->nom.'</span>
							<span>'.$contact->prenom.'</span>
						</a>';
					}
				}
			$html .= '</div>';

		return $html;
	}
}
