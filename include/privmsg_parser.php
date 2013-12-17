<?php
class privmsg_parser{
	public $line;
	public $nick;
	public $user;
	public $host;
	public $mask;
	public $channel;
	public $targetuser;
	public $inchannel;
	public $message;

	public function __construct($line){
		$line = trim($line);
		$this->line = $line;
		//remove first colon ':'
		$line = substr($line, 1);
		$userinfo = substr($line, 0, strpos($line, 'PRIVMSG'));
		$this->mask = $userinfo;
		$this->nick = substr($userinfo, 0, strpos($userinfo, '!'));
		$userinfo = substr($userinfo, strlen($this->nick));
		$userinfo = ltrim($userinfo, '!');
		$this->user = substr($userinfo, 0, strpos($userinfo, '@'));
		$userinfo = substr($userinfo, strlen($this->user));
		$this->host = ltrim($userinfo, '@');
		$this->user = ltrim($this->user, '~');

		$line = substr($line, strlen($this->mask));
		$line = ltrim($line);
		$line = substr($line, strlen('PRIVMSG'));
		$line = ltrim($line);
		$channel = substr($line, 0, strpos($line, ' '));
		$line = substr($line, strlen($channel));
		if ( substr($channel,0, 1) == '#' ){
			print "a\n";
			$this->channel = $channel;
			$targetuser = '';
			$this->inchannel = true;
		} else {
			print "b\n";
			$this->channel = '';
			$this->targetuser = $channel;
			$this->inchannel = false;
		}
		$line = trim($line);
		//get rid of ':'
		$line = substr($line, 1);

		$this->message = $line;
	}

	public function getCleanMessage(){
		global $formattingchars;
		$message = $this->message;
		foreach ( $formattingchars as $char ){
			$message = str_replace($char, '', $message);
		}
		return $message;
	}

}
