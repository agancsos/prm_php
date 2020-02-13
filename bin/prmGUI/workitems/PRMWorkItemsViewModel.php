<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMWorkItemsViewModel extends PRMViewModel {
		private $dataService = NULL;
		private $selectionService = NULL;
		private $workItemService = NULL;
		private $views = array();
		private function addViews() {
			$this->views["Properties"] = new PRMPropertiesViewModel();
			$this->views["Tree"] = new PRMTreeViewModel();
			$this->views["Results"] = new PRMQueryResultViewModel();
			$this->views["WorkItem"] = new PRMWorkItemViewModel();
		}
		private function registerViews(){
			foreach($this->views as $view) {
				$this->selectionService->registerView($view);
			}
		}
        public function __construct($root = "./") {
			global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
            $this->service = PRMService::getInstance();
			$this->selectionService = PRMSelectionService::getInstance();
			$this->workItemService = PRMWorkItemService::getInstance();
			$this->addViews();
			$this->registerViews();
        }
        public function getName() { return "workitems"; }
        public function getTitle() { return "WorkItems"; }
        public function getIsSecure() { return TRUE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
            $this->printHeader();
            if(! $this->sessionService->shouldShowAdmin()){
                redirectPage("/");
            }
			
			print("<div id='grid-container'>");

            // Tree and properties
            print("<div>");
            print("<div id='tree-view'>");
            $this->views["Tree"]->load();
            print("</div>");

            print("<vr class='resize-handler-vertical'></vr>");

            print("<div id='properties-view'>");
            $this->views["Properties"]->load();
            print("</div>");

            print("</div>");

            print("<hr class='resize-handler-horizontal'></hr>");

            // Query results
            print("<div>");
            $this->views["Results"]->load();
            print("</div>");

            print("<hr class='resize-handler-horizontal'></hr>");

            // Selected workitem properties
            print("<div>");
            $this->views["WorkItem"]->load();
            print("</div>");
            print("</div>");

            $this->printFooter();
        }
		public function getDataService() { return $this->dataService; }
	}
?>
