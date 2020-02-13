<?php
	class DataTable {
		private $name = "";
		private $rows = array();

		public function __construct($name="", $rows=array()) { 
			$this->name = $name;
			$this->rows = $rows;
		}

		/**
	 	 * Getters and getters
	 	 */
		public function getRows() { return $this->rows; }
		public function addRow($a) { array_push($this->rows, $a);  }
		public function getColumnIndex($name = "") {
			$i = 0;
			foreach($this->rows[0]->getColumns() as $column) {
				if(strtolower($column->getName()->orgname) == str_replace("-", "_", strtolower($name))) {
					return $i;
				}
				$i++;
			}
			return 0;
		}
	}
?>
