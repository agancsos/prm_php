<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("PRMViewModel.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/SR.php");
	class PRMLinksViewModel extends PRMViewModel {
		private $module = NULL;
		private $uploadService = NULL;
		protected $isLinksEnabled = False;
		public function __construct($root = "./", $linksEnabled = False,  $module="") {
			parent::__construct($root);
			$this->module = $module;
			$this->service = PRMService::getInstance();
			$this->isLinksEnabled = $linksEnabled;
			$this->configService = PRMConfigurationService::getInstance();
			$this->uploadService = PRMUploadService::getInstance();
		}
		public function getName() { return "links"; }
		public function getTitle() { return ""; }
		public function getIsSecure() { return TRUE; }
		public function getIsEnabled() { return TRUE; }
		public function setIsLinksEnabled($a) { $this->isLinksEnabled = $a; }
		public function load() {
			global $__ROOT_FROM_PAGE__;
			if($this->isLinksEnabled && $this->sessionService->shouldShow()) {
				print("<table id = 'link-buttons'>");
				print("<tr>");
				print("<td>");
				print("<img src = \"{$this->uploadService->getUploadBase()}/images/avatars/{$this->sessionService->getuser()->getAvatar()}\" />");
				print(" Welcome back, ");
				if($this->sessionService->getUser() != NULL) {
					print("<a href=\"{$__ROOT_FROM_PAGE__}signout.php\">");
					print("{$this->sessionService->getUser()->getFirst()} {$this->sessionService->getUser()->getLast()}");
					print("</a>");
				}
				print("</td>");
				print("<td ");
				if($this->module == 'dashboard') {
					print(" class='selected-page' ");
				}
				print(" onclick=\"gotoPage('/addins/dashboard')\"><a href='/addins/dashboard'>".SR::$__MODULES_LABEL_DASHBOARD__."</td>");
				print("<td ");
				if($this->module == 'kb') {
					print(" class='selected-page' ");
				}
				print(" onclick=\"gotoPage('/addins/kb')\"><a href='/addins/kb'>".SR::$__MODULES_LABEL_KB__."</td>");

				print("<td ");
				if($this->module == 'kbadmin') {
					print(" class='selected-page' ");
				}
				print(" onclick=\"gotoPage('/addins/kb/admin')\"><a href='/addins/kb/admin'>".SR::$__MODULES_LABEL_KBADMIN__."</td>");
				$modules = array();
				$modules = $this->service->getModules();
				if($modules == NULL) {
					$modules = array();
				}
				foreach($modules as $module2) {
					print("<td ");
					if($this->module == $module2->getName()) {
						print(" class = 'selected-page' ");
					}
					print(" onclick=\"gotoPage('/addins/{$module2->getName()}')\"><a href='/addins/{$module2->getName()}'>".ucfirst($module2->getName())."</td>");
				}
				print("<td ");
				if($this->module == 'filemanager') {
					print(" class='selected-page'");
				}
				print(" onclick=\"gotoPage('/addins/filemanager')\"><a href='/addins/filemanager'>".SR::$__MODULES_LABEL_FILEMANAGER__."</td>"); 
				print("<td ");
				if($this->module == 'settings') {
					print(" class='selected-page'");
				}
				print(" onclick=\"gotoPage('/addins/settings')\"><a href='/addins/settings'>".SR::$__MODULES_LABEL_SETTINGS__."</td>");
				if($this->sessionService->shouldShowAdmin()) {
					print("<td ");
					if($this->module == 'audits') {
						print(" class='selected-page' ");
					}
					print(" onclick=\"gotoPage('/addins/audits')\"><a href='/addins/audits'>".SR::$__MODULES_LABEL_AUDITS__."</td>");
					print("<td ");
					if($this->module == 'admin') {
						print(" class='selected-page' ");
					}
					print(" onclick=\"gotoPage('/addins/admin')\"><a href='/addins/admin'>".SR::$__MODULES_LABEL_ADMIN__."</td>");
				}
				print("<td><form id='search-form' method='GET'>");
				print("<input type='text' placeholder='Search...' name='s' value='".(isset($_GET['s']) ? $_GET['s'] : "")."'/>");
				print("<input type='hidden' name='op' value='".(isset($_GET['op']) ? $_GET['op'] : "")."'/>");
				print("</form></td>");
				printf("</tr>");
				printf("</table>");
			}
		}
	}
?>
