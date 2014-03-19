<?php
abstract class command{
	public $name; //the name of the command
	public $output; //the ouptut;
	public $public = false;//is command available to everyone?
	public $channels = array();
	public $currentchannel;
	public $socket;
	public $needssocket = false;
	public $issuedbyadmin = false;
	public $nick;
	public $server;
	public $isquerymessage = false;
	public $usesSQL = false;
	public $tablenames = array();
	public $sql = array();
	public $usesconfig = false;

	public function __construct(){
		$this->name = '';
		$this->public = false;
		$this->usesSQL = false;
		$this->tablenames = array();
		$this->sql = '';
	}

	public function setNick($nick){
		$this->nick = $nick;
	}

	public function ispublic(){
		return $this->public;
	}

	public function setAdminFlag($flag){
		if ( $flag) {
			$this->issuedbyadmin = true;
		}
	}

	public function match($name){
		return ($this->name == $name);
	}

	public function getName(){
		return $this->name;
	}

	public function process($args = ''){
		//to be overriden by children classes
	}

	public function write(){
		//to be overriden by children classes
		$temp = preg_split("/\n/", $this->output, null, PREG_SPLIT_NO_EMPTY);

		foreach ( $temp as $linenumber => $line){
			$this->reply($line);
			$microseconds = $linenumber * 150000;
			usleep($microseconds);
		}

	}

	public function afterprocess($args = ''){
		//to be overriden by children classes
	}


	public function help(){
		//to be overriden by children classes
		//this is supposed to set the output that the bot
		//spits when it is asked help abot this command
	}


	public function getOutput(){
		return $this->output;
	}

	public function getServer(){
		if ( empty($this->server)){
			return '';
		} else {
			return $this->server;
		}
	}

	public function getChannels(){
		return $this->channels;
	}

	public function setChannels($channels){
		if ( is_array($channels)){
			$this->channels = $channels;
		} else {
			$this->addchannel($channels);
		}
	}

	public function addChannel($channel){
		$this->channels[] = strtolower(trim($channel));
	}

	public function setCurrentChannel($channel){
		$this->currentchannel = $channel;
	}

	public function sendraw($cmd){
		$cmd .= "\n\r";
		fwrite($this->socket, $cmd, strlen($cmd)); //sends the command to the server
		echo $cmd; //displays it on the screen
	}

	public function setSocket($socket){
		$this->socket = $socket;
	}

	public function reply($reply){
		$reply = rtrim($reply);
		$reply .= "\n";
		if ( $this->isquerymessage ){
			if ( ! fwrite($this->socket, "PRIVMSG " . $this->nick . " :" . $reply)){
				throw new Exception('Could not send ' . $reply);
			}
		} else {
			if ( ! fwrite($this->socket, "PRIVMSG " . $this->currentchannel . " :" . $reply)){
				throw new Exception('Could not send ' . $reply);
			}
		}
	}

	public function setQueryMessage($value){
		$this->isquerymessage = $value;
	}

	public function __toString(){
		$ret  = "\n";
		$ret .= '$this->name = ' . $this->name. "\n";
		$ret .= '$this->public = ' . $this->public . "\n";
		$ret .= '$this->currentchannel = ' . $this->currentchannel . "\n";
		$ret .= '$this->nick = ' . $this->nick . "\n";
		$ret .= '$this->needssocket = ' . $this->needssocket . "\n";
		$ret .= '$this->issuedbyadmin = ' . $this->issuedbyadmin . "\n";
		$ret .= '$this->output = ' . $this->output . "\n";
		if ( isset($this->helpArr)){
			$ret .= print_r($this->helpArr, 1);
		}
		$ret .= "\n";
		return $ret;
	}


	public function requiresSQL(){
		return $this->usesSQL;
	}

	public function getTableNames() {
		return $this->tablenames;
	}

	public function getSQL(){
		return $this->sql;
	}

	public function requiresConfig(){
		return $this->usesconfig;
	}

	public function getConfigFileName(){
		return $this->name . '-config.php';
	}
}
