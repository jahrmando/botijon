<?php

//Si conoces la ip publica desde donde el bot va a correr
//anotala manualmente. Si no, intentar averiguarla mediante la funcion getip().
//Se recomienda lo primero.
//solo una de las dos siguientes lineas debe estar comentada

$ip = '216.239.130.22';
//$ip = getIp();



if ( $ip == '216.239.132.66' ){ //esta ip es la que finalmente el bot estara usando cuando corra (debemos conocerla)
	$config = new stdClass();
	$config->server = "irc.freenode.net";
	$config->ip = $ip;
	$config->port = 6667;
	$config->nick = 'mastah';
	$config->password = '';
	$config->realname = "botijon";
	$config->commandsDir= $systemroot . "/commands/";
	$config->pidDir = $systemroot . "/pid/";
	$config->debug = false;//print some extra information that cuould be useful for debugging purposes
	$config->commandchar = '!';
	$config->adminisnormaluser = false;
	$config->adminips= array();
	$config->adminips[] = '216.239.130.22';
	$config->adminips[] = '216.239.129.22';
	$config->adminips[] = '216.239.129.23';
	$config->adminips[] = '2607:fe90:2:0:223:54ff:fe27:42e0';
	$config->adminips[] = 'joel.omnis.com';
	$config->channels = array("#linux.mx");
	$config->maxMessages[3] = 4; //4 messages in 3 seconds
	$config->maxMessages[4] = 6; //6 messages in 4 seconds
	$config->maxMessages[5] = 9; //9 messages in 5 seconds

} else { //si el else ocurre, es que estamos corriendolo desde un ambiente de prueba.

	$config = new stdClass();
	$config->server = "irc.freenode.net";
	$config->ip = $ip;
	$config->port = 6667;
	$config->nick = 'mastah';
	$config->password = '';
	$config->realname = "botijon";
	$config->pidDir = $systemroot . "/pid/";
	$config->commandsDir= $systemroot . "/commands/";
	$config->debug = true; //print some extra information that cuould be useful for debugging purposes
	$config->commandchar = '!';
	$config->adminisnormaluser = true;
	$config->adminips= array();
	$config->adminips[] = '216.239.129.22';
	$config->adminips[] = '216.239.129.23';
	$config->adminips[] = '2607:fe90:2:0:223:54ff:fe27:42e0';
	$config->adminips[] = 'joel.omnis.com';
	$config->channels = array("#linux.mx.testing");
	$config->maxMessages[3] = 4; //4 messages in 3 seconds
	$config->maxMessages[4] = 6; //6 messages in 4 seconds
	$config->maxMessages[5] = 9; //9 messages in 5 seconds
}

