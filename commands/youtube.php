<?php

class youtube extends command {
	public function __construct()
	{
		$this->name = "youtube";
		$this->public = true;
		global $config;
		$this->apikey = $config->youtube->apikey;
	}

	public function help(){
		return "Uso: !youtube <palabra o frase> . Busca vídeos en youtube de acuerdo al texto proporcionado.";
	}

	public function process($args){
		$this->output = "";
		if( strlen(trim($args)) > 0 ){
			try{
				$url = "https://www.googleapis.com/youtube/v3/search?part=id%2Csnippet&maxResults=3&q=".urlencode($args)."&key=".$this->apikey;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
				$respuesta = curl_exec($ch);
				curl_close($ch);
				$resultados = json_decode($respuesta);
				if( isset($resultados->error) ){
					$this->output = "Tuvimos un problema en la busqueda: ".$resultados->error->errors[0]->reason;
				}else{
					if( count($resultados->items)>0 ){
						$videos = array();
						foreach( $resultados->items as $video ){
							array_push( $videos , $video->snippet->title." - http://http://www.youtube.com/watch?v=".$video->id->videoId  );
						}
						$this->output = join("\n", $videos);
					}else{
						$this->output = "No obtuve resultados de tu busqueda";
					}
				}
			}catch(Exception $e){
				$this->output = "Tuve un problema al realizar tu busqueda.";
			}
		}else{
			$this->output = "Digame que buscar, tampoco soy adivino.";
		}
	}
}
