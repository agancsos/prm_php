<?php
	class PRMListFormItem {
		private $id = "";
		private $fields = array();
		
		public function __construct($id="",$fields=array()) {
			$this->id = $id;
			$this->fields = $fields;
		}
		
		public function addField($a) { array_push($this->fields, $a); }
		public function getFields() { return $this->fields; }
		public function getId() { return $this->id; }
	}
?>
