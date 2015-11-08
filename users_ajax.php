<?php
	include_once "auth.inc.php";

	$result = array(
		"error" => "",
	);

	$action = $_POST["action"];

	switch ($action) {
		case "remove":
			$username = $_POST["username"];

			if (!is_admin()) {
				$result["error"] = "you have to be an admin";
				break;
			}

			auth_remove_user($username);
			break;
		case "edit":
			$username = $_POST["username"];
			$password = $_POST["password"];
			$is_admin = $_POST["is_admin"];

			if (!empty($password)) {
				auth_change_password($username, $password);
			}

			auth_change_is_admin($username, $is_admin == "true" ? 1 : 0);
			break;
		default:
			$result["error"] = "unknown command '$action'";
	}

	echo json_encode($result);
?>
