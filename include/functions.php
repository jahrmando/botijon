<?php
function getIp(){
	$os = strtolower(PHP_OS);
	if ( preg_match ('/win/', $os)){
		$text = `ipconfig`;
		$lines = preg_split("/\n/", $text, null, PREG_SPLIT_NO_EMPTY);
		while ( count($lines)){
			$line = array_shift($lines);
			if ( ! preg_match("/\d+\.\d+\.\d+\.\d+/", $line)) continue;
			if ( ! preg_match("/IP/", $line)) continue;
			$line = trim($line);
			$line = substr($line, strpos($line, ':'));
			$line = trim($line);
			if ( preg_match("/\d+\.\d+\.\d+\.\d+/", $line)){
				return $line;
			} else{
				throw new Exception('No se pudo determinar la IP.' . __LINE__);
			}
		}
		throw new Exception('No se pudo determinar la IP.'  . __LINE__);
	} else {//linux
		$ip = `ifconfig | grep "inet addr"| cut -d: -f2| head -n1| cut -d" " -f1`;
		return trim($ip);
	}
}


function get_url( $url ) {
	$get = curl_init();
	curl_setopt( $get, CURLOPT_USERAGENT, "PHP Botijon" );
	curl_setopt( $get, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $get, CURLOPT_URL, $url );
	$out = curl_exec( $get );
	$resp = curl_getinfo( $get );
	curl_close ( $get );
	return $out;
}