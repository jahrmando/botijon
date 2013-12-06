<?php

/*
 * NOTA: Este comando requiere tener instalada la extension oauth para PHP
 * Esta puede ser instalada usando "pecl oauth" y agregando a su php.ini "extension=oauth.so" (Probado en debian)
 */

class twitter extends command {
	public function __construct()
	{
		$this->name = "twitter";
		$this->public = true;
		$this->config =array(
		"consumer_key"		=>	"your consumer key",
		"consumer_secret"	=>	"your consumer secret",
		"access_token"		=>	"your access token",
		"access_secret"		=>	"your access secret"
		);
	}

	public function help(){
		return "Uso: !twitter <username> . Devuelve la última entrada en la cuenta de twitter del usuario.";
    }

	public function process($args){
		$this->output = "";
		$args = trim($args);
		if( strlen($args)>0){
			try{
				$oauth = new OAuth( $this->config["consumer_key"], $this->config["consumer_secret"], OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
				$oauth->setToken($this->config["access_token"], $this->config["access_secret"]);
				$hashtag = false;
				if ( preg_match( "/^#/" , $args ) ){
					$hashtag = true;
					$oauth->fetch("https://api.twitter.com/1.1/search/tweets.json?q=".urlencode($args)."&count=3");
				}else{
					$oauth->fetch("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name={$args}&count=1");
				}
				$twitter_data = json_decode($oauth->getLastResponse());
				if( $hashtag == false && count($twitter_data)>0){
					$this->output = $twitter_data[0]->text;
				}elseif( $hashtag && isset($twitter_data->statuses) ){
					$tuits = array();
					foreach( $twitter_data->statuses as $tuit ){
						array_push( $tuits , $tuit->text );
					}
					$this->output = join("\n", $tuits);
				}else{
					if( is_array($twitter_data) ){
						$this->output = "Ups, el usuario no existe.";
					}else{
						$this->output = "Es un usuario bloqueado y no tengo acceso a sus tuits.";
					}
				}
			}catch(Exception $e){
				$this->output = "Es un usuario con candadito y no tengo acceso a sus tuits.";
			}
		}else{
			$this->output = "Ingresa un usuario.";
		}
	}
}
