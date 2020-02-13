<?php
	include_once("PRMLocalService.php");
	include_once("PRMSecurityService.php");
	include_once("PRMConfigurationService.php");
	include_once("PRMObjectService.php");
	class PRMService {
		private static $instance = NULL;
		private $localService = NULL;
		private $dataService = NULL;
		private $securityService = NULL;
		private $objectService = NULL;
		private $configurationService = NULL;
	
		/**
		 * This is the default constructor
		 */
		private function __construct(){
			$this->localService = PRMLocalService::getInstance();
			$this->dataService = PRMDataService::getInstance();
			$this->objectService = PRMObjectService::getInstance(); 
			$this->securityService = PRMSecurityService::getInstance();
			$this->configurationService = PRMConfigurationService::getInstance();
		}

		public function getStatuses() { 
			$result = array();
			foreach (PRMStatus::getItterator() as $builtin) {
				$tempItem = new PRMGeneralItem();
				$tempItem->setId(PRMStatus::getOrdinal($builtin));
				$tempItem->setName(str_replace("_STATUS", "", $builtin));	
				array_push($result, $tempItem);
			}

			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_STATUS");
			foreach ($rawResult->getRows() as $row) {
				$tempItem = new PRMGeneralItem();
				$tempItem->setId($row->getColumn("PRM_STATUS_ID")->getValue());
				$tempItem->setName($row->getColumn("PRM_STATUS_VALUE")->getValue());
				array_push($result, $tempItem);
			}
			return $result;		
		}

		/**
		 * This method returns the instance of the PRMService
		 * @return Instance of the PRMService
		 */
		public static function getInstance() {
			if(PRMService::$instance == NULL) {
				PRMService::$instance = new PRMService();
			}
			return PRMService::$instance;
		}

		/**
		 * This method runs to ensure that all dependices exist and 
		 * configurations are set
		 */
		public function startup(){
			if($this->localService->readConfig("DEBUG") == "1"){
				$this->auditMessage("Startup", "Starting", "Service");
			}
		}

		public static function testMethod() { return "YES"; }
		public function readConfig($name) { return $this->localService->getConfigValue($name); } 


		/**
		 * This method audits messages in the error log stored in the local database.
		 * @param event Query or event to be logged
		 * @param message Error or message to be logged
	 	 * @param component Area of the system
		 */
		public function auditMessage($event, $message, $component) { $this->localService->auditMessage($event, $message, $component); }

		/**
	 	 * This method retrieves the headers for the audits
		 * @return Names for the headers
		 */
		public function getAuditHeaders() { return $this->localService->getAuditHeaders(); }

		/**
		 * This method retrieves the version from the database
		 * @return Version from the databse
		 */
		public function databaseVersion() { return $this->localService->databaseVersion(); }

		/**
		 * This method retrieves the audits
		 * @return Rows for the headers
		 */
		public function getAudits() { return $this->localService->getAudits(); }
		public function getSessions() { return $this->dataService->getHandler()->query("SELECT * FROM PRM_SESSION"); }																																		  
		/**
		 * Getters and setters
		 */
		public function setBasePath($a) { $this->localService->setBasePath($a); }
		public function getBasePath() { return $this->localService->getBasePath(); }
		public function purgeAudits() { $this->localService->purgeAudits(); }
		public function getUser($token) { return $this->securityService->getUser($token); }
		public function isSetup($connection = NULL) { return $this->dataService->isSetup($connection); }
		public static function getHostIp($host) { return gethostbyname($host); }
		public function getModules() { return $this->dataService->getModules(); }
		public function getSessionHeaders() { return $this->dataService->getHandler()->getColumns("SELECT * FROM PRM_SESSION"); }
		public function runQuery($query) {
			if(strpos(strtolower($query),"delete") || strpos(strtolower($query), "truncate")) {
				return FALSE;
			}
			$this->auditMessage("Query", $query, "APPLICATION");
			return $this->dataService->runQuery($query); 
		}

		public function getFormFields($viewModel) { return PRMFormService::getFormFields($viewModel); }
	}
?>
