<?php
class hint extends command {

	public function __construct(){
		$this->name = 'hint';
		$this->public = true;
		$this->channels = array('#php-es');
		$this->server = 'irc.freenode.net';
	}

	public function help(){
		return "Uso: !hint <cadena a buscar en los nombres de funciones>";
	}	
	
	public function process($args){
		$this->output = "";
		$args = strtolower(trim($args));
		
		if ( empty($args)){
			$this->output = $this->help();
			return 0;
		}
		
		if ( strlen($args) < 3){
			$this->output = "Por favor dame por lo menos 3 caracteres del nombre de la función.";
			return 0;
		}
		
		global $db;
		
		$fixedargs = mysql_real_escape_string ($args);
		$fixedargs = '%' . $fixedargs . '%';
		
		$sql = "select name from functions where name like :string  order by name ASC";
		$r = $db->Parse($sql, 1);
		$r->Bind(":string", $fixedargs);
		$r->Execute();
		//print "\n". $r->getSql() . "\n";
		$found = array();
		while ( $row = $r->GetRow() ){
			$found[] = $row->name;
		}
		
		if ( count($found) == 0){
			$this->output = "No encontré ninguna funcion que contenga la cadena <{$args}>";
		} elseif ( count($found) > 20) {
			$this->output = "Primeras 20 funciones: ";
			$this->output .= join(", ", array_slice($found, 0, 20));
			$this->output .= "\nPara resultados más específicos provea más caracteres.";
		} else {
			$this->output = "Encontré las siguientes funciones: " . join(", ", $found);
		}

	}

}