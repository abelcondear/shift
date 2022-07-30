<?php
include_once "../../core/class/common.php";
include_once "../../core/class/mysql.php";
include_once "../../core/class/mail.php";
include_once "../../core/class/enumeration.php";

$utl = new \Common\Utils($_GET);
$db_query = new \MySQL\Query(\Enumeration\Environment::development);
//$db_query = new \MySQL\Query(\Enumeration\Environment::preproduction);

$action = $utl->data_client->action;

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
	
$sh_time_start = date("m/d/Y h:i:s A", strtotime("{$utl->data_client->weekday} " .
	"{$rows_intern[0]["time_start"]}"));
$sh_time_end = date("m/d/Y h:i:s A", strtotime("{$utl->data_client->weekday} " .
	"{$rows_intern[0]["time_end"]}"));
			
$data_attendee = Array(
	"gender" => ($rows_extern[0]["gender"] == "M") ? \Enumeration\Genre::P_Male:\Enumeration\Genre::P_Female
,	"name" => $rows_extern[0]["name"]
,	"surname" => $rows_extern[0]["surname"]
,	"email" => $rows_extern[0]["email"]
);

$data_intern = Array(
	"gender" => ($rows_intern[0]["gender"] == "M") ? \Enumeration\Genre::D_Male:\Enumeration\Genre::D_Female
,	"name" => $rows_intern[0]["name"]
,	"surname" => $rows_intern[0]["surname"]
,	"location" => $rows_intern[0]["location"]
,	"email" => $rows_intern[0]["email"]
);

$data_administrator = Array();	
foreach($rows_administrator as $row) {
	array_push($data_administrator, [
		"gender" => ($row["gender"] == "M") ? \Enumeration\Genre::P_Male:\Enumeration\Genre::P_Female
	,	"name" => $row["name"]
	,	"surname" => $row["surname"]
	,	"email" => $row["email"]
	]);
}

$rnd_index = rand(0, sizeof($data_administrator) - 1);
$data_organizer = Array(
	"gender" => $data_administrator[$rnd_index]["gender"]
,	"name" => $data_administrator[$rnd_index]["name"] 
,	"surname" => $data_administrator[$rnd_index]["surname"]
,	"email" => $data_administrator[$rnd_index]["email"]
);

$ics_content = $utl->CreateICS(
	$time_start
,	$time_end
,	$summary
,	$description
,	$location
,	[$attendee, $intern]
,	$organizer
);
		
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');