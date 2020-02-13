<?php
	ini_set('max_execution_time', 300);
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("{$__ROOT_FROM_PAGE__}/classes/SR.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmCommon/prmcommon_all.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmData/prmdata_all.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmService/prmservice_all.php");

	class PRMLocalService {
		private $configFile = "config/config.json";
		private static $instance = NULL;
		private $localDatabase = NULL;
		private $basePath = "";
		private $traceLevel = 1;

		private function __construct() {
			global $__ROOT_FROM_PAGE__;
			$this->basePath = "{$__ROOT_FROM_PAGE__}";
			$this->localDatabase = new DataConnectionSQLite();
			$this->localDatabase->setDatabaseFile(($this->getConfigValue("DatabaseFile") != "" ? $this->getConfigValue("DatabaseFile") : SR::defaultLocalDatabase()));
			$this->createLocalDB();
			$this->importSettings();
			$this->traceLevel = ($this->getConfigValue("TraceLevel") != "" ? $this->getConfigValue("TraceLevel") : 1);
		}

		public static function getInstance() {
			if(PRMLocalService::$instance == NULL) {
				PRMLocalService::$instance = new PRMLocalService();
			}
			return PRMLocalService::$instance;
		}

		/**
	 	 * This method reads the config file and searches for a specific config setting
	 	 * @param configFile Full path to the configuration file
	 	 * @param configKey Name of the configuration to look up
	 	 * @return Value of the key to look up
	 	 */
		private function readConfig($configFile, $configKey){
			if(PRMSystem::fileExists($configFile)){
				if(str_replace(".json", "", $configFile) != $configFile) {
					$propertyStore = new JsonPropertyStore($configFile);
					return $propertyStore->getProperty($configKey);
				}
				try{
					$system = new PRMSystem($configFile);
					$rawContent = $system->readFile();
					foreach(explode("\n", $rawContent) as $pair){
						$comps = explode("=", $pair);
						if(sizeof($comps) == 2){
							if(substr($comps[0], 0) != '#'){
								if($comps[0] == $configKey){
									return trim($comps[1]);
								}
							}
						}
					}
				}
				catch(Exception $e){
				}
			}
			return "";
		}

		public function getConfigValue($configKey) { return $this->readConfig("{$this->basePath}/{$this->configFile}", $configKey); }

		/**
	 	 * This method creates the local database.
	 	 * This method should only need to run once, unless
	 	 * the local database was deleted.
		 */
		private function createLocalDB(){
			$queries = array();
			$longString = "";

			// Audit Logs
			$longString = "create table if not exists audit_log(audit_log_id integer primary key autoincrement,";
			$longString .= "audit_log_event character default '', audit_log_message character default '',";
			$longString .= "audit_log_component character default 'Database', last_updated_date timestamp ";
			$longString .= "default current_timestamp";
			$longString .= ")";
			array_push($queries, $longString);
			$longString = "";

			// Flag
			$longString = "create table if not exists prm_flag(prm_flag_id integer primary key autoincrement,";
			$longString .= "prm_flag_name character default '', prm_flag_value character default '',";
			$longString .= "last_updated_date timestamp default current_timestamp";
			$longString .= ")";
			array_push($queries, $longString);
			$longString = "";

			// History
			$longString = "create table if not exists prm_history(prm_history_id integer primary key autoincrement,";
			$longString .= "prm_history_change character default '', prm_history_version character default '',";
			$longString .= "last_updated_date timestamp default current_timestamp";
			$longString .= ")";
			array_push($queries, $longString);
			$longString = "";

			// File
			$longString = "create table if not exists prm_file(prm_file_id integer primary key autoincrement,";
			$longString .= "prm_file_path character default '', prm_file_name character default '',";
			$longString .= "last_updated_date timestamp default current_timestamp";
			$longString .= ")";
			array_push($queries, $longString);
			$longString = "";

			// Queries
			$longString = "create table if not exists prm_query(prm_query_id integer primary key autoincrement,";
			$longString .= "prm_query_name character default '', prm_query_value character default '',";
			$longString .= "prm_query_provider character default '',prm_query_desc character default '',";
			$longString .= "last_updated_date timestamp default current_timestamp";
			$longString .= ")";
			array_push($queries, $longString);
			$longString = "";


			foreach($queries as $query){
				$error = $this->localDatabase->runQuery($query);
				if($error != TRUE) {
					print("Failed to create table({$error})....<br/>");
				}
			}
		}

		/**
		 * This method audits messages in the error log stored in the local database.
		 * @param event Query or event to be logged
		 * @param message Error or message to be logged
	 	 * @param component Area of the system
	 	 */
		public function auditMessage($event, $message, $component){
			$event = str_replace("'","''", $event);
			$message = str_replace("'","''", $message);
			$component = str_replace("'","''", $component);
			$sql = "insert into audit_log (audit_log_event,audit_log_message,audit_log_component) values (";
			$sql .= "'".$event."','".$message."','".$component."')";
			try{
				$this->localDatabase->runSafeQuery($sql);
			}
			catch(Exception $e){
			}
		}

		/**
		 * This method retrieves the headers for the audits
		 * @return Names for the headers
		 */
		public function getAuditHeaders() { return $this->localDatabase->getColumns("select * from audit_log"); }

		/**
		 * This method retrieves the version from the database
		 * @return Version from the databse
		 */
		public function databaseVersion(){
			global $__ROOT_FROM_PAGE__;
			$mResult = "";
			if(sizeof($this->localDatabase->query("select prm_history_version from prm_history where prm_history_change = 'current_version'")->getRows()) > 0){
				$mResult = $this->localDatabase->query("select prm_history_version from prm_history where prm_history_change = 'current_version'")->getRows()[0]->getColumns()[0]->getValue();
			}
			if($mResult == "") {
				$propertyStore = new JsonPropertyStore($__ROOT_FROM_PAGE__."/classes/prmService/db/database.json");
				$mResult = $propertyStore->getProperty('version');
			}
			return $mResult;
		}

		/**
		 * This method retrieves the audits
		 * @return Rows for the headers
		 */
		public function getAudits(){
			$result = array();
			$rawResults = $this->localDatabase->query("select * from audit_log")->getRows();
			foreach($rawResults as $cursor) {
				$temp = new PRMAudit();
				$temp->setId($cursor->getColumns()[0]->getValue());
				$temp->setEvent($cursor->getColumns()[1]->getValue());
				$temp->setMessage($cursor->getColumns()[2]->getValue());
				$temp->setComponent($cursor->getColumns()[3]->getValue());
				$temp->setDate($cursor->getColumns()[4]->getValue());
				$temp->setUser($cursor->getColumns()[5]->getValue());
				array_push($result, $temp);
			}
			return $result;
		}

			
		/**
		 * This method imports application settings
		 */
		private function importSettings(){
			$this->localDatabase->runQuery("delete from prm_flag where prm_flag_name not like '#%'"); // Delete old
			$this->localDatabase->runQuery("delete from sqlite_sequence where name = 'prm_flag'");

			// Insert standard settings
			$this->localDatabase->runQuery("insert into prm_flag (prm_flag_name, prm_flag_value) values ('Version','".$this->databaseVersion()."')");
			$this->localDatabase->runQuery("insert into prm_flag (prm_flag_name, prm_flag_value) values ('BasePath','".$this->basePath."')");

			// Insert from configuration file
			if(PRMSystem::fileExists($this->basePath."/config")){
				try{
					$system = new PRMSystem($this->basePath."/config");
					$rawContent = $system->readFile();
					foreach(explode("\n", $rawContent) as $pair){
						$comps = explode("=", $pair);
						if(sizeof($comps) == 2){
							if(substr($comps[0], 0) != '#'){
								$this->localDatabase->runQuery("insert into prm_flag (prm_flag_name, prm_flag_value) values ('".$comps[0]."','".$comps[1]."')");
							}
						}
					}
				}
				catch(Exception $e){
				}
			}
		}

		public function traceError($event=NULL,$message=NULL,$component=NULL) {
			if($this->traceLevel > 0) {
				$this->auditMessage($event, $message, $component);
			}
		}

		public function traceWarning($event=NULL,$message=NULL,$component=NULL) {
			if($this->traceLevel > 1) {
				$this->auditMessage($event, $message, $component);
			}
		}

		public function traceInformational($event=NULL,$message=NULL,$component=NULL) {
			if($this->traceLevel > 2) {
				$this->auditMessage($event, $message, $component);
			}
		}

		public function traceVerbose($event=NULL,$message=NULL,$component=NULL) {
			if($this->traceLevel > 3) {
				$this->auditMessage($event, $message, $component);
			}
		}

		public function purgeAudits() { $this->localDatabase->runQuery("delete from audit_log"); }
		public function setBasePath($a) { $this->basePath = $a; }
		public function getBasePath() { return $this->basePath; }
	}
?>
