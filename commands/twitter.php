<?php
class twitter extends command {
	public function __construct()
	{
		$this->name = "twitter";
		$this->public = true;
	}

	public function help(){
		return "Uso: !twitter <username> . Devuelve la última entrada en la cuenta de twitter del usuario.";
    }

	public function process($args){
		$this->output = "";
	    $args = trim($args);
	    $args = str_replace(" ", "+",$args);
		$feed = "http://search.twitter.com/search.atom?q=from:" . $args . "&rpp=1";
		try	{
			$rssReader = simplexml_load_file($feed);
			$this->output = strip_tags((string)$rssReader->entry->content);
			if(empty($this->output)){
				$this->output = "Esta persona no cuenta con twitter o esta protegido";
			}
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
}