<?php
class unmd5 extends command {
  public function __construct(){
    $this->name = 'unmd5';
    $this->public = true;
    $this->server = 'irc.freenode.net';
  }

  public function help(){
    return 'Uso: !unmd5 <cadena>. Devuelve el valor del hash en md5.';
  }

  public function process($args){
    //store the md5 into a database;
    global $dbh;

    $sql = "select count(1) as count from hashes where md5 = :string";
    $r = $dbh->prepare($sql);
    $r->bindParam("string", $args);
    $r->Execute();
    $row = $r->fetch();
	if ( $row['count'] < 1 ){
        $msj = "No encontro el valor del hash." . __LINE__;
    }else{
      $sql = "select string from hashes where md5 = :string";
      $r = $dbh->prepare($sql);
      $r->bindParam("string", $args);
      $r->Execute();
      $row = $r->fetch();
      $msj = "El md5 '{$args}' corresponde a la cadena '{$row["string"]}'";
    }
    $this->output = $msj;
  }

}
