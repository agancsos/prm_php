<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	class PRMUsersViewModel extends PRMActionViewModel {
		protected $__ROOT__ = __DIR__;
		private $dataService = NULL;
		private $model = NULL;
		protected $parent = NULL;
		private $service = NULL;
		private $users = array();
		private $securityService = NULL;
		private $objectService = NULL;
		private $currentId = 0;
		private $fields = array();
		public function __construct($parent) {
			parent::__construct($parent);
			$this->service = PRMService::getInstance();
			$this->dataService = $parent->getDataService();
			$this->objectService = PRMObjectService::getInstance();	
			$this->securityService = PRMSecurityService::getInstance();
			if(isset($_GET['id'])) {
				$this->currentId = $_GET['id'];
			}
		}
		public function getName() { return "Users"; }
		public function getTitle() { return "Users"; }
		public function load() {
			$this->users = $this->objectService->getUsers();
			$this->fields = $this->service->getFormFields("USER");
			if(!isset($_GET['id']) && sizeof($this->users) > 0) {
				$this->currentId = $this->users[0]->getId();
			}
			print("<div id = 'grid-container'>");
			for($i = 0; $i < 3; $i++) {
				print("<div class='grid-div'");
				print(">");
				print("<table class='grid-table'>");
				if($i == 0) {
					foreach($this->users as $user) {
						print("<tr>");
						print("<th style='font-size:14pt;'");
						if($this->currentId == $user->getId()) { 
							print(" class='selected-page' ");
						}
						print(">");
						print("<a ");
						if($this->currentId == $user->getId()) { 
							print(" class='selected-page' ");
						}
						print(" href='?op=".$this->getName()."&id=".$user->getId()."'>");
						print("{$user->getFirst()} {$user->getLast()}");
						print("</a>");
						print("</th>");
						print("</tr>");
					}
				}
				else if($i == 1) {
					$currentUser = $this->dataService->getHandler()->query("SELECT * FROM PRM_USER WHERE PRM_USER_ID = '".$this->currentId."'");
					print("<form method='POST' width=100%  id='add-value'>");
					foreach($this->fields as $field) {
						$fieldValue = "";
						if($currentUser->getRows() > 0 && $currentUser->getRows() != NULL) {
							$columnIndex = $currentUser->getColumnIndex($field->getName());
							$fieldValue = $currentUser->getRows()[0]->getColumns()[$columnIndex]->getValue();
						}
						if($field->getFieldType() == "select") {
							print("<select style='width:100%;' name=\"{$field->getName()}\" title=\"{$field->getLabel()}\">");																											 
							print("<option value=''>".str_replace("PRM ", "", str_replace("ID", "", $field->getLabel()))."</option>");
							foreach($field->getOptions() as $option) {
								print("<option value=\"{$option->getID()}\"");
								if($fieldValue == $option->getID()) {
									print(" selected ");
								}
								print(">{$option->getName()}</option>");
							}
							print("</select>");
						}
						else {
							print("<input title=\"{$field->getLabel()}\"");
							if(!$field->getEnabled()) {
								print(" readonly ");
							}
							print(" type='".$field->getFieldType()."' name=\"{$field->getName()}\" placeholder=\"{$field->getLabel()}\" value=\"{$fieldValue}\" />");
						}
					}
					print("<input type='submit' name='update' value='Update'/>");
					print("<input type='submit' name='password' value='Update Password'/>");
					print("</form>");
					if(isset($_POST['update'])) {
						$user = PRMFormService::userFromForm($_POST);
						$result = $this->securityService->updateUser($user); 
						if($result === True) {																																										
							$this->alert("Account has been updated ({$user->getName()})");
						}																																															 
						else {																																														
							$this->alert("Error updating account: {$result}");
						}  
						print("<script>window.location=window.location;</script>");
					}
					else if(isset($_POST['password'])) {
						$this->securityService->updatePassword($user, $_POST['PRM-USER-PASS']);
					}
				}
				else if($i == 2) {
					print("<form method='POST' style='100%;'  id='add-value'>");
					foreach($this->fields as $field) {
						if($field->getFieldType() == "select") {
							print("<select style='width:100%;' name=\"{$field->getName()}\">");
							print("<option value=''>".str_replace("PRM ", "", str_replace("ID", "", $field->getLabel()))."</option>");
							foreach($field->getOptions() as $option) {
								print("<option value=\"{$option->getID()}\">{$option->getName()}</option>");
							}
							print("</select>");
						}
						else {
							print("<input ");
					   		if(!$field->getEnabled()) {
								print(" readonly ");
							}
							print(" type='".$field->getFieldType()."' name=\"{$field->getName()}\" placeholder=\"{$field->getLabel()}\" value='' />");																		
						}
					}
					print("<input type='submit' name='add' value='Add'/>");
					print("</form>");
					if(isset($_POST['add'])) {
						$user = PRMFormService::userFromForm($_POST);
						$check = $this->securityService->canAdduser($user);
						if($check == True) {
							$result = $this->securityService->addUser($user);
							if($result === True) {
								$this->alert("Welcome to PRM ({$user->getName()})");
							}
							else {
								$this->alert("Error creating account: {$result}");
							}
							print("<script>window.location=window.location;</script>");
						}
						else {
							$this->alert($check);
						}
					}
				}	
				print("</table>");
				print("</div>");
			}
			print("</div>");
		}
	}
?>
