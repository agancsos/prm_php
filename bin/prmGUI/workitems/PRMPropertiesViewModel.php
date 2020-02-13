<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMPropertyFormViewModel.php");
    class PRMPropertiesViewModel extends PRMViewModel {
        private $selectionService = NULL;
        private $workItemService = NULL;
        public function __construct($root = "./") {
            global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
            $this->service = PRMService::getInstance();
            $this->selectionService = PRMSelectionService::getInstance();
            $this->workItemService = PRMWorkItemService::getInstance();
        }
        public function getName() { return "workitems"; }
        public function getTitle() { return "WorkItems"; }
        public function getIsSecure() { return TRUE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
			print("<h2>".SR::$__PROPERTIES_HEADER__."</h2><hr>");
            @$folderForm = new PRMPropertyFormViewModel(PRMTreeNode);
            $this->selectionService->setSelectedItem($this->selectionService->getSelectedNode());
			$folderForm->setUpdateObject($this->selectionService->getSelectedItem());
            $folderForm->addReadOnlyField("Children");
            $folderForm->addReadOnlyField("Id");
            $folderForm->addReadOnlyField("NodeType");
            $folderForm->addReadOnlyField("IsFolder");
			$folderForm->addButton(new Button("Update", $this->workItemService, "updateItem"));
            $folderForm->addButton(new Button("Delete", $this->workItemService, "removeQuery", "red"));
            $folderForm->load();
			$this->reload();
       	}
		public function reload() {
		}
    }
?>
