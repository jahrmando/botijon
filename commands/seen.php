<?php
class seen extends command {

	public function __construct(){
		$this->name = 'seen';
		$this->public = true;
		$this->usesSQL = true;
		$this->tablenames = array('chatlastseen');
		$this->sql = array('create table chatlastseen(nick TEXT NOT NULL, channel TEXT NOT NULL, message TEXT NOT NULL,	messagetime timestamp NOT NULL default current_timestamp, primary key(nick, channel))');
	}

	public function help(){
		return "Uso: !seen <el nick buscado> . Informa de la última vez que fue visto el usuario indicado.";
	}

	public function process($args){
		global $dbh;
		if (! strlen($args)){
			$this->output = '';
			return 0;
		}

		$nick = $args;

		$sql = "select
					*,
					(strftime('%s',datetime('now')) - strftime('%s',messagetime)) as timediff
				from
					chatlastseen
				where
					nick = :nick
					and
					channel = :channel";
		$r = $dbh->prepare($sql);
		$r->bindParam("nick", $nick);
		$r->bindParam("channel", $this->currentchannel);
		$r->Execute();
		$row = $r->fetch();

		if ( empty($row)){
			$this->output = "No se tienen registros del usuario {$nick}";
		} else {

			$tiemporestante = $row['timediff'];

			$dias = intval(floor($row['timediff'] / 86400));
			//sustraer los segundos correspondientes a esos dias completos transcurridos
			$tiemporestante = $tiemporestante - ($dias * 86400);

			$horas = intval(floor($tiemporestante/ 3600));
			//sustraer los segundos correspondientes a las horas completas
			$tiemporestante = $tiemporestante - ($horas * 3600);

			$minutos = intval(floor($tiemporestante / 60));
			//sustraer los segundos correspondientes a los minutos completos restantes
			$tiemporestante = $tiemporestante - ($minutos * 60);
			
			$segundos = $tiemporestante;

			$timeellapsed = '';
			if ( $dias ) {
				$timeellapsed .= $dias . ' dias ';
			}
			if ( $horas){
				$timeellapsed .= $horas . ' horas ';
			}
			if ( $minutos){
				$timeellapsed .= $minutos . ' minutos ';
			}
			if ( $segundos ){
				$timeellapsed .= $segundos . ' segundos';
			}

			if ( ! strlen($timeellapsed)){
				$timeellapsed = " 1 segundo ";
			}

			$this->output = "El usuario {$nick} fue visto por última vez en {$this->currentchannel} hace {$timeellapsed} diciendo: \"{$row['message']}\"";

		}
	}
}
