<?php
	class PRMTreeNode {
		private $name = "";
		private $nodeType = "";
		private $id = "";
		private $isShared = True;
		private $children = array();
		private $isFolder = False;
		public function __construct($id="-1", $name="/", $type="Folder", $shared=True) {
			$this->name = $name;
			$this->id = $id;
			$this->nodeType = $type;
			$this->isShared = $shared;
			if($this->nodeType == "Folder") {
				$this->isFolder = True;
			}
		}
		public function addChild($child) {
			if($this->isFolder == True) {
				if(!in_array($child, $this->children)) {
					array_push($this->children, $child);
				}
			}
		}
		public function getChildren() { return $this->children; }
		public function getId() { return $this->id; }
		public function getName() { return $this->name; }
		public function getNodeType() { return $this->nodeType; }
		public function getIsFolder() { return $this->isFolder; }
		public function getIsShared() { return $this->isShared; }
		public function setName($a) { $this->name = $a; }
		public function setIsShared($a) { $this->isShared = $a; }
	}
?>
