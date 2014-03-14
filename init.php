<?php
//initialize the help array
$helpArr = array();
$commands = array();


//include functions
if ( ! include_once("include/functions.php")){
	throw new Exception('No se pudo incluir el archivo de funciones');
}

//include the proces id manager
if ( ! include_once("include/pidmanager.php")){
	throw new Exception('No se pudo incluir el archivo manejador de procesos.');
}

//include configuration file
if ( ! include_once("config/dbconfig.php")){
	throw new Exception('No se pudo incluir el archivo de configuracion de la base de datos.');
}

//include configuration file
if ( ! include_once("config/config.php")){
	throw new Exception('No se pudo incluir el archivo de configuracion del bot.');
}

//verify the support for sqlite.
$extensions = get_loaded_extensions();

if ( ! in_array('sqlite3',$extensions)){
	throw new Exception('No se encontro soporte para Sqlite. Por favor instale SQLite3 y pruebe de nuevo.');
}

if ( ! in_array('PDO',$extensions)){
	throw new Exception('No se encontro la extension PDO de php. Por favor instalela y pruebe de nuevo.');
}

if ( ! in_array('pdo_sqlite',$extensions)){
	throw new Exception('No se encontro la extension pdo_sqlite. Por favor instalela pruebe de nuevo.');
}

//make sure sqlite db exists
if ( file_exists($dbconfig->db)){
	try {
		$foo = `touch {$dbconfig->db}`;
	} catch ( Exception $e ){
		throw new Exception($e->getMessage());
	}
}


try {
	$dbh = new PDO("sqlite:{$systemroot}/{$dbconfig->db}",  null, null, array(PDO::ATTR_PERSISTENT => true));
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch ( Exception $e ){
	throw new Exception($e->getMessage());
}

if ( ! is_object($dbh)){
	throw new Exception('No se pudo abrir correctamente la base de datos sqlite');
}

//try the $dbh object in a simple query to make sure everything is ok.
try {
	$sql = "select date('now')";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchall();
	//print $result[0][0];
} catch ( Exception $e){
	throw new Exception($e->getMessage());
}

//include twitter configuration file if it exists
if ( file_exists('config/twitter-config.php')){
	if ( ! include_once("config/twitter-config.php")){
		throw new Exception('No se pudo incluir el archivo de configuracion de twitter.');
	}
}

//include shortener configuration file if it exists
if ( file_exists('config/acortador-config.php')){
	if ( ! include_once("config/acortador-config.php")){
		throw new Exception('No se pudo incluir el archivo de configuracion del acortador de URLs.');
	}
}

//include youtube configuration file if it exists
if ( file_exists('config/youtube-config.php')){
	if ( ! include_once("config/youtube-config.php")){
		throw new Exception('No se pudo incluir el archivo de configuracion para busquedas en YouTube.');
	}
}

//include privmsg_parser class
if ( ! include_once("include/privmsg_parser.php")){
	throw  new Exception('No se pudo incluir la clase privmsg_parser.php');
}



//include figlet class
if ( ! include_once("include/figlet.php")){
	throw  new Exception('No se pudo incluir la clase figlet.php');
}


//include irc bot class
if ( ! include_once("include/ircbot.php")){
	throw  new Exception('No se pudo incluir la clase ircbot.php');
}

//include command class
if ( ! include_once("include/command.php")){
	throw new Exception('No se pudo incluir la clase command.php');
}

//instantiate the process id manager
$pid = new pidmanager();
if ( ! $pid instanceof pidmanager ){
	throw new Exception('No se pudo crear instancia del la clase pidmanager.');
}



//set the formatting characters
$formattingchars = array();
$formattingchars[] = chr(hexdec('1f'));//underline
$formattingchars[] = chr(hexdec('0f'));//underline
$formattingchars[] = chr(hexdec(2)); //bold
$formattingchars[] = chr(hexdec(16)); //white text on black background

$colorcode = chr(hexdec(3));//color character
for ( $i = 0; $i <= 15; $i++){
	$num = str_pad($i, 2, '0', STR_PAD_LEFT);
	$formattingchars[] = $colorcode . $num;
}
