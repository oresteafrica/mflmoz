<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="css/index.css">
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/index.js"></script>
</head>
<body>
	<div id="main">

<?php

# class useful for debugging arrays
require 'kint/Kint.class.php';
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

#read csv file and store it in array
$csv = array_map('str_getcsv', file('mfl.csv'));

# calculate array width in order to add column
$csv_width = count($csv[0]);

# add column that will contain the id coming from $db->lastInsertId()
$csv[0][$csv_width] = 'id_mysql';
for ($i = 1; $i < count($csv); $i++) {
	$csv[$i][$csv_width] = 0;
}

# set value for identifying the column containing $db->lastInsertId()
$id_mysql_c = $csv_width;

# increase by 1 the information about array width
$csv_width++;

foreach ($csv[0] as $index => $title) {
	switch ($title) {
		case 'row':
			$row_c = $index;
			break;
		case 'id':
			$id_c = $index;
			break;
		case 'province':
			$province_c = $index;
			break;
		case 'district':
			$district_c = $index;
			break;
		case 'name':
			$name_c = $index;
			break;
		case 'short_name':
			$short_name_c = $index;
			break;
		case 'type':
			$type_c = $index;
			break;
		case 'latitude':
			$latitude_c = $index;
			break;
		case 'longitude':
			$longitude_c = $index;
			break;
		default:
	}
}

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


# copy mysql table `unit_type` in array
$sql = 'SELECT id, name FROM unit_type';
$unit_type = create_array_from_tables ($db, $sql);
# change case to lower for all values in array in order to facilitate search
for ($i = 0; $i < count($unit_type); $i++) {
	$unit_type[$i]['name'] = strtolower($unit_type[$i]['name']);
}

# replace type with codes
$csv_with_codes = $csv;
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$temp = strtolower(trim($csv_with_codes[$i][$type_c]));
	for ($k = 0; $k < count($unit_type); $k++) {
		if ( substr($temp,0,5) == substr($unit_type[$k]['name'],0,5) ) {
			$csv_with_codes[$i][$type_c] = $unit_type[$k]['id'];
			break;
		}
	}
	for ($k = 0; $k < count($unit_type); $k++) {
		if ( levenshtein($temp,$unit_type[$k]['name']) < 8 ) {
			$csv_with_codes[$i][$type_c] = $unit_type[$k]['id'];
			break;
		}
	}
	for ($k = 0; $k < count($unit_type); $k++) {
		if ( levenshtein($temp,$unit_type[$k]['name']) < 5 ) {
			$csv_with_codes[$i][$type_c] = $unit_type[$k]['id'];
			break;
		}
	}
	for ($k = 0; $k < count($unit_type); $k++) {
		if ( levenshtein($temp,$unit_type[$k]['name']) < 1 ) {
			$csv_with_codes[$i][$type_c] = $unit_type[$k]['id'];
			break;
		}
	}

	# check HU name for unit type embedded data
	$temp_name = $csv_with_codes[$i][$name_c];
	if ( strpos($temp_name, 'CS III') > -1 ) {
		$csv_with_codes[$i][$type_c] = 3;
		continue;
	} 
	if ( strpos($temp_name, 'CS II') > -1 ) {
		$csv_with_codes[$i][$type_c] = 3;
		continue;
	} 
	if ( strpos($temp_name, 'CS I') > -1 ) {
		$csv_with_codes[$i][$type_c] = 2;
		continue;
	}
	if ( strpos($temp_name, 'CSURB') > -1 ) {
		$csv_with_codes[$i][$type_c] = 4;
		continue;
	} 
	if ( strpos($temp_name, 'PS') > -1 ) {
		$csv_with_codes[$i][$type_c] = 1;
		continue;
	} 
	if ( strpos($temp_name, 'HD') > -1 ) {
		$csv_with_codes[$i][$type_c] = 8;
		continue;
	} 
	if ( strpos($temp_name, 'HC') > -1 ) {
		$csv_with_codes[$i][$type_c] = 12;
		continue;
	} 
	if ( strpos($temp_name, 'HG') > -1 ) {
		$csv_with_codes[$i][$type_c] = 9;
		continue;
	} 
	if ( strpos($temp_name, 'HR') > -1 ) {
		$csv_with_codes[$i][$type_c] = 7;
		continue;
	} 
	
	if (! is_numeric($csv_with_codes[$i][$type_c])) $csv_with_codes[$i][$type_c] = 29;
}

# copy mysql table `areas` in array containing only provinces
$sql = 'SELECT id, name FROM areas WHERE id > 1 AND id < 13';
$provinces = create_array_from_tables ($db, $sql);
# change case to lower for all values in array in order to facilitate search
for ($i = 0; $i < count($provinces); $i++) {
	$provinces[$i]['name'] = strtolower($provinces[$i]['name']);
}

# replace province with codes
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$temp = strtolower(trim($csv_with_codes[$i][$province_c]));
	for ($k = 0; $k < count($provinces); $k++) {
		if ( levenshtein($temp,$provinces[$k]['name']) < 2 ) {
			$csv_with_codes[$i][$province_c] = $provinces[$k]['id'];
			break;
		}
	}
}

# copy mysql table composed by `areas` and `hierarchy_areas_areas`
# in array containing only districts and id_up
$sql = 'SELECT areas.id, areas.name, hierarchy_areas_areas.id_up FROM areas, hierarchy_areas_areas WHERE areas.id = hierarchy_areas_areas.id';
$districts = create_array_from_tables ($db, $sql);
# change case to lower for all values in array in order to facilitate search
for ($i = 0; $i < count($districts); $i++) {
	$districts[$i]['name'] = strtolower($districts[$i]['name']);
}

# replace districts with codes
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$temp = strtolower(trim($csv_with_codes[$i][$district_c]));
	$id_province = $csv_with_codes[$i][$province_c];
	for ($k = 0; $k < count($districts); $k++) {
		if ( (strpos($temp, $districts[$k]['name']) !== false) and $districts[$k]['id_up'] == $id_province ) {
			$csv_with_codes[$i][$district_c] = $districts[$k]['id'];
			break;
		}
	}	
	for ($k = 0; $k < count($districts); $k++) {
		if ( levenshtein($temp,$districts[$k]['name']) < 4 and $districts[$k]['id_up'] == $id_province ) {
			$csv_with_codes[$i][$district_c] = $districts[$k]['id'];
			break;
		}
	}
}

# round coordinates to 5 decimals
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$csv_with_codes[$i][$latitude_c] = round($csv_with_codes[$i][$latitude_c],6);
	$csv_with_codes[$i][$longitude_c] = round($csv_with_codes[$i][$longitude_c],6);
}


# ini debug ------------------------------------------------------------
if ($debug) {
	echo '__DIR__ = '.__DIR__;
	echo '<br />';
	!Kint::dump( $csv_with_codes );
	$fp = fopen(__DIR__ . '/csv_with_codes.csv', 'w');
	foreach ($csv_with_codes as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);
	exit;
}
# end debug ------------------------------------------------------------


$today_date_mysql = date("Y-m-d H:i:s");

# populate mysql table `units`
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$name_to_insert = strlen($csv_with_codes[$i][$name_c])>3?$csv_with_codes[$i][$name_c]:$csv_with_codes[$i][$short_name_c];
	if (strlen($name_to_insert)<3) continue;
	$sql = 'INSERT INTO units (name) VALUES(' . $db->quote($name_to_insert) . ')';
	$db->exec($sql);
	$csv_with_codes[$i][$id_mysql_c] = $db->lastInsertId();
}

# populate mysql table `units_coord`
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$sql = 'INSERT INTO units_coord (id_unit,lat,lon,alt) VALUES(' . 
		$csv_with_codes[$i][$id_mysql_c] . ',' .
		$csv_with_codes[$i][$latitude_c] . ',' . 
		$csv_with_codes[$i][$longitude_c] .
		',0)';
	$db->exec($sql);
}

# populate mysql table `unit_code`
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$sql = 'INSERT INTO unit_code (id_unit,code,date) VALUES(' . 
		$csv_with_codes[$i][$id_mysql_c] . ',' .
		$db->quote($csv_with_codes[$i][$id_c]) . ',' . 
		$db->quote($today_date_mysql) .
		')';
	$db->exec($sql);
}

# populate mysql table `hierarchy_units_areas`
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$sql = 'INSERT INTO hierarchy_units_areas (id_unit,id_area) VALUES(' . 
		$csv_with_codes[$i][$id_mysql_c] . ',' .
		$csv_with_codes[$i][$district_c] .
		')';
	$db->exec($sql);
}

# populate mysql table `unit_unit_type`
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$sql = 'INSERT INTO unit_unit_type (id_unit,id_type,date) VALUES(' . 
		$csv_with_codes[$i][$id_mysql_c] . ',' .
		$csv_with_codes[$i][$type_c] . ',' . 
		$db->quote($today_date_mysql) .
		')';
	$db->exec($sql);
}

# populate mysql table `unit_unit_authority` with 1
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$sql = 'INSERT INTO unit_unit_authority (id_unit,id_authority,date) VALUES(' . 
		$csv_with_codes[$i][$id_mysql_c] . ', 1, ' . $db->quote($today_date_mysql) . ')';
	$db->exec($sql);
}

# populate mysql table `unit_ministry` with 1
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$sql = 'INSERT INTO unit_ministry (id_unit,id_ministry,date) VALUES(' . 
		$csv_with_codes[$i][$id_mysql_c] . ', 1,' . $db->quote($today_date_mysql) . ')';
	$db->exec($sql);
}

# populate mysql table `unit_unit_state` with 1
for ($i = 1; $i < count($csv_with_codes); $i++) {
	$sql = 'INSERT INTO unit_unit_state (id_unit,id_state,date) VALUES(' . 
		$csv_with_codes[$i][$id_mysql_c] . ', 1,' . $db->quote($today_date_mysql) . ')';
	$db->exec($sql);
}


//----------------------------------------------------------------------------------------------------------
function see_debug() {
	
if ($debug) {
	echo '$csv[0][0] = '.$csv[0][0];
	echo '<br />';
	echo '$csv[0][1] = '.$csv[0][1];
	echo '<br />';
	echo '$csv[1][0] = '.$csv[1][0];
	echo '<br />';
	echo '$csv[1][1] = '.$csv[1][1];
	echo '<br />';
	echo 'implode(\',\',$unit_type[0]) = '.implode(',',$unit_type[0]);
	echo '<br />';
	echo 'unit_type[0][\'id\'] = '.$unit_type[0]['id'];
	echo '<br />';
	echo 'unit_type[0][\'name\'] = '.$unit_type[0]['name'];
	echo '<br />';
	echo '$_SERVER[\'REMOTE_ADDR\'] = '.$_SERVER['REMOTE_ADDR'];
	echo '<br />';
	echo '$row_c = '.$row_c;
	echo '<br />';
	echo '$id_c = '.$id_c;
	echo '<br />';
	echo '$province_c = '.$province_c;
	echo '<br />';
	echo '$district_c = '.$district_c;
	echo '<br />';
	echo '$name_c = '.$name_c;
	echo '<br />';
	echo '$short_name_c = '.$short_name_c;
	echo '<br />';
	echo '$type_c = '.$type_c;
	echo '<br />';
	echo '$latitude_c = '.$latitude_c;
	echo '<br />';
	echo '$longitude_c = '.$longitude_c;
	echo '<br />';
	echo '$id_mysql_c = '.$id_mysql_c;
	echo '<hr />';
	!Kint::dump( $csv_with_codes );
	echo '<hr />';
	!Kint::dump( $provinces );
	echo '<hr />';
	!Kint::dump( $unit_type );
	echo '<hr />';
	!Kint::dump( $csv );
	exit;
}

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
	</div>
</body>
</html>












