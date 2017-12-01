<?php
namespace Daytalytics\Tests\Lib;

use Daytalytics\Database;
use PHPUnit\Framework\TestCase;

class DbFixtureTestCase extends TestCase {
	
	/**
	 * Production DB w/c would be helpful in creating schema
	 * 
	 * @var Database
	 */
	private $prodDb;
	
	
	/**
	 * Test DB connection
	 * 
	 * @var Database
	 */
	protected $db;
	
	
	/**
	 * Table fixtures
	 * 
	 * sample value
	 * <code> 
	 * [
	 * 	'keywords_no_info',
	 * 	'keywords_update_info' => ['records' => [
	 * 		['keyword' => 'test keyword'],
	 * 		['keyword' => 'test keyword 2']
	 * 	  ]
	 * 	]
	 * ]
	 * </code>
	 * 
	 * @var array
	 */
	protected $fixtures = [];
	
	
	protected $requiredFixtures = [
		'raw_data_keys',
		'raw_data_values',
		'parsed_data_keys',
		'parsed_data_values',
		'proxy_server_usage',
		'proxy_servers' => [
			'records' => [
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '173.208.77.41', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '173.208.77.43', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '198.203.28.104', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '198.203.28.86', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '198.203.28.90', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '208.117.13.115', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '208.117.13.89', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '209.220.97.151', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '209.222.12.104', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '209.222.12.109', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '209.222.12.120', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '209.222.12.81', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '216.108.226.238', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '216.172.156.106', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '216.172.156.72', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '216.172.156.89', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '216.172.156.93', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '63.223.114.104', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '63.223.114.43', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '63.223.114.49', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '64.31.62.233', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '64.31.62.248', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '64.31.62.254', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '66.232.112.139', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '67.106.134.3', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '64.31.62.233', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '64.31.62.248', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '64.31.62.254', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '66.232.112.139', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '67.106.134.3', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '67.106.134.51', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '67.202.86.94', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '69.50.197.126', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '96.44.159.25', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '96.44.159.38', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '96.44.159.42', 'port' => 60099, 'username' => '', 'password' => ''],
				['path' => '', 'enabled' => 1, 'socket_enabled' => 0, 'IP' => '96.44.159.54', 'port' => 60099, 'username' => '', 'password' => '']
			]
		]
	];
	
	
	protected function setUp() {
		$this->prodDb = new Database([
		    'username' => getenv('DATABASE_PROD_USER'),
		    'password' => getenv('DATABASE_PROD_PASS'),
		    'hostname' => getenv('DATABASE_PROD_HOST'),
		    'database' => getenv('DATABASE_PROD_NAME')
		]);
		
		// This is now the test DB
		// @see tests/bootstrap.php
		$this->db = Database::get_instance();
		
		$this->fixtures = array_merge($this->fixtures, $this->requiredFixtures);
		
		$this->prepareTables();
	}
	
	protected function tearDown() {
		$this->dropTables();	
		$this->prodDb = null;
		$this->db = null;
	}
	
	protected function prepareTables() {
		if(!empty($this->fixtures)) {
			foreach($this->fixtures as $table => $data) {
				if(!is_string($table)) {
					$table = $data;
				}
				
				$query = $this->prodDb->query("DESCRIBE `{$table}`");
				if($query->num_rows > 0) {
					$schemaFields = [];
					$primaryKeyFieldRow = [];
					
					while($row = $this->prodDb->fetch_assoc($query)) {
						if($row['Key'] == 'PRI') {
							$primaryKeyFieldRow = $row;
						}
						$schemaFields[] = " `{$row['Field']}` {$row['Type']}";
					}
					
					$schemaSql = "CREATE TABLE IF NOT EXISTS `{$table}` (";
					$schemaSql .= implode(',', $schemaFields);
					$schemaSql .= ") ENGINE=MyISAM;";
					
					// Drop the table first to truncate all records created
					// as they may stalled once test comes into PHP fatal error and would halt it.
					$this->db->query("DROP TABLE IF EXISTS `{$table}`;"); 
					$this->db->query($schemaSql);
					
					if(!empty($primaryKeyFieldRow)) {
						$this->db->query("ALTER TABLE `{$table}` ADD PRIMARY KEY (`{$primaryKeyFieldRow['Field']}`);");
						if($primaryKeyFieldRow['Extra'] == 'auto_increment') {
							$this->db->query("ALTER TABLE `{$table}` MODIFY `{$primaryKeyFieldRow['Field']}` {$primaryKeyFieldRow['Type']} unsigned NOT NULL AUTO_INCREMENT;");
						}
					}
				}
				
				if(!empty($data['records'])) {
					foreach($data['records'] as $record) {
						if(!is_array($record)) {
							continue;
						}
						
						$fields = array_keys($record);
						$values = array_values($record);
						// Quote it
						$values = array_map(function($val) {
							return "'{$val}'";
						}, $values);
						
						$insertSql = "INSERT INTO `{$table}` (". implode(',', $fields) .") VALUES (". implode(',', $values) .");";
						$this->db->query($insertSql);
					}
				}
			}
		}
	}
	
	protected function dropTables() {
		if(!empty($this->fixtures)) {
			foreach($this->fixtures as $table => $data) {
				if(!is_string($table)) {
					$table = $data;
				}
				$this->db->query("DROP TABLE `{$table}`;");
			}
		}
	}
}
