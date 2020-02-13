<?php
	include_once("PRMItem.php");
	include_once("PRMLocation.php");
	class PRMUser extends PRMItem {
		protected $first = "";
		protected $last = "";
		protected $status = 0;
		protected $pass = "";
		protected $isSysAdmin = FALSE;
		protected $city = NULL;
		protected $state = NULL;
		protected $country = NULL;
		protected $avatar = NULL;
		protected $role = NULL;
		public function __construct() {
			parent::__construct();
		}
		public function getFirst() { return $this->first; }
		public function getLast() { return $this->last; }
		public function getIsSysAdmin() { return $this->isSysAdmin; }
		public function getCity() { return $this->city; }
		public function getStatus() { return $this->status; }
		public function getState() { return $this->state; }
		public function getCountry() { return $this->country; }
		public function getRole() { return $this->role; }
		public function getAvatar() { return $this->avatar; }
		public function getPassword() { return $this->pass; }
		public function setPassword($a) { $this->pass = $a; }
		public function setFirst($a) { $this->first = $a; } 
		public function setLast($a) { $this->last = $a; }
		public function setStatus($a) { $this->status = $a; }
		public function setState($a) { $this->state = $a; }
		public function setCity($a) { $this->city = $a; }
		public function setCountry($a) { $this->country = $a; }
		public function setIsSysAdmin($a) { $this->isSysAdmin = $a; }
		public function setRole($a) { $this->role = $a; }
		public function setAvatar($a) { $this->avatar = $a; }
		public function getType() { return "User"; }
	}

?>
