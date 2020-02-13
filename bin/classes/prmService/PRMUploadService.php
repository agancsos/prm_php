<?php
	include_once("PRMDataService.php");
	include_once("PRMConfigurationService.php");
	include_once("PRMLocalService.php");
	class PRMUploadService {
		private static $instance = NULL;
		private $configService = NULL;
		private $dataService = NULL;
		public $sessionService = NULL;
		public $localService = NULL;
		public static $ALLOWED_IMAGE_FILES = [ "jpg", "png" ];
		public static $ALLOWED_DOCUMENT_FILES = ["txt","doc","docx","xls","xlsx"];
		
		private function __construct() {
			$this->dataService = PRMDataService::getInstance();
			$this->configService = PRMConfigurationService::getInstance();
			$this->localService = PRMLocalService::getInstance();
		}

		private function generateHash($len) {
			$result = "";
			$chars = [ 
				"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", 
				"m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", 
				"u", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9" ];
			while(strlen($result) < $len) {
				$result .= $chars[(rand() % sizeof($chars) + 0)];
			}
			return $result;
		}

		public static function getInstance() {
			if(PRMUploadService::$instance == NULL) {
				PRMUploadService::$instance = new PRMUploadService();
			}
			return PRMUploadService::$instance;
		}

		private function addFile($path, $name, $type="Avatar", $isPrivate=False) {
			$sql = "INSERT INTO PRM_FILE (PRM_FILE_PATH, PRM_FILE_NAME, PRM_FILE_TYPE, PRM_FILE_ISPRIVATE, PRM_OWNER_ID, MODIFIED_BY_ID, LAST_UPDATED_DATE) VALUES(";
			$sql .= "'".$path."','".$name."','".$type."',";
			if($isPrivate) {
				$sql .= "'1'";
			}
			else {
				$sql .= "'0'";
			}
			$sql .= ",'". $this->sessionService->getUser()->getId(). "','".$this->sessionService->getUser()->getId()."',NOW";
			$sql .= ")";
			$this->dataService->runQuery($sql);
		}

		public function deleteFile($id) {
			$rawResult = $this->dataService->getHandler()->query("SELECT * FROM PRM_FILE WHERE PRM_FILE_ID = '".$id."'");
			if (sizeof($rawResult->getRows()) == 0) {
				return True;
			}
			$path = $rawResult->getRows()[0]->getColumn("PRM_FILE_PATH")->getValue;
			if ($this->dataService->getHandler()->runQuery("DELETE FROM PRM_FILE WHERE PRM_FILE_ID = '".$id."'")) {
				return $this->removeFile($path);
			}
			return False;
		}


		public function getUploadBase() {
			global $__ROOT_FROM_PAGE__;
			$basePath = "{$__ROOT_FROM_PAGE__}/media";
			$this->configService->reload();
			if($this->configService->__PRM_UPLOAD_BASE_PATH__ != "") {
				$basePath = $this->configService->__PRM_UPLOAD_BASE_PATH__;
			}
			return $basePath;
		}

		public function uploadAvatar($file) {
			$name = $this->generateHash(30) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
			if (move_uploaded_file($file["tmp_name"], "{$this->getUploadBase()}/images/avatars/{$name}")) {
				return $name;
			} 
			else {
				return False;
			}
		}

		public function uploadAttachment($file, $parent) {
			if($parent == NULL) {
				return True;
			}
			$name = $this->uploadFile($file);
			if ($name !== False) {
				return $this->attach($name,$parent); 
			}
			return False;
		}

		public function uploadFile($file, $isPrivate=False) {
			$name = $this->generateHash(30) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
			if (move_uploaded_file($file["tmp_name"], "{$this->getUploadBase()}/uploads/{$name}")) {
				//$this->localService->addFile("{$this->getUploadBase()}/uploads", $name, "FILE", $isPrivate);
				return $name;
			}
			else {
				return False;
			}
		}

		private function removeFile($path) {
			try {
				unlink($path);
			}
			catch(Exception $e) {
				return False;
			}
			return True;
		}
	
		public function attach($file, $item) {
			$sessionService = PRMSessionService::getInstance();
			$sql = "INSERT INTO PRM_ITEM_ATTACHMENT ";
			$sql .= "(PRM_ATTACHMENT_PATH, CREATED_BY, LAST_UPDATED_BY, LAST_UPDATED_DATE, PRM_ITEM_ID) VALUES(";
			$sql = $sql . "'". $file."','" . $sessionService->getUser()->getId()."','".$sessionService->getUser()->getId()."',";
			$sql = $sql . "CURRENT_TIMESTAMP,'".$item->getId()."'";
			$sql .= ")";
			return $this->dataService->getHandler()->runQuery($sql);
		}

		public function getFile($id) {
			$rawResults = $this->dataService->getHandler()->query("SELECT * FROM PRM_FILE WHERE PRM_FILE_ID = '".$id."'");																																																				   
			if (sizeof($rawResults->getRows()) == 0) {																																																																								  
				return NULL;																																																																															
			}																																																																																		   
			$file = $rawResults->getRows()[0];																																																																										  
			$tempFile = new PRMFile();																																																																												  
			$tempFile->setId($file->getColumn("PRM_FILE_ID")->getValue());																																																																		
			$tempFile->setIsPrivate($file->getColumn("PRM_FILE_ISPRIVATE")->getValue());																																																																		  
			$tempFile->setPath($file->getColumn("PRM_FILE_PATH")->getValue());																																																																	
			$tempFile->setLastUpdatedDate($file->getColumn("LAST_UPDATED_DATE")->getValue());																																																														   
			$tempFile->setName($file->getColumn("PRM_FILE_NAME")->getValue());
			$tempFile->setFileType($file->getColumn("PRM_FILE_TYPE")->getValue());
			$tempFile->setLastUpdatedBy($file->getColumn("MODIFIED_BY_ID")->getValue());																																																															   
			$tempFile->setCreatedBy($file->getColumn("PRM_OWNER_ID")->getValue());																																																																		
			return $tempFile;																																																																														   
		}

		public function getAttachment($id) {
			$rawResults = $this->dataService->getHandler()->query("SELECT * FROM PRM_ITEM_ATTACHMENT WHERE PRM_ATTACHMENT_ID = '".$id."'");
			if (sizeof($rawResults->getRows()) == 0) {
				return NULL;
			}
			$file = $rawResults->getRows()[0];
			$tempFile = new PRMFile();																																																																											  
			$tempFile->setId($file->getColumn("PRM_ATTACHMENT_ID")->getValue());																																																																	
			$tempFile->setItemId($file->getColumn("PRM_ITEM_ID")->getValue());																																																																	  
			$tempFile->setPath($file->getColumn("PRM_ATTACHMENT_PATH")->getValue());																																																																
			$tempFile->setLastUpdatedDate($file->getColumn("LAST_UPDATED_DATE")->getValue());																																																													   
			$tempFile->setLastUpdatedBy($file->getColumn("LAST_UPDATED_BY")->getValue());																																																														   
			$tempFile->setCreatedBy($file->getColumn("CREATED_BY")->getValue()); 
			return $tempFile;
		}

		public function getAttachments($itemId) {
			$result = array();
			$rawResults = $this->dataService->getHandler()->query("SELECT * FROM PRM_ITEM_ATTACHMENT WHERE PRM_ITEM_ID = '".$itemId."' order by LAST_UPDATED_DATE DESC");
			if (sizeof($rawResults->getRows()) == 0) {
				return $result;
			}
			foreach($rawResults->getRows() as $file) {
				$tempFile = $this->getAttachment($file->getColumn("PRM_ATTACHMENT_ID")->getValue());
				array_push($result, $tempFile);
			}
			return $result;
		}

		public function removeAttachment($id) {
			return $this->dataService->getHandler()->runQuery("DELETE FROM PRM_ITEM_ATTACHMENT WHERE PRM_ATTACHMENT_ID = '".$id."'");
		}
	}
?>
