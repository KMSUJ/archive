<?php
	include_once "auth.inc.php";

	$result = array(
		"error" => "",
	);

	$action = $_POST["action"];

	switch ($action) {
		case "authenticate":
			$username = $_POST["username"];
			$password = $_POST["password"];

			if (!authenticate($username, $password)) {
				$result["error"] = "nieprawidłowy login lub hasło";
			}
			break;
		default:
			$result["error"] = "unknown command '$action'";
	}

	echo utf8_encode(json_encode($result));
?>
