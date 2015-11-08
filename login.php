<?php
	include_once "base_site.inc.php";

	class LoginSite extends BaseSite {
		function title() {
			parent::title();
			echo " - Login";
		}

		function main_content() {
			?>
<div class='dialog'>
<form id='login_form'>
<table>
<tr><th colspan=2>Logowanie:</th></tr>
<tr><td>Login:</td><td><input type='text' id='login_username'></td></tr>
<tr><td>Hasło:</td><td><input type='password' id='login_password'></td></tr>
<tr><td class='submit' colspan=2><input type='submit' class='submit' value='Zaloguj się'></td></tr>
</table>
</form>
</div>

<script>
$('#login_form').submit(function() {
	$.ajax({
		dataType: 'json',
		type: 'POST',
		url: 'login_ajax.php',
		data: {
			'action': 'authenticate',
			'username': $('#login_username').val(),
			'password': $('#login_password').val(),
		},
	}).done(function(data) {
		if (data['error']) {
			$('#error').text(data['error']);
		} else {
			location.href = 'index.php';
		}
	}).fail(function(data) {
		$('#error').html('something gone wrong ' + data['responseText']);
	});

	return false;
})
</script>
			<?php
		}
	}

	$site = new LoginSite();
	$site->show();
?>
