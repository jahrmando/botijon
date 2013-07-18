<?php
class ensartada extends command {

	public function __construct(){
		$this->name = 'ensartada';
		$this->public = true;
		$this->channels = array("#linux.mx");
		$this->server = 'irc.freenode.net';
		$this->labels = Array ( 'ensartadaid' => 'Ensartada #', 'ensartado' => 'Ensartado', 'enviadapor' => 'Enviada por', 'fecha' => 'Fecha', 'comentario' => 'Comentario');
	}

	public function help(){
		return "Uso: !ensartada. Lanza una ensartada al azar ó !ensartada #ensartada";
	}

	public function process($args=null){
		$num = (int) $args;
		if ( $num > 0 ) {
			$url = "http://linux-mx.org/ensartada/json/$num";
		} else {
			$num = (int) 0;
			$num = rand($num, 500);
			$url = "http://linux-mx.org/ensartada/json/$num";	
		}
		$this->output = "";
		try{
			$ensartada = file_get_contents($url);
			$temp = json_decode($ensartada);
			$lines = array();
		} catch ( Exception $e){
			print $e->getMessage();
			$this->reply("No se pudo obtener la ensartada.", $this->channel);
			return;
		}
		foreach ( $temp as $var => $val ) {
			if ( ! is_array($val) ) {
				$var = html_entity_decode($var);
				$lines[] = $this->labels[$var] . ": $val";
			} else {
				foreach ( $val as $val2 ) {
					$lines[] = html_entity_decode($val2);
				}
			}
		}
		$this->output = join("\n", $lines);	
	}
}