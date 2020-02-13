<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMViewModel.php");
	class PRMHomeViewModel extends PRMViewModel {
		public function __construct($root = "./") { 
			parent::__construct($root); 
			if($this->sessionService->shouldShow()) {
				$this->isLinksEnabled = True;
			}
		}
		public function getName() { return "home"; }
		public function getTitle() { return "PRM"; }
		public function getIsSecure() { return False; }
		public function getIsEnabled() { return TRUE; }
		public function load() {
			global $__DATABASE_SERVER__;
			global $__ROOT_FROM_PAGE__;
			$this->printHeader();
			if(!$this->service->isSetup() && $this->getName() != "setup"){
				//$this->service->auditMessage("Redirect", "Redirecting to setup", "Application");
				if($this->configService->__DEBUG__ != "1") {
					//redirectPage("{$__ROOT_FROM_PAGE__}/setup");
				}
			}
			else {
				if(! $this->sessionService->shouldShow()) {
					print("<div id='login-div'>");	
					print("<form id='login-form' method='POST' action='setter.php'>");
					print("<label>Username</label><input type='text' name='login-user' placeholder='jdoe' value='' required autocompleted='off' />");
					print("<label>Password</label><input type='password' name='login-pass' placeholder='***************' value = '' required autocompleted='off' />");
					print("<input type='submit' name='login' value='Login' />");
					print("</div>");
					print("</div>");
				}
				else {
					
				}
			}
			$this->printFooter();
		}
	}
?>
