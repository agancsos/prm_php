<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMPortViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        private $model = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->dataService = $parent->getDataService();
        }
        public function getName() { return "Import"; }
        public function getTitle() { return "Export/Import"; }
        public function load() {
			print("<div style='width:50%;margin-left:25%;'>");
			print("<h3>Import</h3><hr />");
            print("<form method='POST' id='settings-form' enctype='multipart/form-data'>");
            print("<input type='file' name='file' />");
            print("<input type='submit' name='import' value='Import' />");
            if(isset($_POST['import'])) {
				$this->dataService->import($_FILES['file']['tmp_name']);
                //print("<script>window.location=window.location;</script>");
            }			
	
			print("<br/><br/><br/>");	
			print("<h3>Export</h3><hr />");
            print("<form method='POST' id='settings-form' enctype='multipart/form-data'>");
            print("<input type='submit' name='export' value='Export' />");
            if(isset($_POST['export'])) {
				$exportData = $this->dataService->export();
				print("<textarea class='export-data'readonly>{$exportData}</textarea>");
            }
			print("</div>");
		}
	}
?>
