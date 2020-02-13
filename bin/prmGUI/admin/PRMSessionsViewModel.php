<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	class PRMSessionsViewModel extends PRMActionViewModel {
		private $service = NULL;
		private $securityService = NULL;
		private $padLength = 52;
		public function __construct($parent) {
            global $__ROOT_FROM_PAGE__;
            parent::__construct($parent);
			$this->service = PRMService::getInstance();
			$this->securityService = PRMSecurityService::getInstance();
		}
        public function getName() { return "Sessions"; }
        public function getTitle() { return "Sessions"; }
        public function load() {
            $headers = $this->service->getSessionHeaders();
			print("<table id='audit-table'>");
			print("<tr>");
			foreach($headers as $header) {
				print("<th>".strtoupper(str_replace("_", " ", $header))."</th>");
			}
			print("</tr>");
			foreach($this->service->getSessions()->getRows() as $session) {
				print("<tr>");
				for($i = 0; $i < sizeof($headers); $i++) {
					print("<td>");
					if($headers[$i] == "PRM_USER_ID") {
						$tempUser = $this->securityService->getUserById($session->getColumns()[$i]->getValue());
						print("{$tempUser->getFirst()} {$tempUser->getLast()}");
					}
					else if($headers[$i] == "PRM_SESSION_ID") {
						print(str_pad($session->getColumns()[$i]->getValue(), $this->padLength, "0", STR_PAD_LEFT));
					}
					else if($headers[$i] == "PRM_SESSION_TOKEN") {
                        print(str_pad($session->getColumns()[$i]->getValue(), $this->padLength, "#", STR_PAD_LEFT));
                    }
					else {
						 print($session->getColumns()[$i]->getValue());
					}
					print("</td>");
				}
				print("</tr>");
			}	
			print("</table>");
        }
    }
?>
