<?php
	include_once("DataConnection.php");

	class DataConnectionMSSql extends DataConnection {
		public function __construct() {
			$this->databasePort = 1433;
		}

		public function runQuery($query) {
			$result = TRUE;
			if($this->connect()){
				try { 
					mssql_query($query, $this->databaseHandler);
				}
				catch(Exception $e) { $result = FALSE;  }
			}
			$error = $this->databaseHandler->lastErrorMsg();
			$this->disconnect();
			return ($result ? $result : FALSE);
		}

		public function runSafeQuery($query) {
			if($this->connect()) {
				try {
					mssql_query($query, $this->databaseHandler);
				}
				catch(Exception $e) { throw new Exception($e->getMessage()); }
			}
			$this->disconnect();
		}

		protected function connect() {
			try {
				$this->databaseHandler = mssql_connect($this->databaseHost, $this->databaseUser, $this->daatabasePass);
				mssql_select_db($this->databaseService, $this->databseHandler);
			}
			catch(Exception $e) { return FALSE; }
			return TRUE;
		}

		protected function disconnect() {
			mssql_close($this->databaseHandler);
			return TRUE;
		}

		public function query($query) {
			$result = new DataTable();
			if($this->connect()) {
				try {
					$results = mssql_query($query, $this->databaseHandler);
					while($row = mssql_fetch_array($query)) {
						$newRow = new DataRow();
						for($i = 0; $i < mssql_num_fields($results); $i++) {
							$newColumn = new DataColumn();
							$newColumn->setName($mssql_field_name($query, $i));
							$newColumn->setValue($row[$i]);
							$newRow->addColumn($newColumn);
						}
						$result->addRow($newRow);
					}
					mssql_free_result($results);
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
					$results = mssql_query($query, $this->databaseHandler);
					for($i = 0; $i < mssql_num_fields($results); $i++) {
						array_push($result, mssql_field_name($query, $i));
					}
					mssql_free_result($results);
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

		public function getProviderName() { return "Microsoft SQL Server"; }
		public function getDbaDatabase() { return "master"; }
	}
?>
