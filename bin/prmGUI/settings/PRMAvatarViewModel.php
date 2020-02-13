<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	class PRMAvatarViewModel extends PRMActionViewModel {
		protected $__ROOT__ = __DIR__;
		private $dataService = NULL;
		private $model = NULL;
		private $securityService = NULL;
		private $sessionService = NULL;
		protected $parent = NULL;
		private $uploadService = NULL;
		public function __construct($parent) {
			parent::__construct($parent);
			$this->dataService = PRMDataService::getInstance();
			$this->securityService = PRMSecurityService::getInstance();
			$this->uploadService = PRMUploadService::getInstance();
			$this->sessionService = PRMSessionService::getInstance();
		}
		public function getName() { return "Avatar"; }
		public function getTitle() { return "Avatar"; }
		public function load() {
			print("<img class='preview' src=\"{$this->uploadService->getUploadBase()}/images/avatars/{$this->sessionService->getUser()->getAvatar()}\" alt='' />");
			print("<form method='POST' id='settings-form' enctype='multipart/form-data'>");
			print("<input type='file' name='avatar' />");
			print("<input type='submit' name='change-avatar' value='Update' />");
			if(isset($_POST['change-avatar'])) {
				if(in_array(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION), PRMUploadService::$ALLOWED_IMAGE_FILES)) {
					$result = $this->uploadService->uploadAvatar($_FILES['avatar']);
					if($result === False) {
						$this->alert("Failed to upload file");
					}
					else {
						$this->dataService->getHandler()->runQuery("UPDATE PRM_USER SET PRM_USER_AVATAR = '" . $result . "' WHERE PRM_USER_ID = '" . $this->sessionService->getUser()->getId() . "'");
					}
					print("<script>window.location=window.location;</script>");
				}
				else {
					$this->alert("File type not allowed");
				}
			}
		}
	}
?>
