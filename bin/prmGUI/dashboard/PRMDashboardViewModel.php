<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMDashboardViewModel extends PRMViewModel {
        private $kbService = NULL;
        private $connection = NULL;
		private $articles = array();
		private $kbId = NULL;
        public function __construct($root = "./") {
            global $__ROOT_FROM_PAGE__;
            parent::__construct($root);
            $this->kbService = PRMKbService::getInstance();
            $this->service = PRMService::getInstance();
			if(isset($_GET['id'])) {
				$this->kbId = $_GET['id'];
			}
        }
        public function getName() { return "dashboard"; }
        public function getTitle() { return "Dashboard"; }
        public function getIsSecure() { return FALSE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
            $this->printHeader();

            $this->printFooter();
        }
    }
?>
