<?php
/** \file ErrorLogging.php
 * Implements an error logger.
 * Creates a log file upon loging attempt.
 * File name can be changed by changing GMSP_LOG_FILE_NAME definition
 */

/* Make sure we don't expose any info if called directly */
if ( !function_exists( 'add_action' ) ) {
	die("This application is not meant to be called directly!");
}

define( "bg_show_hide_LOG_FILE_NAME", plugin_dir_path(__FILE__) . "/bg_show_hide_log_file.log");

/* TODO: Take care of big log files */
/* TODO: Implement time stamps */

class bg_show_hide_LogFile {
	public function Error( $message) {
		fwrite( $this->_logFileHandle,
			$this->getTimeStamp() . " " ."ERROR: " . $message . "\n"
		);
	}

	public function Warning( $message) {
		fwrite( $this->_logFileHandle,
			$this->getTimeStamp() . " " . "WARNING: " . $message . "\n"
		);
	}

	public function Info( $message) {
		fwrite( $this->_logFileHandle, 
			$this->getTimeStamp(). " " . "INFO: " . $message . "\n"
		);
	}

	 public static function getInstance() {
		if( null == $this->_errorLogInstance) {
			$this->_errorLogInstance = new gmsp_LogFile();
			$$this->_errorLogInstance->_logFileHandle = 
				fopen( $this->_errorLogInstance->_logFileName, 'a');
		 	/* TODO: Check if we have actualy opened a file */
		}

		return $this->_errorLogInstance;
	}

	private function __construct() {
	}

	public function __destruct() {
		if( $this->_logFileHandle != null) {
			fclose( $this->_logFileHandle);
		}
	}

	private function getTimeStamp() {
		return date( "h:i:s d.m.y");
	}
	
	private $_logTimeStamp = "";
	private $_logFileName = bg_show_hide_LOG_FILE_NAME;
	private $_logFileHandle = null;
	private static $_errorLogInstance = null;
};

?>