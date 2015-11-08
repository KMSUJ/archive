<?php
	include_once "base_site.inc.php";

	class SearchSite extends BaseSite {
		function title() {
			parent::title();
			echo " - Szukaj";
		}

		function main_content() {
			?>
			<?php
		}
	}

	$site = new SearchSite();
	$site->show();
?>
