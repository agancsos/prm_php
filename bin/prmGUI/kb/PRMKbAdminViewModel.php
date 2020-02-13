<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMKbAdminViewModel extends PRMViewModel {
        private $dataService = NULL;
        private $connection = NULL;
        private $actions = array();
        private function addActions() {
			$this->actions["Browse"] = new PRMKbBrowseViewModel($this);
			$this->actions["Add"] = new PRMKbAddViewModel($this);
			$this->actions['Edit'] = new PRMKbEditViewModel($this);
        }
        public function __construct($root = "./") {
            global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
            $this->dataService = PRMDataService::getInstance();
            $this->service = PRMService::getInstance();
            $this->connection = $this->dataService->getHandler();
            $this->addActions();
        }
        public function getName() { return "kbadmin"; }
        public function getTitle() { return "KB Admin"; }
        public function getIsSecure() { return TRUE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
            $this->printHeader();
            if(!isset($_GET['op'])) {
                $action = $this->actions["Browse"];
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
				if($action2->getName() == "Edit") {
					print(" disabled ");
				}
                print(" value='".$action2->getName()."'/>");
            }
            print("</form>");

			$action->load();

            $this->printFooter();
        }
    }
?>
