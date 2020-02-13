<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmService/prmservice_all.php");
	abstract class PRMActionViewModel {
		protected $__ROOT__ = __DIR__;
		protected $parent = NULL;
		protected $search = NULL;
		protected $configService = NULL;
		public function __construct($parent) {
			$this->parent = $parent;
			$this->search = $parent->getSearch();
			$this->configService = PRMConfigurationService::getInstance();
		}
		public function updateUrl($url, $name, $value) {
			$newParams = $url;
			$newParams[$name] = $value;
			$newUrl = ($_SERVER['PHP_SELF']."?".http_build_query($newParams));
			return $newUrl;
		}
		public abstract function getName();
		public abstract function getTitle();
		public abstract function load();
		public function alert($msg) {
			print("<script>alert('".$msg."');</script>");
		}
		protected function assertResult($result, $success="Updated object", $failure="Failed to update object....", $displaySuccess=False) {
			if ($this->configService == NULL) {
				$this->configService = PRMConfigurationService::getInstance();
			}
			if ($result == False) {
				$this->alert($failure);
			}
			else {
				if ($this->configService->__SILENT_MODE__ != "1" && $displaySuccess != False) {
					$this->alert($success);
				}
			}
			?><script>window.location=window.location;</script><?php
		}
	}
?>
