<?php
	include_once("PRMItem.php");
	class PRMAgent extends PRMItem {
		private $host; 
		private $javaVersion;
		private $javaPath;
		private $version;
		private $installPath;

		public function __construct() { 
			parent::__construct();
		}
		public function setHost($a) { $this->host = $a; }
		public function setJavaVersion($a) { $this->javaVersion = $a; }
		public function setJavaPath($a) { $this->javaPath = $a; }
		public function setVersion($a) { $this->version = $a; }
		public function setInstallPath($a) { $this->installPath = $a; }
		public function getHost() { return $this->host; }
		public function getJavaVersion() { return $this->javaVersion; }
		public function getJavaPath() { return $this->javaPath; }
		public function getVersion() { return $this->version; }
		public function getInstallPath() { return $this->installPath; }
		public function getType() { return "Agent"; }
	}
?>
