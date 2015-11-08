<?php
	include_once "auth.inc.php";

	class BaseSite {
		function title() {
			echo "KMSUJ";
		}

		function main_menu_entries() {
			?>
<li><a href='search.php'>Szukaj</a></li>
			<?php
		}

		function main_menu() {
			?>
<div class='horizontal_menu'>
<ul>
<?php $this->main_menu_entries()  ?>
<?php $this->admin_menu_entries()  ?>
</ul>
</div>
			<?php
		}

		function admin_menu_entries() {
			if (is_admin()) {
				?>
<li><a href='users.php'>Użytkownicy</a></li>
				<?php
			}
		}

		function logged_menu_entries() {
			if (is_logged()) {
				?>
<li><a href='change_password.php'>Zmiana hasła</a></li>
				<?php
			}
		}

		function logged_menu() {
			if (is_logged()) {
				?>
<div class='horizontal_menu'>
<ul>
<?php $this->logged_menu_entries()  ?>
</ul>
</div>
				<?php
			}
		}

		function main_content() {
		}

		function login_menu() {
			if (!is_logged()) {
				?>
<div class='login_menu'>
<a href='login.php'>Logowanie</a>
</div>
				<?php
			} else {
				?>
<div class='login_menu'>
Zalogowano jako <?php echo username() ?> <a href='logout.php'>wyloguj</a>
</div>
				<?php
			}
		}

		function show() {
			?>
<!DOCTYPE html>

<html>
<head>
<link rel='stylesheet' type='text/css' href='style.css'>
<meta charset='UTF-8'>
<script src='jquery-2.1.4.min.js'></script>
<title><?php $this->title(); ?></title>
</head>
<body>
<div id='error' class='error'></div>
<?php $this->login_menu() ?>
<?php $this->main_menu() ?>
<?php $this->logged_menu() ?>
<div class='main_content'>
<?php $this->main_content() ?>
</div>
</body>
</html>
			<?php
		}
	}
?>
