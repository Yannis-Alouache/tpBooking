<?php

class DestinatireMsg extends Template
{

	public string $message;
	public string $date;

	public function __construct(string $message, string $date)
	{
		$this->message = $message;
		$this->date = $date ?? "(inconnu)";
	}

	public function render($context): string
	{
		$html = '
		<div class="flex justify-end">
			<div class="bg-blue-500 text-white p-2 rounded">'.
				$this->message .'
				
				<!--
				<div class="text-gray-600 text-xs absolute bottom-0 left-0 ml-2 mb-1">
              		Jan 10, 2024 14:30
            	</div>
            	-->
			</div>
		</div>
		';

		return $html;
	}
}
