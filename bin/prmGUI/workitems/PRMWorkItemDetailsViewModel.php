<?php
	class PRMWorkItemDetailsViewModel extends PRMActionViewModel {
		private $selectionService = NULL;
		private $workItemService = NULL;
		private $securityService = NULL;
		public function __construct($parent) {
			$this->parent = $parent;
			$this->search = $parent->getSearch();
			$this->selectionService = PRMSelectionService::getInstance();
			$this->configurationService = PRMConfigurationService::getInstance();
			$this->uploadService = PRMUploadService::getInstance();
			$this->workItemService = PRMWorkItemService::getInstance();
			$this->securityService = PRMSecurityService::getInstance();
		}
		public function getName() { return "Details"; }
		public function getTitle() { return "Details"; }
		public function load() {
			print("<div id='workitem-properties'>");
			if ($this->selectionService->getSelectedWorkItem() != NULL) {
				$user = $this->securityService->getUserById($this->selectionService->getSelectedWorkItem()->getUser());
				print("<div id='workitem-type-div' style='");
				print("background-color:");
				print($this->workItemService->getWorkItemTypeColor($this->selectionService->getSelectedWorkItem()->getProperty("PRM_WORKITEM_TYPE_ID")->getValue()));
				print(" !important;'></div>");
				print("<form class='workitem-details-form' method='POST'>");
				print("<table class='list-form-table'>");
				// Basic properties
				print("<tr><td colspan=2><input type='text' required placeholder = 'Short description' name='PRM_ITEM_NAME' value='".$this->selectionService->getSelectedWorkItem()->getName()."'/></td></tr>");
                print("<tr><td colspan=2><input type='text' required placeholder = 'Label' name='PRM_ITEM_LABEL' value='".$this->selectionService->getSelectedWorkItem()->getLabel()."'/></td></tr>");

				print("<tr><th>Id</th><td><input type='text' readonly disabled value=\"{$this->selectionService->getSelectedWorkItem()->getId()}\"/></td></tr>");
				print("<tr><th>Last Updated Date</th><td><input type='text' readonly disabled value=\"{$this->selectionService->getSelectedWorkItem()->getLastUpdatedDate()}\"/><td><tr>");
				print("<tr><th>Status</th><td><select autocomplete='off' name='PRM_ITEM_STATUS'>");
				foreach (PRMService::getInstance()->getStatuses() as $status) {
					print("<option ");
					if ($status->getId() == $this->selectionService->getSelectedWorkItem()->getStatus()) {
						print(" selected ");
					} 
					print("value=\"{$status->getId()}\">{$status->getName()}</option>");
					
				}
				print("</select></td></tr>");
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
				print("<select name='PRM_ITERATION_ID'>");
				foreach($this->workItemService->getIterations() as $tempIteration) {
					print("<option ");
					if ($tempIteration->getId() == $this->selectionService->getSelectedWorkItem()->getIteration()) {
						print(" selected ");
					}
					print("value=\"{$tempIteration->getId()}\">{$tempIteration->getName()}</option>");
				}
				print("</select>");
				print('</td></tr>');

				// Advanced properties
				$advancedProperties = $this->selectionService->getSelectedWorkItem()->getProperties()->getItems();
				foreach($advancedProperties as $property) {
					if ($this->workItemService->isExtendedProperty($property->getName())) {
						print("<tr><th>".str_replace("_"," ",strtoupper($property->getName()))."</th><td><input type='text' name='".$property->getName()."' value=\"{$property->getValue()}\"/></td></tr>");
					}
				}

				print("<tr><td colspan=2><textarea name='PRM_WORKITEM_DESCRIPTION'>{$this->selectionService->getSelectedWorkItem()->getDescription()}</textarea></td></tr>");
				print("</table>");
				print("<input type='submit' name='update-workitem' value='Update'/>");
				print("</form>");
			}

			if (isset($_POST['update-workitem'])) {
				$this->assertResult($this->workItemService->updateWorkItem($this->selectionService->getSelectedWorkItem()->getId(), $_POST),SR::$__SUCCESS_UPDATE_WORKITEM__, SR::$__FAILURE_UPDATE_WORKITEM__, False); 
			}
			print("</div>");


			// Comments
			print("<div id='workitem-comments'>");
			print("<form method='POST' class='comment-form'>");
			print("<textarea name='comment-text' required placeholder='Add a comment....'></textarea>");
			print("<input type='submit' name='add-comment' value='POST'/>");
			print("</form>");
			if (isset($_POST['add-comment'])) {
				$this->assertResult($this->workItemService->addComment($this->selectionService->getSelectedWorkItem()->getId(), $_POST['comment-text']));
			}

			print("<br />");
			print("<table class='comments-table'>");
			print("<tr>");
			print("<th>User</th><th>Comment</th><th>Comment Date</th>");
			print("</tr>");
			$comments = $this->workItemService->getComments($this->selectionService->getSelectedWorkItem()->getId());
			foreach ($comments as $comment) {
				$user2 = $this->securityService->getUserById($comment->getUser());
				print("<tr>");
				print("<td>");
			   	print("<img class='avatar' src = \"{$this->uploadService->getUploadBase()}/images/avatars/{$user2->getAvatar()}\" />");
			   	print("<span class='avatar-label'>{$user2->getFirst()} {$user2->getLast()}</span>");				
				print("</td>");
				print("<td><textarea readonly>{$comment->getValue()}</textarea></td>");
				print("<td>{$comment->getLastUpdatedDate()}</td>");
				print("</tr>");
			}
			print("</table>");
			print("</div>");
		}
	}
?>
