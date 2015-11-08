<?php
	function files_search($keywords) {
		$query = "";

		$res = mysql_query("SELECT count(*) FROM archive_files WHERE true $query") or die(mysql_error());
		if (mysql_num_rows($res) != 1) {
			die("something gone wrong");
		}

		$row = mysql_fetch_array($res);
		$result["count"] = $row["count(*)"];

		$res = mysql_query("SELECT signature, description FROM archive_files WHERE true $query") or die(mysql_error());

		$result["results"] = array();
		while ($row = mysql_fetch_array($res)) {
			$result["results"] .= $row;
		}

		return $result;
	}
?>
