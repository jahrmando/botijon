<?php
error_reporting(E_ALL);
include_once('../include/privmsg_parser.php');



$text = <<<EOT
:Abr1l!~AndChat51@unaffiliated/abr1l PRIVMSG #linux.mx :ACTION va a quitar el like
:necros!~user@unaffiliated/necros PRIVMSG #linux.mx :Abr1l: eres muy mal pensada
:necros!~user@unaffiliated/necros PRIVMSG #linux.mx :ACTION es un angel
:Montecristo!~Montecris@187.151.136.209 PRIVMSG #linux.mx :jajajajaja
:GNURocco!~alan@187.152.83.145 PRIVMSG #linux.mx :tengo conpas cholos
:lutze!~lutze@190-20-241-62.baf.movistar.cl PRIVMSG #php-es :cualquier ayuda se agradece
:quick!~quick3@148.208.218.24 PRIVMSG #php-es :saludos para todos
:zerver!~zerver@187.195.17.16 PRIVMSG #php-es :!helloworld
:lutze!c86f3d83@gateway/web/freenode/ip.200.111.61.131 PRIVMSG #php-es :o/
:Roa!~roa@ns4009357.ip-192-99-8.net PRIVMSG #linux.mx :xDDD
:adan!~adan@200.94.19.129 PRIVMSG #linux.mx :más el jotito pelon
:hbautista_!~hbautista@32.97.110.50 PRIVMSG #linux.mx :!chiste
:juchipilo!~juchipilo@unaffiliated/juchipilo PRIVMSG botijon :probando probando
EOT;



$lines = preg_split("/\n/", $text, null, PREG_SPLIT_NO_EMPTY);
foreach ( $lines as $line ){
	$parser = new privmsg_parser($line);
	print_r($parser);
}

