<?php
	class PRMFormViewModel {
		private $title = "FORM";
		private $updateObject = NULL;
		private $fields = array();
		private $formClass = "inline-form";
		private $buttons = array();

		public function __construct() {
		}

		private function reloadUpdateObject() {
		}

		public function load() {
			//$this->updateObject = $this->selectionService->getSelectedItem();
			print("<form method='POST' id='".$this->formClass."'>");
			print("<table id='plain-table'>");
			foreach ($this->fields as $field) {
				print("<tr>");
				print("<th>".strtoupper(str_replace("PRM ", "", $field->getLabel()))."</th>");
				print("<td>");
				//$value = $this->updateObject->$property();
				$value = "TEST";
				print("<{$field->getFieldType()} name='".$field->getName()."' ");
				if(!$field->getEnabled()) {
					print(" readonly disabled ");
				}
				if(strtolower($field->getFieldType()) == "text") {
					printf(" value='%s' /", $value);
				}
				print(">");
				if(strtolower($field->getFieldType()) == "select") {
					foreach($field->getOptions() as $option) {
						printf("<option value='%s'>%s</option>", $option->getId(), $option->getName());
					}
					print("</select>");
				}
				if(strtolower($field->getFieldType()) == "textarea") {
					print("</textarea>");
				}
				print("</td>");
				print("</tr>");
			}
			print("</table>");
			foreach($this->buttons as $button) {
				$button->load();
				$buttonTitle = "submit-" . strtolower($button->getTitle());
				if(isset($_POST[$buttonTitle])) {
					$this->reloadUpdateObject();
					if($button->getTarget() != NULL && $button->getAction() != NULL) {
						$target = $button->getTarget();
						$action = $button->getAction();
						if($target->$action($this->updateObject) == False) {
							?><script>alert("Failed to update object...");</script><?php
						}
						else {
							?><script>alert("Updated object!");</script><?php
						}
					}
				}
			}
			print("</form>");
		}

		public function setTitle($a) { $this->title = $a; } 
		public function getTitle() { return $this->title; }
		public function getFormClass() { return $this->formClass; }
		public function setFormClass($a) { $this->formClass = $a; }
		public function setUpdateObject($a) { $this->updateObject = $a; }
		public function getUpdateObject() { return $this->updateObject; }
		public function addButton($a) { 
			foreach($this->buttons as $button) {
				if($button->getTitle() == $a->getTitle()) {
					return;
				}
			}
			array_push($this->buttons, $a);
		}
		public function addField($a) { array_push($this->fields, $a); }
	}
?>
