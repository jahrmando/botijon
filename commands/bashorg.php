<?php
class bashorg extends command {

	public function __construct(){
		$this->name = 'bashorg';
		$this->public = true;
		$this->server = 'irc.freenode.net';
	}
	
	public function help(){
		return "Uso: !bashorg - Lanza un chiste al azar de bashorg";
	}
	
	public function process($args){
		$this->output = "";
		
		$chiste = file_get_contents("http://bash.org/?random0");
		$chiste = str_replace("\n", "", $chiste);
		$chiste = str_replace("\r", "", $chiste);

		
		try{
			preg_match('/<p class="qt">(.*?)<\/p>/', $chiste, $matches);
			$elchiste = $matches[1];
			$templines = html_entity_decode(strip_tags($elchiste, '<br /><br>'));
			$templines = preg_split("/<br \/>/i", $templines, null, PREG_SPLIT_NO_EMPTY);
			//print_r($templines); echo "\n"; exit;
			$lines = array();
			
		} catch ( Exception $e){
			print $e->getMessage();
			$this->reply("hummm... dejenme hacer memoria y acordarme de un chiste...", $this->channel);
			return;
		}
		
		while(count($templines)){			
			$line = array_shift($templines);
			$line = trim($line);
			if ( strlen($line) < 500){
				$lines[] = $line;
			} else {
				while( strlen($line) >= 500){
					$lastcharfornewline = 500;
					for ( $i = 500; $i > 0; $i--){
						if ($line[$i] == ' ' ){
							$lastcharfornewline = $i;
							break;
						}
					}
					$lines[] = substr($line, 0, $lastcharfornewline);
					$line = substr($line, $lastcharfornewline);
				}
				$lines[] = $line;
			}
		}
		
		$this->output = join("\n", $lines);	
	
	}
}