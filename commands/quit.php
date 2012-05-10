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
		$cmd = "PRIVMSG " . $this->currentchannel . " :Adios\n";
		fwrite($this->socket, $cmd, strlen($cmd)); //sends the command to the server
		echo $cmd; //displays it on the screen

		$cmd = "PART {$this->currentchannel}\n";
		fwrite($this->socket, $cmd, strlen($cmd)); //sends the command to the server
		echo $cmd; //displays it on the screen
	}

	public function afterprocess($args=''){
		global $pid;
		global $irc;
		if ( $this->issuedbyadmin){
			foreach ($irc->channels as $channel){
				$cmd = "PART {$channel}\n";
				fwrite($this->socket, $cmd, strlen($cmd)); //sends the command to the server
				echo $cmd; //displays it on the screen
			}
			unset($pid);
			die();
		}
	}
}
