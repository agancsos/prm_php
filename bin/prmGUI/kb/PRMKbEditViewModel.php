<?php
   include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMKbEditViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        private $model = NULL;
        private $securityService = NULL;
        private $sessionService = NULL;
		private $kbService = NULL;
		private $objectService = NULL;
        protected $parent = NULL;
		private $article = NULL;
		private $kbId = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->securityService = PRMSecurityService::getInstance();
            $this->sessionService = PRMSessionService::getInstance();	
			if(isset($_GET['id'])) {
				$this->kbId = $_GET['id'];
			}
			$this->kbService = PRMKbService::getInstance();
			$this->objectService = PRMObjectService::getInstance();
			$this->article = $this->objectService->getArticle($this->kbId);
        }
        public function getName() { return "Edit"; }
        public function getTitle() { return "Edit"; }
        public function load() {
			print("<form method='post' id='kb-form'>");
			print("<input type='hidden' name='PRM-ARTICLE-ID' value=\"{$this->article->getId()}\"/>");
            print("<input type='hidden' name='PRM-ARTICLE-ACCESS' value='0'/>");
            print("<input type='hidden' name='LAST-UPDATED-DATE' value='".(new DateTime())->format('Y-m-d H:i:s')."'/>");
            print("<input type='hidden' name='CREATED-DATE' value='".$this->article->getCreatedDate()."'/>");
			print("<input type='text' required name='PRM-ARTICLE-TITLE' placeholder='TITLE' value=\"{$this->article->getTitle()}\"/>");
            print("<input type='text' required name='PRM-ARTICLE-DESCRIPTION' placeholder='DESCRIPTION' value=\"{$this->article->getDescription()}\"/>");
			print("<select name='PRM-ARTICLE-STATE'>");
			foreach(PRMKbState::getItterator() as $state) {
				print("<option value='".PRMKbState::fromName($state)."'");
				if($this->article->getState() == PRMKbState::fromName($state)) {
					print(" selected ");
				}
				print(">{$state}</option>");
			}
			print("</select>");
            print("<select name='PRM-ARTICLE-STATUS'>");
            foreach(PRMKbStatus::getItterator() as $status) {
                print("<option value='".PRMKbStatus::fromName($status)."'");
                if($this->article->getStatus() == PRMKbStatus::fromName($status)) {
                    print(" selected ");
                }
                print(">{$status}</option>");
            }
            print("</select>");
            print("<textarea name='PRM-ARTICLE-TEXT' required placeholder='Please add meaningful content here...'>{$this->article->getText()}</textarea>");
			print("<input type='submit' name='update' value='Save'/>");
			print("<input type='reset'/>");
			print("</form>");
			if(isset($_POST['update'])) {
				$article = PRMFormService::articleFromForm($_POST);
				if($this->kbService->updateArticle($article)) {
					$this->alert("Successfully updated article!");
                    print("<script>window.location=window.location;</script>");
				}
			}
        }
    }
?>
