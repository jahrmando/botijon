<?php
define ("SELECT", 0);
define ("DML", 1);

class clsDb{
	protected $bindvars = array();
	protected $fatal = null;
	protected $demoronizer_replace;
	protected $demoronizer_replace_htmlent;
	protected $demoronizer_search;

	public function __construct () {
		global $dbconfig;
		$vars = get_object_vars($dbconfig);
		foreach ( $vars as $key => $value ){
			$this->{$key} = $value;
		}
		
		$this->fatal = NULL;
		$ret = 0;
		$this->autocommit = 1;
		$this->setDemoronizer();
		if ($this->host != "NOCONNECT") {
			$ret = $this->Connect ($this->host, $this->port, $this->db, $this->user, $this->password);
		}
		return $ret;
	}

	protected function Connect ($host = "", $port = "", $db = "", $user = "", $password = "") {
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
        $this->db = $db;
        $this->password = $password;

		$this->connection = @mysql_connect($this->host, $this->user, $this->password);
		
		if (! is_resource($this->connection) ){
        	throw new Exception("No se pudo establecer comunicación con la base de datos.");
        }
        if (! @mysql_select_db( $this->db )){
	        throw new Exception("No se pudo establecer comunicación con la base de datos.");
        }
      
		if (!$this->connection) {
			$ret = 1;
		}
		$ret = 0;
		return $ret;
	}

	public function getDbName(){
		return $this->db;
	}

	protected function Disconnect () {
		mysql_close ($this->connection);
	}

	public function Parse ($SQL, $newcontainer = 1) {
		if ($newcontainer) {
			return new clsdbcontainer ($this, $SQL);
		} else {
			preg_match_all ("/(:\w*)/", $SQL, $bindvars);
		}
		$this->bindvarSQL = $this->SQL = $SQL;

		if (preg_match ("/^\W*select/i", $SQL)) {
			$this->querytype = SELECT;
		} else {
			$this->querytype = DML;
		}
		foreach ($bindvars[0] as $bindvar) {
			$this->bindvars["BINDVAR___" . $bindvar . "___BINDVAR"] = 1;
		}
	}

	public function bbn ($name, &$var, $quotes = true) {
		return $this->BindByName ($name, $var, $quotes);
	}

	function BindByName ($name, &$var, $quotes = true) {
		$newname = "BINDVAR___" . $name . "___BINDVAR";
		if (array_key_exists ($newname, $this->bindvars)) {
			$this->bindvars[$name]["name"] = $newname;
			$this->bindvars[$name]["value"] =& $var;
			$this->bindvars[$name]["bound"] = 1;
			$this->bindvars[$name]["quotes"] = $quotes;
			$this->bindvarSQL = preg_replace ("/" . $name . "\b/", $newname, $this->bindvarSQL);
		} else {
			trigger_error ("BindByName: No such variable '$name'", E_USER_WARNING);
		}
	}
	
	
	function Bind ($name, &$var, $quotes = true) {
		$newname = "BINDVAR___" . $name . "___BINDVAR";
		if (array_key_exists ($newname, $this->bindvars)) {
			$this->bindvars[$name]["name"] = $newname;
			$this->bindvars[$name]["value"] =& $var;
			$this->bindvars[$name]["bound"] = 1;
			$this->bindvars[$name]["quotes"] = $quotes;
			$this->bindvarSQL = preg_replace ("/" . $name . "\b/", $newname, $this->bindvarSQL);
		} else {
			trigger_error ("BindByName: No such variable '$name'", E_USER_WARNING);
		}
	}	

	public function Execute ($SQL = "") {
		if (isset($this->fatal) && $this->fatal) {
			return 1;
		}
		if ($SQL) {
			$this->runSQL = $this->bindvarSQL = $this->SQL = $SQL;
		} else {
			$this->runSQL = $this->bindvarSQL;
		}
		if (!$this->runSQL) {
			trigger_error ("No SQL to execute", E_USER_WARNING);
			return -1;
		}
		$this->unboundvars = count ($this->bindvars);

		if (is_array ($this->bindvars)) {
			foreach ($this->bindvars as $bindvar) {
				//if (!$bindvar["bound"]) {
					//print "not bount for $this->runSQL <BR>";
				//}
				
				if ($bindvar["value"] === NULL) {
					if ($this->querytype == SELECT) {
						$this->runSQL = str_replace ($bindvar["name"],  "''", $this->runSQL);
					} else {
						$this->runSQL = str_replace ($bindvar["name"], "NULL", $this->runSQL);
					}
				} elseif ( ! strlen( $bindvar['value']) ) {					
					if ($this->querytype == SELECT) {
						$this->runSQL = str_replace ($bindvar["name"],  "''", $this->runSQL);
					} else {
						$this->runSQL = str_replace ($bindvar["name"], "NULL", $this->runSQL);
					}					
				} else {
					# Demoronize the variable
					if ( is_array($this->demoronizer_search)){
						$bindvar['value'] = str_replace ($this->demoronizer_search, $this->demoronizer_replace_htmlent, $bindvar['value']);
					}
										
					if ($bindvar["quotes"]) {
						$this->runSQL = str_replace ($bindvar["name"], "'" . mysql_real_escape_string ($bindvar["value"]) . "'" , $this->runSQL);
					} else {
						$this->runSQL = str_replace ($bindvar["name"], $bindvar["value"], $this->runSQL);
					}
				}
				$this->unboundvars--;
			}
		}
		
		if ($this->unboundvars != 0) {
			trigger_error ("Not all variables bound", E_USER_WARNING);
			return 1;
		}

		$this->hasrows = 0;
		$this->handler = mysql_query($this->runSQL, $this->connection);
		if ($this->handler) {
			if ( $this->querytype == SELECT){
				$this->hasrows = mysql_num_rows($this->handler);
			} else{
				$this->hasrows = mysql_affected_rows();
			}
			/*
			$handler = mysql_query ("explain " . $this->runSQL,  $this->connection);
			$explain = '';
			while ($row = mysql_fetch_object ($handler)) {
				$explain .= print_r($row, 1) . "\n";
			}*/
		} else {
			$this->error = mysql_error();
			trigger_error ("Execute Error: " . $this->error, E_USER_WARNING);
			return 1;
		}
	}

	public function ExecuteAndReturn ($SQL = "") {
		if ($SQL) $this->runSQL = $this->bindvarSQL = $this->SQL = $SQL;
		$this->Execute ();
		if ($this->hasrows) {
			$ret = mysql_fetch_row ($this->handler);
		}
		return $ret;
	}

	public function GetRow () {
		$ret = null;
		if ($this->hasrows) {
			$ret = mysql_fetch_object ($this->handler);
		}
		return $ret;
	}

	public function RowCount () {
		if ($this->fatal) {
			return 0;
		}
		$ret = mysql_affected_rows ();
		if( $ret == -1 ){ //query failed
			return false;
		} elseif ($ret == 0){ //no rows affected
			return false;
		} elseif ( is_integer($ret) && ($ret >= 1)){//query ok
			return true;
		} else {// never should happen
			return false;
		}
	}

	public function getSql(){
		return $this->runSQL;
	}

	public function getInsertId(){
		return mysql_insert_id($this->connection);
	}
	
	
	public function setDemoronizer(){
	
		# Define the demoronizer search/replace arrays

		/*
		This is the UTF-8 version: http://search.cpan.org/src/JOSEWEEKS/E2-Interface-0.33/Interface.pm

		$s =~ s/\xC2\x82/\xE2\x80\x98/sg; # &sbquo;
		$s =~ s/\xC2\x83/\xC6\x92/sg;     # &fnof;
		$s =~ s/\xC2\x84/\xE2\x80\x9E/sg; # &bdquo;
		$s =~ s/\xC2\x85/\xE2\x80\xA6/sg; # &hellip;
		$s =~ s/\xC2\x86/\xE2\x80\xA0/sg; # &dagger;
		$s =~ s/\xC2\x87/\xE2\x80\xA1/sg; # &Dagger;
		$s =~ s/\xC2\x88/\xCB\x86/sg;     # &circ;
		$s =~ s/\xC2\x89/\xE2\x80\xB0/sg; # &permil;
		$s =~ s/\xC2\x8A/\xC5\xA0/sg;     # &Scaron;
		$s =~ s/\xC2\x8B/\xE2\x80\xB9/sg; # &lsaquo;
		$s =~ s/\xC2\x8C/\xC5\x92/sg;     # &OElig;
		$s =~ s/\xC2\x91/\xE2\x80\x98/sg; # &lsquo;
		$s =~ s/\xC2\x92/\xE2\x80\x99/sg; # &rsquo;
		$s =~ s/\xC2\x93/\xE2\x80\x9C/sg; # &ldquo;
		$s =~ s/\xC2\x94/\xE2\x80\x9D/sg; # &rdquo;
		$s =~ s/\xC2\x95/\xE2\x80\xA2/sg; # &bull;
		$s =~ s/\xC2\x96/\xE2\x80\x93/sg; # &ndash;
		$s =~ s/\xC2\x97/\xE2\x80\x94/sg; # &mdash;
		$s =~ s/\xC2\x98/\xDC\xB2/sg;     # &tilde;
		$s =~ s/\xC2\x99/\xE2\x84\xA2/sg; # &trade;
		$s =~ s/\xC2\x9A/\xC5\xA1/sg;     # &scaron;
		$s =~ s/\xC2\x9B/\xE2\x80\xBA/sg; # &rsaquo;
		$s =~ s/\xC2\x9C/\xC5\x93/sg;     # &oelig;

		*/

		$this->demoronizer_search = array (
			chr (hexdec('82')),
			chr (hexdec('83')),
			chr (hexdec('84')),
			chr (hexdec('85')),
			chr (hexdec('86')),
			chr (hexdec('87')),
			chr (hexdec('88')),
			chr (hexdec('89')),
			chr (hexdec('8A')),
			chr (hexdec('8B')),
			chr (hexdec('8C')),
			chr (hexdec('91')),
			chr (hexdec('92')),
			chr (hexdec('93')),
			chr (hexdec('94')),
			chr (hexdec('95')),
			chr (hexdec('96')),
			chr (hexdec('97')),
			chr (hexdec('98')),
			chr (hexdec('99')),
			chr (hexdec('9A')),
			chr (hexdec('9B')),
			chr (hexdec('9C')),
		);

		$this->demoronizer_replace = array (
			',',
			'<em>f</em>',
			',,',
			'...',
			'',
			'',
			'^',
			' ?/??',
			'',
			'<',
			'Oe',
			'`',
			"'",
			'"',
			'"',
			'*',
			'-',
			'--',
			'<sup>~</sup>',
			'<sup>TM</sup>',
			'',
			'>',
			'oe',
		);

		$this->demoronizer_replace_htmlent = array (
			'&sbquo;',
			'&fnof;',
			'&bdquo;',
			'&hellip;',
			'&dagger;',
			'&Dagger;',
			'&circ;',
			'&permil;',
			'&Scaron;',
			'&lsaquo;',
			'&OElig;',
			'&lsquo;',
			'&rsquo;',
			'&ldquo;',
			'&rdquo;',
			'&bull;',
			'&ndash;',
			'&mdash;',
			'&tilde;',
			'&trade;',
			'&scaron;',
			'&rsaquo;',
			'&oelig;'
		);
	
	}	

}

class clsdbcontainer extends clsdb {
	public function __construct(&$db, $SQL) {
		$this->connection =& $db->connection;
		$this->autocommit =& $db->autocommit;
		$this->Parse ($SQL, 0);
	}
}
