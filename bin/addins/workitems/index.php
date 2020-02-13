<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	$__ROOT_FROM_PAGE__ = "../../";
	include_once("{$__ROOT_FROM_PAGE__}/classes/prmGUI/workitems/prmworkitems_all.php");
	include_once("{$__ROOT_FROM_PAGE__}classes/prmService/prmservice_all.php");
	$page = new PRMWorkItemsViewModel($__ROOT_FROM_PAGE__, TRUE);
	$page->load();
?>
