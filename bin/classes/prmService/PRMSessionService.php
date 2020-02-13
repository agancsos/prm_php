<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmService/prmservice_all.php");
	class PRMSessionService {
		private $token = NULL;
		private $user = NULL;
		private static $instance = NULL;
		private $dataService = NULL;
		private $configService = NULL;
		private $service = NULL;
		private $securityService = NULL;

		private function __construct() {
			$this->service = PRMService::getInstance();
			$this->dataService = PRMDataService::getInstance();
			$this->configService = PRMConfigurationService::getInstance();
			$this->securityService = PRMSecurityService::getInstance();
			if(isset($_COOKIE[SR::$__SESSION_TOKEN_NAME__])) {
				$this->token = $_COOKIE[SR::$__SESSION_TOKEN_NAME__];
				if($this->isValidToken($this->token) === False) {
					setcookie(SR::$__SESSION_TOKEN_NAME__, "");
					$this->token = NULL;
				}
			}
			$this->user = $this->securityService->getUser($this->token);
		}


		private function isValidToken($token) {
			if(sizeof($this->dataService->getHandler()->query("SELECT * FROM PRM_SESSION WHERE PRM_SESSION_TOKEN = '".$token."'")->getRows()) != 1) {
				return False;
			}
			return true;
		}

		public static function getInstance() {
			if(PRMSessionService::$instance == NULL) {
				PRMSessionService::$instance = new PRMSessionService();
			}
			return PRMSessionService::$instance;
		}

		public function shouldShowAdmin() {
			if($this->configService->__DISABLE_ACCESS_CHECK__ == "1") {
				return True;
			}
			else if(($this->getUser() != NULL && $this->getUser()->getIsSysAdmin())){
				return True;
			}
			return False;
		}

		public function shouldShow() { 
			if($this->shouldSHowAdmin()) {
				return True;
			}
			else if($this->token != NULL) {
				return True;
			}
			return False;
		}

		public function setCookie($token, $user) {
			if($token != NULL) {
				setcookie(SR::$__SESSION_TOKEN_NAME__, $token);
				$this->token = $token;
				$sql = "INSERT INTO PRM_SESSION (PRM_SESSION_TOKEN, PRM_USER_ID, CREATED_DATE, LAST_UPDATED_DATE) VALUES (";
				$sql .= "'".$token."', (SELECT PRM_USER_ID FROM PRM_USER WHERE PRM_USER_NAME = '".$user."'), NOW(), NOW())";
				$this->dataService->getHandler()->runQuery($sql);
			}
		}

		public function signout() {
			setcookie(SR::$__SESSION_TOKEN_NAME__, NULL);
			$this->token = NULL;
			$this->user = NULL;
		}

		public function heartbeat() {
			if($this->token != NULL) {
				$this->dataService->getHandler()->runQuery("UPDATE PRM_SESSION SET LAST_UPDATED_DATE=NOW() WHERE PRM_SESSION_TOKEN='".$this->token."'");
			}
		}

		public function setToken($token) {	
			$this->token = $token; 
			$this->user = $this->securityService->getUser($this->token);
		}
		public function setUser($user) { $this->user = $user; }
		public function getToken() { return $this->token; }
		public function getUser() { return $this->user; }
	}
?>
