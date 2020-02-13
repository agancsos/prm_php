<?php	
	class PRMExportItem {
		private $dataService = NULL;
		private $exportTypes = array();
		public function __construct($dataService) {
			$this->dataService = $dataService;
			$this->date = new DateTime();
			$this->client = gethostname();
		}

		public function export() {
			$exportTypes = [ "STATUS", "GROUP", "TEAM", "STATE", "CITY", "COUNTRY", "DESCRIPTOR", "MODEL_DESCRIPTOR" ];
			foreach($exportTypes as $exportType) {
				$this->{"{$exportType}"} = array();
				$rawResults = $this->dataService->getHandler()->query("SELECT * FROM PRM_{$exportType}")->getRows();
				foreach($rawResults as $row) {
					array_push($this->{"{$exportType}"}, $row->getColumns()[1]->getValue());
				}
			}
			return json_encode($this);		
		}
	}
?>
