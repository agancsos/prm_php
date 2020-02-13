<?php
	class PRMPropertyFormViewModel {
		private $title = "FORM";
		private $readOnlyFields = array();
		private $updateObject = NULL;
		private $model = NULL;
		private $formClass = "inline-form";
		private $buttons = array();

		public function __construct($type) {
			$this->model = $type;
		}

		private function reloadUpdateObject(){
			foreach (get_class_methods($this->model) as $property) {
				if (str_replace("set", "", $property) != $property) {
					$formFieldName = str_replace("set", "", $property);
					$this->updateObject->$property($_POST["{$formFieldName}"]);
				}
			}		
		}

		public function load() {
			if ($this->model != NULL) {
				print("<form method='POST' id='".$this->formClass."'>");
				print("<table id='plain-table'>");
				foreach (get_class_methods($this->model) as $property) {
					if (str_replace("get", "", $property) != $property) {
						print("<tr>");
						print("<th>".strtoupper(str_replace("get", "", $property))."</th>");
						print("<td>");
						$value = $this->updateObject->$property();
						if(gettype($value) == "array") {
							$value = sizeof($value);
							print("<input type='text' readonly value=\"{$value}\" />");
						}
						else if(gettype($value) == "boolean" || str_replace("Is", "", $property) != $property) {
							print("<select style='width:100%;border:0;' name='".str_replace("get","",$property)."'");
							if (in_array(str_replace("get","",$property), $this->readOnlyFields) || in_array(str_replace("is","",$property), $this->readOnlyFields) || intval($this->updateObject->getId() < 0)) {
								print(" disabled ");
							}
							print(">");
							print("<option value='0' ");
							if ($value == False || $value == "0") { 
								print(" selected "); 
							}
							print(">False</option>");
							print("<option value='1'");
							if($value == True || $value == "1") { 
								print(" selected "); 
							}
							print(">True</option>");
							print("</select>");
						}
						else if(str_replace("Id", "", $property) != $property) {
							print("<input type='text' readonly value='".str_pad($value, SR::$__WORKITEM_ID_PAD_LENGTH__, "0", STR_PAD_LEFT)."' />");
						}
						else {
							print("<input type='text' name='".str_replace("get","",$property)."'");
							if (in_array(str_replace("get","",$property), $this->readOnlyFields) || intval($this->updateObject->getId()) < 0) {
								print(" readonly ");
							}
							print(" value=\"{$value}\">");
						}
						print("</td>");
						print("</tr>");
					}
				}
				print("</table>");
				foreach($this->buttons as $button) {
					$button->load();
					$buttonTitle = "submit-" . strtolower($button->getName());
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
						?><script>window.location=window.location;</script><?php
					}
				}
				print("</form>");
			}
			else {
				print("No object type provided....");
			}
		}

		public function setTitle($a) { $this->title = $a; } 
		public function getTitle() { return $this->title; }
		public function getReadOnlyFields() { return $this->readOnlyFields; }
		public function addReadonlyField($a) { array_push($this->readOnlyFields, $a); }
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
	}
?>
