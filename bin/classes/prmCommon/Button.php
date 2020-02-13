<?php
	class Button {
		private $title = "";
		private $name = "";
		private $target = NULL;
		private $action = NULL;
		private $color = NULL;

		public function __construct($title="Button", $target=NULL, $action=NULL, $color=NULL, $name="") {
			$this->title = $title;
			$this->name = $name;
			$this->target = $target;
			$this->action = $action;
			$this->color = $color;
		}

		public function load() {
			printf("<input type='submit' name='submit-%s' value='%s' style='background-color:%s;'/>", 
				strtolower($this->getName()), 
				$this->title,
				($this->color != NULL ? $this->color : ""));		
		}

		public function getTitle() { return $this->title; }
		public function setTitle($a) { $this->title = $a; }
		public function getTarget() { return $this->target; }
		public function setTarget($a) { $this->target = $a; }
		public function getAction() { return $this->action; }
		public function setAction($a) { $this->action = $a; }
		public function getName() { 
			if ($this->name == "") {
				return $this->title; 
			}
			return $this->name; 
		}
		public function setName($a) { $this->name = $a; }
	}
?>
