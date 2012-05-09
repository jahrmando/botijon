<?php
class seen extends command {

	public function __construct(){
		$this->name = 'seen';
		$this->public = true;
	}

	public function help(){
		return "Uso: !seen <el nick buscado> . Informa de la última vez que fue visto el usuario indicado.";
	}	
	
	public function process($args){
		global $db;
		$args = trim ($args);
		if (! strlen($args)){
			$this->output = '';
			return 0;			
		}
		
		$nick = $args;
		
		$sql = "select 
					*,
					datediff(now(), messagetime) as daysdiff,
					timediff(now(), messagetime) as hourdiff
				from 
					chatlastseen 
				where 
					nick = :nick 
					and 
					channel = :channel";
		$r = $db->Parse($sql, 1);
		$r->Bind(":nick", $nick);
		$r->Bind(":channel", $this->currentchannel);
		$r->Execute();
		$row = $r->GetRow();

	
		if ( empty($row)){
			$this->output = "No se tienen registros del usuario {$nick}";
		} else {
			
			$timeellapsed = '';
			if ( $row->daysdiff == 0){	

				list($hours, $minutes, $seconds) = preg_split("/:/", $row->hourdiff, null, PREG_SPLIT_NO_EMPTY);
	
				if ( $hours){
					$timeellapsed .= $hours . ' horas ';
				}
				if ( $minutes){
					$timeellapsed .= $minutes . ' minutos ';
				}
				if ( $seconds ){
					$timeellapsed .= $seconds . ' segundos';
				}
				
				if ( ! strlen($timeellapsed)){
					$timeellapsed = " 1 segundo ";
				}
				
				$this->output = "El usuario {$nick} fue visto por última vez en {$this->currentchannel} hace {$timeellapsed} diciendo: \"{$row->message}\"";
			} else {
				$timeellapsed .= $row->daysdiff . ' día';
				if ($row->daysdiff > 1 ) {
					$timeellapsed .= 's';
				}				
				$this->output = "El usuario {$nick} fue visto por última vez en {$this->currentchannel} hace {$timeellapsed} diciendo: \"{$row->message}\"";

			} 					
		}
	}
}