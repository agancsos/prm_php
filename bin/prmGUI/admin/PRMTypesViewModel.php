<?php
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/PRMActionViewModel.php");
	class PRMTypesViewModel extends PRMActionViewModel {
		protected $__ROOT__ = __DIR__;
		private $dataService = NULL;
		private $model = NULL;
		protected $parent = NULL;
		private $sources = array();
		private $currentSource = NULL;
		private function addSources() {
			array_push($this->sources, "ENABLED_MODULE");
			array_push($this->sources, "STATUS");
			array_push($this->sources, "GROUP");
			array_push($this->sources, "TEAM");
			array_push($this->sources, "STATE");
			array_push($this->sources, "CITY");
			array_push($this->sources, "COUNTRY");
			array_push($this->sources, "DESCRIPTOR");
			array_push($this->sources, "MODEL_DESCRIPTOR");
			array_push($this->sources, "RELATIONSHIP_TYPE");
			array_push($this->sources, "WORKITEM_TYPE");
			array_push($this->sources, "WORKITEM_FIELD");
			array_push($this->sources, "ITERATION");
		}
		public function __construct($parent) {
			parent::__construct($parent);
			$this->dataService = $parent->getDataService();
			$this->addSources();
			$this->currentSource = $this->sources[0];
			if(isset($_GET['source'])) {
				$this->currentSource = $_GET['source'];
			}
		}
		public function getName() { return "Types"; }
		public function getTitle() { return "Types"; }
		public function load() {
			$this->model = $this->dataService->getSourceData($this->currentSource);
			print("<div id = 'grid-container'>");
			for($i = 0; $i < 3; $i++) {
				print("<div>");
				print("<table class='grid-table'>");
				if($i == 0) {
					foreach($this->sources as $source) {
						print("<tr>");
						print("<th style='font-size:14pt;'");
						if($this->currentSource == $source) { 
							print(" class = 'selected-page' ");
						}
						print(">");
						print("<a ");
						if($this->currentSource == $source) { 
							print(" class = 'selected-page' ");
						}
						print(" href='?op=".$this->getName()."&source=".$source."'>");
						print(str_replace("_"," ",$source));
						print("</a>");
						print("</th>");
						print("</tr>");
					}
				}
				else if($i == 1) {
					foreach($this->model as $item) {
						$shouldShow = False;
						print("<tr>");
						print("<form method='POST' name='blah' id='item-".$item->getName()."'>");
						print("<input type='text'");
						if(! $item->getCanDelete()) {
							print(" readonly ");
						}
						print("  style='width:100%;display:inline-block;' name='label' placeholder={$this->currentSource} value='". $item->getName()."'/>");
						if ($this->currentSource == "WORKITEM_FIELD") {
							$shouldShow = True;
                        	print("<select name='workitem-type' style='width:100% !important;' autocomplete='off' value=\"{$item->getLastUpdatedDate()}\">");
                        	$types = $this->dataService->getWorkItemTypes();
                        	foreach ($types as $type) {
                            	print("<option value=\"{$type->getId()}\"");
								if ($item->getLastUpdatedDate() == $type->getId()) {
                                    print(" selected selected='selected' ");
                                }
								print(">{$type->getName()}</option>");
                        	}
                        	print("</select>");
                        	print("<select name='field-type' style='width:100%; !important;' autocomplete='off' value=\"{$item->getLastUpdatedBy()}\">");
                        	print("<option value='0'");
							if ($item->getLastUpdatedBy() == "0") { print(" selected "); }
							print(">TEXT</option>");
                        	print("<option value='1'");
							if ($item->getLastUpdatedBy() == "1") { print(" selected "); }
							print(">SELECT</option>");
                        	print("<option value='2'");
							if ($item->getLastUpdatedBy() == "2") { print(" selected "); }
							print(">TEXTAREA</option>");
                        	print("</select>");
                    	}
                    	else if($this->currentSource == "WORKITEM_TYPE") {
							$shouldShow = True;
                        	print("<select name='type-color' style='width:100%; !important;' autocomplete='off'>");
                        	$colors = [ "red", "orange", "yellow", "green", "blue", "indigo", "violet" ];
                        	foreach($colors as $color) {
                            	print("<option");
								if ($item->getLastUpdatedBy() == $color) {
									print(" selected ");
								}
								print("  value=\"{$color}\">{$color}</option>");
                        	}
                        	print("</select>");
                    	}
                    	else if($this->currentSource == "STATE") {
							$shouldShow = True;
                        	print("<input type = 'text' name = 'state-id' placeholder = 'NJ' value = \"{$item->getId()}\"/>");
                    	}

						print("<input type='submit' name='update-". $item->getId() ."'");
						if ($shouldShow == False) {
							print(" style='display:none;' ");
						}
						print(" value='Update'/>");
						if($item->getCanDelete()) {
							print("<input");
							if (!$shouldShow) {
								print("  class='delete-submit'");
							}
							else {
								print(" style='background-color:red;'");
							}
							print("  type='submit' name='delete-" . $item->getId()."'  value='X'/>");
						}
						print("</form>");
						if(isset($_POST["update-{$item->getId()}"]) && $_POST['label'] != "") {
							if ($this->currentSource == "STATE") {
								$this->dataService->getHandler()->runQuery("UPDATE PRM_STATE SET PRM_STATE_ID = '".$_POST['state-id']."', PRM_STATE_NAME='".$_POST['label']."' WHERE PRM_STATE_NAME='".$item->getName()."'");
							}
							else if ($this->currentSource == "WORKITEM_FIELD") {
								$this->dataService->getHandler()->runQuery("UPDATE PRM_ITEM_FIELD SET PRM_ITEM_FIELD_LABEL='".$_POST['label']."', PRM_ITEM_FIELD_NAME='".$_POST['label']."', PRM_ITEM_FIELD_TYPE='".$_POST['field-type']."', WORK_ITEM_TYPE_ID='".$_POST['workitem-type']."' WHERE PRM_ITEM_FIELD_ID='".$item->getId()."'");
							}
							else if ($this->currentSource == "WORKITEM_TYPE") {
								$this->dataService->getHandler()->runQUery("UPDATE PRM_WORKITEM_TYPE SET PRM_ITEM_TYPE_NAME = '".$_POST['label']."', WORKITEM_TYPE_LABEL = '".$_POST['label']."', PRM_ITEM_TYPE_COLOR='".$_POST['type-color']."' WHERE PRM_ITEM_TYPE_ID='".$item->getId()."'");
							}
							else {
								$item->setName($_POST['label']);
								$this->dataService->updateDataSource($this->currentSource, $item);
							}
							print("<script>window.location=window.location;</script>");
						}
						else if(isset($_POST["delete-{$item->getId()}"])) {
							$this->dataService->deleteSourceData($this->currentSource, $item);
							print("<script>window.location=window.location;</script>");
						}
						print("</tr>");
					}
				}
				else if($i == 2) {
					print("<form method='POST' id='add-value'>");
				   	print("<input type='text' name='label' placeholder='New' value=''/>");
					if ($this->currentSource == "WORKITEM_FIELD") {
						print("<select name='workitem-type' style='width:100% !important;'>");
						$types = $this->dataService->getWorkItemTypes();
						foreach ($types as $type) {
							print("<option value=\"{$type->getId()}\">{$type->getName()}</option>");
						}
						print("</select>");
						print("<select name='field-type' style='width:100%; !important;'>");
						print("<option value='0'>TEXT</option>");
						print("<option value='2'>SELECT</option>");
						print("<option value='3'>TEXTAREA</option>");
						print("</select>");
					}
					else if($this->currentSource == "WORKITEM_TYPE") {
						print("<select name='type-color' style='width:100%; !important;'>");																																																															
						$colors = [ "red", "orange", "yellow", "green", "blue", "indigo", "violet" ];
						foreach($colors as $color) {
							print("<option value=\"{$color}\">{$color}</option>");
						}
						print("</select>");									  
					}
					else if($this->currentSource == "STATE") {
						print("<input type = 'text' name = 'state-id' placeholder = 'NJ' />");
					}
					print("<input type='submit' name='add' value='Add'/>");
					print("</form>");
					if(isset($_POST['add']) && $_POST['label'] != "") {
						if ($this->currentSource == "WORKITEM_FIELD") {
							$item = new PRMWorkItem();
							$item->setProperty(new PRMPropertyItem("PRM_ITEM_TYPE_ID", $_POST['workitem-type']));
							$item->setName($_POST['label']);
							$this->dataService->addWorkItemField($item, $_POST['field-type']);
						}
						else if($this->currentSource == "WORKITEM_TYPE") {
							$this->dataService->addWorkItemType($_POST['label'], $_POST['type-color']);			 
						}
						else if ($this->currentSource == "STATE") {
							$this->dataService->getHandler()->runQUery("INSERT INTO PRM_STATE (PRM_STATE_ID, PRM_STATE_NAME) VALUES ('".$_POST['state-id']."', '".$_POST['label']."')");
						}
						else {
							$item = new PRMGeneralItem();
							$item->setName($_POST['label']);
							$this->dataService->addSourceData($this->currentSource, $item);
						}
						print("<script>window.location=window.location;</script>");
					}
				}	
				print("</table>");
				print("</div>");
			}
			print("</div>");
		}
	}
?>
