<?php
	include_once "auth.inc.php";

	$result = array(
		"error" => "",
	);

	$action = $_POST["action"];

	switch ($action) {
		case "change_password":
			$old_password = $_POST["old_password"];
			$new_password = $_POST["new_password"];

			if (!is_logged()) {
				$result["error"] = "musisz się zalogować";
				break;
			}

			if (!auth_check_password(username(), $old_password)) {
				$result["error"] = "stare hasło nie pasuje";
				break;
			}

			auth_change_password(username(), $new_password);
			break;
		default:
			$result["error"] = "unknown command '$action'";
	}

	echo utf8_encode(json_encode($result));
?>
