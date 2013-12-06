<?php
class ensartada extends command {

	public function __construct(){
		$this->name = 'ensartada';
		$this->public = true;
		$this->channels = array("#linux.mx", "#linux.mx.testing");
		$this->server = 'irc.freenode.net';
		$this->labels = Array ( 'ensartadaid' => 'Ensartada #', 'ensartado' => 'Ensartado', 'enviadapor' => 'Enviada por', 'fecha' => 'Fecha', 'comentario' => 'Comentario');
	}

	public function help(){
		return "Uso: !ensartada. Lanza una ensartada al azar ó !ensartada #ensartada";
	}

	public function process($args=null){
		if ( is_numeric($args)){
			$num = intval($args);
			$num = abs($num);
		} else {
			$num = intval( file_get_contents('http://www.linux-mx.org/ensartadas/random/'));
		}

		$url = "http://www.linux-mx.org/ensartada/{$num}/irc/";

		$this->output = "";
		try{
			$ensartada = file_get_contents($url);
			$templines = preg_split("/\n/", $ensartada, null, PREG_SPLIT_NO_EMPTY);
		} catch ( Exception $e){
			print $e->getMessage();
			$this->reply("No se pudo obtener la ensartada.", $this->channel);
			return;
		}
		foreach ( $templines as $key => $value ) {
			$lines[] = html_entity_decode($value);
		}
		$this->output = join("\n", $lines);
	}

}
