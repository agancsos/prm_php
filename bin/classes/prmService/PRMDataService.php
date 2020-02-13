<?php
	ini_set('max_execution_time', 300);
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("{$__ROOT_FROM_PAGE__}/classes/SR.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmCommon/prmcommon_all.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmData/prmdata_all.php");
	
	class PRMDataService {
		private static $instance = NULL;
		private $localService = NULL;
		private $handler = NULL;
		private $configService = NULL;

		private function __construct() {
			global $__ROOT_FROM_PAGE__;	
			$this->localService = PRMLocalService::getInstance();
			$this->configService = PRMConfigurationService::getInstance();
		   	$this->handler = ConnectionFactory::createConnection($this->configService->__PRM_DATABASE_FAMILY__);
			$this->handler->setName($this->configService->__PRM_DATABASE_NAME__);
			$this->handler->setDatabaseUser($this->configService->__PRM_DATABASE_USER__);
			$this->handler->setDatabasePass($this->configService->__PRM_DATABASE_PASS__);
			$this->handler->setDatabaseHost($this->configService->__PRM_DATABASE_HOST__);
			$this->handler->setDatabasePort(intval($this->configService->__PRM_DATABASE_PORT__));
		}

		public function getWorkItemTypes() {
			$result = array();
			$rawResult = $this->handler->query("SELECT * FROM PRM_WORKITEM_TYPE");
			foreach ($rawResult->getRows() as $row) {
				$tempItem = new PRMGeneralItem();
				$tempItem->setId($row->getColumn("PRM_ITEM_TYPE_ID")->getValue());
				$tempItem->setName($row->getColumn("PRM_ITEM_TYPE_NAME")->getValue());
				array_push($result, $tempItem);
			}
			return $result;
		}

		public function addWorkItemField($item, $type) { 
			$sql = "INSERT INTO PRM_ITEM_FIELD (PRM_ITEM_FIELD_TYPE, PRM_ITEM_FIELD_NAME, PRM_ITEM_FIELD_LABEL, WORK_ITEM_TYPE_ID) VALUES (";
			$sql = $sql . "'".$type."','".$item->getName()."','".$item->getName()."','".$item->getProperty("PRM_ITEM_TYPE_ID")->getValue()."'";
			$sql .= ")";
			return $this->handler->runQuery($sql);
		}

		public function addWorkItemType($label, $color) {
			$sql = "INSERT INTO PRM_WORKITEM_TYPE (PRM_ITEM_TYPE_NAME, WORKITEM_TYPE_LABEL, PRM_ITEM_TYPE_COLOR) VALUES (";
			$sql = $sql . "'" . $label."','".$label."','".$color."'";
			$sql .= ")";
			return $this->handler->runQuery($sql);
		}

		public static function getInstance() {
			if(PRMDataService::$instance == NULL) {
				PRMDataService::$instance = new PRMDataService();
			}
			return PRMDataService::$instance;
		}

		public function getModules() {
			$result = array();
			$results = $this->handler->query("SELECT * FROM PRM_ENABLED_MODULE ORDER BY PRM_MODULE_NAME ASC");
			foreach($results->getRows() as $row) {
				$tempObject = new PRMModule();
				$tempObject->setId($row->getColumns()[0]->getValue());
				$tempObject->setName($row->getColumns()[1]->getValue());
				array_push($result, $tempObject);
			}
			return $result;
		}

		public function deleteModule($module) {
			return $this->handler->runQuery("DELETE FROM PRM_ENABLED_MODULE WHERE PRM_MODULE_ID = '". $module->getId()."'");
		}

		public function addModule($label) {
			return $this->handler->runQuery("INSERT INTO PRM_ENABLED_MODULE (PRM_MODULE_NAME) VALUES ('".$label."')");
		}


		public function addStatus($label) {
			return $this->handler->runQuery("INSERT INTO PRM_STATUS (PRM_STATUS_VALUE) VALUES ('".$label."')");
		}

		public function updateStatus($status) {
			return $this->handler->runQuery("UPDATE PRM_STATUS SET PRM_STATUS_VALUE = '".$status->getName()."' WHERE PRM_STATUS_ID = '". $status->getId()."'");
		}

		public function createSchema($connection) {
			$this->handler = $connection;
			global $__ROOT_FROM_PAGE__;
			try {
				// Create temporary connection to create the database
				$adminConnection = ConnectionFactory::createConnection($connection->getProviderName());
				$adminConnection->setDatabasePort($connection->getDatabasePort());
				$adminConnection->setDatabaseHost($connection->getDatabaseHost());
				$adminConnection->setDatabaseService($connection->getDatabaseService());
				$adminConnection->setDatabaseUser($connection->getDbaUser());
				$adminConnection->setDatabasePass($connection->getDbaPass());
				$adminConnection->setName($adminConnection->getDbaDatabase());
				$propertyStore = new JsonPropertyStore($__ROOT_FROM_PAGE__."/classes/prmService/db/database.json");
				$packages = $propertyStore->getProperty("packages");
				$stage = 0;
				foreach($packages as $package) {
					if($stage == $package->stage) {
						printf("%s <br/>", $package->name);
						$queries = $package->queries;
						foreach($queries as $query) {
							if(strtolower($query->family) == strtolower($connection->getProviderName())){
								$checkQuery = str_replace("\$SCHEMA", $connection->getName(), $query->check->query);
								$result = "";
								if($stage > 0 && $checkQuery != "") {
									$result = $this->handler->query($checkQuery);
								}
								if(($checkQuery != "" && sizeof($result->getRows()) == $query->check->value) || $checkQuery == "") {
									$query2 = $query->text;
									$query2 = str_replace("\$SCHEMA", $connection->getName(), $query2);
									$query2 = str_replace("\$USER", $connection->getDatabaseUser(), $query2);
									$query2 = str_replace("\$PASS", $connection->getDatabasePass(), $query2);
									if($stage == 0) {
										if(!$adminConnection->runQuery($query2)) {
										   	$this->localService->auditMessage("Create schema", "Failed to run {$query->name} on {$query->family}", "DATA SERVICE");
									   	}
									}
									else {
										if($query2 != "" && !$this->handler->runQuery($query2)) {
											printf("Failed to run {$query->name} on {$query->family} <br/>");
											$this->localService->auditMessage("Create schema", "Failed to run {$query->name} on {$query->family}", "DATA SERVICE");
										}
									}
								}
							}
						}
					}
					$stage += 1;
				}
			}
			catch(Exception $e) { 
				printf("%s<br/>", $e->getMessage());
				return FALSE; 
			}
			return TRUE;
		}

		public function getSourceData($type) {
			$result = array();
			$table = $this->handler->query("SELECT * FROM PRM_{$type}");
			foreach($table->getRows() as $row) {
				$tempObject = new PRMGeneralItem();
				$tempObject->setID($row->getColumns()[0]->getValue());
				$tempObject->setName($row->getColumns()[1]->getValue());
				if ($type == "WORKITEM_TYPE") {
					$tempObject->setLastUpdatedBy($row->getColumn("PRM_ITEM_TYPE_COLOR")->getValue());
				}
				array_push($result, $tempObject);
			}
	  		if($type == "STATUS") {
			   	foreach(PRMUserStatus::getItterator() as $status) {
				   	$option = new PRMGeneralItem();
				   	$option->setName($status);
				   	$option->setCanDelete(False);
				   	$option->setId(PRMUserStatus::fromName($status));
				   	array_push($result, $option);
			   	}
				foreach(PRMKbStatus::getItterator() as $status) {
					$option = new PRMGeneralItem();
					$option->setName($status);
					$option->setCanDelete(False);
					$option->setId(PRMUserStatus::fromName($status));
					array_push($result, $option);
				}
				foreach(PRMStatus::getItterator() as $status) {
					$option = new PRMGeneralItem();
					$option->setName($status);
					$option->setCanDelete(False);
					$option->setId(PRMUserStatus::fromName($status));
					array_push($result, $option);
				}
			}
			if ($type == "WORKITEM_FIELD") {
				$rawResult = $this->handler->query("SELECT * FROM PRM_ITEM_FIELD");
				foreach ($rawResult->getRows() as $row) {
					$tempItem = new PRMGeneralItem();
					$tempItem->setId($row->getColumn("PRM_ITEM_FIELD_ID")->getValue());
					$tempItem->setName($row->getColumn("PRM_ITEM_FIELD_NAME")->getValue());
					if ($type == "WORKITEM_FIELD") {
                    	$tempItem->setLastUpdatedBy($row->getColumn("PRM_ITEM_FIELD_TYPE")->getValue());
                    	$tempItem->setLastUpdatedDate($row->getColumn("WORK_ITEM_TYPE_ID")->getValue());
                	}
					array_push($result, $tempItem);
				}
			}
			return $result;
		}

		public function updateSourceData($type, $item) {
			$nameColumn = "PRM_{$type}_VALUE";
			$columns = $this->handler->getColumns("SELECT * FROM PRM_{$type}");
			foreach($columns as $row) {
				if(strpos(strtolower($row), "name") && strtolower($row) != strtolower($nameColumn)) {
					$nameColumn = $row;
				}
			}
			$sql = "UPDATE PRM_{$type} SET {$nameColumn} = '".$item->getName()."' WHERE PRM_{$type}_ID = '".$item->getID()."'";
			$this->handler->runQuery($sql);
		}

		public function addSourceData($type, $item) {
			$nameColumn = "PRM_{$type}_VALUE";
			$columns = $this->handler->getColumns("SELECT * FROM PRM_{$type}");
			foreach($columns as $row) {
				if(strpos(strtolower($row), "name") && strtolower($row) != strtolower($nameColumn)) {
					$nameColumn = $row;
				}
			}
			$sql = "INSERT INTO PRM_{$type} ({$nameColumn}) VALUES ('".$item->getName()."')";
			$this->handler->runQuery($sql);
		}

		public function deleteSourceData($type, $item) {
			if ($type == "WORKITEM_FIELD") {
				$type = "ITEM_FIELD";
			}
			if ($type != "WORKITEM_TYPE") {
				$this->handler->runQuery("DELETE FROM PRM_{$type} WHERE PRM_{$type}_ID = '".$item->getId()."'");
			}
			else {	
			}
		}

		public function updateSchema($connection) {
			global $__ROOT_FROM_PAGE__;
			$this->handler = $connection;
			try {
				$propertyStore = new JsonPropertyStore($__ROOT_FROM_PAGE__."/classes/prmService/db/database.json");
				$packages = $propertyStore->getProperty("packages");
				$stage = 1;
				foreach($packages as $package) {
					if($stage == $package->stage) {
						printf("%s <br/>", $package->name);
						$queries = $package->queries;
						foreach($queries as $query) {
							if(strtolower($query->family) == strtolower($connection->getProviderName())){
								//printf("%s <br/>", $query->name);
								$checkQuery = str_replace("\$SCHEMA", $connection->getName(), $query->check->query);
								$result = $this->handler->query($checkQuery);
								if(($checkQuery != "" && sizeof($result) == $query->check->value) || $checkQuery == "") {
									if(!$this->handler->runQuery($query->text)) {
										$localService->auditMessage("Update schema", "Failed to run {$query->name} on {$query->family}", "DATA SERVICE");
									}
								}
							}
						}
					}
					$stage += 1;
				}
			}
			catch(Exception $e) { return FALSE; }
			return TRUE;
		}

		public function getUser($token) { 
			if($this->handler != NULL) {
				return $this->handler->query(SR::getUserQuery($token));
			}
			return new PRMUser(); 
		}

		private function tableExists($name) {
			$result = $this->handler->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . $this->configService->__PRM_DATABASE_NAME__ . "'");
			foreach($result->getRows() as $row) {
				if($row->getColumns()[0]->getValue() == $name) {
					return True;
				}
			}
			return False;
		}
		
		public function isSetup() {
			global $__ROOT_FROM_PAGE__;
			if($this->handler != NULL) {
				$result = $this->handler->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . $this->configService->__PRM_DATABASE_NAME__ . "'");
				$propertyStore = new JsonPropertyStore("{$__ROOT_FROM_PAGE__}/classes/prmService/db/database.json");
			   	$packages = $propertyStore->getProperty("packages");
				$createQueries = $packages[1]->queries;
				if($this->configService->__DEBUG__ == "1") {
					printf("DB: %d | JSON: %d", sizeof($result->getRows()), sizeof($createQueries));
				}
				if(sizeof($result->getRows()) == sizeof($createQueries)) {
					return TRUE;
				}
			}
			return FALSE;
		}

		public function export() {
			$exporter = new PRMExportItem($this);
			return $exporter->export();
		}

		public function import($path) {
			$store = new JsonPropertyStore($path);
			$index = 0;
			foreach($store->getKeys() as $key) {
				if($index > 1) {
					printf("%s <br/>",$key);
					$values = $store->getProperty($key);
					foreach($values as $value) {
						$this->handler->runQuery("INSERT INTO PRM_{$key} (PRM_{$key}_VALUE) VALUES ('".$value."')");
					}
				}
				$index++;
			}		
		}
		
		public function getHandler() { return $this->handler; }
	}
?>
