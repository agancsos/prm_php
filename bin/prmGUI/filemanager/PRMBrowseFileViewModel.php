<?php
   include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMBrowseFileViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        private $model = NULL;
        private $securityService = NULL;
        private $sessionService = NULL;
		private $fileService = NULL;
		private $id = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->securityService = PRMSecurityService::getInstance();
			$this->dataService = PRMDataService::getInstance();
            $this->sessionService = PRMSessionService::getInstance();
            $this->fileService = PRMUploadService::getInstance();
            $this->fileService->sessionService = $this->sessionService;
			if(isset($_GET['id'])) {
				$this->id = $_GET['id'];
			}
        }
        public function getName() { return "Browse"; }
        public function getTitle() { return "Browse"; }
        public function load() {
			if($this->id == NULL) {
				print("<table id='plain-table'>");
				print("<tr>");
				foreach($this->dataService->getHandler()->getColumns("SELECT *,'DELETE','VIEW'  FROM PRM_FILE") as $header) {
					print("<th>".str_replace("_", " ", $header)."</th>");
				}
				print("</tr>");
				foreach($this->dataService->getHandler()->query("SELECT * FROM PRM_FILE WHERE PRM_FILE_TYPE NOT IN ('Avatar')")->getRows() as $row) {
					if($row->getColumn("PRM_FILE_PRIVATE")->getValue() == "0" || ($row->getColumn("PRM_OWNER_ID")->getValue() == $this->sessionService->getId())) {
						print("<tr>");
						print("<td>".$row->getColumn("PRM_FILE_ID")."</td>");
               			print("<td>");
						if($row->getColumn("PRM_FILE_ISPRIVATE") == "1") {
							print("TRUE");
						}
						else{
							print("FALSE");
						}
						print("</td>");
               			print("<td>".$row->getColumn("PRM_FILE_TYPE")->getValue()."</td>");
               			print("<td>".$row->getColumn("PRM_FILEPATH")->getValue()."</td>");
               			print("<td>".$row->getColumn("PRM_FILE_NAME")->getValue()."</td>");
               			print("<td>".$row->getColumn("PRM_FILE_ID")->getValue()."</td>");
               			print("<td>".$this->securityService->getUser($row->getColumn("PRM_OWNER_ID")->getValue())."</td>");
               			print("<td>".$row->getColumn("CREATED_DATE")->getValue()."</td>");
               			print("<td>".$this->securityService->getUser($row->getColumn("MODIFIED_BY_ID")->getValue())."</td>");
               			print("<td>".$row->getColumn("LAST_UPDATED_DATE")->getValue()."</td>");
               			print("<td>");
						print("<form method='post' class='line-form'>");
						print("<input type='hidden' name='id' value='".$row->getColumn("PRM_FILE_ID")->getValue()."'/>");
						print("<input type='submit' name='delete'>DELETE</input>");
						print("</form>");
						if(isset($_POST['delete'])) {
							$this->uploadService->deleteFile($_POST['id']);
						}
						print("</td>");
               			print("<td><a href='./?op=Browse&id=".$row->getColumn("PRM_FILE_ID")->getValue()."'>VIEW</a></td>");
						print("</tr>");
					}
				}
				print("</table>");
       		}	
			else {
				$file = $this->dataService->getHandler()->query("SELECT * FROM PRM_FILE WHERE PRM_FILE_ID = '".$this->id."'")->getRows()[0];
				print("<iframe src='".$file->getColumn("PRM_FILE_PATH")->getValue()."/".$file->getColumn("PRM_FILE_NAME")->getValue()."'/>");
			}
    	}
	}
?>
