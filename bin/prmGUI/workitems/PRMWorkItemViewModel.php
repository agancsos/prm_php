<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMWorkItemViewModel extends PRMViewModel {
        private $selectionService = NULL;
        private $workItemService = NULL;
		private $actions = array();
		private $currentAction = NULL;

		private function prepareActions() {
			$this->actions['Create'] = new PRMCreateWorkItemViewModel($this);
			$this->actions['Details'] = new PRMWorkItemDetailsViewModel($this);
			$this->actions['Links'] = new PRMWorkItemLinksViewModel($this);
			$this->actions['Attachments'] = new PRMWorkItemAttachmentsViewModel($this);
		}
        public function __construct($root = "./") {
            global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
            $this->service = PRMService::getInstance();
            $this->selectionService = PRMSelectionService::getInstance();
            $this->workItemService = PRMWorkItemService::getInstance();
            if (isset($_GET['workitem']) && $_GET['workitem'] != "") {
                $this->selectionService->setSelectedWorkItem($this->workItemService->getWorkItem($_GET['workitem']));
            }
			$this->prepareActions();
        }
        public function getName() { return "workitems"; }
        public function getTitle() { return "WorkItems"; }
        public function getIsSecure() { return TRUE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
            print("<h2>".SR::$__WORKITEM_HEADER__."</h2><hr>");
			if(!isset($_GET['tab']) || (isset($_GET['tab']) && !array_key_exists($_GET['tab'], $this->actions))){
               	$this->currentAction = $this->actions['Details'];
           	}
           	else if(isset($_GET['tab'])) {
               	$actionName = $_GET['tab'];
               	$this->currentAction = $this->actions["{$actionName}"];
           	}
           	print("<form method='get' class='tree-tools'>");
			print("<input type='hidden' name='op' value='".(isset($_GET['op']) ? $_GET['op'] : "")."'/>");
			print("<input type='hidden' name='id' value='".(isset($_GET['id']) ? $_GET['id'] : "")."'/>");
			print("<input type='hidden' name='workitem' value='".(isset($_GET['workitem']) ? $_GET['workitem'] : "")."'/>");
           	foreach($this->actions as $action) {
               	print("<input type='submit' style='width:".(1/sizeof($this->actions) * 100)."% !important; ");
               	if($action->getName() == $this->currentAction->getName()) {
                   	print("background-color:#3399cc");
               	}
				else {
					print("background-color:black");
				}
				print(" !important;' name='tab'");
               	print(" value='".$action->getName()."' title='".$action->getTitle()."'/>");
           	}
           	print("</form>");
			if ($this->selectionService->getSelectedWorkItem() != NULL || $this->currentAction->getName() == "Create") {
				$this->currentAction->load();
			}
			else {
               	print(SR::$__NO_WORKITEM_MESSAGE__);
           	}
        }
    }
?>
