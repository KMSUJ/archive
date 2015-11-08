<?php
	include_once "init.inc.php";
	include_once "auth.inc.php";
	include_once "files.inc.php";

	$result = array(
		"error" => "",
	);

	$action = $_POST["action"];

	switch ($action) {
		case "add":
			if (!is_logged()) {
				$result["error"] = "musisz być zalogowany";
				break;
			}

			$signature = $_POST["signature"];
			$date_from = $_POST["date_from"];
			$date_to = $_POST["date_to"];
			$description = $_POST["description"];

			files_add($signature, $date_from, $date_to, $description);

			break;
		case "search":
			$keywords = $_POST["keywords"];
			$count_limit = $_POST["limit"];
			$page = $_POST["page"];

			$res = files_search($keywords, ($page-1)*$count_limit, $page*$count_limit);

			$result["count"] = $res["count"];
			$result["results"] = $res["results"];
			break;
		case "autocomplete_tag":
			$keywords = $_POST["keywords"];
			$result["results"] = array(join(" ", $keywords));
			break;
		case "autocomplete_person":
			$keywords = $_POST["keywords"];
			$result["results"] = array(join(" ", $keywords));
			break;
		default:
			$result["error"] = "unknown command '$action'";
	}

	echo utf8_encode(json_encode($result));
?>
