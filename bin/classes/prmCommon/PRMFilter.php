<?php
	class PRMFilter extends PRMItem {
		private $joinCondition = "";
		private $field = "";
		private $condition = "";
		private $value = "";

		public function __construct($joinCondition="", $field="", $condition="", $value="") {
			$this->joinCondition = $joinCondition;
			$this->field = $field;
			$this->condition = $condition;	
			$this->value = $value;
		}

		public function getJoinCondition() { return $this->joinCondition; }
		public function setJoinCondition($a) { $this->joinCondition = $a; }
		public function getField() { return $this->field; }
		public function setField($a) { $this->field = $a; }
		public function getCondition() { return $this->condition; }
		public function setCondition($a) { $this->condition = $a; }
		public function getValue() { return $this->value; }
		public function setValue($a) { $this->value = $a; }
		public function getType() { return "Filter"; }
	}
?>
