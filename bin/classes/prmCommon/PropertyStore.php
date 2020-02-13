<?php
	abstract class PropertyStore {
		protected $handle = NULL;
		protected $source;
		public function __construct($source=NULL) {
			$this->source = $source;
		}
		public abstract function getProperty($name);
		public abstract function writeProperty($name, $value);
		public abstract function getKeys();
	}
?>
