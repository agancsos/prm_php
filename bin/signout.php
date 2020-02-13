<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	$__ROOT_FROM_PAGE__ = "./";
	include_once("classes/prmService/PRMSessionService.php");
	$sessionService = PRMSessionService::getInstance();
	$sessionService->signout();
	print("<meta http-equiv=\"refresh\" content=\"0; url=/\" />");	
?>
