<?php
	include_once "init.inc.php";
	include_once "utils.inc.php";

	function auth_fingerprint($username) {
		$username_encoded = urlencode($username);

		$result = mysql_query("SELECT password FROM users WHERE username = '$username_encoded'") or die(mysql_error());
		if (mysql_num_rows($result) != 1) {
			return False;
		}

		$row = mysql_fetch_array($result);
		$password_encoded = $row["password"];

		return hash("sha512", $password_encoded.$_SERVER["HTTP_USER_AGENT"].$_SERVER["REMOTE_ADDR"]."fingerprint");
	}

	function auth_check_password($username, $password) {
		$username_encoded = urlencode($username);

		$result = mysql_query("SELECT salt, password FROM users WHERE username = '$username_encoded'") or die(mysql_error());
		if (mysql_num_rows($result) != 1) {
			return False;
		}

		$row = mysql_fetch_array($result);
		$salt = $row["salt"];
		$password_encoded = hash("sha512", $password.$salt);

		if ($password_encoded != $row["password"]) {
			return False;
		}

		return True;
	}

	function auth_add_user($username, $password, $is_admin=false) {
		$username_encoded = urlencode($username);
		$salt = hash("sha512", uniqid(mt_rand(1, mt_getrandmax()), true));
		$password_encoded = hash("sha512", $password.$salt);

		mysql_query("INSERT INTO users (username, password, salt, is_admin) VALUES ('$username_encoded', '$password_encoded', '$salt', '$is_admin')") or die(mysql_error());
	}

	function auth_remove_user($username) {
		$username_encoded = urlencode($username);

		mysql_query("DELETE FROM users WHERE username = '$username_encoded'") or die(mysql_error());
	}

	function auth_change_password($username, $password) {
		$username_encoded = urlencode($username);
		$salt = hash("sha512", uniqid(mt_rand(1, mt_getrandmax()), true));
		$password_encoded = hash("sha512", $password.$salt);

		mysql_query("UPDATE users SET salt = '$salt', password = '$password_encoded' WHERE username = '$username_encoded'") or die(mysql_error());
	}

	function auth_change_is_admin($username, $is_admin) {
		$username_encoded = urlencode($username);

		mysql_query("UPDATE users SET is_admin = $is_admin WHERE username = '$username_encoded'") or die(mysql_error());
	}

	function authenticate($username, $password) {
		if (! auth_check_password($username, $password)) {
			return False;
		}

		$_SESSION["username"] = $username;
		$_SESSION["fingerprint"] = auth_fingerprint($username);

		return True;
	}

	function deauthenticate() {
		unset($_SESSION["username"]);
		unset($_SESSION["fingerprint"]);
	}

	function is_logged() {
		if (!isset($_SESSION["username"])) {
			return False;
		}

		$username_encoded = urlencode($_SESSION["username"]);

		$result = mysql_query("SELECT password FROM users WHERE username = '$username_encoded'") or die(mysql_error());
		if (mysql_num_rows($result) != 1) {
			return False;
		}

		$row = mysql_fetch_array($result);
		$password_encoded = $row["password"];
		$fingerprint = auth_fingerprint($_SESSION["username"]);

		return $fingerprint == $_SESSION["fingerprint"];
	}

	function is_admin() {
		if (!is_logged()) {
			return false;
		}

		$username_encoded = urlencode($_SESSION["username"]);

		$result = mysql_query("SELECT is_admin FROM users WHERE username = '$username_encoded'") or die(mysql_error());
		if (mysql_num_rows($result) != 1) {
			die("something gone wrong");
		}

		$row = mysql_fetch_array($result);
		$is_admin = $row["is_admin"];

		return $is_admin;
	}

	function username() {
		if (is_logged()) {
			return $_SESSION["username"];
		}
		return NULL;
	}

	function assert_is_admin() {
		if (!is_admin()) {
			redirect_site("login.php");
			exit;
		}
	}

	function assert_is_logged() {
		if (!is_logged()) {
			redirect_site("login.php");
			exit;
		}
	}
?>
