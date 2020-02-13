<?php
	abstract class PRMKbState {
		const NONE			= 0;
		const ACTIVE_STATE	= 1;
		const PRIVATE_STATE   = 2;
		const PROTECTED_STATE = 3;	
		public static function getItterator() {
			return  [ 'NONE', 'ACTIVE_STATE', 'PRIVATE_STATE', 'PROTECTED_STATE'] ;
		}
		public static function fromName($name) {
			if($name =="NONE") {
				return PRMKbState::NONE;
			}
			else if($name == "ACTIVE_STATE") {
				return PRMKbState::ACTIVE_STATE;
			}
			else if($name == "PRIVATE_STATE") {
				return PRMKbState::PRIVATE_STATE;
			}
			else if($name == "PROTECTED_STATE") {
				return PRMKbState::PROTECTED_STATE;
			}
		}
	}

	abstract class PRMItemType {
		const NONE	  = 0;
		const WORKITEM  = 1;
		const AGENT	 = 2;
		public static function getItterator() { 
			return [ 'NONE', 'WORKITEM', 'AGENT' ];
		}
		public static function fromName($name) {
			if($name == "NONE") { return PRMItemType::NONE; }
			else if($name == "WORKITEM") { return PRMItemType::WORKITEM; }
			else if($name == "AGENT") { return PRMItemType::AGENT; }
		}
	}

	abstract class PRMKbStatus {
		const NONE	= 0;
		const ACTIVE  = 1;
		const RETIRED = 2;
		const REMOVED = 3;
		public static function getItterator() {
			return  [ 'NONE', 'ACTIVE', 'RETIRED', 'REMOVED'] ;
		}
		public static function fromName($name) {
			if($name == "NONE") {
				return PRMKbState::NONE;
			}
			else if($name == "ACTIVE") {
				return PRMKbStatus::ACTIVE;
			}
			else if($name == "RETIRED") {
				return PRMKbStatus::RETIRED;
			}
			else if($name == "REMOVED") {
				return PRMKbStatus::REMOVED;
			}
		}
		public static function getOrdinal($name) {
			if($name == "NONE") {
				return NONE;
			}
			else if($name == "ACTIVE") {
				return ACTIVE;
			}
			else if($name == "RETIRED") {
				return RETIRED;
			}
			else if($name == "REMOVED") {
				return REMOVED;
			}
		}
	}

	abstract class PRMStatus {
		const NEW_STATUS = 0;
		const ACTIVE_STATUS = 1;
		const REMOVED_STATUS = -1;
		public static function getItterator() {
			return  [ 'NEW_STATUS', 'ACTIVE_STATUS', 'REMOVED_STATUS'] ;
		}
		public static function fromName($name) {
			if($name =="NEW_STATUS") {
				return PRMStatus::NEW_STATUS;
			}
			else if($name == "ACTIVE_STATUS") {
				return PRMStatus::ACTIVE_STATUS;
			}
			else if($name == "REMOVED_STATUS") {
				return PRMStatus::REMOVED_STATUS;
			}
		}
		public static function fromValue($value) {
			if ($value == 0) { return PRMStatus::NEW_STATUS; }
			if ($value == 1) { return PRMStatus::ACTIVE_STATUS; }
			if ($value == -1) { return PRMStatus::REMOVED_STATUS; }
		}
		public static function getOrdinal($name) {
			if($name =="NEW_STATUS") {
				return 0;
			}
			else if($name == "ACTIVE_STATUS") {
				return 1;
			}																																																																																		   
			else if($name == "REMOVED_STATUS") {																																																																										
				return -1;
			}   
		}
	}

	abstract class PRMStateEnum {
		const NONE_STATE = 0;
		const ACTIVE_STATE = 1;
		const CLOSED_STATE = 2;
		public static function getItterator() { 
			return [ 'NONE_STATE', 'ACTIVE_STATE', 'CLOSED_STATE' ];
		}
		public static function fromName($name) {
			if($name == "NONE_STATE") { return PRMState::NONE_STATE; }
			else if($name == "ACTIVE_STATE") { return PRMState::ACTIVE_STATE; }
			else if($name == "CLOSED_STATE") { return PRMState::CLOSED_STATE; }
		}
	}

	abstract class PRMUserStatus {
		const NEW_STATUS = 1;
		const LOCKED_STATUS = 0;
		public static function getItterator() {
			return [ 'NEW_STATUS', 'LOCKED_STATUS' ];
		}
		public static function fromName($name) {
			if($name == "NEW_STATUS") {
				return PRMUserStatus::NEW_STATUS;
			}
			else if($name == "LOCKED_STATUS") {
				return PRMUserStatus::LOCKED_STATUS;
			}
		}
	}
?>
