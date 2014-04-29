<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

/*****************************************************************
*    Advanced Membership System                                  *
*    Copyright (c) 2013 MASDYN, All Rights Reserved.             *
*****************************************************************/

class MySQLDatabase {
	
	private $connection;
	public $last_query;
	private $magic_quotes_active;
	private $real_escape_string_exists;
	
    function __construct() {
    	$this->open_connection();
		$this->magic_quotes_active = get_magic_quotes_gpc();
		$this->real_escape_string_exists = function_exists( "mysql_real_escape_string" );
    }

	public function open_connection() {

		if (function_exists('mysqli_connect')) {
			$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
			if (mysqli_connect_errno()) {
			    printf("Connect failed: %s\n", mysqli_connect_error());
			    exit();
			}
		} else {
			$this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
			if (!$this->connection) {
				die("Database connection failed: " . mysql_error());
			} else {
				$db_select = mysql_select_db(DB_NAME, $this->connection);
				if (!$db_select) {
					die("Database selection failed: " . mysql_error());
				}
			}
		}
	}

	public function close_connection() {
		if(isset($this->connection)) {
			if (function_exists('mysqli_connect')) {
				mysqli_close($this->connection);
			} else {
				mysql_close($this->connection);
			}
			unset($this->connection);
		}
	}

	public function query($sql) {
		$this->last_query = $sql;
		if (function_exists('mysqli_connect')) {
			$result = mysqli_query($this->connection, $sql);
		} else {
			$result = mysql_query($sql, $this->connection);
		}
		$this->confirm_query($result);
		return $result;
	}
	
	public function escape_value( $value ) {
		if( $this->real_escape_string_exists ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }

			if (function_exists('mysqli_connect')) {
				$value = mysqli_real_escape_string($this->connection, $value );
			} else {
				$value = mysql_real_escape_string( $value );
			}
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}
	
	// "database-neutral" methods
	public function fetch_array($result_set) {
		if (function_exists('mysqli_connect')) {
			return mysqli_fetch_array($result_set);
		} else {
			return mysql_fetch_array($result_set);
		}
	}

	public function num_rows($result_set) {
		if (function_exists('mysqli_connect')) {
			return mysqli_num_rows($result_set);
		} else {
			return mysql_num_rows($result_set);
		}
	}

	public function insert_id() {
		// get the last id inserted over the current db connection
		if (function_exists('mysqli_connect')) {
			return mysqli_insert_id($this->connection);
		} else {
			return mysql_insert_id($this->connection);
		}
	}
  
	public function affected_rows() {
		if (function_exists('mysqli_connect')) {
			return mysqli_affected_rows($this->connection);
		} else {
			return mysql_affected_rows($this->connection);
		}
	}

	private function confirm_query($result) {
		if (!$result) {
			if (function_exists('mysqli_connect')) {
				$error = mysqli_error($this->connection);
			} else {
				$error = mysql_error($this->connection);
			}
		    $output = "Database query failed: " .$error . "<br /><br />";
		    $output .= "Last SQL query: " . $this->last_query;
		    die( $output );
		}
	}
	
}

$database = new MySQLDatabase();
$db =& $database;

?>