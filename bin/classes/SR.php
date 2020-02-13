<?php
	class SR {
		public static $__APPLICATION_NAME__ = "Private Record Management";
		public static $__MODULES_LABEL_ADMIN__ = "Admin";
		public static $__MODULES_LABEL_DASHBOARD__ = "Dashboard";
		public static $__MODULES_LABEL_SETTINGS__ = "Settings";
		public static $__MODULES_LABEL_AUDITS__ = "Audits";
		public static $__MODULES_LABEL_KB__ = "WIKI";
		public static $__MODULES_LABEL_KBADMIN__ = "WIKI ADMIN";
		public static $__MODULES_LABEL_FILEMANAGER__ = "File Manager";

		public static $__SETUP_LABEL_PROVIDER__ = "Provider";
		public static $__SETUP_LABEL_SOURCE__ = "Data Source";
		public static $__SETUP_LABEL_SERVICE__ = "Data Service";
		public static $__SETUP_LABEL_DATABASE__ = "Database";
		public static $__SETUP_LABEL_PORT__ = "Port";
		public static $__SETUP_LABEL_USERNAME__ = "Username";
		public static $__SETUP_LABEL_PASSWORD__ = "Password";
		public static $__SETUP_LABEL_DBA_USER__ = "DBA Username";
		public static $__SETUP_LABEL_DBA_PASS__ = "DBA Password";

		public static $__SUCCESS_CREATE_WORKITEM__ = "Created workitem!";
		public static $__SUCCESS_UPDATE_WORKITEM__ = "Updated workitem!";
		public static $__FAILURE_CREATE_WORKITEM__ = "Failed to create workitem....";
		public static $__FAILURE_UPDATE_WORKITEM__ = "Failed to update workitem...";

		public static $__SESSION_TOKEN_NAME__ = "PRM_SESSION_TOKEN";

		public static $__SYSTEM_START_ID__ = 999999990;


		public static $__QUERY_ICON__ = "/media/images/icons/tree/query-black.png";

		public static $__WORKITEM_ID_PAD_LENGTH__ = 20;

		public static $__NO_QUERY_SELECTED__ = "No query item selected";
		public static $__NO_WORKITEM_MESSAGE__ = "No workitem selected";
		public static $__QUERY_HEADER__ = "Query";
		public static $__TREE_HEADER__ = "Navigation Tree";
		public static $__PROPERTIES_HEADER__ = "Properties";
		public static $__WORKITEM_HEADER__ = "Workitem";
		public static $__FILTERS_EDITOR_TITLE__ = "Filters";
		public static $__COLUMNS_EDITOR_TITLE__ = "Display Columns";


		public static function defaultLocalDatabase() {
			global $__ROOT_FROM_PAGE__;
			return $__ROOT_FROM_PAGE__."/classes/prmService/db/local_service.db";
		}
		public static function getuserQuery($token) {
			return "SELECT * FROM PRM_USER a JOIN PRM_SESSION b ON b.PRM_USER_ID = a.PRM_USER_ID WHERE b.PRM_SESSION_TOKEN = '{$token}'";
		}
	}
?>
