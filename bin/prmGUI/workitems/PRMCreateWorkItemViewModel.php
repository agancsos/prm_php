<?php
    class PRMCreateWorkItemViewModel extends PRMActionViewModel {
        private $selectionService = NULL;
        private $workItemService = NULL;
		private $workitemType = NULL;
		private $securityService = NULL;
        public function __construct($parent) {
            $this->parent = $parent;
            $this->search = $parent->getSearch();
            $this->selectionService = PRMSelectionService::getInstance();
            $this->configurationService = PRMConfigurationService::getInstance();
            $this->uploadService = PRMUploadService::getInstance();
            $this->workItemService = PRMWorkItemService::getInstance();
			$this->securityService = PRMSecurityService::getInstance();
			if (isset($_GET['workitem_type'])) {
				$this->workitemType = $_GET['workitem_type'];
			}
        }
        public function getName() { return "Create"; }
        public function getTitle() { return "Create"; }
        public function load() {
			print("<div id='workitem-properties'>");
			$user = PRMSessionService::getInstance()->getUser();
			if ($this->selectionService->getSelectedWorkItem() != NULL) {
				$user = $this->securityService->getUserById($this->selectionService->getSelectedWorkItem()->getUser());
			}
	       	print("<div id='workitem-type-div' style='");
			print("background-color:");
			print($this->workItemService->getWorkItemTypeColor($this->workitemType));
			print(" !important;'></div>");
			print("<form class='workitem-details-form' method='POST'>");
			print("<table class='list-form-table'>");
			// Basic properties
			print("<tr><th>Workitem Type</th><td><select required name='PRM_WORKITEM_TYPE_ID' onchange='reloadWithType(this)'>");
			print("<option value=''>Type</option>");
			foreach ($this->workItemService->getWorkItemTypes() as $type) {
				print("<option");
				if ($this->workitemType == $type->getId()) {
					print(" selected ");
				}
				print(" value=\"{$type->getId()}\">{$type->getName()}</option>");
			}
			print("</select></td></tr>");
			print("<tr><td colspan=2><input type='text' placeholder='Short description' required name='PRM_ITEM_NAME'/></td></tr>");
			print("<tr><th>Last Updated Date</th><td><input type='text' readonly disabled/><td><tr>");
			print("<tr><th>Owner</th><td>");
			print("<select name='PRM_USER_ID'>");
			print("<option value=''>Owner</option>");
			foreach(PRMObjectService::getInstance()->getUsers() as $tempUser) {
				print("<option ");
				if ($tempUser->getId() == $user->getId()) {
					print(" selected ");
				}
				print("value=\"{$tempUser->getId()}\">{$tempUser->getFirst()} {$tempUser->getLast()}</option>");
			}
			print("</select>");
			print('</td></tr>');
			print("<tr><th>Iteration</th><td>");
        	print("<select name='PRM_WORKITEM_TYPE_ID'>");
        	foreach($this->workItemService->getIterations() as $tempIteration) {
            	print("<option value=\"{$tempIteration->getId()}\">{$tempIteration->getName()}</option>");
        	}
        	print("</select>");
        	print('</td></tr>');

			// Advanced properties
			$advancedProperties = $this->workItemService->getRawColumns($this->workitemType);;
			foreach($advancedProperties as $property) {
				if ($this->workItemService->isExtendedProperty($property)) {
					print("<tr><th>".str_replace("_"," ",strtoupper($property->getName()))."</th><td><input type='text' name='".$property->getName()."'/></td></tr>");
				}
			}

        	print("<tr><td colspan=2><textarea placeholder='Description' name='PRM_WORKITEM_DESCRIPTION'></textarea></td></tr>");
			print("</table>");
			print("<input type='submit' name='create-workitem' value='Create'/>");
			print("</form>");

			if (isset($_POST['create-workitem'])) {
				$this->assertResult($this->workItemService->addWorkItem($_POST, $this->workitemType), SR::$__SUCCESS_CREATE_WORKITEM__, SR::$__FAILURE_CREATE_WORKITEM__, True);				
			}
		}
    }
?>
