<?php

// Are we on the deployment server?
if ($_SERVER['SERVER_NAME'] == 'people.oregonstate.edu') {
	$host = 'oniddb.cws.oregonstate.edu';
	$username = 'smartw-db';
	$password = 'pdcIbBUUGkzkGv4P';
	$database = 'smartw-db';
} else {
	$host = 'localhost';
	$username = 'smartw';
	$password = 'db-password';
	$database = 'web';
}

// Make sure we have a PHP_VERSION_ID
if (!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);

	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}


class Database {
	private $old_api = false;
	private $database_handle;

	public function __construct() {
		// PHP 5 or PHP 7?
		$this->old_api = PHP_VERSION_ID < 50500;

		// Production or development server?
		if ($_SERVER['SERVER_NAME'] == 'people.oregonstate.edu') {
			$host = 'oniddb.cws.oregonstate.edu';
			$username = 'smartw-db';
			$password = 'pdcIbBUUGkzkGv4P';
			$database = 'smartw-db';
		} else {
			$host = 'localhost';
			$username = 'smartw';
			$password = 'db-password';
			$database = 'web';
		}

		if ($old_api) {
			$this->database_handle = mysql_connect($host, $username, $password);
			mysql_select_db($database);
		} else {
			$this->database_handle = mysqli_connect($host, $username, $password, $database);  
		}

		// Did the connection work?
		$this->database_handle || die('Error: Could not connect to database at host ('.$host.')');
	}

	public function query($query_text) {
		$retval = array();

		if ($this->old_api) {
			$result = mysql_query($query_text);
			$number = mysql_num_rows($result);

	    for ($i = 0; $i < $number; $i++)
	    	$retval[] = mysql_fetch_array($result);
		} else {
	    $result = mysqli_query($this->database_handle, $query_text);
	    $number = mysqli_num_rows($result);

	    for ($i = 0; $i < $number; $i++)
	    	$retval[] = mysqli_fetch_array($result);
    }

    return $retval;
	}
}

global $database;
$database = new Database();

?>

