<?php
class func extends command {

	public function __construct(){
		$this->name = 'func';
		$this->public = true;
		$this->channels = array('#php-es');
		$this->server = 'irc.freenode.net';
	}

	public function help(){
		return "Uso: !func <nombre de una funcion php>";
	}
	
	public function process($args){
		$this->output = "";
		$args = strtolower($args);
		
		if ( empty($args)){
			$this->output = $this->help();
			return;
		}
		
		global $db;
		
		$sql = "select * from functions where name = :functionname";
		$r = $db->Parse($sql, 1);
		$r->Bind(":functionname", $args);
		$r->Execute();
		$row = $r->GetRow();
		if ( empty($row)){
			$this->output = "Lo siento, no encontré la función <{$args}>";
		} else {
			$this->output = $row->name . ' ' . $row->version . "\n";
			$this->output .= $row->description . "\n";
			$this->output .= $row->signature;
		}

	}

}