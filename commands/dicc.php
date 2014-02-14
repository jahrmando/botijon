<?php
class dicc extends command {

	public function __construct(){
		$this->name = 'dicc';
		$this->public = true;
	}

	public function help(){
		return "Uso: !definicion <palabra> . Devuelve el significado de la palabra según la RAE.";
	}

	public function process($args){
		$args = trim($args);
		$cArgs = count(explode(" ",$args));
		$this->output = "";
		if( $cArgs > 1 ){
			$this->output = "Debe ser una palabra no una frase";
		} else {
			if( !strlen($args)>0 ){
				$this->output = "Cadena demasiado corta.";
			}else{
				$url = "http://lema.rae.es/drae/srv/search?val=".urlencode($args);
				$respuesta = file_get_contents($url);
				$datos = "";
				if( preg_match('/<div[^<>]*[^<>]*>(?<content>.*?)<\/div>/', $respuesta , $datos )){
					$resultados = array();
					preg_match_all('/<p[^<>]*class="q"[^<>]*>(?<content>.*?)<\/p>/', @$datos["content"] , $resultados );
					$resultados = array_slice($resultados["content"],0,4);
					$output = array();
					foreach( $resultados as $key => $resultado ){
						array_push($output, html_entity_decode(trim(strip_tags($resultado))));
					}
					$this->output = join("\n", $output);
				}else{
					$this->output = "No obtuvimos resultados para tu palabra.";
				}
			}
		}
	}
}
