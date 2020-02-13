<?php
	class DataColumn {
		private $name	   = "";
		private $value	  = "";
		private $type	   = "String";

		public function __construct($name="", $value="", $type="") { 
			$this->name  = $name;
			$this->value = $value;
			$this->type  = $type;
		}

		/**
	 	 * Getters and setters
	 	 */
	 	public function getName() { return $this->name; }
	 	public function getValue() { return $this->value; }
	 	public function getType() { return $this->type; }
	 	public function setName($a) { $this->name = $a; }
	 	public function setValue($a) { $this->value = $a; }
	 	public function setType($a) { $this->type = $a; }
	}
?>
