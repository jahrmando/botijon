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
		
		try {
			
			$referer = 'http://www.google.com/q=chistes';
			$url="http://www.chistes.com/ChisteAlAzar.asp?n=3";
			$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
						
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			//curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_REFERER, $referer);
			curl_setopt($ch, CURLOPT_URL,$url);
			$chiste = curl_exec($ch);
			curl_close($ch);
			
			if (empty($chiste)){
				$this->reply('No pude obtener un chiste para contarles... Lo siento.');
				return;
			} 		
				
			$chiste = str_replace("\n", "", $chiste);
			$chiste = str_replace("\r", "", $chiste);

		} catch ( Exception $e){
			print $e->getMessage();
			$this->reply('No pude obtener un chiste para contarles... Lo siento.');
			return;
		}

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