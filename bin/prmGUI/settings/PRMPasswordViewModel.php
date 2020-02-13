<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	class PRMPasswordViewModel extends PRMActionViewModel {
		protected $__ROOT__ = __DIR__;
		private $dataService = NULL;
		private $model = NULL;
		private $securityService = NULL;
		protected $parent = NULL;
		public function __construct($parent) {
			parent::__construct($parent);
			$this->dataService = PRMDataService::getInstance();
			$this->securityService = PRMSecurityService::getInstance();
		}
		public function getName() { return "Password"; }
		public function getTitle() { return "Password"; }
		public function load() {
			print("<form method='POST' id='settings-form'>");
			print("<div class='label'>Current</div>");
			print("<input type='password' required name='current' placeholder='***********' />");
			print("<div class='label'>Confirm</div>");
			print("<input type='password' required name='current-confirm' placeholder='***********' />");
			print("<div class='label'>New</div>");
			print("<input type='password' required name='new' placeholder='***********' />");
			print("<input type='submit' name='change-password' value='Update' />");

			if(isset($_POST['change-password'])) {
				$current = $_POST['current'];
				$confirm = $_POST['current-confirm'];
				$new = $_POST['new'];
				$validation = $this->securityService->validateCurrent($current, $confirm);
				if($validation === True) {
					$this->securityService->updatePassword($new);
					print("<script>window.location=window.location;</script>");
				}
				else {
					$this->alert($validation);
				}
			}
		}
	}
?>
