<?php
	include_once("PropertyStore.php");
	class JsonPropertyStore extends PropertyStore {
		public function __construct($source=NULL) {
			parent::__construct($source);
			$this->source = $source;
			if(file_exists($source)) {
				$this->handle = json_decode(file_get_contents($this->source));
			}
		}
		public function getProperty($name) { 
			if(property_exists($this->handle, $name)) {
				return $this->handle->{$name}; 
			}
			return "";
		}
		public function writeProperty($name, $value) {
			$this->handle->{$name} = $value;
			$file = fopen($this->source, "w");
			fwrite($file, json_encode($this->handle));
			fclose($file);
		}
		public function getKeys() { 
			$result = array();
			foreach($this->handle as $key=>$value) { 
				array_push($result, $key);
			}
			return $result;
		}
	}
?>
