<?php
include_once "../../core/class/common.php";
include_once "../../core/class/mysql.php";
include_once "../../core/class/enumeration.php";

$utl = new \Common\Utils($_POST, $_SERVER);
//$db_query = new \MySQL\Query(\Enumeration\Environment::development);
$db_query = new \MySQL\Query(\Enumeration\Environment::preproduction);

$action = $utl->data_client->action;

if ($action == \Enumeration\Action::cancel_shift) {		
	$db_query->BeginTran();

	try {
		$sql = "UPDATE shift_extern " .
			"SET active = 1 " .
			"WHERE " .
			"id_extern = {$utl->data_client->txt_email}"
			")";
		$r = $db_query->Run($sql);
		
		$sql = "SELECT LAST_INSERT_ID() AS last_id_inserted";
		$rows_inserted = $db_query->Execute($sql);
		
		$dataset = Array();
		array_push($dataset, [$rows_inserted[0]["last_id_inserted"]]);
		echo $utl->buildOutput($dataset);
		
		$db_query->CommitTran();
	}
	catch (Exception $e) {
		$num_line = $e->getLine();
		$error_message = $e->getMessage();		
		echo "Error [Line:{$num_line}]: {$error_message}\n";
		
		$db_query->RollbackTran();
	}	
}

$db_query = null;
$utl = null;