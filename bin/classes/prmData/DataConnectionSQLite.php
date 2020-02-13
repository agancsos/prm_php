<?php
	include_once("DataConnection.php");

	class DataConnectionSQLite extends DataConnection {
		protected $databaseFile = "";

		public function __construct() {
		}

		public function runQuery($query) {
			$result = TRUE;
			if($this->connect()){
				$result = $this->databaseHandler->exec($query);
			}
			$error = $this->databaseHandler->lastErrorMsg();
			$this->disconnect();
			return ($result ? $result : FALSE);
		}

		public function runSafeQuery($query) {
			if($this->connect()) {
				$this->databaseHandler->exec($query);
			}
			$this->disconnect();
		}

		protected function connect() {
			try {
				$this->databaseHandler = new SQLite3($this->databaseFile);
			}
			catch(Exception $e) { return FALSE; }
			return TRUE;
		}

		protected function disconnect() {
			$this->databaseHandler->close();
			return TRUE;
		}

		public function query($query) {
			$result = new DataTable();
			if($this->connect()) {
				$rawData = $this->databaseHandler->query($query);
				while($row = $rawData->fetchArray()) {
					$tempRow = new DataRow();
					for($i = 0; $i < $rawData->numColumns(); $i++) {
						$tempColumn = new DataColumn($rawData->columnName($i), $row[$i]);
						$tempRow->addColumn($tempColumn);
					}
					$result->addRow($tempRow);
				}
				$row = $rawData->fetchArray();
			}
			$this->disconnect();
			return $result;
		}

		public function getColumns($query) {
			$result = array();
			if($this->connect()) {
				$this->statementHandler = $this->databaseHandler->prepare($query);
				$rawData = $this->statementHandler->execute();
				for($i = 0; $i < $rawData->numColumns(); $i++) {
					array_push($result, $rawData->columnName($i));
				}
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

		public function getProviderName() { return "SQLite"; }
		public function setDatabaseFile($a) { $this->databaseFile = $a; }
		public function getDatabseFile() { return $this->databaseFile; }
		public function getDbaDatabase() { return "master"; }
	}
?>
