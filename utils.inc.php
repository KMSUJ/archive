<?php
	function redirect_meta($destination) {
		echo "<meta http-equiv='refresh' content='0, url=\"$destination\"' />";
	}

	function redirect_site($destination) {
		?>
<!DOCTYPE html>

<html>
<head>
<?php redirect_meta($destination); ?>
</head>
</html>
		<?php
	}
?>
