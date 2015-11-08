<?php
	include_once "auth.inc.php";
	include_once "base_site.inc.php";

	class ChangePasswordSite extends BaseSite {
		function title() {
			parent::title();
			echo " - Zmiana hasła";
		}

		function main_content() {
			?>
<div class='dialog'>
<form id='passwd_form'>
<table>
<tr><th colspan=2>Zmiana hasła:</th></tr>
<tr><td>Stare hasło:</td><td><input type='text' id='passwd_old_password'></td></tr>
<tr><td>Nowe hasło:</td><td><input type='password' id='passwd_new_password'></td></tr>
<tr><td>Powtórz hasło:</td><td><input type='password' id='passwd_new_password2'></td></tr>
<tr><td class='submit' colspan=2><input type='submit' class='submit' value='Zmień'></td></tr>
</table>
</form>
</div>

<script>
$('#passwd_form').submit(function() {
	$('#error').text("");

	old_password = $('#passwd_old_password').val();
	new_password = $('#passwd_new_password').val();
	new_password2 = $('#passwd_new_password2').val();

	if (new_password != new_password2) {
		$('#error').text("nowe hasła się nie zgadzają");
		return false;
	}

	$.ajax({
		dataType: 'json',
		type: 'POST',
		url: 'change_password_ajax.php',
		data: {
			'action': 'change_password',
			'old_password': old_password,
			'new_password': new_password,
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

	assert_is_logged();
	$site = new ChangePasswordSite();
	$site->show();
?>
