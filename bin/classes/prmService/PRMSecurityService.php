<?php
	include_once("PRMDataService.php");
	class PRMSecurityService {
		private $dataService = NULL;
		private static $instance = NULL;
		private $users = array();
		private $objectService = NULL;
		public static $__CRUD_PERMISSIONS__ = [ "C" => "Create", "R" => "Read", "U" => "Update", "D" => "Delete" ];

		public function generateToken($length = 30) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$token = '';
			for($i = 0; $i < $length; $i++) {
				$token .= $characters[rand(0, $charactersLength - 1)];
			}
			return $token;
		}
		private function __construct() {
			$this->dataService = PRMDataService::getInstance();
			$this->objectService = PRMObjectService::getInstance();
			$this->users = $this->objectService->getUsers();
		}
		public static function getInstance() {
			if(PRMSecurityService::$instance == NULL){
				PRMSecurityService::$instance = new PRMSecurityService();
			}
			return PRMSecurityService::$instance;
		}
		public function recordExists($a) {
		}
		private function addPermissions() {
		}
		
		public function getUser($token) {
			$tempUser = new PRMUser();
			$records = array();
			try {
				if($this->dataService->getUser($token) != NULL) {
					$records = $this->dataService->getUser($token);
				}
			}
			catch(Exception $e) { }
			if(sizeof($records->getRows()) == 0) { return NULL; }
			$record = $records->getRows()[0];
			$tempUser->setId($record->getColumns()[0]->getValue());
			$tempUser->setFirst($record->getColumns()[1]->getValue());
			$tempUser->setLast($record->getColumns()[2]->getValue());
			$tempUser->setName($record->getColumns()[3]->getValue());
			$tempUser->setLabel($tempUser->getName());
			$tempUser->setStatus($record->getColumns()[4]->getValue());
			$tempUser->setIsSysAdmin($record->getColumns()[5]->getValue());
			$tempUser->setRole($record->getColumns()[11]->getValue());
			$tempUser->setAvatar($record->getColumns()[12]->getValue());
			return $tempUser;
		}

		public function getUserById($id) {
			$tempUser = new PRMUser();
			$records = array();
			try {
				if($this->dataService->getHandler()->query("SELECT * FROM PRM_USER WHERE PRM_USER_ID = '".$id."'") != NULL) {
					$records = $this->dataService->getHandler()->query("SELECT * FROM PRM_USER WHERE PRM_USER_ID = '".$id."'");
				}
			}
			catch(Exception $e) { }
			if(sizeof($records->getRows()) == 0) { return NULL; }
			$record = $records->getRows()[0];
			$tempUser->setId($record->getColumns()[0]->getValue());
			$tempUser->setFirst($record->getColumns()[1]->getValue());
			$tempUser->setLast($record->getColumns()[2]->getValue());
			$tempUser->setName($record->getColumns()[3]->getValue());
			$tempUser->setLabel($tempUser->getName());
			$tempUser->setStatus($record->getColumns()[4]->getValue());
			$tempUser->setIsSysAdmin($record->getColumns()[5]->getValue());
			$tempUser->setRole($record->getColumns()[11]->getValue());
			$tempUser->setAvatar($record->getColumn("PRM_USER_AVATAR")->getValue());
			return $tempUser;
		}
		
		public function canAddUser($user) {
			$this->users = $this->objectService->getUsers();
			foreach($this->users as $cursor) {
				if($cursor->getId() == $user->getId()) {
					return "Same ID";
				}
				if($cursor->getName() == $user->getName()) {
					return "Same Username";
				}
			}
			return True;
		}

		public function userFromRow($row) {
			$user = new PRMUser();
			return $user;
		}

		public function addUser($user) {
			$sql = "INSERT INTO PRM_USER (";
			$columns = [
				"PRM_USER_FIRST","PRM_USER_LAST","PRM_USER_NAME","PRM_USER_PASS","PRM_USER_STATUS","PRM_USER_ISSYSADMIN","CREATED_DATE","PRM_STATE_ID","PRM_CITY_ID","PRM_COUNTRY_ID",
				"PRM_USER_ROLE","LAST_UPDATED_DATE"
			];
			$values = [
				$user->getFIrst(), $user->getLast(), $user->getName(), md5($user->getPassword()), $user->getStatus(), $user->getIsSysAdmin(), "CURRENT_TIMESTAMP", $user->getState(),
				$user->getCity(),$user->getCountry(), $user->getRole()
			];
			$sql .= implode(",", $columns);
			$sql .= ") VALUES (";
			$i = 0;
			foreach($values as $value) {
				if($i > 0){ 
					$sql .= ",";
				}
				if($value == "CURRENT_TIMESTAMP") {
					$sql = $sql . "CURRENT_TIMESTAMP";
				}
				else if($value == NULL || $value == "") { 
					$sql .= "NULL"; 
				}
				else { 
					$sql = $sql . "'" . $value . "'"; 
				} 
				$i++;
			}
			$sql = $sql . ",CURRENT_TIMESTAMP";
			$sql .= ")";
			return $this->dataService->getHandler()->runQuery($sql);
		}

		public function updateUser($user) {
			$sql = "UPDATE PRM_USER SET ";
			$columns = [
				"PRM_USER_FIRST","PRM_USER_LAST","PRM_USER_NAME","PRM_USER_STATUS","PRM_USER_ISSYSADMIN","PRM_STATE_ID","PRM_CITY_ID","PRM_COUNTRY_ID", "PRM_USER_ROLE","LAST_UPDATED_DATE"
			];
			if(!PRMSecurityService::isMd5($user->getPassword())) {
				$user->setPassword(md5($user->getPassword()));
			}
			$values = [
				$user->getFIrst(), $user->getLast(), $user->getName(), $user->getStatus(), $user->getIsSysAdmin(), $user->getState(),	$user->getCity(),$user->getCountry(), $user->getRole(), "CURRENT_TIMESTAMP"
			];
			if(sizeof($columns) != sizeof($values)) { return; }
			$i = 0;
			foreach($values as $value) {
				if($i > 0){
					$sql .= ",";
				}
				$sql .= ($columns[$i] . " = ");
				if($value == NULL || $value == "") {
					$sql .= "NULL";
				}
				else if($value == "CURRENT_TIMESTAMP") {
					$sql = $sql . "CURRENT_TIMESTAMP";
				}
				else {
					$sql = $sql . "'" . $value . "'";
				}
				$i++;
			}
			$sql = $sql . " WHERE PRM_USER_ID = '" . $user->getId() . "'";
			return $this->dataService->getHandler()->runQuery($sql);
		}

		public function getToken($username, $password) {
			if(sizeof($this->dataService->getHandler()->query("SELECT PRM_USER_ID FROM PRM_USER WHERE PRM_USER_NAME='".$username."' AND PRM_USER_STATUS='1'")->getRows()) == 1) {
				if(sizeof($this->dataService->getHandler()->query("SELECT PRM_USER_ID FROM PRM_USER WHERE PRM_USER_NAME='".$username."' AND PRM_USER_STATUS='1' AND PRM_USER_PASS='".$password."'")->getRows()) == 1) {
					$token = $this->generateToken();
					if(sizeof($this->dataService->getHandler()->query("SELECT PRM_SESSION_ID FROM PRM_SESSION WHERE PRM_SESSION_TOKEN='".$token."'")->getRows()) == 0) {
						return $token;					
					}
					else {
						throw new Exception("Token not unique");
					}				
				}
				else {
					throw new Exception("Password does not match user");
				}
			}
			else {
				throw new Exception("User does not exist");
			}	
			return NULL;
		}

		private function getCurrentPassword($id) {
			$rawData = $this->dataService->getHandler()->query("SELECT PRM_USER_PASS FROM PRM_USER WHERE PRM_USER_ID='".$id."'");
			if($rawData != NULL && sizeof($rawData->getRows()) > 0) {
				return $rawData->getRows()[0]->getColumns()[0]->getValue();
			}
			return "";
		}

		public function deleteUser($id) {
			/**
			 * Do not ever delete a user record from the database
			 */
		}

		public function validateCurrent($user, $current, $confirm) {
			if(md5($current) === md5($confirm)) {
				if($this->getCurrentPassword($user->getId()) == md5($current)){
					return True;
				}
				else {
					return "Wrong current password";
				}
			}
			else {
				return "Current and confirm do not match";
			}
		}

		public function isUserInGroup($user, $group) {
			$userGroups = $this->userGroups($user);
			foreach($userGroups as $cursor) {
				if($cursor->getId() == $group->getId()) {
					return True;
				}
			}
			return False;
		}

		public function userGroups($user) {
			$result = array();
			$records = $this->dataService->getHandler()->query("SELECT * FROM PRM_GROUP_MEMBER WHERE PRM_USER_ID='".$user->getId()."'");
			foreach($records->getRows() as $row) {
				$group = new PRMGeneralItem();
				$group->setId($row->getColumns()[0]->getValue());
				$group->setName($row->getColumns()[1]->getValue());
				array_push($result, $group);
			}
			return $result;
		}

		public function removeGroupMember($group, $userId) {
			$this->dataService->getHandler()->runQuery("DELETE FROM PRM_GROUP_MEMBER WHERE PRM_GROUP_ID = '".$group->getId()."' AND PRM_USER_ID = '". $userId."'");
		}

		public function addGroupMember($group, $userId) {
			$this->dataService->getHandler()->runQuery("INSERT INTO PRM_GROUP_MEMBER (PRM_GROUP_ID, PRM_USER_ID) VALUES ('".$group->getId()."', '". $userId."')");
		}


		public function isUserInTeam($user, $team) {
			$userTeams = $this->userTeams($user);
			foreach($userTeams as $cursor) {
				if($cursor->getId() == $team->getId()) {
					return True;
				}
			}
			return False;
		}

		public function userTeams($user) {
			$result = array();
			$records = $this->dataService->getHandler()->query("SELECT * FROM PRM_TEAM_MEMBER WHERE PRM_USER_ID='".$user->getId()."'");
			foreach($records->getRows() as $row) {
				$team = new PRMGeneralItem();
				$team->setId($row->getColumns()[0]->getValue());
				$team->setName($row->getColumns()[1]->getValue());
				array_push($result, $team);
			}
			return $result;
		}

		public function removeTeamMember($team, $userId) {
			$this->dataService->getHandler()->runQuery("DELETE FROM PRM_TEAM_MEMBER WHERE PRM_TEAM_ID = '".$team->getId()."' AND PRM_USER_ID = '". $userId."'");
		}

		public function addTeamMember($team, $userId) {
			$this->dataService->getHandler()->runQuery("INSERT INTO PRM_TEAM_MEMBER (PRM_TEAM_ID, PRM_USER_ID) VALUES ('".$team->getId()."', '". $userId."')");
		}


		public function updatePassword($user, $new) {
			$sql = "UPDATE PRM_USER SET PRM_USER_PASS='".md5($new)."' WHERE PRM_USER_ID='".$user->getId()."'";
			$this->dataService->getHandler()->runQuery($sql);
		}

		public static function isMd5($str = ''){
			return (preg_match('/^[a-f0-9]{32}$/', $str) === 1 ? True : False);
		}
	}
?>
