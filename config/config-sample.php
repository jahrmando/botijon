<?php

//esta variable nos indica si el bot funciona en estado de desarrollo o en modo real
$dev = 1; //  0 or 1,  true or false

if ( $dev ){
	// valores de configuracion para desarrollo o pruebas
	$config = new stdClass();
	$config->botpassword = 'here your bot password';//make it difficult to guess
	$config->server = "irc.freenode.net";
	$config->ip = getIp();
	$config->port = 6667;
	$config->nick = 'botijon-testing';
	$config->password = '';
	$config->realname = "botijon-testing";
	$config->pidDir = $systemroot . "/pid/";
	$config->commandsDir= $systemroot . "/commands/";
	$config->debug = true; //print some extra information that cuould be useful for debugging purposes
	$config->commandchar = '!';
	$config->channels = array("#your.testing.channel.s");
	$config->maxMessages[3] = 4; //4 messages in 3 seconds
	$config->maxMessages[4] = 6; //6 messages in 4 seconds
	$config->maxMessages[5] = 9; //9 messages in 5 seconds

} else {
	// valores de configuracion para modo real o produccion
	$config = new stdClass();
	$config->botpassword = 'here your bot password'; //make it difficult to guess
	$config->server = "irc.freenode.net";
	$config->ip = getIp();
	$config->port = 6667;
	$config->nick = 'mastah';
	$config->password = '';
	$config->realname = "botijon";
	$config->commandsDir= $systemroot . "/commands/";
	$config->pidDir = $systemroot . "/pid/";
	$config->debug = false;//print some extra information that cuould be useful for debugging purposes
	$config->commandchar = '!';
	$config->channels = array("#yourchannel1", "#yourchannel2", "#yourchanneletc");
	$config->maxMessages[3] = 4; //4 messages in 3 seconds
	$config->maxMessages[4] = 6; //6 messages in 4 seconds
	$config->maxMessages[5] = 9; //9 messages in 5 seconds

}
