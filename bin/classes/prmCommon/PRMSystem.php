<?php
	class PRMSystem {
		private $source = "";
		private $target = "";

		public function __construct($a="",$b="") {
			$this->source = $a;
			$this->target = $b;
		}

		public static function fileExists($a) { return file_exists($a); }

		public function readFile() {
			if($this->fileExists($this->source)) {
				return file_get_contents($this->source, TRUE);
			}
		}
	}
?>
