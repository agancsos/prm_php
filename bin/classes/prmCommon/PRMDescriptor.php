<?php
	include_once("PRMGroup.php");
	include_once("PRMPermission.php");
	include_once("PRMItem.php");
	class PRMDescriptor extends PRMItem {
		protected $permission = NULL;
		protected $group = NULL;
		public function __construct() {
			parent::__construct();
		}
		public function getPermission() { return $this->permission; }
		public function getGroup() { return $this->group; }
		public function setPermission($a) { $this->permission = $a; }
		public function setGroup($a) { $this->group = a; }
		public function getType() { return "Descriptor"; }
	}

?>
