<?php
	include_once("DataConnection.php");

	class DataConnectionMySql extends DataConnection {
		public function __construct() {
			$this->databasePort = 3306;
		}

		public function runQuery($query) {
			$result = TRUE;
			if($this->connect()){
				if(!$this->databaseHandler->query($query)){
					$error = $this->databaseHandler->error;
					return $error;
				}
			}
			$this->disconnect();
			return True;
		}

		public function runSafeQuery($query) {
			if($this->connect()) {
				try {
					$this->databaseHandler->query($query);
				}
				catch(Exception $e) { throw new Exception($e->getMessage()); } 
			}
			$this->disconnect();
		}

		protected function connect() {
			try {
				$this->databaseHandler = new mysqli($this->databaseHost, 
					$this->databaseUser, 
					$this->databasePass,
					$this->databaseService, 
					$this->databasePort); 
				if($this->databaseHandler->connect_error) {
					$this->databaseHandler = NULL;
					return FALSE;
				}
			}
			catch(Exception $e) { echo "HERE"; return FALSE; }
			return TRUE;
		}

		protected function disconnect() {
			try {
				if($this->databaseHandler != NULL) {
					$this->databaseHandler->close();
				}
			}
			catch(Exception $e) { }
			return TRUE;
		}

		public function query($query) {
			$result = new DataTable();
			if($this->connect()) {
				try {
					$results = $this->databaseHandler->query($query);
					if($results) {
						while($row = $results->fetch_array()) {
							$tempRow = new DataRow();
							for($i = 0; $i < $results->field_count; $i++) {
								$tempColumn = new DataColumn();
								$tempColumn->setName($results->fetch_field_direct($i));
								$tempColumn->setValue($row[$i]);
								$tempRow->addColumn($tempColumn);
							}
							$result->addRow($tempRow);
						}
						$results->free();
					}
				}
				catch(Exception $e) { throw new Exception($e->getMessage()); }
			}
			$this->disconnect();
			return $result;
		}

		public function getColumns($query) {
			$result = array();
			if($this->connect()) {
				try {
					$queryResult = $this->databaseHandler->query($query);
					for($i = 0; $i < $queryResult->field_count; $i++) {
						array_push($result, $queryResult->fetch_field_direct($i)->name);
					}
				}
				catch(Exception $e) { throw new Exception($e->getMessage()); }
			}
			$this->disconnect();
			return $result;
		}

		public function getRowId($query, $row) {
			$result = 0;
			if($this->connect()){ 
			}
			$this->disconnect();
			return $result;
		}

		public function getProviderName() { return "MySQL"; }
		public function getDbaDatabase() { return "information_schema"; }
		public function setName($a) {
			$this->name = $a;
			$this->databaseService = $a;
		}
	}
?>
