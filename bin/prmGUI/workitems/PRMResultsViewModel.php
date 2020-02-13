<?php
    class PRMResultsViewModel extends PRMActionViewModel {
        protected $__ROOT__ = __DIR__;
        private $selectionService = null;
        private $workItemService = null;
		private $dataService = NULL;
		private $securityService = NULL;
		private $uploadService = NULL;
        protected $parent = NULL;
        public function __construct($parent) {
            parent::__construct($parent);
            $this->selectionService = PRMSelectionService::getInstance();
            $this->workItemService = PRMWorkItemService::getInstance();
			$this->dataService = PRMDataService::getInstance();
			$this->securityService = PRMSecurityService::getInstance();
			$this->uploadService = PRMUploadService::getInstance();
			$this->configService = PRMConfigurationService::getInstance();
        }
        public function getName() { return "Results"; }
        public function getTitle() { return "Results"; }
        public function load() {
            print("<table id='plain-table'>");
			print("<tr>");
			$headers = $this->workItemService->getColumnHeaders($this->selectionService->getSelectedNode()->getId());
			$query = $this->workItemService->buildQuery($this->selectionService->getSelectedNode()->getId());
			print("<th>WorkItem ID</th>");
			foreach($headers as $header) {
				printf("<th>%s</th>", strtoupper(str_replace("_", " ", $header)));
			}
			print("</tr>");
			$records = $this->dataService->getHandler()->query($query);
			foreach($records->getRows() as $row) {
				print("<tr>");
				$newParams = $_GET;
                $newParams['workitem'] = $row->getColumn("PRM_ITEM_ID")->getValue();
                $newUrl = ($_SERVER['PHP_SELF']."?".http_build_query($newParams));
                print("<td><a class='td-link' href=\"$newUrl\">".$row->getColumn("PRM_ITEM_ID")->getValue()."</td>");
				foreach($headers as $header) {
					$value = $this->workItemService->getColumnValue($this->selectionService->getSelectedNode()->getId(), $header, $row->getColumn("PRM_ITEM_ID")->getValue());
					print("<td>");
					if($header == "PRM_USER_ID") {
						$user = $this->securityService->getUserById($value);
                		//print("<img class='avatar' src = \"{$this->uploadService->getUploadBase()}/images/avatars/{$user->getAvatar()}\" />");
						print("<span>{$user->getFirst()} {$user->getLast()}</span>");
					}
					else if($header == "LAST_UPDATED_BY") {
                        $user = $this->securityService->getUserById($value);
						if ($user != NULL) {
                        	//print("<img class='avatar' src = \"{$this->uploadService->getUploadBase()}/images/avatars/{$user->getAvatar()}\" />");
                        	print("<span>{$user->getFirst()} {$user->getLast()}</span>");
						}
                    }
					else if($header == "PRM_ITEM_STATE") {
						if ($value == 0) { print("Closed"); }
						else if($value == 1) { print("Active"); }	
					}
					else if($header == "PRM_ITEM_STATUS") {
						print(str_replace("_STATUS", "", PRMObjectService::getInstance()->getStatusName($value)));
					}
					else if($header == "PRM_WORKITEM_TYPE_ID") {
						print("<div style=\"min-height: 50% !important;min-width:100% !important;background-color:{$this->workItemService->getWorkItemTypeColor($value)}\">{$this->workItemService->getWorkItemTypeName($value)}</div>");
					}
					else if($header == "PRM_ITEM_ISDIRTY") {
						print(($value == 0 ? "False" : "True"));
					}
					else if($header == "PRM_ITEM_TYPE_ID") {
						print($this->workItemService->getWorkItemTypeName($value));
					}
					else {
						print($value);
					}
					print("</td>");
				}	
				print("</tr>");
			}
            print("</table>");
			if($this->configService->__SHOW_QUERY__ == "1") {
				printf("Query:<br /> %s <br/>", $query);
			}
        }
    }
?>
