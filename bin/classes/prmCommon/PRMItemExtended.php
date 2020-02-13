<?php
	include_once("PRMItem.php");
	class PRMPropertyItem extends PRMItem {
		private $value = NULL;
		public function __construct($name="",$value="") {
			parent::__construct();
			$this->name = $name;
			$this->value = $value;
		}
		public function setValue($a) { $this->value = $a; }
		public function getValue() { return $this->value; }	
		public function getType() { return "Property"; }
	}

	class PRMPropertyCollection {
		private $items = array();
		public function __construct() { 
		}
		public function addItem($a) { 
			if(!$this->contains($a)) {
				array_push($this->items, $a); 
			}
			else {
				foreach($this->items as $item) {
					if ($item->getName() == $a->getName()) {
						$item->setValue($a->getValue());
					}
				}
			}
		}
		public function getItems() { return $this->items; }
		public function contains($a) { 
			foreach($this->items as $item) {
				if($item->getName() == $a->getName()) {
					return True;
				}
			}
			return False;
		}
		public function getItem($a) { 
			foreach($this->items as $item) {
				if ($item->getName() == $a) {
					return $item;
				}
			}
			return new PRMPropertyItem();
		}
		public function removeItem($a) { 
			$index = 0;
			foreach($this->items as $item) {
				if ($item->getName() == $a) {
					unset($this->items[$index]);
				}
				$index++;
			}
		}
	}

	class PRMGeneralItem extends PRMItem {
		private $canDelete = True;
		public function getType() { return "Generic"; }
		public function setCanDelete($a) { $this->canDelete = $a; }
		public function getCanDelete() { return $this->canDelete; }
	}

	class PRMCollectionItem extends PRMItem {
		private $items = array();
		public function getType() { return "Collection"; }
		public function addItem($item) { array_push($this->items, $item); }
		public function getItems() { return $this->items; }
	}
?>
