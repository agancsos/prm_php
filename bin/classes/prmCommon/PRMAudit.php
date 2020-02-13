<?php
	include_once("PRMItem.php");
	class PRMAudit extends PRMItem{
		private $event = "";
		private $message = "";
		private $component = "";

		public function __construct($id=0, $event="", $msg="", $comp="", $date="", $user=0) {
			parent::__construct();
			$this->id = $id;
			$this->event = $event;
			$this->message = $msg;
			$this->component = $comp;
			$this->lastUpdatedDate = $date;
			$this->user = $user;
		}

		/**
		 * Getters and setters
		 */
		public function setEvent($a) { $this->event = $a; }
		public function setMessage($a) { $this->message = $a; }
		public function setComponent($a) { $this->component = $a; }
		public function setDate($a) { $this->setLastUpdatedDate($a); }
		public function getEvent() { return $this->event; }
		public function getMessage() { return $this->message; }
		public function getComponent() { return $this->component; }
		public function getDate() { return $this->getLastUpdatedDate(); }
		public function getType() { return "Audit"; }
		public function toString() { return ("{$this->id};{$this->event};{$this->message};{$this->component};{$this->lastUpdatedDate}"); }
	}
?>
