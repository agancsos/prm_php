<?php
   include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMAddFileViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        private $model = NULL;
        private $securityService = NULL;
        private $sessionService = NULL;
		private $fileService = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->securityService = PRMSecurityService::getInstance();
            $this->sessionService = PRMSessionService::getInstance();
			$this->fileService = PRMUploadService::getInstance();
			$this->fileService->sessionService = $this->sessionService;
        }
        public function getName() { return "Add"; }
        public function getTitle() { return "Add"; }
        public function load() {
            print("<form method='POST' id='settings-form' enctype='multipart/form-data'>");
            print("<input type='file' name='file' />");
			print("<select style='width:100%;height:40px;' name='private>");	
			print("<option value=''></option>");
			print("<option value='0' selected>Public</option>");
			print("<option value='1'>Private</option>");
			print("</select>");
            print("<input type='submit' name='upload' value='Upload' />");
            if(isset($_POST['upload'])) {
                $result = $this->uploadService->uploadFile($_FILES['file'], $_POST['private']);
                if($result === False) {
                    $this->alert("Failed to upload file");
                }
				else {
					$this->alert("File uploaded!");
				}
                print("<script>window.location=window.location;</script>");
            }
        }
    }
?>
