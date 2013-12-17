<?php
class adminlogin extends command {

	public function __construct(){
		$this->name = 'adminlogin';
		$this->public = true;
	}

	public function help(){
		return "Uso: !adminlogin <bot password> .  Nota: no usar este comando en un canal, hacerlo por mensaje privado al bot.";
	}

	public function process($args){
		print 'processing adminlogin' . "\n";
		global $irc;
		if ( $args == $irc->botpassword ){
			$irc->addAdmin($this->nick);
			echo 'added admin' . $args;
		}
		$irc->showAdmins();
	}

}
