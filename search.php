<?php
	include_once "base_site.inc.php";

	class SearchSite extends BaseSite {
		function title() {
			parent::title();
			echo " - Szukaj";
		}

		function main_content() {
			?>
<div class='search'>
<form id='search_form'>
<input type='text' id='search_box' class='wide'>
<input type='submit' id='search_button' class='submit' value='szukaj'>
<div class='pages'>
Znaleziono <span id='results_count'>0</span> artykułów<br>
Strona <input type='number' id='results_page' class='short' value='1' min='1' step='1'> z <span id='results_pages'>0</span>
<input type='button' id='results_change_page' value='Przejdź' class='submit'>
</div>
</form>
</div>

<div class='results'>
<ul id='results'>
</ul>
</div>

<script>
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
				$('#results_pages').text(data['count'] / page_limit);
				for (i in data['results']) {
					file = data['results'][i];
					results.append("<li><div class='title'>"+file["signature"]+"</div><div class='description'>"+file["description"]+"</div></li>");
				}
			}
		}).fail(function(data) {
			$('#error').text(data['error']);
		}).always(function(data) {
			console.log(data);
		});

		return false;
	}

	$('#search_form').submit(reload_results);
	$('#results_change_page').click(reload_results);
</script>
			<?php
		}
	}

	$site = new SearchSite();
	$site->show();
?>
