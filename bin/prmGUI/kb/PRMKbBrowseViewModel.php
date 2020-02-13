<?php 
   include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMKbBrowseViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        private $model = NULL;
        private $securityService = NULL;
        private $sessionService = NULL;
		private $kbId = 0;
		private $articles = array();
        protected $parent = NULL;
		private $kbService = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->dataService = PRMDataService::getInstance();
            $this->securityService = PRMSecurityService::getInstance();
			$this->kbService = PRMKbService::getInstance();
            $this->sessionService = PRMSessionService::getInstance();
			if(isset($_GET['id'])) {
				$this->kbId = $_GET['id'];
			}
        }
        public function getName() { return "Browse"; }
        public function getTitle() { return "Brose"; }
        public function load() {
            if($this->search != "") {
                $this->articles = $this->kbService->getArticlesBySearch($this->search, FALSE);
            }
            else {
                $this->articles = $this->kbService->getArticles(FALSE);
            }

            if(sizeof($this->articles) > 0) {
                if($this->kbId != NULL) {	
					print("<table class='kb-table-browse'>");
                    $article = $this->articles[0];
                    print("<tr><th style='max-width:25% !important;'>ID</th><td>{$article->getId()}</td></tr>");
                    print("<tr><th style='max-width:25% !important;'>CREATED DATE</th><td>{$article->getCreatedDate()}</td></tr>");
                    print("<tr><th style='max-width:25% !important;'>MODIFIED DATE</th><td>{$article->getLastUpdatedDate()}</td></tr>");
                    print("<tr><th style='max-width:25% !important;'>TITLE</th><td>{$article->getTitle()}</td></tr>");
                    print("<tr><th style='max-width:25% !important;'>DESCRIPTION</th><td>{$article->getDescription()}</td></tr>");
                    print("<tr style='height:100%;'><td colspan=2>{$article->getText()}</td></tr>");
                }
                else {
					print("<table class='kb-table'>");
                    foreach($this->articles as $article) {
                        print("<tr>");
                        print("<th>Title</th><th>Created Date</th><th>Last Updated Date</th>");
						print("<th>State</th><th>Status</th><th>Edit</th>");
                        print("</tr>");
                        print("<tr>");
                        print("<td><a href='./?op=Browse&id=".$article->getId()."'>{$article->getTitle()}</a></td>");
                        print("<td>{$article->getCreatedDate()}</td>");
                        print("<td>{$article->getLastUpdatedDate()}</td>");
						print("<td>".PRMKbState::getItterator()[$article->getState()]."</td>");
						print("<td>".PRMKbStatus::getItterator()[$article->getStatus()]."</td>");
						print("<td><a href='./?op=Edit&id=".$article->getId()."'>EDIT</a></td>");
                        print("</tr>");
                    }
                }
				print("</table>");
			}
            else {
                print("<p class='no-records-found'>No records found.... </p>");
            }
        }
    }
?>
