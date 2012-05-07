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
    global $db;
    
    #set to error message
    $msj = "No encontro el valor del hash.";
    $sql = "select count(1) as count from hashes where md5 = :string";
    $r = $db->Parse($sql, 1);
    $r->Bind(":string", $args);
    $r->Execute();
    $row = $r->GetRow();
    if ( $row->count == 0 ){
      $ch = curl_init('http://www.md5decrypter.com/'); 
      curl_setopt ($ch, CURLOPT_POST, 1);     
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt ($ch, CURLOPT_POSTFIELDS, "hash={$args}"); 
      $data =curl_exec ($ch);
      curl_close ($ch);
      $exp = explode("{$args}<br/><b class='red'>Normal Text: </b>",$data);
      unset($data);
      if(count($exp)>1){
        $exp = explode('<br/><br/>',$exp[1]);
        $msj = " el valor del hash md5 {$args} es ".$exp[0];
        unset($exp);
      }
    }else{
      $msj = "No encontro el valor del hash.";
      $sql = "select string from hashes where md5 = :string";
      $r = $db->Parse($sql, 1);
      $r->Bind(":string", $args);
      $r->Execute();
      $row = $r->GetRow();
      $msj = " el valor del hash md5 {$args} es ".$row->string;
    }
    $this->output = $msj;
  }
  
}