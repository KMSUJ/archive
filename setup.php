<!DOCTYPE html>

<html>
<head>
<meta charset='UTF-8'>
<title>Setup</title>
</head>
<body>

<?php
	include_once "init.inc.php";
	include_once "auth.inc.php";

	init();

	if (!isset($_POST["setup"])) {
		?>
<form method='POST'>
username: <input type='text' name='username'><br>
password: <input type='password' name='password'><br>
<input type='submit' value='setup' name='setup'>
</form>
		<?php
	} else {
		$username = $_POST["username"];
		$password = $_POST["password"];

		mysql_query("CREATE TABLE tags (
				tag_name VARCHAR(128) PRIMARY KEY
			);
		") or die(mysql_error());

		mysql_query("CREATE TABLE categories_to_archive_files (
				signature VARCHAR(128) REFERENCES archive_files,
				tag_name VARCHAR(128) REFERENCES tags
			);
		") or die(mysql_error());

		mysql_query("CREATE TABLE people (
				person_id INTEGER PRIMARY KEY AUTO_INCREMENT,
				title VARCHAR(128),
				name VARCHAR(128),
				surname VARCHAR(128),
				birth_date DATE,
				death_date DATE,
				check(birth_date < death_date)
			);
		") or die(mysql_error());

		mysql_query("CREATE TABLE archive_files (
				signature VARCHAR(128) PRIMARY KEY,
				date_from DATE,
				date_to DATE,
				description TEXT,
				check(date_lower_bound < date_upper_bound)
			);
		") or die(mysql_error());

		mysql_query("CREATE TABLE people_to_archive_files (
				person_id INTEGER REFERENCES people,
				signature VARCHAR(128) REFERENCES archive_files
			);
		") or die(mysql_error());

		mysql_query("CREATE TABLE users (
				username VARCHAR(128) PRIMARY KEY,
				password VARCHAR(128),
				salt VARCHAR(128),
				is_admin BOOLEAN
			);
		") or die(mysql_error());

		auth_add_user($username, $password, true);
		echo "OK";
	}
?>

</body>
</html>
