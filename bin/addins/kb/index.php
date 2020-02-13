<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	$__ROOT_FROM_PAGE__ = "../../";
	include_once("{$__ROOT_FROM_PAGE__}classes/prmGUI/prmgui_all.php");
	include_once("{$__ROOT_FROM_PAGE__}classes/prmService/prmservice_all.php");
	$page = new PRMKbViewModel($__ROOT_FROM_PAGE__, TRUE);
	$page->load();
?>
