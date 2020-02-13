<?php
    class PRMWorkItemLinksViewModel extends PRMActionViewModel {
        private $selectionService = NULL;
        private $workItemService = NULL;
        public function __construct($parent) {
            $this->parent = $parent;
            $this->search = $parent->getSearch();
            $this->selectionService = PRMSelectionService::getInstance();
            $this->configurationService = PRMConfigurationService::getInstance();
            $this->uploadService = PRMUploadService::getInstance();
            $this->workItemService = PRMWorkItemService::getInstance();
        }
        public function getName() { return "Links"; }
        public function getTitle() { return "Links"; }
        public function load() {
			$relationshipTypes = $this->workItemService->getRelationshipTypes();
			foreach($relationshipTypes as $type) {
				print("<h3>{$type->getName()}</h3>");
				print("<table class='list-form-table'>");
				print("<tr>");
				print("<form method='POST' class='list-form'>");
				print("<td><input type='text' name='existing-id' value='' placeholder='Existing WorkItem ID'/></td>");
				print("<td><input type='submit' name='add-existing-item' value='Add'/></td>");
				print("</tr>");
				print("</form>");
				print("</table>");
				if (isset($_POST['add-existing-item'])) {
					$this->assertResult($this->workItemService->addRelation($this->selectionService->getSelectedWorkItem(), $_POST['existing-id'], $type));
				}
				print("<table class='list-form-table'>");
				$relatedItems = $this->workItemService->getRelatedItems($this->selectionService->getSelectedWorkItem(), $type);
				foreach($relatedItems as $item2) {
					print("<tr>");
					print("<form method='POST' class='list-form'>");
					print("<input type='hidden' name='item2-id' value=\"{$item2->getId()}\"/>");
					$newParams = $_GET;
                	if(isset($newParams['workitem'])) {
                    	$newParams['workitem'] = $item2->getId();
                	}
                	$newUrl = ($_SERVER['PHP_SELF']."?".http_build_query($newParams));
					print("<a href=\"$newUrl\"><td>{$item2->getId()}</td></a>");
					print("<td>{$item2->getName()}</td>");
					print("<td><input type='submit' name='delete-relation' value='Delete'/></td>");
					print("</form>");
					if (isset($_POST['delete-relation'])) {
						$this->assertResult($this->workItemService->removeRelation($this->selectionService->getSelectedWorkItem()->getId(), $_POST['item2-id']));
					}
					print("</tr>");
				}
				print("</table>");
			}
        }
    }
?>
