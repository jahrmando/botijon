<?php
class ircbot{

	public $server;
	public $port;
	public $nick;
	public $password;
	public $channels;
	public $socket;
	public $host;
	public $commandsDir;
	public $commands = array();
	public $lastDbReload;
	public $lastphrasetime = 0;
	public $lasttimeusersspoke = 0;
	public $admins = array();
	public $loadedconfig = false;
	public $connectedtoserver = false;
	public $gotHost = false;
	public $identified = false;
	public $joinedchannels = 0;
	public $commandqueue =array();


	public function __construct(){
		//load configuration
		global $config;

		$vars = get_object_vars($config);
		foreach ( $vars as $key => $value ){
			$this->{$key} = $value;
		}
		if ( count($vars)){
			$this->loadedconfig = 1;
		} else {
			throw new Exception('No se pudo cargar la información de la configuración');
		}
	}

	public function run(){
		$this->loadcommands();
		$this->connect();
		$this->getHost();
		$this->identify();
		$this->joinchannels();
		$this->runloop();
	}

	public function loadcommands(){
		global $helpArr;
		global $commands;
		//include all commands
		if (is_dir($this->commandsDir)) {
			if ($dh = opendir($this->commandsDir)) {
				while (($file = readdir($dh)) !== false) {
					if ( $file == '.' || $file == '..') continue;
					if ( $file == '.svn') continue;
					if ( ! preg_match("/\.php$/i", $file)) continue;

					include_once ($this->commandsDir . '/' . $file);
					$commandname = preg_replace("/\.php$/", "", $file);
					$thecommand = new $commandname();
					$commandserver = $thecommand->getServer();
					//if no	command server specified, then the command should be available everywhere
					if ( empty($commandserver)){
						$commands[$commandname] = $thecommand;
					} else {
						//command is only for a specific server or irc network
						if ( $this->server == $commandserver ){
							$commands[$commandname] = $thecommand;
						}
					}
				}
				closedir($dh);
			}

			foreach ( $commands as $commandname => $command){
				$channels = $command->getChannels();
				$helptext = $command->help();

				if ( is_array($channels)){
					if ( count($channels)){
						foreach ( $channels as $channelname){
							$helpArr[$commandname][$channelname] = $helptext;
						}
					} else {
						$helpArr[$commandname]['all'] = $helptext;
					}
				}
			}
		} else {
			throw new Exception('El directorio de comandos no existe.');
			throw new Exception('El directorio de comandos no existe.');
		}

		if ( ! count($commands)){
			throw new Exception('La lista de comandos esta vacía.');
		}
	}

	public function connect(){
		while ( ! is_resource($this->socket)){
			print "Intentando obtener socket...\n";
			//Open the socket connection to the IRC server
			$this->socket = fsockopen($this->server, $this->port, $errno, $errstr, 2);
		}
		if ( is_resource($this->socket)){
			//Ok, we have connected to the server, now we have to send the login commands.
			$this->sendraw("PASS "); //Sends the password not needed for most servers
			$this->sendraw("NICK $this->nick"); //sends the nickname
			//Parameters: <username> <hostname> <servername> <realname>
			$this->sendraw("USER {$this->nick} {$this->ip} {$this->server} :{$this->realname}");
			$this->connectedtoserver = true;
		} else {
			$this->connectedtoserver = false;
			throw new Exception('No se pudo conectar al server host. Error: ' . $errstr);
		}
	}

	protected function getHost(){
		$attempts = 0;
		while(! feof($this->socket)){
			//get a line of data from the server
			$currentline = fgets($this->socket, 1024);
			$currentline = trim($currentline);

			//search for a line like this:
			//NOTICE botijon :*** Your host is calvino.freenode.net[calvino.freenode.net/6667], running version hyperion-1.0.2b
			//:irc.juchipila.com 004 botijon irc.juchipila.com ngircd-0.12.1 aios biIklmnoPstv

			preg_match("/^.*your host is ([a-zA-Z\._-]+).*$/i", $currentline, $matches);

			//display the recived data from the server
			echo $currentline. "\n";

			if( ! empty($matches[1])){
				$this->gotHost = true;
				$this->host = $matches[1];
				break;
			}
			flush();
			$attempts++;
			if ( $attempts > 50 ){
				throw new Exception('No se pudo detectar el host al cual se realizo la conección');
			}
		}
	}

	protected function identify(){

		//wait for the MOTD line to send join command
		//:irc.juchipila.com 376 boti :End of MOTD command

		while(! feof($this->socket) ){

			//get a line of data from the server
			$currentline = fgets($this->socket, 1024);
			$currentline = trim($currentline);

			//display the recived data from the server
			echo $currentline. "\n";

			if( preg_match("/376/", $currentline) && preg_match("/MOTD/i", $currentline) ){
				break;
			}

			flush();
		}

		//Join the chanel
		$this->sendraw("msg NickServ identify " . $this->password);
		$this->identified = true;
	}

	protected function joinchannels(){
		//Join the chanel
		foreach ( $this->channels as $channel){
			$this->sendraw("JOIN $channel");
			$this->joinedchannels++;
		}
	}

	public function runloop(){
		global $db;
		$lastdbrefresh = time();
		while(! feof($this->socket)){
			//get a line of data from the server
			$currentline = trim(fgets($this->socket, 1024));

			if ( (empty($currentline)) || (! strlen($currentline))){
				continue;
			}

			//display the recived data from the server
			echo $currentline. "\n";


			//determine the type of line in order to process it the right way
			$isservermessage   = false;
			$isusermessage     = false;
			$isjoinmessage     = false;
			$ispartmessage     = false;
			$ispingmessage     = false;
			$isquitmessage     = false;
			$iskickmessage     = false;
			$ismodemessage     = false;
			$ishelpmessage     = false;
			$isnickmessage     = false;
			$isnoticemessage   = false;
			$isservicesmessage = false;
			$istopicmessage    = false;
			$servermessageflag = ":" . $this->host;
			$templine = $currentline;

			//determine the type of line by analizing the presence of the strings PRIVMSG, JOIN or PART etc
			$finished = false;
			while(! $finished){
				if ( substr($templine,0, strlen($servermessageflag)) == $servermessageflag) { $finished = true; $isservermessage = true; break;}
				if ( substr($templine,0, strlen('PING :'))    == 'PING :')    { $finished = true; $ispingmessage = true; break;}
				if ( substr($templine,0, strlen(' PRIVMSG ')) == ' PRIVMSG ') { $finished = true; $isusermessage = true; break;}
				if ( substr($templine,0, strlen(' JOIN '))    == ' JOIN ')    { $finished = true; $isjoinmessage = true; break;}
				if ( substr($templine,0, strlen(' PART '))    == ' PART ')    { $finished = true; $ispartmessage = true; break;}
				if ( substr($templine,0, strlen(' QUIT '))    == ' QUIT ')    { $finished = true; $isquitmessage = true; break;}
				if ( substr($templine,0, strlen(' KICK '))    == ' KICK ')    { $finished = true; $iskickmessage = true; break;}
				if ( substr($templine,0, strlen(' MODE '))    == ' MODE ')    { $finished = true; $ismodemessage = true; break;}
				if ( substr($templine,0, strlen(' NICK '))    == ' NICK ')    { $finished = true; $isnickmessage = true; break;}
				if ( substr($templine,0, strlen(' NOTICE '))  == ' NOTICE ')  { $finished = true; $isnoticemessage = true; break;}
				if ( substr($templine,0, strlen(' TOPIC '))   == ' TOPIC ')   { $finished = true; $istopicmessage  = true; break;}
				if ( substr($templine,0, strlen(':services')) == ':services') { $finished = true; $isservicesmessage = true; break;}
				if ( empty($templine)) {$finished = true; break;}
				$templine = substr($templine, 1);
			}

			//process line according to its type
			if ( $ispingmessage ){
				//respond ping
				$this->processping($currentline);
			} elseif ($isservermessage){
				//a message from the server
				print "server message " . $currentline . "\n";
			} elseif ($isusermessage){
				//process the line
				$this->processUserMessage($currentline);
			} elseif ( $isjoinmessage){
				print "join message " . $currentline . "\n";
			} elseif ( $ispartmessage ){
				print "part message " . $currentline . "\n";
			} elseif ( $isquitmessage){
				print "quit message " . $currentline . "\n";
			} elseif ( $ismodemessage ){
				print "mode message " . $currentline . "\n";
			} elseif ( $isnickmessage){
				print "nick mesage " . $currentline . "\n";
			} elseif ( $iskickmessage ){
				print "kick message "  . $currentline . "\n";
			} elseif ( $isnoticemessage ){
				print "notice message " . $currentline . "\n";
			} elseif ( $isservicesmessage ){
				print ":services message " . $currentline . "\n";
			} elseif ( $istopicmessage ){
				print ":topic message " . $currentline . "\n";
			} else {
				throw new Exception('Unknown type of line: ' . $currentline);
			}

			flush(); //This flushes the output buffer forcing the text in the while loop to be displayed "On demand"

			$currenttime = time();
			if ( $currenttime - $lastdbrefresh > 60*5){
				$db = new clsDb();
				if ( ! $db instanceof clsDb ){
					throw new Exception('No se pudo instanciar la clase manejadora de la base de datos');
				}
			}
		}
	}

	public function reply($reply, $channel, $nick){
		$reply = rtrim($reply);
		$reply .= "\n";
		$channel = trim($channel);
		$nick = trim($nick);
		if ( ! fwrite($this->socket, "PRIVMSG " . $channel . $nick . " :" . $reply)){
			throw new Exception('Could not send ' . $reply);
		}

	}


	public function sendraw($cmd){
		$cmd = rtrim($cmd);
		$cmd .= "\n";
		fwrite($this->socket, $cmd, strlen($cmd)); //sends the command to the server
		echo $cmd; //displays it on the screen
	}

	public function processping($currentline){
		//Reply with pong
		$this->sendraw("PONG :" . substr($currentline, 6));
	}

	public function  processUserMessage($line){
		global $commands;
		global $helpArr;
		global $db;
		global $config;

		$parser = new privmsg_parser($line);
		$line = $parser->getCleanMessage();
		$userisadmin = false;
		if ( in_array($parser->nick, $this->admins)){
			$userisadmin = true;
		}
		//first character
		$firstchar = substr($line, 0,1);
		if ( $firstchar ==  $config->commandchar ){
			$firstword = substr($line, 0, (strpos($line, " ") ? strpos($line, " ") : strlen($line) ) );
			$commandname = substr($firstword, 1);
			$commandname = strtolower(trim($commandname));

			if ( in_array($commandname, array_keys($commands))){
				$arguments = substr($line, strlen($firstword));
				$arguments = trim($arguments);
				$command = new $commandname($arguments);
				$command->setSocket($this->socket);
				$command->setCurrentChannel($parser->channel);
				$command->setAdminFlag($userisadmin);
				$command->setNick($parser->nick);

				$runcommand = false;
				if ( $command->ispublic()){
					$channels = $command->getChannels();
					if ( is_array($channels)){
						if ( count($channels)){
							if ( in_array($parser->channel, $channels)){
								$runcommand = true;
							}
						} else {
							$runcommand = true;
						}
					} else {
						$runcommand = true;
					}
				} elseif ( $userisadmin ){
					$runcommand = true;
				} else {
					$this->reply("Lo siento, pero solo obedezco a mi amo.", $parser->channel, $parser->nick);
				}

				if ( $runcommand ){
					$command->process($arguments);
					$command->write();
					$command->afterprocess();
				}
			}
		}

	}

	public function addAdmin($admin){
		$this->admins[] = $admin;
	}

	public function showAdmins(){
		if (empty($this->admins)){
			print 'no hay admins';
		} else {
			foreach ($this->admins as $key => $admin ){
				print 'admin[' . $key . '] = '.  $admin . "\n";
			}
		}
	}

	public function __toString(){
		global $commands;
		global $commandsDir;
		$ret  = '';
		$ret .= '$commandsDir = ' . $commandsDir . "\n";
		$ret .= '$this->loadedconfig = ' . $this->loadedconfig . "\n";
		$ret .= '$this->connectedtoserver = ' . $this->connectedtoserver . "\n";
		$ret .= '$this->gotHost = ' . $this->gotHost . "\n";
		$ret .= '$this->identified = ' . $this->identified . "\n";
		$ret .= '$this->joinedchannels = ' . $this->joinedchannels . "\n";
		$ret .= '$this->host = ' . $this->host . "\n";
		$ret .= '$this->nick = ' . $this->nick . "\n";
		$ret .= '$this->ip = ' . $this->ip . "\n";
		$ret .= '$this->server = ' . $this->server . "\n";
		$ret .= '$this->realname = ' . $this->realname . "\n";
		$ret .= '$this->channels = ' . join(", ", $this->channels) ."\n";
		$ret .= '$commands = ' . join (", ", $commands) . "\n";
		return $ret;
	}
}
