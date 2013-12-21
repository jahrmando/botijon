<?php
class md5 extends command {

	public function __construct(){
		$this->name = 'md5';
		$this->public = true;
		$this->server = 'irc.freenode.net';
		$this->usesSQL = true;
		$this->tablenames = array('hashes');
		$this->sql = array('create table hashes(string TEXT NOT NULL,md5 TEXT NOT NULL,sha1 TEXT NOT NULL, primary key(string))');
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
		global $dbh;

		$sql = "select count(1) as count from hashes where string = :string";
		$r = $dbh->prepare($sql);
		$r->bindParam("string", $args, PDO_PARAM_STR);
		$r->Execute();
		$row = $r->fetch();
		if ( $row->count == 0 ){
			$md5 = md5($args);
			$sha1 = sha1($args);
			$sql = "insert into hashes ( string , md5, sha1) values (:string, :md5, :sha1)";
			$r = $dbh->prepare($sql);
			$r->bindParam("string", $args);
			$r->bindParam("md5", $md5);
			$r->bindParam("sha1", $sha1);
			$r->Execute();
		}
	}
}
