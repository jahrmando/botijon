<?php
class helloworld extends command {
	
	public function __construct(){
		$this->name = 'helloworld';
		$this->public = true;
	}
	
	public function process(){
		$this->output = 'helloworld' . "\n";
	}
	
}