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
<tr><td>Sygnatura:</td><td><input type='text' id='document_add_signature'></td></tr>\
<tr><td>Data od [mm/dd/yyyy]:</td><td><input type='date' id='document_add_date_from'></td></tr>\
<tr><td>Data do [mm/dd/yyyy]:</td><td><input type='date' id='document_add_date_to'></td></tr>\
<tr><td colspan=2>Opis:</td></tr>\
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
					'action': 'add',
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
					results.append("<li><div class='title'>"+file["signature"]+"</div><div class='description'>"+file["description"]+"</div></li>");
				}
			}
		}).fail(function(data) {
			$('#error').text(data['responseText']);
		});

		return false;
	}

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
