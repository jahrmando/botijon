<?php
set_time_limit(0);
ini_set('display_errors', 'on');
try {
	$systemroot = dirname(__FILE__);
	if ( ! chdir($systemroot)){
		throw new Exception('No se pudo cambiar de directorio a ' . $systemroot);
	}
	if ( ! include_once ("init.php")){
		throw new Exception('No se pudo encontrar el archivo de inicialización.');
	}
	$irc = new ircbot();
	$irc->run();
	$dbh->close();
} catch (Exception $e){
	print "Ocurrió un error durante la ejecucion: " . $e->getMessage();
}
echo "\n";
