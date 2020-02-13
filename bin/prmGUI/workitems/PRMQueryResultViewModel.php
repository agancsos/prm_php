<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMQueryResultViewModel extends PRMViewModel {
        private $selectionService = NULL;
        private $workItemService = NULL;
		private $actions = array();
		private $currentAction = null;
		private function addActions() {
			$this->actions['Editor'] = new PRMFilterEditorViewModel($this);
			$this->actions['Results'] = new PRMResultsViewModel($this);
			if(isset($_GET['op']) && array_key_exists($_GET['op'], $this->actions)) {
				$this->currentAction = $this->actions[$_GET['op']];
			}
			else {
				$this->currentAction = $this->actions['Results'];
			}
		}
        public function __construct($root = "./") {
            global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
            $this->service = PRMService::getInstance();
            $this->selectionService = PRMSelectionService::getInstance();
            $this->workItemService = PRMWorkItemService::getInstance();
			$this->addActions();
        }
        public function getName() { return "workitems"; }
        public function getTitle() { return "WorkItems"; }
        public function getIsSecure() { return TRUE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
            print("<h2>".SR::$__QUERY_HEADER__."</h2><hr>");
			if($this->selectionService->getSelectedNode()->getIsFolder()) {
				print(SR::$__NO_QUERY_SELECTED__);
			}
			else {
            	print("<form method='get' class='tree-tools'>");
				print("<input type='hidden' name='id' value='".($_GET['id'] != null ? $_GET['id'] : "0")."'/>");
				print("<input type='hidden' name='op' value='".(isset($_GET['op']) ? $_GET['op'] : "")."'/>");
            	print("<input type='hidden' name='id' value='".(isset($_GET['id']) ? $_GET['id'] : "")."'/>");			
            	foreach($this->actions as $action) {
                	print("<input type='submit' name='op' ");
					if($action->getName() == $this->currentAction->getName()) {
						print(" style='background-color: #3399cc;' ");
					}
					else {
						print(" style='background-color: black;' ");
					}
					print(" value='".$action->getName()."' title='".$action->getTitle()."'/>");
            	}
				print("</form>");
				$this->currentAction->load();
			}
        }
    }
?>
