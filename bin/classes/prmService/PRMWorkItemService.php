<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmCommon/prmcommon_all.php");
	class PRMWorkItemService {	
		private static $instance = NULL;
		private $workItemService = NULL;
		private $root = NULL;
		private $dataService = NULL;
		private $configService = NULL;
		private function __construct() {
			$this->dataService = PRMDataService::getInstance();
			$this->configService =  PRMConfigurationService::getInstance();
			$this->refresh();
		}
		public static function getInstance() {
			if(PRMWorkItemService::$instance == NULL) {
				PRMWorkItemService::$instance = new PRMWorkItemService();
			}
			return PRMWorkItemService::$instance;
		}
		public function getIterations() { 
			$result = array();
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_ITERATION");
			foreach ($rawResult->getRows() as $row) {
				$tempIteration = new PRMGeneralItem();
				$tempIteration->setId($row->getColumn("PRM_ITERATION_ID")->getValue());
				$tempIteration->setName($row->getColumn("PRM_ITERATION_NAME")->getValue());
				array_push($result, $tempIteration);
			}
			return $result;
		}
		public function getWorkItemTypeColor($id) {
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_WORKITEM_TYPE WHERE PRM_ITEM_TYPE_ID = '".$id."'");
			if(sizeof($rawResult->getRows()) == 0) {
				return "";
			}
			return $rawResult->getRows()[0]->getColumn("PRM_ITEM_TYPE_COLOR")->getValue();
		}
		public function getWorkItemTypes() {
			$result = array();
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_WORKITEM_TYPE");
			foreach ($rawResult->getRows() as $row) {
				$tempItem = new PRMGeneralItem();
				$tempItem->setId($row->getColumn("PRM_ITEM_TYPE_ID")->getValue());
				$tempItem->setName($row->getColumn("PRM_ITEM_TYPE_NAME")->getValue());
				array_push($result, $tempItem);
			}
			return $result;
		}
		private function findTable($field) {
			$tables = [ "PRM_ITEM", "PRM_WORKITEM", "PRM_ITEM_FIELD" ];
			foreach ($tables as $table) {
				$rawResult = $this->dataService->getHandler()->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->configService->__PRM_DATABASE_NAME__."' AND COLUMN_NAME = '".$field."' AND TABLE_NAME = '".$table."'");
				if (sizeof($rawResult->getRows()) == 1) {
					return $table;
				}
			}
			return "";
		}
		public function getWorkItemTypeName($id) {
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_WORKITEM_TYPE WHERE PRM_ITEM_TYPE_ID = '".$id."'");
			if(sizeof($rawResult->getRows()) == 0) {
				return "";
			}
			return $rawResult->getRows()[0]->getColumn("PRM_ITEM_TYPE_NAME")->getValue();
		}
		public function addWorkItem($form, $type) {
			$newItem = new PRMGeneralItem();
			$newItem->setName($form['PRM_ITEM_NAME']);
			$newItem->setUser(PRMSessionService::getInstance()->getUser()->getId());
			$newItem->setId(PRMObjectService::getInstance()->addObject($newItem));
			if ($newItem->getId() == NULL || $newItem->getId() == "") {
				return False;
			}
			$tempResult = $this->dataService->getHandler()->runQuery("INSERT INTO PRM_WORKITEM (PRM_ITEM_ID, PRM_WORKITEM_TYPE_ID) VALUES ('".$newItem->getId()."', '".$form['PRM_WORKITEM_TYPE_ID']."')");
			if (!$tempResult){ return False; }
			return $this->updateWorkItem($newItem->getId(), $form);
		}
		public function updateWorkItem($id, $form) {
			PRMObjectService::getInstance()->markDirty($id);
			foreach ($form as $key=>$field) {
				$sql = "";
				if ($field != "Update") {
					if ($field == "PRM_USER_ID") {
						$sql = "UPDATE PRM_WORKITEM SET PRM_USER_ID = '" . $field . "' WHERE PRM_ITEM_ID = '" . $id . "'";
					}
					else {
						$sql = "UPDATE " . $this->findTable($key) . " SET " . $key . " = '" . $field . "' WHERE PRM_ITEM_ID = '" . $id . "'";
					}
					if (! $this->dataService->getHandler()->runQuery($sql)) {
						return False;
					}
				}
			}
			PRMObjectService::getInstance()->markDirty($id, False);		
			return True;
		}
		public static function encodeText($text) {
			$text = str_replace("'", "''", $text);
			$text = str_replace('"', "\"", $text);
			return $text;
		}
		public function addComment($id, $comment) {
			$sql = "INSERT INTO PRM_WORKITEM_COMMENT (PRM_ITEM_ID, PRM_USER_ID, WORKITEM_COMMENT_TEXT, LAST_UPDATED_DATE) VALUES (";
			$sql = $sql . "'".$id."', '".PRMSessionService::getInstance()->getUser()->getId()."','".PRMWorkItemService::encodeText($comment)."',CURRENT_TIMESTAMP";
			$sql .= ")";
			return $this->dataService->getHandler()->runQuery($sql);
		}
		public function getComments($id) {
			$result = array();
			$rawResults = $this->dataService->getHandler()->query("SELECT * FROM PRM_WORKITEM_COMMENT WHERE PRM_ITEM_ID = '".$id."' ORDER BY LAST_UPDATED_DATE DESC");
			foreach ($rawResults->getRows() as $row) {
				$tempItem = new PRMPropertyItem();
				$tempItem->setId($row->getColumn("WORKITEM_COMMENT_ID")->getValue());
				$tempItem->setPId($row->getColumn("PRM_ITEM_ID")->getValue());
				$tempItem->setValue($row->getColumn("WORKITEM_COMMENT_TEXT")->getValue());
				$tempItem->setUser($row->getColumn("PRM_USER_ID")->getValue());
				$tempItem->setLastUpdatedDate($row->getColumn("LAST_UPDATED_DATE")->getValue());
				array_push($result, $tempItem);
			}
			return $result;
		}
		public function getRelationshipTypes() {
			$result = array();
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_RELATIONSHIP_TYPE");
			foreach($rawResult->getRows() as $type) {
				$tempItem = new PRMGeneralItem();
				$tempItem->setId($type->getColumn("PRM_RELATIONSHIP_TYPE_ID")->getValue());
				$tempItem->setName($type->getColumn("PRM_RELATIONSHIP_TYPE_NAME")->getValue());
				array_push($result, $tempItem);
			}
			return $result;
		}
		public function getRelatedItems($item, $type) {
			$result = array();
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_WORKITEM_RELATION WHERE PRM_RELATIONSHIP_TYPE_ID = '".$type->getId()."'");
			foreach($rawResult->getRows() as $item2) {
				array_push($result, $this->getWorkItem($item2->getColumn("PRM_ITEM_ID2")->getValue()));
			}
			return $result;
		}
		public function removeRelationship($id1, $id2) {
			return $this->dataService->getHandler()->runQuery("DELETE FROM PRM_WORKITEM_RELATION WHERE PRM_ITEM_ID = '".$id1."' AND PRM_ITEM_ID2 = '".$id2."'");
		}
		public function isUniqueRelation($id1, $id2) {
			$rawResult = $this->dataService->getHandler()->query("SELECT 1 FROM PRM_WORKITEM_RELATION WHERE PRM_ITEM_ID='".$id1."' AND PRM_ITEM_ID2='".$id2."'");
			return (sizeof($rawResult->getRows()) == 0);
		}
		public function addRelation($item, $id, $type) {
			if ($this->getWorkItem($id) == NULL) {
				return False;
			}
			if (!$this->isUniqueRelation($item->getId(), $id)) {
				return False;
			}
			if($item->getId() == $id) {
				return False;
			}
			return $this->dataService->getHandler()->runQuery("INSERT INTO PRM_WORKITEM_RELATION (PRM_ITEM_ID, PRM_ITEM_ID2, PRM_RELATIONSHIP_TYPE_ID) VALUES ('".$item->getId()."','".$id."','".$type->getId()."')");
		}
		private function getChildren($id) {
			$result = array();
			$rawRecords = $this->dataService->getHandler()->query("SELECT * FROM PRM_QUERY WHERE PRM_QUERY_PID = '".$id."'");
			foreach($rawRecords->getRows() as $row) {
				$newNode = new PRMTreeNode($row->getColumn("PRM_QUERY_ID")->getValue(),
											$row->getColumn("PRM_QUERY_NAME")->getValue(), 
											($row->getColumn("PRM_QUERY_ISFOLDER")->getValue() == "1" ? "Folder" : "Query"), 
											$row->getColumn("PRM_QUERY_ISSHARED")->getValue());  	
				if($newNode->getIsFolder()){
					foreach($this->getChildren($newNode->getId()) as $child){ 
						$newNode->addChild($child);
					}
				}
				array_push($result, $newNode);
			}
			return $result;
		}
		private function buildRoot() {
			$this->root = new PRMTreeNode();
			foreach($this->getChildren($this->root->getId()) as $child) {
				$this->root->addChild($child);
			}
		}
		public function removeWorkItem($id) {
			return $this->dataService->getHandler()->runQuery("UPDATE PRM_ITEM SET PRM_ITEM_STATUS = '".PRM_STATUS::REMOVED_STATUS."' WHERE PRM_ITEM_ID = '".$id."'"); 
		}
		public function lookupItem($id, $node=null) {
			if($node == null) {
				$node = $this->root;
			}
			foreach($node->getChildren() as $child) {
				if($child->getId() == $id) {
					return $child;
				}
				else if($child->getIsFolder() && $this->lookupItem($id, $child) != NULL) {
					return $this->lookupItem($id, $child);
				}
			}
			return null;
		}
		public function getWorkItem($id) {
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_WORKITEM WHERE PRM_ITEM_ID = '".$id."'");
			if (sizeof($rawResult->getRows()) == 0) {
				return NULL;
			}
			$tempWorkItem = new PRMWorkItem();
			$row = $rawResult->getRows()[0];
			$tempWorkItem->setId($row->getColumn("PRM_ITEM_ID")->getValue());
			$propertyNames = $this->getRawColumns($row->getColumn("PRM_WORKITEM_TYPE_ID")->getValue());
			foreach ($propertyNames as $propertyName) {
				$tempProperty = new PRMPropertyItem();
				$tempProperty->setName($propertyName);
				$value = $this->getRawColumnValue($id, $propertyName);
				$tempProperty->setValue($value);
				$tempWorkItem->setProperty($tempProperty);
				if ($propertyName == "PRM_ITEM_STATE") {
					$tempWorkItem->setState($value);
				}
				if ($propertyName == "PRM_ITEM_STATUS") {
					$tempWorkItem->setStatus($value);
				}
				if ($propertyName == "PRM_ITEM_ISDIRTY") {
					$tempWorkItem->setIsDirty(($value == "1" ? True : False));
				}
				if ($propertyName == "PRM_ITEM_ID") {	
					$tempWorkItem->setId($value);
				}
				if ($propertyName == "LAST_UPDATED_DATE") {
					$tempWorkItem->setLastUpdatedDate($value);
				}
				if ($propertyName == "PRM_ITEM_NAME") {
					$tempWorkItem->setName($value);
				}
				if ($propertyName == "PRM_ITEM_LABEL") {
					$tempWorkItem->setLabel($value);
				}
				if ($propertyName == "PRM_USER_ID") {
					$tempWorkItem->setUser($value);
				}
				if ($propertyName == "PRM_WORKITEM_DESCRIPTION") { 
					$tempWorkItem->setDescription($value);
				}
			}
			return $tempWorkItem;
		}
		public function refresh() {
			$this->buildRoot();
			return $this->root;
		}
		public function getRoot() {
			return $this->root;
		}
		public function updateItem($item) {
			$sql = "UPDATE PRM_QUERY SET PRM_QUERY_ISSHARED = '". $item->getIsShared()."', PRM_QUERY_NAME='".$item->getName()."' WHERE PRM_QUERY_ID='".$item->getId()."'";
			return $this->dataService->getHandler()->runQuery($sql);
		}
		public function addItem($item, $parent) {
			$sql = "INSERT INTO PRM_QUERY (PRM_QUERY_ID, PRM_QUERY_NAME, PRM_QUERY_ISFOLDER, PRM_QUERY_ISSHARED, PRM_USER_ID) VALUES (";
			$sql = $sql . "'".$parent->getId()."','".$item->getName()."','".$item->getIsFolder()."','".$item->getIsShared()."','".PRMSessionService::getInstance()->getUser()->getId()."'";
			$sql = $sql . ")";
			$this->dataService->getHandler()->runQuery($sql);
			$result = $this->dataService->getHandler()->query("SELECT PRM_QUERY_ID FROM PRM_QUERY WHERE PRM_QUERY_PID = '".$parent->getId()."' AND PRM_QUERY_NAME = '".$item->getName()."' ORDER BY PRM_QUERY_ID DESC");
			return $result->getRows()[0]->getColumns()[0]->getValue();
		}
		public function removeQuery($query) {
			if (! $this->dataService->getHandler()->runQuery("DELETE FROM PRM_QUERY_FILTER WHERE PRM_QUERY_ID = '".$query->getId()."'")) {
				return False;
			}
			if (! $this->dataService->getHandler()->runQuery("DELETE FROM PRM_QUERY_COLUMNS WHERE PRM_QUERY_ID = '".$query->getId()."'")) {
				return False;
			}
			return $this->dataService->getHandler()->runQuery("DELETE FROM PRM_QUERY WHERE PRM_QUERY_ID = '".$query->getId()."'");
		}
		public function removeFilter($filterId) {
			return $this->dataService->getHandler()->runQuery("DELETE FROM PRM_QUERY_FILTER WHERE PRM_QUERY_FILTER_ID = '".$filterId."'");
		}
		public function getColumns() {
			$result = array();
			$results = $this->dataService->getHandler()->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->configService->__PRM_DATABASE_NAME__."' AND (TABLE_NAME = 'PRM_WORKITEM' OR TABLE_NAME = 'PRM_ITEM')");																						   
			array_push($result, new PRMGeneralItem("", "FIELD"));
			foreach($results->getRows() as $column) { 
				$tempItem = new PRMGeneralItem($column->getColumn("COLUMN_NAME")->getValue(), str_replace("_", " ", $column->getColumn("COLUMN_NAME")->getValue()));
				array_push($result, $tempItem);																																																																 
			}
			$results = $this->dataService->getHandler()->query("SELECT PRM_ITEM_FIELD_NAME FROM PRM_ITEM_FIELD a JOIN PRM_ITEM_VALUE b ON b.PRM_ITEM_FIELD_ID = a.PRM_ITEM_FIELD_ID");																																							  
			foreach($results->getRows() as $column) {																																																																							   
				$tempItem = new PRMGeneralItem($column->getColumn("PRM_ITEM_FIELD_NAME")->getValue(), $column->getColumn("PRM_ITEM_FIELD_NAME")->getValue());
				array_push($result, $tempItem);
			}																 
			return $result;
		}
		public function getRawColumns($typeId=0) {
			$result = array();
			$results = $this->dataService->getHandler()->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->configService->__PRM_DATABASE_NAME__."' AND (TABLE_NAME = 'PRM_WORKITEM' OR TABLE_NAME = 'PRM_ITEM')");
			foreach($results->getRows() as $column) {
				if (! in_array($column->getColumn("COLUMN_NAME")->getValue(), $result)) {
					array_push($result, $column->getColumn("COLUMN_NAME")->getValue());
				}
			}
		   	$results = $this->dataService->getHandler()->query("SELECT PRM_ITEM_FIELD_NAME FROM PRM_ITEM_FIELD a JOIN PRM_ITEM_VALUE b ON b.PRM_ITEM_FIELD_ID = a.PRM_ITEM_FIELD_ID");
			if ($typeId != 0) {
				$results = $this->dataService->getHandler()->query("SELECT PRM_ITEM_FIELD_NAME FROM PRM_ITEM_FIELD a JOIN PRM_ITEM_VALUE b ON b.PRM_ITEM_FIELD_ID = a.PRM_ITEM_FIELD_ID WHERE PRM_WORKITEM_TYPE_ID = '".$typeId."'");
			}
			foreach($results->getRows() as $column) {
				if (! in_array($column->getColumn("PRM_ITEM_FIELD_NAME")->getValue(), $result)) {
					array_push($result, $column->getColumn("PRM_ITEM_FIELD_NAME")->getValue());
				}
			}
			return $result;
		}
		public function addFilter($item, $parent) {
			$sql = "INSERT INTO PRM_QUERY_FILTER (PRM_QUERY_ID, PRM_QUERY_FIELD, PRM_QUERY_CONDITION, PRM_QUERY_VALUE, PRM_QUERY_JOIN_CONDITION) VALUES ('".$parent->getId()."'";
			$sql = $sql . ", '".$item->getField()."','".$item->getCondition()."','".$item->getValue()."','".$item->getJoinCondition()."'";
			$sql .= ")";
			$this->dataService->getHandler()->runQuery($sql);
			$result = $this->dataService->getHandler()->query("SELECT PRM_QUERY_FILTER_ID FROM PRM_QUERY_FILTER WHERE PRM_QUERY_ID = '".$parent->getId()."' ORDER BY PRM_QUERY_FILTER_ID DESC");
			if (sizeof($result->getRows()) == 0) {
				return False;
			}
			return $result->getRows()[0]->getColumns()[0]->getValue();
		}
		public function updateFilter($item) {
			$sql = "UPDATE PRM_QUERY_FILTER SET PRM_QUERY_FIELD='".$item->getField()."', PRM_QUERY_CONDITION='";
			$sql = $sql . $item->getCondition() . "',PRM_QUERY_VALUE='".$item->getValue()."',PRM_QUERY_JOIN_CONDITION='".$item->getJoinCondition()."'";
			$sql .= (" WHERE PRM_QUERY_FILTER_ID = '".$item->getId()."'");
			return $this->dataService->getHandler()->runQuery($sql);
		}
		public function addColumn($queryId, $name) {
			$result = $this->dataService->getHandler()->query("SELECT 1 FROM PRM_QUERY_COLUMN WHERE PRM_QUERY_ID = '".$queryId."' AND COLUMN_NAME = '".$name."'");
			if(sizeof($result->getRows()) > 0) {
				return False;
			}
			return $this->dataService->getHandler()->runQuery("INSERT INTO PRM_QUERY_COLUMN (PRM_QUERY_ID, COLUMN_NAME, COLUMN_ORDER) VALUES ('".$queryId."','".$name."', '0')");
		}
		public function removeColumn($queryId, $name) {
			return $this->dataService->getHandler()->runQuery("DELETE FROM PRM_QUERY_COLUMN WHERE PRM_QUERY_ID = '".$queryId."' AND COLUMN_NAME = '".$name."'");
		}
		public function isExtendedProperty($columnName) {
			$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->configService->__PRM_DATABASE_NAME__."' AND (TABLE_NAME = 'PRM_WORKITEM' OR TABLE_NAME = 'PRM_ITEM') AND COLUMN_NAME = '".$columnName."'";
			$results = $this->dataService->getHandler()->query($sql);
			if(sizeof($results->getRows()) > 0) {
				return False;
			}
			return True;
		}
		public function buildQuery($queryId) {
			$query = "SELECT * FROM PRM_ITEM a1 RIGHT JOIN PRM_WORKITEM a ON a1.PRM_ITEM_ID = a.PRM_ITEM_ID LEFT JOIN PRM_ITEM_VALUE b ON b.PRM_ITEM_ID = a.PRM_ITEM_ID LEFT JOIN PRM_ITEM_FIELD c ON b.PRM_ITEM_FIELD_ID = c.PRM_ITEM_FIELD_ID ";
			$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_QUERY_FILTER  WHERE PRM_QUERY_ID = '".$queryId."'");
			if(sizeof($results->getRows()) > 0) {
				$query .= " WHERE ";
				$i = 0;
				foreach($results->getRows() as $column) {
					if($i > 0) {
						$query = $query . " " . $column->getColumn("PRM_QUERY_JOIN_CONDITION")->getValue();
					}
					if(!$this->isExtendedProperty($column->getColumn("PRM_QUERY_FIELD")->getValue())) {
						$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->configService->__PRM_DATABASE_NAME__."' AND TABLE_NAME = 'PRM_WORKITEM' AND COLUMN_NAME = '".$column->getColumn("PRM_QUERY_FIELD")->getValue()."'";
						if(sizeof($this->dataService->getHandler()->query($sql)->getRows()) == 1) {
							$query = $query . "a." . $column->getColumn("PRM_QUERY_FIELD")->getValue() . $column->getColumn("PRM_QUERY_CONDITION")->getValue() . $column->getColumn("PRM_QUERY_VALUE")->getValue();
						}
						else {
							$query = $query . "a1." . $column->getColumn("PRM_QUERY_FIELD")->getValue() . $column->getColumn("PRM_QUERY_CONDITION")->getValue() . $column->getColumn("PRM_QUERY_VALUE")->getValue();
						}
					}
					else {
						$query = $query . "(c.PRM_ITEM_FIELD_NAME = ' " . $column->getColumn("PRM_QUERY_FIELD")->getValue() . "' AND b.PRM_ITEM_FIELD_VALUE " . $column->getColumn("PRM_QUERY_CONDITION")->getValue() . $column->getColumn("PRM_QUERY_VALUE")->getValue() .  ")";
					}
					$i++;
				}
			}
			return $query;
		}
		public function getColumnHeaders($queryId) {
			$result = array();
			$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_QUERY_COLUMN WHERE PRM_QUERY_ID = '".$queryId."'");
			if(sizeof($results->getRows()) == 0) {
				$results = $this->dataService->getHandler()->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->configService->__PRM_DATABASE_NAME__."' AND (TABLE_NAME = 'PRM_WORKITEM' OR TABLE_NAME = 'PRM_ITEM')");
				foreach($results->getRows() as $column) {
					if (!in_array($column->getColumn("COLUMN_NAME")->getValue(), $result) && $column->getColumn("COLUMN_NAME")->getValue() != "PRM_ITEM_ID") {
						array_push($result, $column->getColumn("COLUMN_NAME")->getValue());
					}
				}
				$results = $this->dataService->getHandler()->query("SELECT PRM_ITEM_FIELD_NAME FROM PRM_ITEM_FIELD a JOIN PRM_ITEM_VALUE b ON b.PRM_ITEM_FIELD_ID = a.PRM_ITEM_FIELD_ID");
				foreach($results->getRows() as $column) {
					if (! in_array($column->getColumn("COLUMN_NAME")->getValue(), $result) && $column->getColumn("COLUMN_NAME")->getValue() != "PRM_ITEM_ID") {
						array_push($result, $column->getColumn("PRM_ITEM_FIELD_NAME")->getValue());
					}
				}
			}
			else {
				foreach($results->getRows() as $column) {
					if (! in_array($column->getColumn("COLUMN_NAME")->getValue(), $result) && $column->getColumn("COLUMN_NAME")->getValue() != "PRM_ITEM_ID") {
						array_push($result, $column->getColumn("COLUMN_NAME")->getValue());
					}
				}
			}
			return $result;
		}
		public function getRawColumnValue($id, $columnName) {
			if(!$this->isExtendedProperty($columnName)) {
				return $this->dataService->getHandler()->query("SELECT * FROM PRM_ITEM a RIGHT JOIN PRM_WORKITEM b ON a.PRM_ITEM_ID = b.PRM_ITEM_ID WHERE a.PRM_ITEM_ID = '".$id."'")->getRows()[0]->getColumn($columnName)->getValue();
			}
			else {
				$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_ITEM_VALUE a JOIN PRM_ITEM_FIELD b on a.PRM_ITEM_FIELD_ID = b.PRM_ITEM_FIELD_ID WHERE PRM_ITEM_ID = '".$id."' AND PRM_ITEM_FIELD_NAME = '".$columnName."'");
				if(sizeof($results->getRows()) == 1) {
					return $results->getRows()[0]->getColumn("PRM_ITEM_VALUE")->getValue();
				}
			}
		}
		public function getColumnValue($queryId, $columnName, $id) {
			if(!$this->isExtendedProperty($columnName)) {
				return $this->dataService->getHandler()->query("SELECT * FROM PRM_ITEM a RIGHT JOIN PRM_WORKITEM b ON a.PRM_ITEM_ID = b.PRM_ITEM_ID WHERE a.PRM_ITEM_ID = '".$id."'")->getRows()[0]->getColumn($columnName)->getValue();
			}
			else {
				$results = $this->dataService->getHandler()->query("SELECT * FROM PRM_ITEM_VALUE a JOIN PRM_ITEM_FIELD b on a.PRM_ITEM_FIELD_ID = b.PRM_ITEM_FIELD_ID WHERE PRM_ITEM_ID = '".$id."' AND PRM_ITEM_FIELD_NAME = '".$columnName."'");
				if(sizeof($results->getRows()) == 1) {
					return $results->getRows()[0]->getColumn("PRM_ITEM_VALUE")->getValue();
				}
			}
		}
		public function columnListed($name, $queryId) {
			$result = (sizeof($this->dataService->getHandler()->query("SELECT 1 FROM PRM_QUERY_COLUMN WHERE PRM_QUERY_ID = '" . $queryId . "' AND COLUMN_NAME = '".$name."'")->getRows()));
			if ($result > 0) {
				return boolval(True);
			}
			return boolval(False);
		}
		public function getFilterColumns($filterId) {
		}
	}
?>
