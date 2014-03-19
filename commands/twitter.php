<?php
/*
 * Es necesario configurar las API keys en config/twitter-config.php
 *
 * NOTA: Este comando hace uso de la extension OAuth para PHP
 * Se recomienda su instalación por medio de "pecl oauth" y agregando a php.ini "extension=oauth.so"
 * (Probado en Debian, Fedora)
 *
 */

class twitter extends command {
	public function __construct()
	{
		$this->name = "twitter";
		$this->public = true;
		$this->usesconfig = true;
		global $config;
		if ( isset($config->twitter)){			
			$this->config =array(
				"consumer_key"		=>	$config->twitter->consumer_key,
				"consumer_secret"	=>	$config->twitter->consumer_secret,
				"access_token"		=>	$config->twitter->access_token,
				"access_secret"		=>	$config->twitter->access_token_secret
			);
		}
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
					$args = str_replace("@","",$args);
					$oauth->fetch("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name={$args}&count=1");
				}
				$twitter_data = json_decode($oauth->getLastResponse());
                if( $hashtag == false && count($twitter_data)>0){
                    $date = date('d/m/y H:i', strtotime($twitter_data[0]->created_at));
                    $this->output = "@{$args} : " . $twitter_data[0]->text . " -- {$date}";
                }elseif( $hashtag && isset($twitter_data->statuses) ){
                    $twits = array();
                    foreach( $twitter_data->statuses as $twit ){
                        array_push( $twits , $twit->text );
                    }
                    $this->output = "{$args}: " . join("\n", $twits);
                }else{
                    if( is_array($twitter_data) ){
                        $this->output = "Ups! el usuario @{$args} no existe.";
                    }else{
                        $this->output = "El usuario @{$args} tiene sus twitts como privados.";
                    }
				}
			}catch(Exception $e){
				$this->output = "No fue posible obtener twitts de {$args}. Ha ocurrido un error.";
			}
		}else{
			$this->output = "Ingresa un usuario.";
		}
	}
}
