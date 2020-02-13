<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMListFormViewModel.php");
    class PRMFilterEditorViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
		private $selectionService = null;
		private $workItemService = null;
        protected $parent = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
			$this->selectionService = PRMSelectionService::getInstance();
			$this->workItemService = PRMWorkItemService::getInstance();
        }
        public function getName() { return "Editor"; }
        public function getTitle() { return "Editor"; }
        public function load() {
			print("<h3>".SR::$__FILTERS_EDITOR_TITLE__."</h3><hr />");
			$filtersForm = new PRMListFormViewModel();
			$joinConditionField = new PRMFormField("select","PRM_QUERY_JOIN_CONDITION", "filter-join-condition");
			$options = array();
			array_push($options, new PRMGeneralItem("", "Join", ""));
			array_push($options, new PRMGeneralItem("AND", "AND", ""));
			array_push($options, new PRMGeneralItem("OR", "OR", ""));
			$joinConditionField->setOptions($options);
			$filtersForm->addField($joinConditionField);		
			
			$fieldField = new PRMFormField("select","PRM_QUERY_FIELD", "filter-field");
            $options = $this->workItemService->getColumns();
            $fieldField->setOptions($options);
            $filtersForm->addField($fieldField);

			$conditionField = new PRMFormField("select","PRM_QUERY_CONDITION", "filter-condition");
            $options = array();
            array_push($options, new PRMGeneralItem(">", ">", ""));
            array_push($options, new PRMGeneralItem("<", "<", ""));
			array_push($options, new PRMGeneralItem("=", "=", ""));
			array_push($options, new PRMGeneralItem("<>", "<>", ""));
			array_push($options, new PRMGeneralItem("LIKE", "LIKE", ""));
			array_push($options, new PRMGeneralItem("IN", "IN", ""));
            $conditionField->setOptions($options);
            $filtersForm->addField($conditionField);

			$valueField = new PRMFormField("input type='text'","PRM_QUERY_VALUE", "filter-value");
            $filtersForm->addField($valueField);

			$filtersForm->addButton(new Button("Add", NULL, NULL, NULL, "add-filter"));
			$filtersForm->addListButton(new Button("Save", NULL, NULL, NULL, "save-filter"));
			$filtersForm->addListButton(new Button("Delete", NULL, NULL, "red", "delete-filter"));

            $filterRows = PRMFormService::getFilters($this->selectionService->getSelectedNode()->getId());
            foreach ($filterRows as $tempRow) {
                $filtersForm->addRow($tempRow);
            }

			$filtersForm->load();

			print("<br/><br/><h3>".SR::$__COLUMNS_EDITOR_TITLE__."</h3><hr />");
			$columnsForm = new PRMListFormViewModel();
			$columnsForm->setUseRowButtons(true);
			foreach ($this->workItemService->getRawColumns() as $tempRow) {
				$tempItemFields = array();
				$tempField = new PRMAdvancedFormField("input type='text'", 'COLUMN_NAME', "column-name", "PRM_QUERY_COLUMN");
				$tempField->setValue($tempRow);
				$tempField->setEnabled(False);
				array_push($tempItemFields, $tempField); 
				$tempItem = new PRMListFormItem($this->selectionService->getSelectedNode()->getId(), $tempItemFields);
				$columnsForm->addRow($tempItem);
				$exists = $this->workItemService->columnListed($tempRow, $this->selectionService->getSelectedNode()->getId());
				if ($exists) {
					$addRemoveColumnsButton = new Button("Remove", NULL, NULL, "red", "add-remove-column");
				}
				else {
					$addRemoveColumnsButton = new Button("Add", NULL, NULL, "", "add-remove-column");
				}
				$columnsForm->addListButton($addRemoveColumnsButton);
			}
			$columnsForm->load();
			

			// Submit handlers
			if (isset($_POST["submit-add-filter"])) {
				$parent = $this->selectionService->getSelectedNode();
				$item = new PRMFilter($_POST['filter-join-condition'], $_POST['filter-field'], $_POST['filter-condition'], $_POST['filter-value']);
				$this->assertResult($this->workItemService->addFilter($item, $parent));
			}
			else if(isset($_POST['submit-save-filter'])) {
				$item = new PRMFilter((isset($_POST['filter-join-condition']) ? $_POST['filter-join-condition'] : ""), $_POST['filter-field'], $_POST['filter-condition'], $_POST['filter-value']);
				$item->setId($_POST['row-id']);
				$this->assertResult($this->workItemService->updateFilter($item));
			}
			else if(isset($_POST['submit-delete-filter'])) {
				$this->assertResult($this->workItemService->removeFilter($_POST['row-id']));
			}
			else if(isset($_POST['submit-add-remove-column'])) {
				$exists = $this->workItemService->columnListed($_POST['column-name'], $this->selectionService->getSelectedNode()->getId());
				if ($exists === True) {
					$this->assertResult($this->workItemService->removeColumn($this->selectionService->getSelectedNode()->getId(), $_POST['column-name']));
				}
				else {
					$this->assertResult($this->workItemService->addColumn($this->selectionService->getSelectedNode()->getId(), $_POST['column-name']));
				}
			}
		}
	}
?>
