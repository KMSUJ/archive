<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Biblioteka KMS UJ</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="global_css.css" rel="stylesheet" type="text/css" media="all">
	</head>
<body>
	<div align=center height=600>

<table bgcolor=#bed5e0 width=800><tr><td align=left>
<a class='menu_l' href='logowanie.php'>zaloguj się</a></td><td align=right>
</td></tr></table>
<br><table width=800 cellpadding=2 cellspacing=3 style='border: solid 1px #666666'>

<tr align=center bgcolor=#666666 style='color: white; font-weight: bold'><td width=40>Id</td><td width=200>Segregator</td><td width=330  align=center></td><td  width=70>Rok</td><td  width=200>Opis</td></tr>
<?
function printRow($id, $segregator, $rok) {
	echo '<tr bgcolor=#e0eff9><td align=right width=90>';
	echo $id
	echo '</td><td>';
	echo $segregator;
	echo '</td><td>';
	echo $opis;
	echo '</td></tr>';
}

mysql_connect('localhost', 'kmsuj_http', '79m02k15');

mysql_select_db('biblioteka');

$my_query = "SELECT * FROM ";

switch($_GET['wg']) {
	case 'nazwisko':
		$my_query += 'archive_files JOIN people_to_archive_files '
			+ 'ON archive_files.id = people_to_archive_files.archive_file_id JOIN people '
			+ 'ON people_to_archive_files.person_id = people.person_id WHERE surname=\"' + url_encode($_GET['q_string']) + '\";';
		break;
	case 'rok':
		$my_query += 'archive_files WHERE year(date_lower_bound) <= ' + url_encode($_GET['q_string'])
			+ 'AND year(date_upper_bound) >= ' + url_encode($_GET['q_string']) + ';';
		break;
	case 'segregator':
		$my_query += 'archive_files WHERE segregator=' + url_encode($_GET['q_string']) + ';';
		break;
	case 'opis':
		$my_query += 'archive_files WHERE opis=' + url_encode($_GET['q_string']) + ';';
		break;
}

$search_results = mysql_query($query);

mysql_close();

$n = mysql_numrow($search_results);

for($i = 1; $i <= $n; $i++) {
	printRow(mysql_result($search_result, $i, "id"),
		 mysql_result($search_result, $i, "segregetor"),
		 //mysql_result($search_result, $i, ""),
		 mysql_result($search_result, $i, "description")
	);
}

?>

</table><br><button class='blue_b' onClick='location="katalog.php"'>nowe szukanie &gt;</button>
</div></body></html>
