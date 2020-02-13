<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmService/PRMService.php");
	class PRMInstanceViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        protected $parent = NULL;
		private $service = NULL;
        public function __construct($parent) {
			parent::__construct($parent);
			$this->service = PRMService::getInstance();
        }
        public function getName() { return "Instance"; }
        public function getTitle() { return "Instance"; }
        public function load() {
			global $__ROOT_FROM_PAGE__;
            print("<table id = 'plain-table'>");
            print("<tr><th style='width:25%;'>Host</th><td style='width:75%;'>{$this->service->getHostIp("127.0.0.1")}</td></tr>");
            print("<tr><th style='width:25%;'>Version</th><td style='width:75%;'>{$this->service->databaseVersion()}</td></tr>");
            $store = new JsonPropertyStore("{$__ROOT_FROM_PAGE__}/config/config.json");
            foreach($store->getKeys() as $key) {
                print("<tr>");
                print("<th>{$key}</th>");
                print("<td>{$store->getProperty($key)}</td>");
                print("</tr>");
            }
            print("</table>");
		}		
	}
?>
