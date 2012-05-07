<?php
class chiste extends command {

	public function __construct(){
		$this->name = 'chiste';
		$this->public = true;
	}
	
	public function help(){
		return "Uso: !chiste. Lanza un chiste al azar";
	}
	
	public function process($args){
		$this->output = "";
		
		$chiste = file_get_contents("http://www.chistes.com/ChisteAlAzar.asp");
		$chiste = str_replace("\n", "", $chiste);
		$chiste = str_replace("\r", "", $chiste);

		
		try{
			preg_match('/<div class="chiste">(.*?)<\/div>/', $chiste, $matches);
			$elchiste = $matches[1];
			$templines = preg_split("/(\n|<BR>)/i", $elchiste, null, PREG_SPLIT_NO_EMPTY);
			print $elchiste . "\n";
			$lines = array();
			
		} catch ( Exception $e){
			print $e->getMessage();
			$this->reply("hummm... dejenme hacer memoria y acordarme de un chiste...", $this->channel);
			return;
		}
		
		while(count($templines)){			
			$line = array_shift($templines);
			$line = strip_tags($line);
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