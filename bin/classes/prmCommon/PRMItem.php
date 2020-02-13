<?php
	include_once("PRMDescriptor.php");
	abstract class PRMItem {
		protected $id = 0;
		protected $name = "";
		protected $label = "";
		protected $createdDate = "";
		protected $pid = "";
		protected $lastUpdatedDate = "";
		protected $isDirty = False;
		protected $lastUpdatedBy = "";
		protected $createdBy = 0;
		protected $state = 0;
		protected $status = -1;
		protected $descriptor = array();
		public function __construct($id=0,$name="",$label="") {
			$this->id = $id;
			$this->name = $name;
			$this->label = $label;
		}
		public function getId() { return $this->id; }
		public function getName() { return $this->name; } 
		public function getLabel() { return $this->label; }
		public function getCreatedDate() { return $this->createdDate; }
		public function getLastUpdatedDate() { return $this->lastUpdatedDate; }
		public function getUser() { return $this->getCreatedBy(); }
		public function setId($a) { $this->id = $a; }
		public function setName($a) { $this->name = $a; }
		public function setLabel($a) { $this->label = $a; }
		public function setCreatedDate($a) { $this->createdDate = $a; }
		public function setLastUpdatedDate($a) { $this->lastUpdatedDate = $a; }
		public function setUser($a) { $this->setCreatedBy($a); }
		public function setLastUpdatedBy($a) { $this->lastUpdatedBy = $a; }
		public function getLastUpdatedBy() { return $this->lastUpdatedBy; }
		public function setCreatedBy($a) { $this->createdBy = $a; }
		public function getCreatedBy() { return $this->createdBy; }
		public function getPid() { return $this->pid; }
		public function setPid($a) { $this->pid = $a; }
		public function setState($a) { $this->state = $a; }
		public function getState() { return $this->state; }
		public function setIsDirty($a) { $this->isDirty = $a; }
		public function getIsDirty() { return $this->isDirty; }
		public function setStatus($a) { $this->status = $a; }
		public function getStatus() { return $this->status; }
		public function addDescriptor($a) {
			if(! in_array($a, $this->descriptor)) {
				array_push($this->descriptor, $a); 
			}
		}
		public function getDescriptor() { return $this->descriptor; }
		public abstract function getType();
	}
?>
