<?php
class help extends command {

	public function __construct(){
		$this->name = 'help';
		$this->public = true;
	}

	public function help(){
		return "Uso: help <comando> . Devuelve información acerca de algunos comandos del bot.";
	}
	

	public function process($args = ''){	
		global $helpArr;			
		global $commands;
		$this->output = "";
		$args = trim($args);
		if ( empty( $args)){
			$this->output = "Comandos disponibles: ";
			$commandarr = array();
			
			foreach ( $commands as $commandname => $command){
				$channelok = false;
				$userok = false;
				$channels = $command->getChannels();
				if ( is_array($channels)){
					if ( count($channels)){
						if ( in_array($this->currentchannel, $channels)){
							$channelok = true;
						}						
					} else {
						$channelok = true;
					}
				} else {
					$channelok = true;	
				}
				
				if ( $command->isPublic()){
					$userok = true;
				} else {
					if ( $this->issuedbyadmin ){
						$userok = true;
					}
				}
				if ( $channelok && $userok){
					$commandarr[] = $commandname;
				}
		
			}
			$this->output .= join(", ", $commandarr);		
			$this->output .= ". Para pedir ayuda sobre un comando en particular, escriba !help y el nombre del comando.";
		} else {
			$args = trim(strtolower($args));
			$argsarr = preg_split("/ /", $args, null, PREG_SPLIT_NO_EMPTY);
			$commandname = array_shift($argsarr);	
			if ( in_array($commandname, array_keys($helpArr))){
				$command = new $commandname();
				$this->output = $command->help();
			} else {
				$this->output = "El comando < {$commandname} > no es válido.";
			}
		}
	}
}