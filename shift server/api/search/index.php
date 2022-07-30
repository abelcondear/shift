<?php
include_once "../../core/class/common.php";
include_once "../../core/class/mysql.php";
include_once "../../core/class/enumeration.php";

$utl = new \Common\Utils($_POST, $_SERVER);
//$db_query = new \MySQL\Query(\Enumeration\Environment::development);
$db_query = new \MySQL\Query(\Enumeration\Environment::preproduction);

$action = $utl->data_client->action;

if ($action == \Enumeration\Action::search_shift) {
	if (strlen($utl->data_client->txt_date)) {
		$mysql_weekday = \Enumeration\LabelDate::mysql_weekday[
			date('w', strtotime($utl->FormatDate($utl->data_client->txt_date)))
		];
	}
	
	$sql = "SELECT DISTINCT " . 
	"	sw.id_intern, i.name, i.surname, i.gender, si.price, si.location, " .
	"	sp.name AS speciality_name, sw.weekday, sw.time_start, sw.time_end " .
	"FROM " .
	"	shift_extern se " .
	"RIGHT JOIN shift_weekday sw " .
	"ON " .
	"	se.id_intern = sw.id_intern AND " .
	"	DAYNAME(se.weekday) = DAYNAME(sw.weekday) AND " .
	"	se.time_start = sw.time_start AND " .
	"	se.time_end = sw.time_end " .
	"RIGHT JOIN intern i " .
	"ON " .
	"	sw.id_intern = i.id " .
	"RIGHT JOIN usertype u " .
	"ON ".
	"	i.id_usertype = u.id " .	
	"RIGHT JOIN shift_intern si " .
	"ON ".
	"	i.id = si.id_intern " .
	"RIGHT JOIN speciality sp " .
	"ON " .
	"	si.id_speciality = sp.id " .
	"WHERE " .
	"	LOWER(u.name) = 'interno' AND " .
	"	se.id_intern is NULL AND ";
	
	if (strlen($utl->data_client->cmb_professional)) {
		$sql .= " sp.name = '{$utl->data_client->cmb_professional}' AND ";
	}
	
	if (strlen($utl->data_client->cmb_range_a)) {
		$sql .= " sw.time_start >= CONVERT('{$utl->data_client->cmb_range_a}:00', TIME) AND ";
	}
	
	if (strlen($utl->data_client->cmb_range_b)) {
		$sql .= " sw.time_end <= CONVERT('{$utl->data_client->cmb_range_b}:00', TIME) AND ";
	}
	
	if (strlen($utl->data_client->opt_dayname) && $utl->data_client->opt_dayname != "Any") {
		$sql .= " DAYNAME(sw.weekday) = '{$utl->data_client->opt_dayname}' AND ";
	}

	if (strlen($utl->data_client->txt_date)) {
		$sql .= " WEEKDAY(sw.weekday) >= {$mysql_weekday} AND ";
	}

	$sql = substr($sql, 0, -strlen(" AND "));
	$sql .= " ORDER BY sw.weekday, sw.time_start LIMIT 10";
	$rows = $db_query->Execute($sql);
	$dataset = Array();
	
	foreach($rows as $row) {
		array_push($dataset, [
			$row["id_intern"]
		,	$row["name"]
		,	$row["surname"]
		,	$row["gender"]
		,	($row["gender"] == "M") ? \Enumeration\Genre::D_Male:\Enumeration\Genre::D_Female
		,	$row["price"]
		,	$row["location"]
		,	$row["speciality_name"]
		,	$utl->GetDateOfWeek($row["weekday"])
		,	$row["time_start"]		
		]);
	}
	
	echo $utl->buildOutput($dataset);
} elseif ($action == \Enumeration\Action::get_shift) {
	$mysql_weekday = \Enumeration\LabelDate::mysql_weekday[
		date('w', strtotime($utl->data_client->weekday))
	];
	
	$sql = "SELECT DISTINCT " . 
	"	sw.id_intern, i.name, i.surname, i.gender, i.email, si.price, " .
	"	si.location, sp.name AS speciality_name, sw.weekday, sw.time_start, sw.time_end " .
	"FROM " .
	"	shift_extern se " .
	"RIGHT JOIN shift_weekday sw " .
	"ON " .
	"	se.id_intern = sw.id_intern AND " .
	"	DAYNAME(se.weekday) = DAYNAME(sw.weekday) AND " .
	"	se.time_start = sw.time_start AND " .
	"	se.time_end = sw.time_end " .
	"RIGHT JOIN intern i " .
	"ON " .
	"	sw.id_intern = i.id " .
	"RIGHT JOIN usertype u " .
	"ON ".
	"	i.id_usertype = u.id " .	
	"RIGHT JOIN shift_intern si " .
	"ON ".
	"	i.id = si.id_intern " .
	"RIGHT JOIN speciality sp " .
	"ON " .
	"	si.id_speciality = sp.id " .
	"WHERE " .
	"	LOWER(u.name) = 'interno' AND " .
	"	se.id_intern is NULL AND " .
	"	sw.id_intern = {$utl->data_client->id_intern} AND " .
	"	WEEKDAY(sw.weekday) = {$mysql_weekday} AND " .
	"	sw.time_start = '{$utl->data_client->time_start}'";
	$rows_intern = $db_query->Execute($sql);

	$sql = "SELECT id, name, surname, gender, email FROM extern " .
		"WHERE id = {$utl->data_client->id_extern}";
	$rows_extern = $db_query->Execute($sql);
		
	$dataset = Array();
	array_push($dataset, [
		$utl->data_client->id_intern
	,	$rows_intern[0]["name"]
	,	$rows_intern[0]["surname"]
	,	$rows_intern[0]["gender"]
	,	$rows_intern[0]["email"]
	,	$rows_intern[0]["price"]
	,	$rows_intern[0]["location"]
	,	$rows_intern[0]["speciality_name"]
	,	$utl->data_client->weekday
	,	$utl->data_client->time_start
	,	$utl->data_client->id_extern
	,	$rows_extern[0]["name"]
	,	$rows_extern[0]["surname"]
	,	$rows_extern[0]["gender"]
	,	$rows_extern[0]["email"]
	]);
	
	echo $utl->buildOutput($dataset);
} elseif ($action == \Enumeration\Action::search_extern) {
	$sql = "SELECT id FROM extern WHERE cardid = {$utl->data_client->txt_cardid}";
	$rows = $db_query->Execute($sql);
	$dataset = Array();
	
	if ($rows === false || sizeof($rows) == 0) {
		array_push($dataset, [-1]);
	} else {
		array_push($dataset, [$rows[0]["id"]]);
	}
	
	echo $utl->buildOutput($dataset);
} 

$db = null;
$utl = null;
