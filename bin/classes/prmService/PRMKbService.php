<?php
	class PRMKbService {
		private static $instance = NULL;
		private $dataService = NULL;
		private $objectService = NULL;

		private function __construct() {
			$this->dataService = PRMDataService::getInstance();
			$this->objectService = PRMObjectService::getInstance();
		}

		public static function getInstance() {
			if(PRMKbService::$instance == NULL) {
				PRMKbService::$instance = new PRMKbService();
			}
			return PRMKbService::$instance;
		}

		public function getArticlesById($id, $restricted=TRUE) {
			$result = array();
			$query = "SELECT * FROM PRM_ARTICLE a JOIN PRM_ITEM b ON a.PRM_ITEM_ID = b.PRM_ITEM_ID WHERE a.PRM_ITEM_ID='".$id."'";
			if($restricted === TRUE) {
				$query .= "  AND PRM_ARTICLE_STATE = '" . PRMKbState::ACTIVE_STATE . "' AND PRM_ARTICLE_STATUS = '" . PRMKbStatus::ACTIVE . "'";
			}
			$rawResults = $this->dataService->getHandler()->query($query);
			foreach($rawResults->getRows() as $row) {
				array_push($result, $this->objectService->getArticle($row->getColumn("PRM_ITEM_ID")->getValue()));
			}
			return $result;
		}

		public function getArticlesBySearch($keywords, $restricted=TRUE) {
			$result = array();
			$query = "SELECT * FROM PRM_ARTICLE a JOIN PRM_ITEM b ON a.PRM_ITEM_ID = b.PRM_ITEM_ID WHERE (PRM_ARTICLE_DESCRIPTION LIKE '%".$this->search."%' OR PRM_ARTICLE_TEXT LIKE '%".$this->search."%' OR PRM_ARTICLE_TITLE LIKE '%".$this->search."%')";
			if($restricted === TRUE) {
				$query .= " AND PRM_ARTICLE_STATE = '" . PRMKbState::ACTIVE_STATE . "' AND PRM_ARTICLE_STATUS = '" . PRMKbStatus::ACTIVE . "'";
			}
			$rawResults = $this->dataService->getHandler()->query($query);
			foreach($rawResults->getRows() as $row) {
				array_push($result, $this->objectService->getArticle($row->getColumns("PRM_ITEM_ID")->getValue()));
			}
			return $result;
		}

		public function getArticles($restricted=TRUE) {
			$result = array();
			$query = "SELECT * FROM PRM_ARTICLE a JOIN PRM_ITEM b ON a.PRM_ITEM_ID = b.PRM_ITEM_ID";
			if($restricted === TRUE) {
				$query .= " WHERE PRM_ARTICLE_STATE = '" . PRMKbState::ACTIVE_STATE . "' AND PRM_ARTICLE_STATUS = '" . PRMKbStatus::ACTIVE . "'";
			}
			$rawResults = $this->dataService->getHandler()->query($query);
			foreach($rawResults->getRows() as $row) {
				array_push($result, $this->objectService->getArticle($row->getColumn("PRM_ITEM_ID")->getValue()));
			}
			return $result;
		}

		public function addArticle($article) {
			$id = $this->objectService->addObject($article);
			$article->setId($id);
			$query = "INSERT INTO PRM_ARTICLE (PRM_ITEM_ID, PRM_ARTICLE_TITLE, PRM_ARTICLE_DESCRIPTION, PRM_ARTICLE_TEXT, PRM_ARTICLE_ACCESS, PRM_ARTICLE_STATE, PRM_ARTICLE_STATUS) VALUES (";
			$query .= ("'".$article->getId()."', '".$article->getTitle()."','".$article->getDescription()."','".$article->getText()."','".$article->getAccess()."','".$article->getState()."','");
			$query .= ($article->getStatus()."'");
			$query .= ")";
			$this->dataService->getHandler()->runQuery($query);
		}

		public function removeArticle($article) {
		}

		public function updateArticle($article) {
			$sql = "UPDATE PRM_ARTICLE SET PRM_ARTICLE_TITLE='".$article->getTitle()."',";
			$sql .= " PRM_ARTICLE_DESCRIPTION='".$article->getDescription()."',PRM_ARTICLE_TEXT='".$article->getText()."',PRM_ARTICLE_ACCESS='".$article->getAccess()."',";
			$sql .= " PRM_ARTICLE_STATE='".$article->getState()."',PRM_ARTICLE_STATUS='".$article->getStatus()."'";
			$sql .= " WHERE PRM_ITEM_ID='".$article->getId()."'";
			if($this->dataService->getHandler()->runQuery($sql)) {
				$this->objectService->updateObject($article);
				return TRUE;
			}
			return FALSE;
		}
	}
?>
