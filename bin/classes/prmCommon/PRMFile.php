<?php
	include_once("PRMItem.php");
	class PRMFile extends PRMItem {
		private $path = "";
		private $itemId = NULL;
		private $isPrivate = False;
		private $fileType = "FILE";
		
		public function __construct() {
			parent::__construct();
		}
		public function setFileType($a) { $this->fileType = $a; }
		public function getFileType() { return $this->fileType; }
		public function setIsPrivate($a) { $this->isPrivate = $a; }
		public function getIsPrivate() { return $this->isPrivate; }
		public function setPath($a) { $this->path = $a; }
		public function getPath() { return $this->path; }
		public function setItemId($a) { $this->itemId = $a; }
		public function getItemId() { return $this->itemId; }
		public function getType() { return "File"; }
		public function getFileName() { 
			if ($this->getName() != "") {
				return $this->getName();
			}
			$comps = explode("/", $this->path);
			return $comps[sizeof($comps) - 1];
		}
	}
?>
