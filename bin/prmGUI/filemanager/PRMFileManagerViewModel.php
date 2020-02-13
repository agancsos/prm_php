<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMFileManagerViewModel extends PRMViewModel {
        private $actions = array();

        private function addActions() {
            $this->actions['Browse'] = new PRMBrowseFileViewModel($this);
			$this->actions['Add'] = new PRMAddFileViewModel($this);
        }
        public function __construct($root = "./") {
            parent::__construct($root);
            $this->service = PRMService::getInstance();
            $this->configService = PRMConfigurationService::getInstance();
            if(isset($_GET['s'])) {
                $this->search = $_GET['s'];
            }
            $this->addActions();
        }
        public function getName() { return "filemanager"; }
        public function getTitle() { return "File Manager"; }
        public function getIsSecure() { return FALSE; }
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
            print("<form method='get' id = 'page-tools'>");
            foreach($this->actions as $action2) {
                print("<input type='submit' name='op' ");
                if($action2->getName() == $action->getName()) {
                    print(" style='background-color:#3399cc;' ");
                }
                print(" value='".$action2->getName()."'/>");
            }
            print("</form>");
            print("<div ");
			if ($action->getName() == "Add") {
				print(" class='action-container'");
			}
			print(">");
            $action->load();
            print("</div>");
            $this->printFooter();
        }
    }
?>
