<?php
	include_once("PRMItem.php");

	class PRMCountry extends PRMItem {
		public function __construct() { parent::__construct(); }
		public function getType() { return "Country"; }
	}
	class PRMCity extends PRMItem {
		public function __construct() { parent::__construct(); }
		public function getType() { return "City"; }
	}
	class PRMState extends PRMItem {
		public function __construct() { parent::__construct(); }
		public function getType() { return "State"; }
	}
?>
