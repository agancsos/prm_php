<?php
	include_once("DataRow.php");
	include_once("DataTable.php");
	include_once("DataColumn.php");
	abstract class DataConnection { 
		protected $databaseFile = "";
		protected $databaseHandler = NULL;
		protected $statementHandler = NULL;
		protected $databaseUser	 = NULL;
		protected $databasePass	 = NULL;
		protected $databaseHost	 = NULL;
		protected $databaseService  = NULL;
		protected $databasePort	 = NULL;
		protected $dbaUser		  = NULL;
		protected $dbaPass		  = NULL;
		protected $name			 = NULL;
		public function __construct() { }
		public abstract function runQuery($query);
		public abstract function runSafeQuery($query);
		protected abstract function connect();
		protected abstract function disconnect();
		public abstract function query($query);
		public abstract function getRowId($query, $row);
		public abstract function getProviderName();
		public abstract function getDbaDatabase();
		public function setDatabaseUser($a) { $this->databaseUser = $a; }
		public function setDatabasePass($a) { $this->databasePass = $a; }
		public function getDatabaseUser() { return $this->databaseUser; }
		public function getDatabasePass() { return $this->databasePass; }
		public function setDatabaseHost($a) { $this->databaseHost = $a; }
		public function setName($a) { $this->name = $a; }
		public function getName() { return $this->name; }
		public function setDatabaseService($a) { $this->databaseService = $a; }
		public function setDatabasePort($a) { $this->databasePort = $a; }
		public function getDatabaseHost() { return $this->databaseHost; }
		public function getDatabaseService() { return $this->databaseService; }
		public function getDatabasePort() { return $this->databasePort; }
		public function setDbaUser($a) { $this->dbaUser = $a; }
		public function setDbaPass($a) { $this->dbaPass = $a; }
		public function getDbaUser() { return $this->dbaUser; }
		public function getDbaPass() { return $this->dbaPass; }
	}
?>
