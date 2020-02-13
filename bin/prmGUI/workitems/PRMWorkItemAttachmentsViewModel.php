<?php
	class PRMWorkItemAttachmentsViewModel extends PRMActionViewModel {
		private $uploadService = NULL;
		private $selectionService = NULL;
		public function __construct($parent) {
			$this->parent = $parent;
			$this->search = $parent->getSearch();
			$this->selectionService = PRMSelectionService::getInstance();
			$this->configurationService = PRMConfigurationService::getInstance();
			$this->uploadService = PRMUploadService::getInstance();
		}
		public function getName() { return "Attachments"; }
		public function getTitle() { return "Attachments"; }
		public function load() {
			print("<form method='POST' id='settings-form' enctype='multipart/form-data'>");
			print("<input type='file' name='attachment' />");
			print("<input type='submit' name='upload-attachment' value='Attach' />");
			if(isset($_POST['upload-attachment'])) {
				if(in_array(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION), PRMUploadService::$ALLOWED_DOCUMENT_FILES)) {
					$result = $this->uploadService->uploadAttachment($_FILES['attachment'], $this->selectionService->getSelectedWorkItem());
					if($result === False) {
						$this->alert("Failed to upload file");
					}
					else {
					}
					print("<script>window.location=window.location;</script>");
				}
				else {
					$this->alert("File type not allowed");
				}
			}
			print("<br/>");
			if ($this->selectionService->getSelectedWorkItem() != NULL) {
				$files = $this->uploadService->getAttachments($this->selectionService->getSelectedWorkItem()->getId());
				print("<table class='attachments-table'>");
				print("<tr>");
				print("<th>File</th>");
				print("<th>Modified Date</th>");
				print("<th>Delete</th>");
				print("</tr>");
				foreach($files as $file) {
					print("<tr>");
					print("<form method='POST' class='list-form'>");
					print("<td style='display:none;'><input type='hidden' name='attachment-id' value='".$this->selectionService->getSelectedWorkItem()->getId()."'/></td>");
					print("<td><a href=\"{$this->uploadService->getUploadBase()}/{$file->getFileName()}\" target='_blank'>{$file->getFileName()}</a></td>");
					print("<td>{$file->getLastUpdatedDate()}</td>");
					print("<td><input type='submit' name='delete-attachment' value='Delete' style='background-color:red;color:white;'/></td>");
					print("</form>");
					print("</tr>");
				}
				print("</table>");
			}


			if (isset($_POST['delete-attachment'])) {
				$this->assertResult($this->uploadService->removeAttachment($_POST['attachment-id']));
			}
		}
	}
?>
