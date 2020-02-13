<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMTablesViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->dataService = $parent->getDataService();
            $this->modules = $this->dataService->getModules();
			$this->configService = PRMConfigurationService::getInstance();
			if(isset($_GET['s'])) {
				$this->search=$_GET['s'];
			}
        }
        public function getName() { return "Tables"; }
        public function getTitle() { return "Tables"; }
        public function load() {
            print("<table id='plain-table'>");
			print("<tr>");
			print("<th>Table</th><th>Column</th>");
			print("</tr>");
			$query = "SELECT TABLE_NAME,COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->configService->__PRM_DATABASE_NAME__."'";
			$query .= " AND TABLE_NAME LIKE '%".$this->search."%'";
			$results = $this->dataService->getHandler()->query($query);
			foreach($results->getRows() as $row) {
				print("<tr>");
				print("<td>".$row->getColumns()[0]->getValue()."</td>");
				print("<td>".$row->getColumns()[1]->getValue()."</td>");
				print("</tr>");
			}
            print("</table>");
        }
    }
?>
