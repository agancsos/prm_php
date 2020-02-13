<?php
    include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
    class PRMTeamsViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $dataService = NULL;
        private $model = NULL;
        protected $parent = NULL;
		private $currentSource = NULL;
		private $objectService = NULL;
		private $sources = array();
        public function __construct($parent) {
            parent::__construct($parent);
            $this->dataService = $parent->getDataService();
			$this->objectService = PRMObjectService::getInstance();
			$this->sources = $this->objectService->getTypeItems("TEAM");
			if(sizeof($this->sources) > 0) {
				$this->currentSource = $this->sources[0];
			}
			if(isset($_GET['source'])) {
				foreach($this->sources as $source) {
					if($source->getName() == $_GET['source']){
						$this->currentSource = $source;
					}
				}
			}
        }
        public function getName() { return "Teams"; }
        public function getTitle() { return "Teams"; }
        public function load() {
			print("<div id = 'grid-container'>");
			for($i = 0; $i < 2; $i++) {
				print("<div>");
				print("<table class='grid-table'>");
				if($i == 0) {
					foreach($this->sources as $source) {
						print("<tr>");
						print("<th style='font-size:14pt;'");
						if($this->currentSource->getName() == $source->getName()) { 
							print(" class = 'selected-page' ");
						}
						print(">");
                        print("<a ");
						if($this->currentSource->getName() == $source->getName()) { 
							print(" class = 'selected-page' ");
						}
						print(" href='?op=".$this->getName()."&source=".$source->getName()."'>");
						print(str_replace("_"," ",$source->getName()));
						print("</a>");
						print("</th>");
						print("</tr>");
					}
				}
				else if($i == 1) {
					$allUsers = $this->objectService->getUsers();
					print("<table class='grid-table'>");
					foreach($allUsers as $user) {
						print("<tr>");
						print("<td>{$user->getFirst()} {$user->getLast()}</td>");
						if($this->currentSource != NULL) {
							print("<td>");
							print("<form method='POST' class='add-remove-form'>");
							print("<input type='hidden' name='id' value=\"{$user->getId()}\" />");
							print("<input type='submit' style='cursor:pointer;width:100%;height: 40px;margin-top:-2px;'  name='add-remove-user' value='");
							if(PRMSecurityService::getInstance()->isUserInTeam($user, $this->currentSource)) {
								print("Remove");
							}
							else {
								print("Add");
							}					
							print("' />");
							print("</form>");
							if(isset($_POST['add-remove-user'])) {
								if($_POST['add-remove-user'] == "Add") {
									PRMSecurityService::getInstance()->addTeamMember($this->currentSource, $_POST['id']);
								}
								else if($_POST['add-remove-user'] == "Remove") {
									PRMSecurityService::getInstance()->removeTeamMember($this->currentSource, $_POST['id']);
								}
                            	print("<script>window.location=window.location;</script>");
							}
							print("</td>");	
						}
						print("</tr>");
					}
					print("</table>");
				}
				print("</table>");
				print("</div>");
			}
			print("</div>");
        }
    }
?>
