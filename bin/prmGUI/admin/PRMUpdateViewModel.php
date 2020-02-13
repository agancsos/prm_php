<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	class PRMUpdateViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
		private $dataService = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
			parent::__construct($parent);
			$this->dataService = $parent->getDataService();
        }
        public function getName() { return "Update"; }
        public function getTitle() { return "Update"; }
        public function load() {
			print("<form method = 'post' class = 'plain-form'>");
            print("<textarea id='query-text' placeholder='Query to execute' class='query-input' name='query-text'></textarea>");
            print("<input class='query-submit'type='submit' name = 'submit' value = 'Submit'/>");
            print("<input class='query-clear' type='submit' name='clear' value='Clear'/>");
            print("</form>");
            if(isset($_POST['submit'])) {
                if($this->dataService->getHandler()->runQuery($_POST['query-text'])) {
                    print("Success<br/>");
                }
                else {
                    print("Failed!<br/>");
                }
                print("<script>window.location=window.location;</script>");
            }
			else if(isset($_POST['clear'])) {                                                                                                                                                                                                                                                                                                                   
                print("<script>document.getElementById('query-text').value = '';</script>");
                print("<script>window.location=window.location;</script>");
            }
		}		
	}
?>
