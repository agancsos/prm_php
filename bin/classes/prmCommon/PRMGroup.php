<?php
	include_once("PRMItem.php");
	include_once("PRMUser.php");
	class PRMGroup extends PRMItem {
		protected $members = array();
		public function __construct() {
			parent::__construct();
		}
		public function addMember($a) {
			if(! in_array($this->members, $a)) {
				array_push($this->members, $a);
			}
		}
		public function getMembers() { return $this->members; }
		public function getType() { return "Group"; }
	}

?>
