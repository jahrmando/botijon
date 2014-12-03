<?php

class convertir extends command {
	public function __construct()
	{
		$this->name = "convertir";
		$this->public = true;
	}

	public function help(){
		return "Uso: !convertir <expresion> Convierte una expresion de conversión de divisas.";
	}

	public function process($args){
		$answer = "";
		$args = trim($args);
		if( strlen($args)>0){
			$entrada = urlencode($args);
			$url = "https://www.google.com.mx/search?q={$entrada}&oq=200&aqs=chrome.1.69i57j69i59j69i65l2j0l2.3015j0j8&client=ubuntu-browser&sourceid=chrome&es_sm=122&ie=UTF-8";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/37.0.2062.120 Chrome/37.0.2062.120 Safari/537.36');
			$html = curl_exec($ch);
			$web = new DomDocument;
			@$web->loadHTML($html);
			$nodos = @$web->getElementById('topstuff')->getElementsByTagName('div');
			$answer = "No pude convertir lo que me pides.";
			if( $nodos ){
				$nodos = iterator_to_array($nodos);
				if( count($nodos) === 6 ){
					$answer = utf8_decode($nodos[3]->nodeValue." ".$nodos[4]->nodeValue);
				}
			}
		}else{
			$answer = "Ingresa una expresion.";
		}
		$this->reply($answer, $this->currentchannel, $this->nick);
	}
}
