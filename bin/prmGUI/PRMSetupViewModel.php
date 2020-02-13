<?php
	include_once("PRMViewModel.php");
	class PRMSetupViewModel extends PRMViewModel {
		public function __construct($root = "./") {
			$this->isLinksEnabled = FALSE;
			parent::__construct($root);
			$this->configService = PRMConfigurationService::getInstance();
		}
		public function getName() { return "setup"; }
		public function getTitle() { return "Setup"; }
		public function getIsSecure() { return FALSE; }
		public function getIsEnabled() { return TRUE; }
		public function load() {
			$this->printHeader();
		}
	}
?>
