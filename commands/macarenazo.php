<?php
class macarenazo extends command {

	public function __construct(){
		$this->name = 'macarenazo';
		$this->public = true;
	}

	public function help(){
		return 'Uso: !macarenazo';
	}
	
	public function process($args){
		$this->output = "
o      o     o    o     o    <o     <o>    o>    o
|.    \|.   \|/   //    X     \      |    <|    <|>
/\     >\   /<    >\   /<     >\    /<     >\   /<";
	}
	

}
