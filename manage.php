<?php session_start(); ?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" href="js/jquery-ui-1.12.1.custom/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="css/datatables.min.css"/>
		<link rel="stylesheet" href="css/manage.css">
		<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
		<script type="text/javascript" src="js/datatables.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCIeZTMfIeXUkueMevGm5dizgIcmCFkKRo"></script>
		<script type="text/javascript" src="js/manage.js"></script>
		<title>Lista Mestre de Unidades de Saúde</title>
	</head>
	<body>
		<div id="login_logon"></div>
		<div id="form_edit" style="display:none;"></div>
		<div class="container">
			<div class="fix">
				<div class="headiv" id="hl">
					<table>
						<tbody>
							<tr><td colspan="2">Nome ou código</td></tr>
							<tr>
								<td><input type="text" maxlength="100" /></td>
								<td><img src="css/png/search.png" alt="Busca" /></td>
							</tr>
							<tr><td colspan="2"><select disabled="disabled" id="search_results"></select></td></tr>
						</tbody>
					</table>
				</div>
				<div class="headiv" id="hc"><h2>Lista Mestre de Unidades de Saúde</h2></div>
				<div class="headiv" id="hr"></div>
			</div>
			<div class="drop" id="tree"></div>
			<div class="drop" id="map"></div>
			<div class="drop" id="form">
				<div class="inform" id="form_elements"><table><tbody></tbody></table></div>
			</div>
			<div class="fix" id="footer">
				<table id="hu_list">

<?php
# check whether script is running in localhost
$localhosts = array(
    '127.0.0.1',
    'localhost',
	'::1'
);
if(in_array($_SERVER['REMOTE_ADDR'], $localhosts)) {
ini_set('display_errors', '1');
error_reporting(E_ALL | E_STRICT);
}

# (de)activate debug
$debug = false;

$ini_file = '../cron/mfl.ini';
$ini_array = parse_ini_file($ini_file);
$user = $ini_array['user'];
$pass = $ini_array['pass'];
$sdsn = $ini_array['sdsn'];

$opts = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
try {
    $db = new PDO($sdsn, $user, $pass, $opts);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Problemas de conexão à base de dados:<br/>' . $e);
}

$sql = 'SELECT * FROM mfl_table_view';
$tabquery = $db->query($sql);
$tabquery->setFetchMode(PDO::FETCH_ASSOC);
if ($tabquery->rowCount() < 1) { echo '<h1>A base de dados é vazia</h1>'; exit; }

echo '<thead>';
$i = 0;
foreach ($tabquery as $record) {
	echo '<tr>' ;
	foreach ($record as $key => $row) {
		if ($i==0) {
			echo '<th>' . $key . '</th>';
		} else {
			if ($key=='Serviços') {
				echo '<td style="font-size:xx-small;">' . services($db,$row) . '</td>' ;
			} else {
				echo '<td>' . $row . '</td>' ;
			}
		}
	}
	echo '</tr>' ;
	if ($i==0) { echo '</thead><tbody>' ; }
	$i++;
}
echo '</tbody>' ;




if ($debug) { see_debug($table); exit; }




//----------------------------------------------------------------------------------------------------------
function see_debug($table) {
	require 'kint/Kint.class.php';
	echo '<hr />';
	!Kint::dump( $table );

}
//----------------------------------------------------------------------------------------------------------
function create_array_from_tables ($db, $sql) {
	$tabquery = $db->query($sql);
	$tabquery->setFetchMode(PDO::FETCH_ASSOC);
	$return = [];
	foreach ($tabquery as $tabres) {
		array_push($return, $tabres);
	}
	return $return;
}

//----------------------------------------------------------------------------------------------------------
?>

				</table>
			</div>
		</div>
	</body>
</html>












