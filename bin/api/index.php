<?php
	/**
	 * http_response_code(404);
	 * json_encode(array("message" => "No products found.")
	 */
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");

	class PRMRestAPI {
		public function __construct() {
		}
	}
?>
