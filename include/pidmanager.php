<?php
/**
 * PID File Manager will create and manage a pid file for command line scripts
 * to prevent multiple copies from running at the same time
 *
 */
class pidmanager {

	private $piddir;
	private $mypid;
	private	$runname;
	private $pidfile;
	private $dieonfatal;
	private $createdfile;

	/**
	 * Constructor.
	 *
	 * if you don't want your script to die on multiple instances pass false
	 * to the constructor
	 *
	 * @param bool $dieonfatal
	 * @return bool
	 */
	public function __construct($dieonfatal = true) {
		global $config;
		$this->piddir = $config->pidDir;
		$this->dieonfatal = $dieonfatal;
		$this->runname = str_replace(".php$","",basename($_SERVER["PHP_SELF"]));
		$this->mypid = getmypid();

		if ( !file_exists($this->piddir) || !is_dir($this->piddir) ) {
			$this->fatal("PID Dir does not exist (".$this->piddir.")",1);
			return false;
		}

		if ( ! is_writable($this->piddir)){
			$this->fatal("PID Dir is not writeable (".$this->piddir.")",1);
			return false;
		}

		$this->pidfile = $this->piddir . $this->runname . ".pid";

		if ( file_exists($this->pidfile) && is_file($this->pidfile) ) {
			$checkpid = trim(file_get_contents($this->pidfile));
			if ( $checkpid == $this->mypid ) {
				unlink($this->pidfile);
			} else {
				if ( file_exists("/proc/". $checkpid) ) {
					$this->fatal("Another copy of this script is already running.",0);
					return false;
				}
			}
		}

		clearstatcache();
		$this->createdfile = @file_put_contents($this->pidfile, $this->mypid, LOCK_EX) > 0 ? true : false;

		if ( !$this->createdfile ) {
			$this->fatal("Unable to create pid file.");
			return false;
		}

		return true;
	}

	/**
	 * Handle printing of errors and optionally exiting the script
	 *
	 * @param unknown_type $message
	 */
	protected function fatal ($message, $code = 0 ) {
		if ( $_ENV["SHLVL"] == 2 || $code > 0 ) {
			echo $message ."\n";
		}
		if ( $this->dieonfatal ) {
			exit(1);
		}
	}

	/**
	 * Destructor will cleanup pid files
	 *
	 */
	public function __destruct() {
		if ( $this->createdfile ) {
			unlink($this->pidfile);
		}
	}

}
