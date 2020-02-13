<?php
	include_once("PRMItem.php");
	class PRMPermission extends PRMItem{
		private $enabled = FALSE;
		public function __construct() {
			parent::__construct();
		}
		public function setEnabled($a) { $this->enabled = $a; }
		public function getEnabled() { return $this->enabled; }
		public function getType() { return "Permission"; }
	}
?>
