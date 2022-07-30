<?php
$amount_page = 8;
$current_page = isset($_POST["current_page"]) ? (int)$_POST["current_page"]:1;
$tablename = $_POST["name"];
$url_referer = $_SERVER["HTTP_REFERER"];

$referer = substr($url_referer, strrpos($url_referer, "/") + 1, strlen($url_referer) - strrpos($url_referer, "/")); 
$pagename = substr($referer, 0, strrpos($referer, "."));

$host = "localhost";

if (strpos(strtolower($_SERVER["SERVER_NAME"]), "localhost") !== false) {
	$db   = "shift";
	$user = "root";
	$pass = "root";
} else {
	$db = "hexa19_shift";
	$user = "hexa19";
	$pass = "hexAueTRDCsf032020";
}

$port = "3306";
$charset = 'utf8';

$options = [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES => false,
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";

try {
     $pdo = new \PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$stmt = $pdo->query(
	"SELECT CAST(CEIL(CAST(COUNT(id) AS DECIMAL(10,2)) / $amount_page.0) AS UNSIGNED) AS max_page FROM $tablename"
);

$row = $stmt->fetch();
$max_page = $row["max_page"];

$offset = $amount_page * ($current_page - 1);
$sql = "";

if ($pagename == "list_intern") {
	$arr_fields = array("id", "name", "surname", "email", "username", "IF (active=1, 'Si', 'No') AS active");
	$arr_order_fields = array("name", "surname");
}

if ($pagename == "list_extern") {
	$arr_fields = array("id", "name", "surname", "cardid", "email", "IF (active=1, 'Si', 'No') AS active");
	$arr_order_fields = array("name", "surname");
}

if ($pagename == "list_speciality" || $pagename == "list_usertype") {
	$arr_fields = array("id", "name");
	$arr_order_fields = array("name");
}

if ($pagename == "list_shift_intern") {
	$arr_fields = array(
		"t.id"
	,	"concat(i.name, ' ', i.surname) AS name_intern"
	,	"s.name AS name_speciality"
	,	"t.dayname"
	,	"DATE_FORMAT(t.timeon, '%H:%i') AS timeon"
	,	"DATE_FORMAT(t.timeoff, '%H:%i') AS timeoff"
	);
	$arr_order_fields = array("name_intern", "name_speciality");	

	$fields = implode(", ", $arr_fields);
	$order_fields = implode(", ", $arr_order_fields);
		
	$sql = "SELECT $fields " .
			"FROM $tablename t INNER JOIN intern i ON t.id_intern = i.id " . 
			"INNER JOIN speciality s ON t.id_speciality = s.id " .
			"ORDER BY $order_fields LIMIT $amount_page OFFSET $offset";	
}

if ($pagename == "list_shift_extern") {	
	$arr_fields = array(
		"t.id"
	,	"concat(i.name, ' ', i.surname) AS name_intern"
	,	"concat(e.name, ' ', e.surname) AS name_extern"
	,	"DATE_FORMAT(t.meeting, '%d-%m-%Y %H:%i') AS meeting"
	,	"IF (t.confirm=1, 'Si', 'No') AS confirm"
	,	"IF (t.active=1, 'Si', 'No') AS active"
	);
	$arr_order_fields = array("name_intern", "name_extern");

	$fields = implode(", ", $arr_fields);
	$order_fields = implode(", ", $arr_order_fields);
		
	$sql = "SELECT $fields " .
			"FROM $tablename t INNER JOIN intern i ON t.id_intern = i.id " . 
			"INNER JOIN extern e ON t.id_extern = e.id " .
			"ORDER BY $order_fields LIMIT $amount_page OFFSET $offset";	
}

if (strlen($sql) == 0) {
	$fields = implode(", ", $arr_fields);
	$order_fields = implode(", ", $arr_order_fields);
	
	$sql = "SELECT $fields " .
			"FROM $tablename ORDER BY $order_fields LIMIT $amount_page OFFSET $offset";
}
		
$stmt = $pdo->query($sql);

//output data-response
echo "$pagename\r\n";
echo "$current_page\r\n";
echo "$max_page\r\n";

while ($row = $stmt->fetch()) {
	$line = "";
	foreach($row as $value) {
		$line .= "\"" . (string)$value . "\",";
	}
	$line = substr($line, 0, strlen($line) - 1);
	echo "$line\r\n";
}

$pdo = null;
