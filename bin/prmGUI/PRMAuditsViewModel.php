<?php
	include_once("PRMViewModel.php");
	class PRMAuditsViewModel extends PRMViewModel {
		public function __construct($root = "./") {
			parent::__construct($root);
			$this->service = PRMService::getInstance();
			$this->configService = PRMConfigurationService::getInstance();
			if(isset($_GET['s'])) {
				$this->search = $_GET['s'];
			}
		}
		public function getName() { return "audits"; }
		public function getTitle() { return "Audits"; }
		public function getIsSecure() { return True; }
		public function getIsEnabled() { return TRUE; }
		public function load() {
			$this->printHeader();
			$headers = $this->service->getAuditHeaders();
			if($this->sessionService->shouldShowAdmin()){
				print("<form method='post' id = 'page-tools'>");
				print("<input type='submit' name = 'action' value = 'Purge'/>");
				print("</form>");
			}
			print("<table id='audit-table'>");
			print("<tr>");
			foreach($headers as $header) {
				print("<th>".strtoupper(str_replace("_", " ", $header))."</th>");
			}
			print("</tr>");
			foreach($this->service->getAudits() as $audit) {
				if($this->search == "" || ($this->search != "" && strpos(strtolower($audit->toString()), strtolower($this->search)))){
					print("<tr>");
					print("<td>{$audit->getId()}</td>");
					print("<td>{$audit->getEvent()}</td>");
					print("<td>{$audit->getMessage()}</td>");
					print("<td>{$audit->getComponent()}</td>");
					print("<td>{$audit->getDate()}</td>");
					print("<td>{$audit->getUser()}</td>");
					print("</tr>");
				}
			}	
			print("</table>");

			if(isset($_POST['action'])) {
				if($_POST['action'] == "Purge") {
					$this->service->purgeAudits();
					redirectPage("./");
				}
			}
			$this->printFooter();
		}
	}
?>
