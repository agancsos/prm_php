<?php
	include_once("DataConnection.php");
	class DataConnectionOracle extends DataConnection {
		public function __construct() {
			$this->datbasePort = 1521;
		}

		public function runQuery($query) {
			$result = TRUE;
			if($this->connect()){
				try {
					$this->statementHandler = oci_parse($this->databaseHandler, $query);
					oci_execute($this->statementHandler);
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
					$this->statementHandler = oci_parse($this->databaseHandler, $query);
					oci_execute($this->statementHandler);
				}
				catch(Exception $e) { throw new Exception($e->getMessage()); }
			}
			$this->disconnect();
		}

		protected function connect() {
			try {
				$this->databaseHandler = oci_connect($this->databaseUser, $this->databasePass, "{$this->databaseHost}/{$this->databaseService}");
			}
			catch(Exception $e) { return FALSE; }
			return TRUE;
		}

		protected function disconnect() {
			oci_close($this->databaseHandler);
			return TRUE;
		}

		public function query($query) {
			$result = new DataTable();
			if($this->connect()) {
				try {
					$this->statementHandler = oci_parse($this->databaseHandler, $query);
					$results = oci_execute($this->statementHandler);
					while($record = oci_fetch_array($this->statementHandler, OCI_ASSOC+OCI_RETURN_NULLS)) {
						$newRow = new DataRow();
						for($i = 0; $i < oci_num_fields($this->statementHandler); $i++) {
							$newColumn = new DataColumn();
							$newColumn->setName(oci_field_name($this->statementHandler, $i));
							$newColumn->setValue($record[$i]);
							$newRow->addColumn($newColumn);
						}
						$result->addRow($newRow);
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
					$this->statementHandler = oci_parse($this->databaseHandler, $query);
					$results = oci_execute($this->statementHandler);
					for($i = 0; $i < oci_num_fields($this->statementHandler); $i++) {
						array_push($result, oci_field_name($this->statementHandler, $i));
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
		public function getProviderName() { return "Oracle"; }
		public function getDbaDatabase() { return ""; }
	}
?>
