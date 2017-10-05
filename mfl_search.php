<?php

$var = 'mfl_search';
if(!isset($_GET[$var])) { exit; }
if($_GET[$var] === '') { exit; }
if($_GET[$var] === false) { exit; }
if($_GET[$var] === null) { exit; }
if(empty($_GET[$var])) { exit; }
$mfl_search = $_GET[$var];

$localhosts = array(
    '127.0.0.1',
    'localhost',
	'::1'
);

if(in_array($_SERVER['REMOTE_ADDR'], $localhosts)) {
ini_set('display_errors', '1');
error_reporting(E_ALL | E_STRICT);
}

$ini_array = parse_ini_file('../cron/mfl.ini');
$sdsn = $ini_array['sdsn'];
$user = $ini_array['user'];
$pass = $ini_array['pass'];

$opts = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
try {
    $db = new PDO($sdsn, $user, $pass, $opts);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Problemas de conexão à base de dados:<br/>' . $e);
}

$sql = 'SELECT count(id) AS n FROM units WHERE name LIKE "%'.$mfl_search.'%"';
$tabquery = $db->query($sql);
$tabquery->setFetchMode(PDO::FETCH_ASSOC);
$rows = $tabquery->fetch();
$n = $rows['n'];

$result = array();

if ($n >= 1) {
	$sql = 'SELECT * FROM units WHERE name LIKE "%'.$mfl_search.'%"';
	$rows = $db->query($sql);
	$rows->setFetchMode(PDO::FETCH_ASSOC);
	foreach ($rows as $row) {
		$result[] = array($row['id'], $row['name']);
	}
	echo json_encode($result);
	exit;
}

$sql = 'SELECT count(id_unit) AS n FROM unit_code WHERE code LIKE "%'.$mfl_search.'%"';
$tabquery = $db->query($sql);
$tabquery->setFetchMode(PDO::FETCH_ASSOC);
$rows = $tabquery->fetch();
$n = $rows['n'];

if ($n >= 1) {
	$sql = 'SELECT * FROM unit_code WHERE code LIKE "%'.$mfl_search.'%"';
	$rows = $db->query($sql);
	$rows->setFetchMode(PDO::FETCH_ASSOC);
	foreach ($rows as $row) {
		$result[] = array($row['id_unit'], $row['code']);
	}
	echo json_encode($result);
	exit;
}

echo '0';

?>
