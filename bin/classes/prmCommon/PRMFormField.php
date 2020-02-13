<?php
	include_once("PRMItem.php");
	class PRMFormField extends PRMItem {
		private $type = NULL;
		private $columnName = NULL;
		private $enabled = True;
		private $options = NULL;
		private $getterName = "";
		private $canDelete = True;
		public function __construct($type="text", $column="", $name="") {
			parent::__construct();
			$this->type = $type;
			$this->name = $name;
			$this->columnName = $column; 
			$this->label = str_replace("_", " ", $column);
		}

		public function getType() { return "Field"; }
		public function getFieldType() { return $this->type; }
		public function getColumnName() { return $this->columnName; }
		public function setFieldType($a) { $this->type = $a; }
		public function setColumnName($a) { 
			$this->name = str_replace("_", "-", $a);
			$this->columnName = $a; 
			$this->label = str_replace("_", " ", $a);
		}
		public function getEnabled() { return $this->enabled; }
		public function setEnabled($a) { $this->enabled = $a; }
		public function setOptions($a) { $this->options = $a; }
		public function addOption($a) {
			if($this->options == NULL) {
				$this->options = array();
			}
			if(!in_array($a, $this->options)) {
				array_push($this->options, $a);
			}
		}
		public function getOptions() { return $this->options; }
		public function getGetter() { return $this->getterName; }
		public function setGetter($a) { $this->getterName = $a; }
		public function setCanDelete($a) { $this->canDelete = $a; }
		public function getCanDelete() { return $this->canDelete; }
	}
	class PRMAdvancedFormField extends PRMFormField {
		private $table = "";
		private $classField = "";
		private $value = NULL;
		private $requiresPrevious = False;
		
		public function __construct($type="text", $column="", $name="", $table="") {
			parent::__construct($type,$column,$name);
			$this->table = $table;
		}

		public function getTable() { return $this->table; }
		public function setTable($a) { $this->table = $a; }
		public function getClassField() { return $this->classField; }
		public function setClassField($a) { $this->classField = $a; }
		public function setValue($a) { $this->value = $a; }
		public function getValue() { return $this->value; }
		public function getRequiresPrevious() { return $this->requiresPrevious; }
		public function setRequiresPrevious($a) { $this->requiresPrevious = $a; }
	}
?>
