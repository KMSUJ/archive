<?php
	include_once "config.inc.php";

	function init() {
		GLOBAL $mysql_server;
		GLOBAL $mysql_username;
		GLOBAL $mysql_password;
		GLOBAL $mysql_database;

		STATIC $is_initialized = False;

		if (! $is_initialized) {
			session_start();
			mysql_connect($mysql_server, $mysql_username, $mysql_password);
			mysql_select_db($mysql_database);

			$is_initialized = True;
		}
	}

	init();
?>
