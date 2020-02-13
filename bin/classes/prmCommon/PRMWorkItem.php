<?php
	include_once("PRMItem.php");
	class PRMWorkItem extends PRMItem {	
		private $properties = NULL;
		private $description = "";
		private $iteration = "";
		public function __construct() { 
			$this->properties = new PRMPropertyCollection();
		}
		public function setProperty($a) {
			if (!$this->properties->contains($a)) {
				$this->properties->addItem($a);
			}
		}
		public function setIteration($a) { $this->iteration = $a; }
		public function getIteration() { return $this->iteration; }
		public function setDescription($a) { $this->description = $a; }
		public function getDescription() { return $this->description; }
		public function getProperty($a) { return $this->properties->getItem($a); }
		public function getProperties() { return $this->properties; }
		public function getType() { return "Workitem"; }
	}
?>
