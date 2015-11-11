<?php
	include_once "base_site.inc.php";

	class SearchSite extends BaseSite {
		function title() {
			parent::title();
			echo " - Szukaj";
		}

		function add_document() {
			if (is_logged()) {
				?>
<input type='button' id='document_add_button' class='submit' value='Dodaj dokument'>

<script>
	document_add_active = false;

	$('#document_add_button').click(function() {
		if (document_add_active) {
			return;
		}

		document_add_active = true;
		$('body').append("\
<div class='absolute_popup_dialog document_add' id='document_add'>\
<div class='dialog'>\
<form id='document_add_form'>\
<table>\
<tr><th colspan=2>Nowy dokument</th></tr>\
<tr><td><label for='document_add_signature'>Sygnatura:</label></td><td><input type='text' id='document_add_signature'></td></tr>\
<tr><td><label for='document_add_date_from'>Data od [mm/dd/yyyy]:</label></td><td><input type='date' id='document_add_date_from'></td></tr>\
<tr><td><label for='document_add_date_to'>Data do [mm/dd/yyyy]:</label></td><td><input type='date' id='document_add_date_to'></td></tr>\
<tr><td colspan=2><label for='document_add_description'>Opis:</label></td></tr>\
<tr><td colspan=2><textarea id='document_add_description' class='document_add_description'></textarea></td></tr>\
<tr><td colspan=2 class='submit'><input type='submit' value='Dodaj' class='submit'>\
	<input type='button' id='document_add_cancel' value='Zamknij' class='submit'></td></tr>\
</table>\
</form>\
</div>\
</div>\
		");

		$('#document_add_form').submit(function() {
			$('#error').text('');

			signature = $('#document_add_signature').val();
			date_from = $('#document_add_date_from').val();
			date_to = $('#document_add_date_to').val();
			description = $('#document_add_description').val();

			if (signature === '') {
				$('#error').text('sygnatura nie może być pusta');
				return false;
			}

			if (date_from === '' || date_to === '') {
				$('#error').text('data nie może być pusta');
				return false;
			}

			if (date_from > date_to) {
				$('#error').text('data od nie może być po dacie do');
				return false;
			}

			$.ajax({
				dataType: 'json',
				type: 'POST',
				url: 'search_ajax.php',
				data: {
					'action': 'document_add',
					'signature': signature,
					'date_from': date_from,
					'date_to': date_to,
					'description': description,
				},
			}).done(function(data) {
				if (data['error']) {
					$('#error').text(data['error']);
				} else {
					$(this).parents('#document_add').remove();
					document_add_active = false;
					reload_results();
				}
			}).fail(function(data) {
				$('#error').text(data['responseText']);
			});

			return false;
		});

		$('#document_add_cancel').click(function() {
			$(this).parents('#document_add').remove();
			document_add_active = false;
		});
	});
</script>
				<?php
			}
		}

		function main_content() {
			?>
<?php $this->add_document(); ?>
<div class='search'>
<form id='search_form'>
<input type='text' id='search_box' class='search_box'>
<input type='submit' id='search_button' class='submit' value='szukaj'>
</form>
<ul class='search_additional_parameters'>
<li>
<div class='entry'>
<div class='header'>Osoby powiązane</div>
<ul id='search_people' class='search_people'></ul>
<form id='search_add_person_form'>
<input type='text' id='search_add_person_input'>
<input type='submit' value='dodaj' class='submit'>
</form>
</div>
</li>
<li>
<div class='entry'>
<div class='header'>Kategorie</div>
<ul id='search_tags' class='search_tags'></ul>
<form id='search_add_tag_form'>
<input type='text' id='search_add_tag_input'>
<input type='submit' value='dodaj' class='submit'>
</form>
</div>
</li>
</ul>
<div class='pages'>
<form id='search_page_form'>
Znaleziono artykułów: <span id='results_count'>0</span><br>
Strona <input type='number' id='results_page' class='short' value='1' min='1' step='1'> z <span id='results_pages'>0</span>
<input type='submit' id='results_change_page' value='Przejdź' class='submit'>
</form>
</div>
</div>

<div class='results'>
<ul id='results'>
</ul>
</div>

<script>
/*
 * search form
 */
	page_limit = 50;

	document_edit_active = false

	function search_results_show_edit() {
		if (document_edit_active) {
			return;
		}
		document_edit_active = true;

		signature = $(this).attr('data-signature');

		$.ajax({
			dataType: 'json',
			type: 'POST',
			url: 'search_ajax.php',
			data: {
				'action': 'get_document_data',
				'signature': signature,
			},
		}).done(function(data) {
			if (data['error']) {
				$('#error').text(data['error']);
				document_edit_active = false;
			} else {
				file = data['result'];
				signature = file["signature"];
				$('body').append("\
<div class='absolute_popup_dialog document_edit' id='document_edit'>\
<div class='dialog'>\
<form id='document_edit_form'>\
<table>\
<tr><th colspan=2>Nowy dokument</th></tr>\
<tr><td>Sygnatura:</td><td>"+signature+"</td></tr>\
<tr><td><label for='document_edit_date_from'>Data od [mm/dd/yyyy]:</label></td><td><input type='date' id='document_edit_date_from' value='"+file["date_from"]+"'></td></tr>\
<tr><td><label for='document_edit_date_to'>Data do [mm/dd/yyyy]:</label></td><td><input type='date' id='document_edit_date_to' value='"+file["date_to"]+"'></td></tr>\
<tr><td colspan=2><label for='document_edit_description'>Opis:</label></td></tr>\
<tr><td colspan=2><textarea id='document_edit_description' class='document_edit_description'>"+file["description"]+"</textarea></td></tr>\
<tr><td colspan=2 class='submit'><input type='submit' value='Zapisz' class='submit'>\
	<input type='button' id='document_edit_cancel' value='Zamknij' class='submit'></td></tr>\
</table>\
</form>\
</div>\
</div>\
				");


				$('#document_edit_form').submit(function() {
					$('#error').text('');

					date_from = $('#document_edit_date_from').val();
					date_to = $('#document_edit_date_to').val();
					description = $('#document_edit_description').val();

					div_document_edit = $(this).parents('#document_edit');

					if (date_from === '' || date_to === '') {
						$('#error').text('data nie może być pusta');
						return false;
					}

					if (date_from > date_to) {
						$('#error').text('data od nie może być po dacie do');
						return false;
					}

					$.ajax({
						dataType: 'json',
						type: 'POST',
						url: 'search_ajax.php',
						data: {
							'action': 'document_edit',
							'signature': signature,
							'date_from': date_from,
							'date_to': date_to,
							'description': description,
						},
					}).done(function(data) {
						if (data['error']) {
							$('#error').text(data['error']);
						} else {
							div_document_edit.remove();
							document_edit_active = false;
							reload_results();
						}
					}).fail(function(data) {
						$('#error').text(data['responseText']);
					});

					return false;
				});

				$('#document_edit_cancel').click(function() {
					$(this).parents('#document_edit').remove();
					document_edit_active = false;
				});
			}
		}).fail(function(data) {
			$('#error').text(data['responseText']);
			document_edit_active = false;
		});
	}

	document_remove_active = false
	function search_results_show_remove() {
		if (document_remove_active) {
			return;
		}
		document_remove_active = true;

		signature = $(this).attr('data-signature');
		
		$('body').append("\
<div class='popup_dialog' id='document_remove'>\
<div class='dialog'>\
<table>\
<tr><th colspan=2>Usuwanie dokumentu "+signature+"</th></tr>\
<tr><td colspan=2 class='submit'><input id='document_remove_remove' type='button' class='submit' value='Usuń'><input id='document_remove_cancel' type='button' class='submit' value='Anuluj'></td></tr>\
</table>\
</div>\
</div>\
		");

		$('#document_remove_cancel').click(function() {
			$(this).parents('#document_remove').remove();
			document_remove_active = false;
		});

		$('#document_remove_remove').click(function() {
			div_document_remove = $(this).parents('#document_remove');
			$.ajax({
				dataType: 'json',
				type: 'POST',
				url: 'search_ajax.php',
				data: {
					'action': 'document_remove',
					'signature': signature,
				},
			}).done(function(data) {
				if (data['error']) {
					$('#error').text(data['error']);
				} else {
					div_document_remove.remove();
					document_remove_active = false;
					reload_results();
				}
			}).fail(function(data) {
				$('#error').text(data['responseText']);
			});
		});
	}

	function reload_results() {
		keywords = $('#search_box').val().split(' ');
		page = $('#results_page').val();

		$.ajax({
			dataType: 'json',
			type: 'POST',
			url: 'search_ajax.php',
			data: {
				'action': 'search',
				'keywords': keywords,
				'page': page,
				'limit': page_limit,
			},
		}).done(function(data) {
			if (data['error']) {
				$('#error').text(data['error']);
			} else {
				results = $('#results');
				results.children().remove();

				$('#results_count').text(data['count']);
				$('#results_pages').text(Math.ceil(data['count'] / page_limit));
				for (i in data['results']) {
					file = data['results'][i];

					results.append("<li><?php
							if (is_logged()) {
								echo "<div class='search_results_options'>";
								echo "<input data-signature='\"+file[\"signature\"]+\"' class='search_results_show_edit submit' type='button' value='edytuj'>";
								echo "<input data-signature='\"+file[\"signature\"]+\"' class='search_results_show_remove submit' type='button' value='usuń'>";
								echo "</div>";
							} ?><div class='title'>"+file["signature"]+"</div><div class='date'>"+file["date_from"]+" - "+file["date_to"]+"</div>"
								+"<div class='description'>"+file["description"]+"</div></li>");
				}
			}
		}).fail(function(data) {
			$('#error').text(data['responseText']);
		});

		return false;
	}

	$(document).on('click', '.search_results_show_edit', search_results_show_edit);
	$(document).on('click', '.search_results_show_remove', search_results_show_remove);

	$('#search_form').submit(reload_results);
	$('#search_page_form').submit(reload_results);

/*
 * people form
 */
	$('#search_add_person_input').autocomplete({
		source: function(request, response) {
			keywords = $('#search_add_person_input').val().split(' ');

			$.ajax({
				dataType: 'json',
				type: 'POST',
				url: 'search_ajax.php',
				data: {
					'action': 'autocomplete_person',
					'keywords': keywords,
				},
			}).done(function(data) {
				if (data['error']) {
					$('#error').text(data['error']);
				} else {
					response(data['results']);
				}
			}).fail(function(data) {
				$('#error').text(data['responseText']);
			});
		},
	});

	$('#search_add_person_form').submit(function() {
		person_input = $('#search_add_person_input');

		if (person_input.val() != '') {
			$('#search_people').append('<li>'+person_input.val()+'</li>');
			person_input.val('');
		}
		return false;
	});

/*
 * people form
 */
	$('#search_add_tag_input').autocomplete({
		source: function(request, response) {
			keywords = $('#search_add_tag_input').val().split(' ');

			$.ajax({
				dataType: 'json',
				type: 'POST',
				url: 'search_ajax.php',
				data: {
					'action': 'autocomplete_tag',
					'keywords': keywords,
				},
			}).done(function(data) {
				if (data['error']) {
					$('#error').text(data['error']);
				} else {
					response(data['results']);
				}
			}).fail(function(data) {
				$('#error').text(data['responseText']);
			});
		},
	});

	$('#search_add_tag_form').submit(function() {
		tag_input = $('#search_add_tag_input');

		if (tag_input.val() != '') {
			$('#search_tags').append('<li>'+tag_input.val()+'</li>');
			tag_input.val('');
		}
		return false;
	});
</script>
			<?php
		}
	}

	$site = new SearchSite();
	$site->show();
?>
