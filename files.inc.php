<?php
	function files_search($keywords, $limit_from, $limit_to) {
		$query = "";

		$res = mysql_query("SELECT count(*) FROM archive_files WHERE true $query") or die(mysql_error());
		if (mysql_num_rows($res) != 1) {
			die("something gone wrong");
		}

		$row = mysql_fetch_array($res);
		$result["count"] = $row["count(*)"];

		$res = mysql_query("SELECT signature, description FROM archive_files WHERE true $query LIMIT $limit_from, $limit_to") or die(mysql_error());

		$result["results"] = array();
		while ($row = mysql_fetch_array($res)) {
			$result["results"][] = array('signature'=>urldecode($row['signature']), 'description'=>urldecode($row['description']));
		}

		return $result;
	}

	function files_add($signature, $date_from, $date_to, $description) {
		$signature = urlencode($signature);
		$date_from = urlencode($date_from);
		$date_to = urlencode($date_to);
		$description = urlencode($description);

		mysql_query("INSERT INTO archive_files (signature, date_from, date_to, description) VALUES ('$signature', '$date_from', '$date_to', '$description')") or die(mysql_error());
	}
?>
