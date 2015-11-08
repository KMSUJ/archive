<?php
	include_once "auth.inc.php";
	include_once "utils.inc.php";

	deauthenticate();

	redirect_site("index.php");
?>
