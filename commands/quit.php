<?php
class quit extends command {

	public function __construct(){
		$this->name = 'quit';
		$this->public = false;
	}

	public function help(){
		return "Uso: !quit ";
	}
	
	public function process($args){
		$cmd = "PRIVMSG " . $this->channel . " :Adios\n";
		fwrite($this->socket, $cmd, strlen($cmd)); //sends the command to the server
		echo $cmd; //displays it on the screen

		$cmd = "PART {$this->channel}\n";
		fwrite($this->socket, $cmd, strlen($cmd)); //sends the command to the server
		echo $cmd; //displays it on the screen
	}
	
	public function afterprocess($args=''){
		global $pid;
		if ( $this->issuedbyadmin){
			unset($pid);			
			die();
		}		
	}
}