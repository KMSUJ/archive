<?php
	include_once "base_site.inc.php";

	class UsersSite extends BaseSite {
		function title() {
			parent::title();
			echo " - Użytkownicy";
		}

		function main_content() {
			?>
<input type='button' id='user_add_button' value="Nowy użytkownik" class='submit'>
<div class='users'>
<table>
<tr><th>Login</th><th>Admin</th></tr>
			<?php
				$result = mysql_query("SELECT username, is_admin FROM users;") or die(mysql_error());
				while ($row = mysql_fetch_array($result)) {
					$username = urldecode($row["username"]);
					$is_admin = $row["is_admin"];
					echo "<tr class='user_entry'><td class='username'>$username</td><td class='is_admin'>$is_admin</td></tr>";
				}
			?>
</table>
</div>

<script>
/*
 * User add dialog
 */

user_add_active = false;

$('#user_add_button').click(function() {
	$('body').append("\
<div class='popup_dialog' id='user_add'>\
<div class='dialog'>\
<form id='user_add_form'>\
<table>\
<tr><th colspan=2>Nowy użytkownik</th></tr>\
<tr><td>Login</td><td><input type='text' id='user_add_username'></td></tr>\
<tr><td>Admin</td><td><input type='checkbox' id='user_add_is_admin'></td></tr>\
<tr><td>Hasło:</td><td><input type='password' id='user_add_password'></td></tr>\
<tr><td>Powtórz hasło:</td><td><input type='password' id='user_add_password2'></td></tr>\
<tr><td colspan=2 class='submit'><input type='submit' class='submit' value='Dodaj'>\
	<input id='user_add_cancel' type='button' class='submit' value='Anuluj'></td></tr>\
</table>\
</form>\
</div>\
</div>\
	");

	$('#user_add_form').submit(function() {
		$('#error').text('');

		username = $('#user_add_username').val();
		password = $('#user_add_password').val();
		password2 = $('#user_add_password2').val();
		is_admin = $('#user_add_is_admin').prop('checked');

		if (password != password2) {
			$('#error').text('hasła się nie zgadzają');
			return false;
		}

		$.ajax({
			dataType: 'json',
			type: 'POST',
			url: 'users_ajax.php',
			data: {
				'action': 'add',
				'username': username,
				'is_admin': is_admin,
				'password': password,
			},
		}).done(function(data) {
			if (data['error']) {
				$('#error').text(data['error']);
			} else {
				location.reload();
			}
		}).fail(function(data) {
			$('#error').text(data['responseText']);
		});
		return false;
	});

	$('#user_add_cancel').click(function() {
		$(this).parents('#user_add').remove();
	});
});

/*
 * User edit dialog
 */
user_edit_active = false;

$('.user_entry').click(function() {
	if (user_edit_active) {
		return;
	}

	user_edit_active = true;

	username = $(this).find('td.username').html();
	is_admin = $(this).find('td.is_admin').html();
	$('body').append("\
<div class='popup_dialog' id='user_edit'>\
<div class='dialog'>\
<form id='user_edit_form'>\
<table>\
<tr><th colspan=2>"+username+"</th></tr>\
<tr><td>Admin</td><td><input type='checkbox' id='user_edit_is_admin' "+(is_admin==1?"checked":"")+"></td></tr>\
<tr><td>Hasło:</td><td><input type='password' id='user_edit_password'></td></tr>\
<tr><td>Powtórz hasło:</td><td><input type='password' id='user_edit_password2'></td></tr>\
<tr><td colspan=2 class='submit'><input type='submit' class='submit' value='Zapisz'>\
	<input id='user_edit_remove' type='button' class='submit' value='Usuń'>\
	<input id='user_edit_cancel' type='button' class='submit' value='Anuluj'></td></tr>\
</table>\
</form>\
</div>\
</div>\
	");

	$('#user_edit_form').submit(function() {
		$('#error').text('');

		password = $('#user_edit_password').val();
		password2 = $('#user_edit_password2').val();
		is_admin = $('#user_edit_is_admin').prop('checked');

		if (password != password2) {
			$('#error').text('hasła się nie zgadzają');
			return false;
		}

		$.ajax({
			dataType: 'json',
			type: 'POST',
			url: 'users_ajax.php',
			data: {
				'action': 'edit',
				'username': username,
				'is_admin': is_admin,
				'password': password,
			},
		}).done(function(data) {
			if (data['error']) {
				$('#error').text(data['error']);
			} else {
				location.reload();
			}
		}).fail(function(data) {
			$('#error').text(data['responseText']);
		});
		return false;
	});

/*
 * User remove dialog
 */

	$('#user_edit_remove').click(function() {
		$('body').append("\
<div class='popup_dialog' id='user_remove'>\
<div class='dialog'>\
<table>\
<tr><th colspan=2>Usuwanie "+username+"</th></tr>\
<tr><td colspan=2 class='submit'><input id='user_remove_remove' type='button' class='submit' value='Usuń'><input id='user_remove_cancel' type='button' class='submit' value='Anuluj'></td></tr>\
</table>\
</div>\
</div>\
		");

		$('#user_remove_cancel').click(function() {
			$(this).parents('#user_remove').remove();
		});

		$('#user_remove_remove').click(function() {
			$.ajax({
				dataType: 'json',
				type: 'POST',
				url: 'users_ajax.php',
				data: {
					'action': 'remove',
					'username': username,
				},
			}).done(function(data) {
				if (data['error']) {
					$('#error').text(data['error']);
				} else {
					location.reload();
				}
			}).fail(function(data) {
				$('#error').text(data['responseText']);
			});
		});
	});

	$('#user_edit_cancel').click(function() {
		$(this).parents('#user_edit').remove();
		user_edit_active = false;
	});
});
</script>
			<?php
		}
	}

	assert_is_admin();
	$site = new UsersSite();
	$site->show();
?>
