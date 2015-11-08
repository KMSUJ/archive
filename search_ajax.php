<?php
	include_once "init.inc.php";
	include_once "files.inc.php";

	$result = array(
		"error" => "",
	);

	$action = $_POST["action"];

	switch ($action) {
		case "search":
			$keywords = $result["keywords"];
			$count_limit = $result["limit"];
			$page = $result["page"];

			$res = files_search($keywords, ($page-1)*$count_limit, $count_limit);

			$result["count"] = $res["count"];
			$result["results"] = $res["results"];
			break;
		default:
			$result["error"] = "unknown command '$action'";
	}

	echo utf8_encode(json_encode($result));
?>
