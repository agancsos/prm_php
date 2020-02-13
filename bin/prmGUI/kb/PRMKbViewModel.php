<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
    class PRMKbViewModel extends PRMViewModel {
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
        public function getName() { return "kb"; }
        public function getTitle() { return "KB"; }
        public function getIsSecure() { return FALSE; }
        public function getIsEnabled() { return TRUE; }
        public function load() {
            $this->printHeader();
			if($this->kbId != NULL) { 
				$this->articles = $this->kbService->getArticlesById($this->kbId, TRUE); 
			}
			else if($this->search != "") { 
				$this->articles = $this->kbService->getArticlesBySearch($this->search, TRUE);
			}
			else {	
				$this->articles = $this->kbService->getArticles(TRUE); 
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
						print("</tr>");
						print("<tr>");
						print("<td><a href='./?id=".$article->getId()."'>{$article->getTitle()}</a></td>");
						print("<td>{$article->getCreatedDate()}</td>");
						print("<td>{$article->getLastUpdatedDate()}</td>");
						print("</tr>");
					}
				}
				print("</table>");
			}
			else {
				print("<p class='no-records-found'>No records found.... </p>");
			}

            $this->printFooter();
        }
    }
?>
