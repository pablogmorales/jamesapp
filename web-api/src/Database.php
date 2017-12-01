<?php

namespace Daytalytics;

class Database
{
	public $connectlink;	//Database Connection Link
	private $connection_details;

	private static $singleton_instance;

	public static function get_instance() {
		if (!isset(self::$singleton_instance)) {
			self::$singleton_instance = new Database();
		}

		return self::$singleton_instance;
	}

	public static function force_new_instance() {
		return new Database();
	}

	public function __construct(array $connection_details = []) {
		if(!empty($connection_details)) {
			$this->connection_details = $connection_details;
		} else {
			$this->connection_details = [
				'username' => getenv('DATABASE_USER'),
				'password' => getenv('DATABASE_PASS'),
				'hostname' => getenv('DATABASE_HOST'),
				'database' => getenv('DATABASE_NAME')
			];
		}

		$this->connectlink = $this->connect();
	}

	public function connect($db = false) {
		if ($db === false) {
			if (!empty($this->connection_details))
				$db = $this->connection_details;
			else {
				throw new DatabaseException("no connection details available");
			}
		}
		
		$link = @mysqli_connect($db['hostname'],$db['username'],$db['password']);
		if ($link === false) {
			throw new DatabaseException("Failed to connect: " . mysqli_connect_error());
		}
		
		$db_selected = mysqli_select_db($link, $db['database']);
		if (!$db_selected) {
			$mysql_error = mysqli_error($link);
			mysqli_close($link);
			throw new DatabaseException("Can't use {$db['database']}: " . $mysql_error);
		}
		
		$timezone_set = mysqli_query($link, 'SET time_zone = "+0:00"');
		if (!$timezone_set) {
			mysqli_close($link);
			throw new DatabaseException('Could not set timezone');
		}
		
		return $link;
	}

	public function escape_string($string) {
		return @mysqli_real_escape_string($this->connectlink, $string);
	}

	public function __destruct() {
		$this->disconnect();
	}
	
	
	public function disconnect() {
		@mysqli_close($this->connectlink);
	}
	
	
	function select_db($db_name) {
		$res = mysqli_select_db($this->connectlink, $db_name);
		if ($res === false) {
			throw new DatabaseException("Can't use $db_name: " . mysqli_error($this->connectlink));
		}
		
		return true;
	}
	
	function unbuffered_query($sql) {
		$res = mysqli_real_query($this->connectlink, $sql);
		
		if ($res === false) {
			throw new DatabaseException('Invalid query: ' . mysqli_error($this->connectlink) . ' ' . $sql);
		}
		mysqli_use_result($this->connectlink);
		
		return $res;
	}

	public function query($sql) {
		$res = mysqli_query($this->connectlink, $sql);
		
		if ($res === false) {
			throw new DatabaseException('Invalid query: ' . mysqli_error($this->connectlink) . $sql);
		}
		
		return $res;
	}
	
	public function fetch_assoc($res) {
		if ($res === false) {
			throw new DatabaseException('Invalid result resource: ');
		}
		
		$row = mysqli_fetch_assoc($res);
		
		if (is_null($row)) {
			return false;
		}
		
		return $row;
	}

	public function fetch_rows($result) {
		throw new DatabaseException("fetch_rows is deprecated");
	}

	public function count($result) {
		if ($result === false) {
			throw new DatabaseException('Invalid result resource: ');
		}

		return mysqli_num_rows($result);
	}

	public function affected_rows() {
		return mysqli_affected_rows($this->connectlink);
	}
	
	/**
	 * @name is_connected()
	 * @description check that a database connection exists.
	 */
	function is_connected() {
		if (!$this->connectlink)
			return false;
		else
			return true;
	}

	/**
	 * Expected database format is:
	 * table: raw_data_keys
	 * fields: raw_id (INT, AUTO_INCREMENT) | key (VARCHAR(255)) | created (TIMESTAMP, default:CURRENT_TIMESTAMP) | module (VARCHAR(255))
	 * indices: raw_id (PRIMARY), (key,created) (INDEX)
	 *
	 * table: raw_data_values
	 * fields: raw_id (INT) | request (BLOB)| data (LONGBLOB)
	 * indices: raw_id (PRIMARY)
	 *
	 * table: parsed_data_keys
	 * fields: parsed_id (INT, AUTO_INCREMENT) | key (VARCHAR(255)) | created (TIMESTAMP, default:CURRENT_TIMESTAMP) | module (VARCHAR(255))
	 * indices: parsed_id (PRIMARY), (key,created) (INDEX)
	 *
	 * table: parsed_data_values
	 * fields: parsed_id (INT) | data (LONGBLOB)
	 * indices: parsed_id (PRIMARY)
	 */

	/**
	 * @name set_raw_data()
	 * @description Save a raw request-response pair in the database.
	 * Fail is a database error occurs.
	 * Fail if the $key is longer than 255 bytes.
	 * Fail is the $data is empty() //revised to succede if data is an empty string
	 * Fail if the $module_identifier isn't a string.
	 * Fail is the $module_identifier is longer than 255 bytes.
	 * @returns
	 * true on success.
	 * false on failure.
	 */
	function set_raw_data($key, $request, $data, $module_identifier, $time=false) {		
		if (!is_string($data)) {
			throw new DatabaseException('Data is not a string.');
		}elseif (!is_string($module_identifier)) {
			throw new DatabaseException('Module identifier is not a string.');
		}elseif (!is_string($key)) {
			throw new DatabaseException('Key is not a string.');
		}elseif (strlen($module_identifier)>255) {
			throw new DatabaseException('Module identifier is more than 255 characters.');
		}
		
		$key = $this->hash_key($key, $module_identifier);
		
		$data = gzcompress($data);
		
		$key = $this->escape_string($key);
		$module_identifier = $this->escape_string($module_identifier);
		$request = $this->escape_string($request);
		$data = $this->escape_string($data);
		if(empty($time))
			$time = time();
		$time = date('Y-m-d H:i:s', $time);
		if($time === false) {
			throw new DatabaseException('Time is invalid.');
		}
		$time = $this->escape_string($time);

		$sql_1 = "INSERT INTO raw_data_keys (`key`, `module`, `created`) VALUES ('$key', '$module_identifier', '$time')";
		$res = $this->query($sql_1);
		if(!$res) {
			throw new DatabaseException('Inserting keys failed ' . mysqli_error($this->connectlink));
		}
		$raw_id = mysqli_insert_id($this->connectlink);
		$sq_2 = "INSERT INTO raw_data_values (`raw_id`, `request`, `gzipped_data`) VALUES ($raw_id, '$request', '$data')";
		$res = $this->query($sq_2);
		if(!$res) {
			throw new DatabaseException('Inserting values failed ' . mysqli_error($this->connectlink));
		}
		return TRUE;
	}

	/**
	 * @name get_raw_data()
	 * @description Get the data for the given key that is no older than $age
	 * where $age is a strtotime() friendly string (e.g. 2009-01-01 00:00:00, or -1 days)
	 * Fail if $age is invalid.
	 * Fail if a database error occurs.
	 * Fail if the data doesn't exist.
	 * @returns
	 * The data, if found.
	 * false on failure.
	 */
	function get_raw_data($key, $age, $module_identifier) {
		$date = strtotime($age);
		if ($date == false) {
			throw new DatabaseException('Date is invalid.');
		}
		$date = date('Y-m-d H:i:s', $date);

		$key = $this->hash_key($key, $module_identifier);

		$key = $this->escape_string($key);
		$module_identifier = $this->escape_string($module_identifier);

		$sql = "SELECT rvalue.gzipped_data FROM raw_data_values AS rvalue
		        INNER JOIN raw_data_keys AS kvalue ON rvalue.raw_id=kvalue.raw_id
		        WHERE kvalue.created >= '$date' AND kvalue.key = '$key' AND kvalue.module = '$module_identifier' ORDER BY created DESC LIMIT 1";
		$res = $this->query($sql);
		if(!$res) {
			throw new DatabaseException('Query error: '.mysqli_error($this->connectlink));
		}
		
		$row = mysqli_fetch_assoc($res);
		if(!isset($row)) {
			return FALSE;
		}
		$data = gzuncompress($row['gzipped_data']);
		
		if ($data === false)
			return false;
		
		return $data;
	}

	/**
	 * @name get_raw_request()
	 * @description Get the request used to cache the
	 * given data
	 */
	function get_raw_request($key, $age, $module_identifier) {
		$date = strtotime($age);
		if ($date == false) {
			throw new DatabaseException('Date is invalid.');
		}
		$date = date('Y-m-d H:i:s', $date);

		$key = $this->hash_key($key, $module_identifier);

		$sql = "SELECT rvalue.request FROM raw_data_values AS rvalue
		        INNER JOIN raw_data_keys AS kvalue ON rvalue.raw_id=kvalue.raw_id
		        WHERE kvalue.created >= '$date' AND kvalue.key = '$key' AND kvalue.module = '$module_identifier' ORDER BY created DESC LIMIT 1";

		$result = $this->query($sql);
		if($result) {
			$request =  mysqli_fetch_row($result);
			if($result !== false) {
				return $request[0];
			}else {
				return FALSE;
			}
		}else {
			return FALSE;
		}

	}

	/**
	 * @name set_parsed_data
	 * @description Save the result of parsing the raw data.
	 * Fail is a database error occurs.
	 * Fail if the $key is longer than 255 bytes.
	 * Fail is the $data is empty()
	 * Fail if the $module_identifier isn't a string.
	 * Fail is the $module_identifier is longer than 255 bytes.
	 * @returns
	 * true on success.
	 * false on failure.
	 */
	function set_parsed_data($key, $data, $module_identifier, $time=false) {
		if ($data === '') {
			throw new DatabaseException('Data is empty.');
		}elseif (!is_string($data)) {
			throw new DatabaseException('Data is not a string.');
		}elseif (!is_string($module_identifier)) {
			throw new DatabaseException('Module identifier is not a string.');
		}elseif (!is_string($key)) {
			throw new DatabaseException('Key is not a string.');
		}elseif (strlen($key)>255) {
			throw new DatabaseException('Key is more than 255 characters.');
		}elseif (strlen($module_identifier)>255) {
			throw new DatabaseException('Module identifier is more than 255 characters.');
		}
		
		$key = $this->hash_key($key, $module_identifier);
		
		$key = $this->escape_string($key);
		$data = $this->escape_string($data);
		if(empty($time))
			$time = time();
		$time = date('Y-m-d H:i:s', $time);
		if($time === false) {
			throw new DatabaseException('Time is invalid.');
		}
		$time = $this->escape_string($time);

		$sql_1 = "INSERT INTO parsed_data_keys (`key`, `module`, `created`) VALUES ('$key', '$module_identifier', '$time')";
		$res = $this->query($sql_1);
		if (!$res) {
			throw new DatabaseException('Inserting keys failed ' . mysqli_error($this->connectlink));
		}
		$parsed_id = mysqli_insert_id($this->connectlink);
		$sq_2 = "INSERT INTO parsed_data_values (`parsed_id`, `data`) VALUES ($parsed_id, '$data')";
		$res = $this->query($sq_2);
		if(!$res) {
			throw new DatabaseException('Inserting values failed ' . mysqli_error($this->connectlink));
		}
		return TRUE;
	}

	/**
	 * @name get_parsed_data()
	 * @description Get the data for the given key that is no older than $age
	 * where $age is a strtotime() friendly string (e.g. 2009-01-01 00:00:00, or -1 days)
	 * Fail if $age is invalid.
	 * Fail if a database error occurs.
	 * Fail if the data doesn't exist.
	 * @returns
	 * The data, if found.
	 * false on failure.
	 */
	function get_parsed_data($key, $age, $module_identifier) {
		$date = strtotime($age);
		if ($date == false) {
			throw new DatabaseException('Date is invalid.');
		}
		$date = date('Y-m-d H:i:s', $date);

		$key = $this->hash_key($key, $module_identifier);

		$key = $this->escape_string($key);
		$age = $this->escape_string($age);
		$module_identifier = $this->escape_string($module_identifier);

		$sql = "SELECT rvalue.data FROM parsed_data_values AS rvalue
		        INNER JOIN parsed_data_keys AS kvalue ON rvalue.parsed_id=kvalue.parsed_id
		        WHERE kvalue.created >= '$age' AND kvalue.key = '$key' AND kvalue.module = '$module_identifier' ORDER BY created DESC LIMIT 1";

		$res = $this->query($sql);
		if(!$res) {
			throw new DatabaseException('Query error: '.mysqli_error($this->connectlink));
		}
		
		
		$row = mysqli_fetch_row($res);
		if(!isset($row)) {
			return FALSE;
		}
		
		return $row[0];
	}
	
	 function hash_key($key, $module_identifier) {
		return sha1($module_identifier . ' ' . $key);
	}
	
	/**
	 * @name get_module_database_name()
	 * @usage string get_module_database_name(string $module_identifier)
	 * @returns a mysql UNSAFE database name.
	 * @description Sometimes a module will need it's own database. This function
	 * generates a database name that the module can use.
	 */
	function get_module_database_name($module_identifier) {
		return 'web_information_api_' . md5($module_identifier);
	}
}
?>
