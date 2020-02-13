<?php
	class PRMListFormViewModel {
		private $title = "FORM";
		private $updateObject = NULL;
		private $fields = array();
		private $formClass = "list-form";
		private $buttons = array();
		private $listButtons = array();
		private $rows = array();
		private $useRowButtons = 0;

		public function __construct() {
		}

		public function reloadUpdateObject() {
			
		}

		public function load() {
			print("<form method='POST' id='".$this->formClass."' class='".$this->formClass."'>");
			print("<table id='list-form-table' class='list-form-table'>");
			print("<tr>");
			$rowIndex = 0;
			foreach ($this->fields as $field) {
				print("<td style='width:" . (sizeof($this->fields) * 100) . " !important;'>");
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
				print("</td>");
				$rowIndex++;
			}
			foreach($this->buttons as $button) {
				print("<td>");
				$button->load();
				print("</td>");
			}
			print("</tr>");
			print("</table>");
			print("</form>");

			print("<br/>");

			$rowIndex = 0;
			print("<table id='list-form-table' class='list-form-table'>");
			foreach($this->rows as $row) {
				print("<tr>");
				print("<form method='POST' id='".$this->formClass."' class='".$this->formClass."'>");
				print("<td style='display:none;'><input name='row-id' type='hidden' value=\"{$row->getId()}\"></input></td>");
				foreach ($row->getFields() as $field) {
					if (($field->getRequiresPrevious() == True && $rowIndex > 0) || $field->getRequiresPrevious() == False) {
						print("<td>");
					 	$value = $field->getValue();
						print("<{$field->getFieldType()} autocomplete=\"off\" name='".$field->getName()."' ");
						if(!$field->getEnabled()) {
							print(" readonly ");
						}
			   			if(strtolower($field->getFieldType()) == "input type='text'" || strtolower($field->getFieldType()) == "text") {
							printf(" value='%s' /", $value);
						}
						print(">");
						if(strtolower($field->getFieldType()) == "select") {
							foreach($field->getOptions() as $option) {
								print("<option ");
								if ($field->getValue() == $option->getId()) { 
									print(" selected='selected' ");
								}
								printf("value = '%s'>%s</option>", $option->getId(), $option->getName());
							}
							print("</select>");
						}
			   			print("</td>");
					}
				}
				if ($this->useRowButtons === True) {
					if (sizeof($this->listButtons) > $rowIndex) {
						print("<td>");
						$this->listButtons[$rowIndex]->load();
						print("</td>");
					}
				}
				else {
					foreach($this->listButtons as $button) {
						print("<td>");
						$button->load();
						print("</td>");
					}
				}
				print("</form>");
				print("</tr>");
				$rowIndex++;
			}

			print("</table>");
		}

		public function setTitle($a) { $this->title = $a; } 
		public function getTitle() { return $this->title; }
		public function getFormClass() { return $this->formClass; }
		public function setFormClass($a) { $this->formClass = $a; }
		public function setUpdateObject($a) { $this->updateObject = $a; }
		public function getUpdateObject() { return $this->updateObject; }
		public function setUseRowButtons($a) { $this->useRowButtons = boolval($a); }
		public function getUseRowButtons() { return boolval($this->useRowButtons); }
		public function addButton($a) { 
			foreach($this->buttons as $button) {
				if($button->getTitle() == $a->getTitle()) {
					return;
				}
			}
			array_push($this->buttons, $a);
		}
		public function addListButton($a) {
			foreach($this->listButtons as $button) {
				if($button->getTitle() == $a->getTitle() && !$this->useRowButtons) {
					return;
				}
			}
			array_push($this->listButtons, $a);
		}
		public function addField($a) { array_push($this->fields, $a); }
		public function addRow($a) { array_push($this->rows, $a); }
	}
?>
