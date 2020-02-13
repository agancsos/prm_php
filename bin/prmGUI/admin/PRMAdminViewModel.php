<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMAdminViewModel extends PRMViewModel {
		private $dataService = NULL;
		private $connection = NULL;
		private $actions = array();
		private function addActions() {
            $this->actions["Instance"] = new PRMInstanceViewModel($this);
            $this->actions["Types"] = new PRMTypesViewModel($this);
            $this->actions["Users"] = new PRMUsersViewModel($this);
			$this->actions["Groups"] = new PRMGroupsViewModel($this);
			$this->actions["Teams"] = new PRMTeamsViewModel($this);
			$this->actions["Sessions"] = new PRMSessionsViewModel($this);
			$this->actions["Tables"] = new PRMTablesViewModel($this);
			$this->actions["Import"] = new PRMPortViewModel($this);
            $this->actions["Update"] = new PRMUpdateViewModel($this);
            $this->actions["Query"] = new PRMQueryViewModel($this);
		}
        public function __construct($root = "./") {
			global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
			$this->dataService = PRMDataService::getInstance();
            $this->service = PRMService::getInstance();
			$this->connection = $this->dataService->getHandler();
			$this->addActions();
        }
        public function getName() { return "admin"; }
        public function getTitle() { return "Admin"; }
        public function getIsSecure() { return TRUE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
            $this->printHeader();
            if(! $this->sessionService->shouldShowAdmin()){
                redirectPage("/");
            }
            if(!isset($_GET['op']) || (isset($_GET['op']) && !array_key_exists($_GET['op'], $this->actions))){
				$action = $this->actions['Instance']; 
            }
            else if(isset($_GET['op'])) {
                $actionName = $_GET['op'];
                $action = $this->actions["{$actionName}"];
            }
            print("<form method='get' id='page-tools'>");
			foreach($this->actions as $action2) {
				print("<input type='submit' name='op' ");
				if($action2->getName() == $action->getName()) {
					print(" style='background-color:#3399cc;' ");
				}
				print(" value='".$action2->getName()."' title='".$action2->getTitle()."'/>");
			}
            print("</form>");
            $action->load();
            $this->printFooter();
        }
		public function getDataService() { return $this->dataService; }
	}
?>
