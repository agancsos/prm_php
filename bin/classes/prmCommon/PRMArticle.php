<?php
	include_once("PRMItem.php");
	include_once("PRMEnums.php");
	class PRMArticle extends PRMItem {
		private $description = "";
		private $text = "";
		private $access = "";

		public function __construct() {
			$this->state = PRMKbState::NONE;
			$this->status = PRMKbStatus::NONE;
		}
		
		public function getType() { return "Article"; }
		public function getTitle() { return $this->getName(); }
		public function getDescription() { return $this->description; }
		public function getText() { return $this->text; }
		public function getAccess() { return $this->access; }
		public function getState() { return $this->state; }
		public function getStatus() { return $this->status; }
		public function setTitle($a) { $this->setName($a); }
		public function setDescription($a) { $this->description = $a; }
		public function setText($a) { $this->text = $a; }
		public function setAccess($a) { $this->access = $a; }
		public function setState($a) { $this->state = $a; }
		public function setStatus($a) { $this->status = $a; } 
	}
?>
