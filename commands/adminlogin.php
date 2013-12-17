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
		global $irc;
		if ( $args == $irc->botpassword ){
			$irc->addAdmin($this->nick);
			$irc->reply('Autenticacion exitosa. Ahora eres admin.', $this->currentchannel, $this->nick);
		} else {
			$irc->reply('Lo siento... Contraseña inválida.', $this->currentchannel, $this->nick);
		}
	}

}
