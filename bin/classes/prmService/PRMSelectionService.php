<?php
	class PRMSelectionService {	
		private static $instance = NULL;
		private $selectedQuery = NULL;
		private $selectedWorkitem = NULL;
		private $workItemService = NULL;
		private $registeredViews = array();
		private $selectedNode = NULL;
		private $selectedItem = NULL;
		private function __construct() {
			$this->workItemService = PRMWorkItemService::getInstance();
		}
		public static function getInstance() {
			if(PRMSelectionService::$instance == NULL) {
				PRMSelectionService::$instance = new PRMSelectionService();
			}
			return PRMSelectionService::$instance;
		}
		public function registerView($view) {
			foreach($this->registeredViews as $view2) {
				if($view->getName() == $view2->getName()) {
					return;
				}
			}
			array_push($this->registeredViews, $view);
		}
		public function unregisterView($view) {
			$index = 0;
			foreach($this->registeredViews as $view2) {
				if($view->getName() == $view2->getName()) {
					unset($array[$index]);
				}
				$index++;
			}
		}
		public function getRegisteredViews() { return $this->registeredViews; }
		public function setSelectedQuery($a) { $this->selectedQuery = $a; }
		public function getSelectedQuery() { return $this->selectedQuery; }
		public function setSelectedWorkitem($a) { $this->selectedWorkitem = $a; }
		public function getSelectedWorkitem() { return $this->selectedWorkitem; }
		public function setSelectedId($a) {
			$this->setSelectedNode($this->workItemService->lookupItem($a));
		}
		public function setSelectedNode($a) { $this->selectedNode = $a; }
		public function getSelectedNode() { return $this->selectedNode; }
		public function setSelectedItem($a) { $this->selectedItem = $a; }
		public function getSelectedItem() { return $this->selectedItem; }
	}
?>
