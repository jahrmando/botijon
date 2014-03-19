<?php

class acortar extends command {
	public function __construct()
	{
		$this->name = "acortar";
		$this->public = true;
		$this->usesconfig = true;
		global $config;
		$this->type = $config->acortar->type;
		if( $this->type != 'google'){
			$this->type = 'bitly';
			$this->token = $config->acortar->token;
		}
	}

	public function help(){
		return "Uso: !acortar <url> . Crea una url corta para url proporcionada.";
	}

	public function process($args){
		$answer = "";
		$args = trim($args);
		if( strlen($args)>0){
			try{
				if( $this->type == 'google' ){
					$parametros = json_encode(array("longUrl"=>urlencode($args)));
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_URL,"https://www.googleapis.com/urlshortener/v1/url");
					curl_setopt($ch,CURLOPT_POST,1);
					curl_setopt($ch,CURLOPT_POSTFIELDS, $parametros );
					curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
					$respuesta = curl_exec($ch);
					curl_close($ch);
					$datos = json_decode($respuesta);
					$answer = $datos->id;
				}else{
					$url = urlencode( $args );
					if( !preg_match("/^http/",$url ) ){
						$url = "http://".$url;
						$args = $url;
					}
					$respuesta = file_get_contents("https://api-ssl.bitly.com/v3/user/link_save?access_token={$this->token}&longUrl={$url}");
					$datos = json_decode($respuesta);
					if( $datos->status_code == 500 ){
						$answer = "Tuvimos un problema acortando la url.";
					}else{
						$answer = $datos->data->link_save->link;
					}
				}
				if( strlen($answer) > strlen($args) ){
					$answer = "La tienes muy chiquita, no te la puedo recortar más.";
				}
			}catch(Exception $e){
				$answer = "Tuve un problema acortando la url proporcionada, intenta de nuevo por favor.";
			}
		}else{
			$answer = "Ingresa una URL.";
		}
		$this->reply($answer, $this->currentchannel, $this->nick);
	}
}
