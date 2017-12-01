<?php

require(dirname(__DIR__). '/config/bootstrap.php');
use Daytalytics\Database;


class GarbageCollection {
	
	private $db;
	
	public function __construct() {
		$this->db = Database::get_instance();
	}
	
	public function perform() {
		$this->cleanupCacheRecords();
		$this->optimizeTables();
	}
	
	private function cleanupCacheRecords() {
		$cacheTables = [
			'raw_data_keys' => ['primary_key' => 'raw_id', 'assocTables' => ['raw_data_values']], 
			'parsed_data_keys' => ['primary_key' => 'parsed_id', 'assocTables' => ['parsed_data_values']]
		];
		
		foreach ($cacheTables as $table => $tableData) {
			$recordIds = [];
			$ts = date('Y-m-d H:i:s', strtotime('-30 days'));
			$sql = "SELECT * FROM `{$table}` WHERE created < '{$ts}';";
			$query = $this->db->query($sql);
			
			if($query->num_rows > 0) {
				while($row = $this->db->fetch_assoc($query)) {
					$recordIds[] = $row[$tableData['primary_key']];
				}
			}
			
			if(!empty($recordIds)) {
				echo "Deleting records in `{$table}` table" . PHP_EOL;
				$this->db->query("DELETE FROM `{$table}` WHERE `{$tableData['primary_key']}` IN (" . implode(',', $recordIds) . ")");

				foreach ($tableData['assocTables'] as $assocTable) {
					echo "Deleting records in `{$assocTable}` table" . PHP_EOL;
					$this->db->query("DELETE FROM `{$assocTable}` WHERE `{$tableData['primary_key']}` IN (" . implode(',', $recordIds) . ")");
				}
			}
		}
	}
	
	private function optimizeTables() {
		echo "Starting to optimize tables" . PHP_EOL;
		$sql = "SHOW TABLE STATUS FROM `" . getenv('DATABASE_NAME') . "` WHERE Data_free > 0;";
		$query = $this->db->query($sql);
		
		if($query->num_rows > 0) {
			while($row = $this->db->fetch_assoc($query)) {
				$this->db->query("OPTIMIZE TABLE `{$row['Name']}`;");
				echo "Done optimizing `{$row['Name']}` table" . PHP_EOL;
			}
		} else {
			echo "Found no tables to optimize" . PHP_EOL;
		}
	}
	
}

$gc = new GarbageCollection();
$gc->perform();