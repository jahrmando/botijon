<?php
class figlet extends command {


	public function __construct(){
		$this->name = "figlet";
		$this->public = true;
		$this->maxlength = 15;
	}

	public function help(){
		return "Uso: !figlet <texto> . Crea la frase en ASCIIART.";
	}

	public function process($args){
		$figlet = new Zend_Text_Figlet();
		if ( strlen( $args) > $this->maxlength){
			$this->output = 'Los siento. Solo acepto un maximo de ' . $this->maxlength . ' caracteres';
		} else {
			$this->output = $figlet->render($args);
		}
	}
}
