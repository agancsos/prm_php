<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	$__ROOT_FROM_PAGE__ = "../";
	include_once("../classes/prmGUI/prmgui_all.php");
	include_once("../classes/prmService/prmservice_all.php");
	include_once("../classes/SR.php");
	$page = new PRMSetupViewModel($__ROOT_FROM_PAGE__);
	$page->load();
	$configService = PRMConfigurationService::getInstance();
	$broker = PRMService::getInstance();
	$dataService = PRMDataSErvice::getInstance();
	print("<form id = 'setup-form' name = 'setup-form' method = 'POST'>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_PROVIDER__."</label>");
	print("<select name = 'setup_provider'>");
	foreach(ConnectionFactory::getProviders() as $provider) {
		print("<option value = \"{$provider->getProviderName()}\">".$provider->getProviderName()."</option>");
	}   
	print("</select>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_SOURCE__."</label>");
	print("<input type = 'text' required autocompleted='off' id = 'setup_source' name = 'setup_source' placeholder = 'PRM_SERVER' value = \"{$configService->__PRM_DATABASE_HOST__}\"/>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_SERVICE__."</label>");
	print("<input type = 'text' autocompleted='off' id = 'setup_service' name = 'setup_service' placeholder = 'PRM_SERVICE' value = \"{$configService->__PRM_DATABASE_SERVICE__}\"/>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_DATABASE__."</label>");
	print("<input type = 'text' required autocompleted='off' id = 'setup_database' name = 'setup_database' placeholder = 'PRM_DATABASE' value = \"{$configService->__PRM_DATABASE_NAME__}\"/>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_PORT__."</label>");
	print("<input type = 'text' required autocompleted='off' id = 'setup_port' name = 'setup_port' placeholder = 'PRM_PORT' value = \"{$configService->__PRM_DATABASE_PORT__}\"/>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_USERNAME__."</label>");
	print("<input type = 'text' required autocompleted='off' id = 'setup_username'  name = 'setup_username' placeholder = 'PRM_USER' value = \"{$configService->__PRM_DATABASE_USER__}\"/>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_PASSWORD__."</label>");
	print("<input type = 'password' autocompleted='off' required id = 'setup_password' name = 'setup_password' placeholder = '*********' value = \"{$configService->__PRM_DATABASE_PASS__}\"/>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_DBA_USER__."</label>");
	print("<input type = 'text' autocompleted='off' id = 'setup_dba_user' name = 'setup_dba_user' placeholder = 'DBA_USER' value =''/>");
	print("<label class = 'label'>".SR::$__SETUP_LABEL_DBA_PASS__."</label>");
	print("<input type = 'password' autocompleted='off' id = 'setup_dba_pass' name = 'setup_dba_pass' placeholder = '********' value =''/>");
	print("<input type = 'submit' name = 'setup_submit' value = 'Create'/>");
	print("<input type = 'submit' name = 'setup_submit' value = 'Update'/>");
	print("<input type = 'submit' name = 'setup_submit' value = 'Join'/>");
	print("</form>");
	print("<div id='log-frame'>");
	if(isset($_POST["setup_submit"])) {
		// Write configuration to property store
		$propertyStore = new JsonPropertyStore("../config.json");
		$setupUser = $_POST['setup_username'];
		$setupPass = $_POST['setup_password'];
		$setupHost = $_POST['setup_source'];
		$setupDatabase = $_POST['setup_database'];
		$setupService = $_POST['setup_service'];
		$providerName = $_POST['setup_provider'];
		$setupPort = $_POST['setup_port'];
		$dbaUser = $_POST['setup_dba_user'];
		$dbaPass = $_POST['setup_dba_pass'];
		
		$propertyStore->writeProperty("PRM_DATABASE", $setupDatabase);
		$propertyStore->writeProperty("PRM_PORT", $setupPort);
		$propertyStore->writeProperty("PRM_USER", $setupUser);
		$propertyStore->writeProperty("PRM_PASS", $setupPass);
		$propertyStore->writeProperty("PRM_SERVER", $setupHost);
		$propertyStore->writeProperty("PRM_SERVICE", $setupService);		
		$propertyStore->writeProperty("PRM_PROVIDER", $providerName);
		$configService->reload();

		// Create and configure data connection
		$connection = ConnectionFactory::createConnection($providerName);
		$connection->setName($setupDatabase);
		$connection->setDbaUser($dbaUser);
		$connection->setDbaPass($dbaPass);
		$connection->setDatabaseUser($setupUser);
		$connection->setDatabasePass($setupPass);
		$connection->setDatabaseHost($setupHost);
		$connection->setDatabasePort($setupPort);

		
		// CHeck if create or update
		if($_POST["setup_submit"] == "Create") {
			if($_POST['setup_dba_user'] != "") {
				if($broker->isSetup($connection)) {
					printf("Cannot create database if already exists....<br/>");
				}
				else {
					if($dataService->createSchema($connection)) {
						printf("Created schema....<br/>");
					}
					else {
						printf("Failed to create schema....<br/>");
					}
				}
			}
			else {
				print("Create requires DBA_USER and DBA_PASS values....<br/>");
			}
		}
		elseif($_POST["setup_submit"] == "Update") {
			if(!$broker->isSetup($connection)) {
				printf("Cannot update database if does not exist....<br/>");
			}
			else {
				if($dataService->updateSchema($connection)) {
					printf("Updated schema....<br/>");
				}
				else {
					printf("Failed to update schema....<br/>");
				}
			}
		}
	}
	print("</div>");
	$page->printFooter();   
?>
