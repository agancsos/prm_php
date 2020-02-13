<?php
   include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMKbAddViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        private $model = NULL;
        private $securityService = NULL;
        private $sessionService = NULL;
		private $kbService = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->securityService = PRMSecurityService::getInstance();
            $this->sessionService = PRMSessionService::getInstance();
			$this->kbService = PRMKbService::getInstance();
        }
        public function getName() { return "Add"; }
        public function getTitle() { return "Add"; }
        public function load() {
			print("<form method='post' id='kb-form'>");
			print("<input type='hidden' name='PRM-ARTICLE-ID' value=''/>");
            print("<input type='hidden' name='PRM-ARTICLE-STATUS' value='0'/>");
            print("<input type='hidden' name='PRM-ARTICLE-ACCESS' value='0'/>");
            print("<input type='hidden' name='CREATED-DATE' value='".(new DateTime())->format('Y-m-d H:i:s')."'/>");
            print("<input type='hidden' name='LAST-UPDATED-DATE' value=''/>");
			print("<input type='text' required name='PRM-ARTICLE-TITLE' placeholder='TITLE'/>");
            print("<input type='text' required name='PRM-ARTICLE-DESCRIPTION' placeholder='DESCRIPTION'/>");
            print("<textarea name='PRM-ARTICLE-TEXT' required placeholder='Please add meaningful content here...'></textarea>");
			print("<input type='submit' name='add' value='Save'/>");
			print("<input type='reset'/>");
			print("</form>");
			if(isset($_POST['add'])) {
				$article = PRMFormService::articleFromForm($_POST);
				$this->kbService->addArticle($article);
				if($article->getId() != "") {
					$this->alert("Successfully submitted article!");
                    print("<script>window.location=window.location;</script>");					
				}	
			}
        }
    }
?>
