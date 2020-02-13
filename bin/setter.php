<?php
	$__ROOT_FROM_PAGE__ = "./";
	include_once("classes/prmService/prmservice_all.php");
	$securityService = PRMSecurityService::getInstance();
	$sessionService = PRMSessionService::getInstance();


	if(!$sessionService->shouldShow()) {
		$tempUsername = $_POST['login-user'];
		$tempPassword = md5($_POST['login-pass']);

		try {
			$token = $securityService->getToken($tempUsername, $tempPassword); 

			if($token != NULL) {
				$sessionService->setCookie($token, $tempUsername);
			}
		}
		catch(Exception $e) {
			print("## " . $e->getMessage());
		}
		print("<meta http-equiv=\"refresh\" content=\"0; url=/\" />");	
	}
?>
