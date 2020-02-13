<?php
	include_once("PRMItem.php");
	include_once("PRMUser.php");
	
	class PRMTeam extends PRMItem {
		protected $members = array();
		public function __construct() {
			parent::__construct();
		}
		public function addMember($a) {
			if(!in_array($a, $this->members)) {
				array_push($this->members, $a);
			}
		}
		public function getMembers() { return $this->members; }
		public function getType() { return "Team"; }
	}
?>
