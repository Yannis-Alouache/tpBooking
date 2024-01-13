<?php

class RecepteurMsg extends Template
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
		<div class="flex justify-start">
			<div class="bg-gray-300 p-2 rounded text-black">'.
				$this->message .'
			</div>
		</div>
		';

		return $html;
	}
}
