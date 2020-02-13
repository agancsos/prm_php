<?php
	class DataRow {
		private $columns = array();

		public function __construct($columns=array()) { 
			$this->columns = $columns;
		}

		/**
	 	 * Getters and setters
	 	 */
	 	public function getColumns() { return $this->columns;  }
		public function getColumn($name) {
			foreach($this->columns as $cursor) {
				if($cursor->getName()->name == $name) {
					return $cursor;
				}
			}
			return NULL;
		}
	 	public function addColumn($a) { 
			foreach($this->columns as $column) {
				if($column->getName() == $a->getName()) {
					return;
				}
			}
			array_push($this->columns, $a); 
		}
	}
?>
