<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmService/PRMLocalService.php");
	class PRMConfigurationService {
		private static $instance = NULL;
		private $localService = NULL;
		public $__DISABLE_ACCESS_CHECK__ = NULL;
		public $__PRM_DATABASE_NAME__ = "";
		public $__PRM_DATABASE_PORT__ = "";
		public $__PRM_DATABASE_USER__ = "";
		public $__PRM_DATABASE_PASS__ = "";
		public $__PRM_DATABASE_FAMILY__ = "";
		public $__PRM_DATABASE_HOST__ = "";
		public $__PRM_DATABASE_SERVICE__ = "";
		public $__PRM_UPLOAD_BASE_PATH__ = "";
		public $__TREE_INDENTATION__ = "10";
		public $__SHOW_QUERY__ = "0";
		public $__DEBUG__ = "";	
		public $__SILENT_MODE__ = "0";
		private function __construct() {
			$this->localService = PRMLocalService::getInstance();
			$this->reload();
		}
		public function reload() { 
			$this->__DISABLE_ACCESS_CHECK__ = $this->setValue("DisableAccessCheck", NULL);
			$this->__PRM_DATABASE_NAME__ = $this->setValue("PRM_DATABASE", "");
			$this->__PRM_DATABASE_PORT__ = $this->setValue("PRM_PORT" ,"");
			$this->__PRM_DATABASE_USER__ = $this->setValue("PRM_USER" ,"");
			$this->__PRM_DATABASE_PASS__ = $this->setValue("PRM_PASS", "");
			$this->__PRM_DATABASE_HOST__ = $this->setValue("PRM_SERVER", "");
			$this->__PRM_DATABASE_SERVICE__ = $this->setValue("PRM_SERVICE", "");
			$this->__PRM_DATABASE_FAMILY__ = $this->setValue("PRM_PROVIDER", "MySQL"); 
			$this->__PRM_UPLOAD_BASE_PATH__ = $this->setValue("PRM_UPLOAD_BASE_PATH", "");
			$this->__TREE_INDENTATION__ = $this->setValue("TREE_INDENTATION", "10");
			$this->__SHOW_QUERY__ = $this->setValue("SHOW_QUERY", "0");
			$this->__DEBUG__ = $this->setValue("DEBUG", "");
			$this->__SILENT_MODE__ == $this->setValue("SILENT_MODE", "0");
		}
		private function setValue($map, $default) {
			if ($this->localService->getConfigValue($map) == "") {
				return $default;
			}
			return $this->localService->getConfigValue($map);
		}
		public static function getInstance() {
			if(PRMConfigurationService::$instance == NULL) {
				PRMConfigurationService::$instance = new PRMConfigurationService();
			}
			return PRMConfigurationService::$instance;
		}
	}
?>
