<!DOCTYPE html>

<html>
<head>
<meta charset='UTF-8'>
<title>Setup</title>
</head>
<body>

<?php
	include_once "init.inc.php";

	init();

	if (!isset($_POST["clean"])) {
		?>
<form method='POST'>
<input type='submit' value='clean' name='clean'>
</form>
		<?php
	} else {
		mysql_query("
DROP TABLE IF EXISTS
	categories,
	categories_to_archive_files,
	people,
	archive_files,
	people_to_archive_files,
	users;
		") or die(mysql_error());

		echo "OK";
	}
?>

</body>
</html>
