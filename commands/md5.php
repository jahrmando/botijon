<?php
class md5 extends command {

	public function __construct(){
		$this->name = 'md5';
		$this->public = true;
		$this->server = 'irc.freenode.net';
	}

	public function help(){
		return 'Uso: !md5 <cadena>. Devuelve el md5 hash de la cadena.';
	}
	
	public function process($args){
		$this->output = md5($args);
		$this->save($args);
	}
	
	protected function save($args){		
		//store the md5 into a database;
		global $db;
		$sql = "select count(1) as count from hashes where string = :string";
		$r = $db->Parse($sql, 1);
		$r->Bind(":string", $args);
		$r->Execute();
		$row = $r->GetRow();
		if ( $row->count == 0 ){
			$md5 = md5($args);
			$sha1 = sha1($args);
			$sql = "insert into hashes ( string , md5, sha1) values (:string, :md5, :sha1)";
			$r = $db->Parse($sql, 1);
			$r->Bind(":string", $args);
			$r->Bind(":md5", $md5);
			$r->Bind(":sha1", $sha1);
			$r->Execute();
		}		
	}
}