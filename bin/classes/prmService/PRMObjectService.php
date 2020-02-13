<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmCommon/prmcommon_all.php");
	include_once("PRMDataService.php");
	class PRMObjectService {
		private $dataService = NULL;
		private static $instance = NULL;

		private function __construct() {
			$this->dataService = PRMDataService::getInstance();
		}
	
		public static function getInstance() {
			if(PRMObjectService::$instance == NULL) {
				PRMObjectService::$instance = new PRMObjectService();
			}
			return PRMObjectService::$instance;
		}

		public function markDirty($id, $dirty=True) {
			if ($dirty) {
				return $this->dataService->getHandler()->runQuery("UPDATE PRM_ITEM SET PRM_ITEM_ISDIRTY='1', LAST_UPDATED_DATE=current_timestamp,LAST_UPDATED_BY='".PRMSessionService::getInstance()->getUser()->getId()."' WHERE PRM_ITEM_ID = '".$id."'");
			}
			return $this->dataService->getHandler()->runQuery("UPDATE PRM_ITEM SET PRM_ITEM_ISDIRTY='0',LAST_UPDATED_DATE=current_timestamp,LAST_UPDATED_BY='".PRMSessionService::getInstance()->getUser()->getId()."' WHERE PRM_ITEM_ID = '".$id."'");
		}

		public function getStatusName($id) {
			if ($id > 999) {
				$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_STATUS WHERE PRM_STATUS_ID = '".$id."'");
				if (sizeof($rawResult->getRows()) == 0) {
					return "NA";
				}
				return $rawResult->getRows()[0]->getColumn("PRM_STATUS_VALUE")->getValue();
			}
			foreach(PRMStatus::getItterator() as $cursor) {
				if (PRMStatus::fromName($cursor) == PRMStatus::fromValue($id)) {
					return $cursor;
				}
			}
			return "NA";
		}

		public function objectExists($object) {
			return $this->recordExists("PRM_ITEM", "PRM_ITEM_ID", $object->getId());
		}

		private function recordExists($table, $field, $value) { 
			$result = $this->dataService->query("SELECT 1 FROM {$table} WHERE {$field} = '". $value . "'");
			if(sizeof($result->getRows()) > 0) { 
				return TRUE;
			}
			return FALSE;
		}

		public function addObject($object) {
			$sql = "INSERT INTO PRM_ITEM (PRM_ITEM_NAME, PRM_ITEM_LABEL,CREATED_DATE) VALUES ('" . $object->getName() . "', '" . $object->getLabel() . "',CURRENT_TIMESTAMP)";
			if($this->dataService->getHandler()->runQuery($sql) === True) {
				$rawResult = $this->dataService->getHandler()->query("SELECT PRM_ITEM_ID FROM PRM_ITEM WHERE PRM_ITEM_NAME = '" . $object->getName() . "' AND PRM_ITEM_LABEL = '" . $object->getLabel() . "'");
				if(sizeof($rawResult->getRows()) > 0) {
					return $rawResult->getRows()[0]->getColumns()[0]->getValue();
				}
			}
			return False;
		}

		public function removeObject($object) { 
			$this->dataService->getHandler()->runQuery("DELETE FROM PRM_ITEM WHERE PRM_ITEM_ID = '" . $object->getId() . "'");
			return $object;
		}

		public function getObjectLite($id) {
			$result = new PRMGeneralItem();
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_ITEM WHERE PRM_ITEM_ID = '".$id."'");
			if (sizeof($rawResult->getRows()) == 0) {
				return $result;
			}
			$row = $rawResult->getRows()[0];
			$result->setId($id);
			$result->setName($row->getColumn("PRM_ITEM_NAME")->getValue());
			$result->setCreatedDate($row->getColumn("CREATED_DATE")->getValue());
			$result->setUserId($row->getColumn("PRM_USER_ID")->getValue());
			$result->setLastUpdatedDate($row->getColumn("LAST_UPDATED_DATE")->getValue());
			$result->setLabel($row->getColumn("PRM_ITEM_LABEL")->getValue());
			$result->setState($row->getColumn("PRM_ITEM_STATE")->getValue());
			$result->setStatus($row->getColumn("PRM_ITEM_STATus")->getValue());
			$result->setIsDirty($row->getColumn("PRM_ITEM_ISDIRTY")->getValue());
			return $result;
		}

		public function getTypeItems($type) {
			$result = array();
			$records = $this->dataService->getHandler()->query("SELECT * FROM PRM_{$type}");
			foreach($records->getRows() as $row) {
				$item = new PRMGeneralItem();
				$item->setId($row->getColumns()[0]->getValue());				
				$item->setName($row->getColumns()[1]->getValue());
				$item->setLabel($row->getColumns()[2]->getValue());
				array_push($result, $item);
			}
			return $result;
		}

		public function updateObject($object) { 
			$sql = "UPDATE PRM_ITEM SET PRM_ITEM_NAME='".$object->getName()."',PRM_ITEM_LABEL='".$object->getLabel()."',LAST_UPDATED_DATE=CURRENT_TIMESTAMP WHERE PRM_ITEM_ID='".$object->getId()."'";
			$this->dataService->getHandler()->runQuery($sql);
		}

		public function getUser($id) {
			$result = new PRMUser();
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_USER WHERE PRM_USER_ID = '".$id."'");
			if(sizeof($rawResult->getRows()) > 0) {
				$row = $rawResult->getRows()[0];
				$result->setId($row->getColumns()[0]->getValue());
				$result->setName($row->getColumns()[2]->getValue());
				$result->setLabel($result->getName());
				$result->setFirst($row->getColumns()[1]->getValue());
				$result->setLast($row->getColumns()[2]->getValue());
				$result->setCountry($row->getColumns()[10]->getValue());
				$result->setCity($row->getColumns()[9]->getValue());
				$result->setState($row->getColumns()[8]->getValue());
				$result->setStatus($row->getColumns()[4]->getValue());
				$result->setIsSysAdmin($row->getColumns()[5]->getValue());
				$result->setRole($row->getColumns()[11]->getValue());
			}
			return $result;
		}

		public function getUsers() {
			$result = array();
			$rawResult = $this->dataService->getHandler()->query("SELECT PRM_USER_ID FROM PRM_USER ORDER BY PRM_USER_FIRST ASC, PRM_USER_LAST ASC");
			foreach($rawResult->getRows() as $row) {
				array_push($result, $this->getUser($row->getColumns()[0]->getValue()));
			}
			return $result;
		}

		public function getArticle($id) {
			$result = new PRMArticle();
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_ARTICLE a JOIN PRM_ITEM b ON a.PRM_ITEM_ID = b.PRM_ITEM_ID WHERE a.PRM_ITEM_ID = '".$id."'");
			if(sizeof($rawResult->getRows()) == 1) {
				$row = $rawResult->getRows()[0];
				$result->setId($row->getColumn("PRM_ITEM_ID")->getValue());
				$result->setTitle($row->getColumn("PRM_ARTICLE_TITLE")->getValue());
				$result->setDescription($row->getColumn("PRM_ARTICLE_DESCRIPTION")->getValue());
			  	$result->setText($row->getColumn("PRM_ARTICLE_TEXT")->getValue());
				$result->setAccess($row->getColumn("PRM_ARTICLE_ACCESS")->getValue());
				$result->setState($row->getColumn("PRM_ARTICLE_STATE")->getValue());
				$result->setStatus($row->getColumn("PRM_ARTICLE_STATUS")->getValue());
				$result->setCreatedDate($row->getColumn("CREATED_DATE")->getValue());																																																																  
				$result->setLastUpdatedDate($row->getColumn("LAST_UPDATED_DATE")->getValue());																																																																  
			}
			return $result;
		}

		public function getStatuses() {
			$result = array();
			$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_STATUS ORDER BY PRM_STATUS_VALUE ASC");
			foreach($results->getRows() as $row) {
				$tempObject = new PRMStatus();
				$tempObject->setId($row->getColumns()[0]->getValue());
				$tempObject->setName($row->getColumns()[1]->getValue());
				array_push($result, $tempObject);
			}
			return $result;
		}

		public function getStates() {
			$result = array();
			$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_STATE ORDER BY PRM_STATE_NAME ASC");
			foreach($results->getRows() as $row) {
				$tempObject = new PRMState();
				$tempObject->setId($row->getColumns()[0]->getValue());
				$tempObject->setName($row->getColumns()[1]->getValue());
				array_push($result, $tempObject);
			}
			return $result;
		}

		public function getCities() {
			$result = array();
			$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_CITY ORDER BY PRM_CITY_NAME ASC");
			foreach($results->getRows() as $row) {
				$tempObject = new PRMCity();
				$tempObject->setId($row->getColumns()[0]->getValue());
				$tempObject->setName($row->getColumns()[1]->getValue());
				array_push($result, $tempObject);
			}
			return $result;
		}

		public function getCountries() {
			$result = array();
			$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_COUNTRY ORDER BY PRM_COUNTRY_NAME ASC");
			foreach($results->getRows() as $row) {
				$tempObject = new PRMCountry();
				$tempObject->setId($row->getColumns()[0]->getValue());
				$tempObject->setName($row->getColumns()[1]->getValue());
				array_push($result, $tempObject);
			}
			return $result;
		}
	}
		
?>
