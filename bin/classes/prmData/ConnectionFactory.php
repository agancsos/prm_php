<?php
	include_once("DataConnection.php");
	include_once("DataConnectionMySql.php");
	include_once("DataConnectionOracle.php");
	include_once("DataConnectionMSSql.php");

	class ConnectionFactory {
		public function __construct() { }
		public static function createConnection($type) {
			foreach(ConnectionFactory::getProviders() as $provider) {
				if($provider->getProviderName() == $type) {
					$className = get_class($provider);
					return new $className();
				}
			}
		}
		public static function getProviders() {
			$providers = array();
			array_push($providers, new DataConnectionMySql());
			//array_push($providers, new DataConnectionOracle());
			//array_push($providers, new DataConnectionMSSql());
			return $providers;
		}
	}
?>
