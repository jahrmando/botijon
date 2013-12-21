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
	
	include_once("include/command.php");
	include_once("commands/seen.php");
	$seen = new seen();
	$seen->process('chapulin');
	print_r($seen);
	
} catch (Exception $e){
	print "Ocurrió un error durante la ejecucion: " . $e->getMessage();
}
echo "\n";
