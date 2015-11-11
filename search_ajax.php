<?php
	include_once "init.inc.php";
	include_once "auth.inc.php";

	$result = array(
		"error" => "",
	);

	$action = $_POST["action"];

	switch ($action) {
		case "document_add":
			if (!is_logged()) {
				$result["error"] = "musisz być zalogowany";
				break;
			}

			$signature = urlencode($_POST["signature"]);
			$date_from = urlencode($_POST["date_from"]);
			$date_to = urlencode($_POST["date_to"]);
			$description = urlencode($_POST["description"]);

			mysql_query("INSERT INTO archive_files (signature, date_from, date_to, description) VALUES ('$signature', '$date_from', '$date_to', '$description')") or die(mysql_error());

			break;
		case "search":
			$keywords = array();
			foreach ($_POST["keywords"] as $k) {
				$keywords[] = urlencode($k);
			}
			$count_limit = urlencode($_POST["limit"]);
			$page = urlencode($_POST["page"]);
			$limit_from = ceil(($page - 1) * $count_limit);
			$limit_to = ceil($page * $count_limit);

			$query = "";

			$res = mysql_query("SELECT count(*) FROM archive_files WHERE true $query") or die(mysql_error());
			if (mysql_num_rows($res) != 1) {
				die("something gone wrong");
			}
			$row = mysql_fetch_array($res);
			$result["count"] = $row["count(*)"];

			$res = mysql_query("SELECT * FROM archive_files WHERE true $query LIMIT $limit_from, $limit_to") or die(mysql_error());
			$result["results"] = array();
			while ($row = mysql_fetch_array($res)) {
				$item = array();
				foreach ($row as $k => $v) {
					$item[$k] = urldecode($v);
				}
				$result["results"][] = $item;
			}
			break;
		case "autocomplete_tag":
			$keywords = $_POST["keywords"];
			$result["results"] = array(join(" ", $keywords));
			break;
		case "autocomplete_person":
			$keywords = $_POST["keywords"];
			$result["results"] = array(join(" ", $keywords));
			break;
		case "get_document_data":
			$signature = urlencode($_POST["signature"]);
			$res = mysql_query("SELECT * FROM archive_files WHERE signature = '$signature'") or die(mysql_error());
			if (mysql_num_rows($res) != 1) {
				$result["error"] = "signature '$signature' does not exist";
				break;
			}

			$item = array();
			foreach (mysql_fetch_array($res) as $k => $v) {
				$item[$k] = urldecode($v);
			}
			$result["result"] = $item;
			break;
		case "document_remove":
			if (!is_logged()) {
				$result["error"] = "musisz być zalogowany";
				break;
			}

			$signature = urlencode($_POST["signature"]);
			mysql_query("DELETE FROM archive_files WHERE signature = '$signature'") or die(mysql_error());
			break;
		case "document_edit":
			if (!is_logged()) {
				$result["error"] = "musisz być zalogowany";
				break;
			}

			$signature = urlencode($_POST["signature"]);
			$date_from = urlencode($_POST["date_from"]);
			$date_to = urlencode($_POST["date_to"]);
			$description = urlencode($_POST["description"]);

			mysql_query("UPDATE archive_files SET date_from = '$date_from', date_to = '$date_to', description = '$description' WHERE signature = '$signature'") or die(mysql_error());
			break;
		default:
			$result["error"] = "unknown command '$action'";
	}

	echo utf8_encode(json_encode($result));
?>
