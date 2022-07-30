<?php
include_once "../../core/class/common.php";
include_once "../../core/class/mysql.php";
include_once "../../core/class/mail.php";
include_once "../../core/class/enumeration.php";

$utl = new \Common\Utils($_POST, $_SERVER);
$mail = new \Mailing\Mail();
//$db_query = new \MySQL\Query(\Enumeration\Environment::development);
$db_query = new \MySQL\Query(\Enumeration\Environment::preproduction);

$action = $utl->data_client->action;

if ($action == \Enumeration\Action::confirm_shift) {
	$db_query->BeginTran();
	
	try {
		$mysql_weekday = \Enumeration\LabelDate::mysql_weekday[
			date('w', strtotime($utl->data_client->weekday))
		];
		
		$sql = "SELECT DISTINCT " . 
		"	sw.id_intern, i.name, i.surname, i.gender, i.email, " .
		"	si.time_interval, si.price, si.location, sp.name as speciality_name, " .
		"	sw.time_start, sw.time_end " .
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
		"	se.id_intern IS NULL AND " .
		"	sw.id_intern = {$utl->data_client->id_intern} AND " .
		"	WEEKDAY(sw.weekday) = {$mysql_weekday} AND " .
		"	sw.time_start = '{$utl->data_client->time_start}'";
		$rows_intern = $db_query->Execute($sql);
		
		$sql = "SELECT id, name, surname, gender, email " .
			"FROM extern WHERE id = {$utl->data_client->id_extern}";
		$rows_extern = $db_query->Execute($sql);

		$sql = "SELECT i.id, i.name, i.surname, i.gender, email " .
			"FROM intern i INNER JOIN usertype u WHERE LOWER(u.name) = 'administrador' " .
			"ORDER BY u.name";
		$rows_administrator = $db_query->Execute($sql);
			
		$confirm = 1;	
		$sql = "INSERT INTO shift_extern" .
			"(" .
			"	id_intern, id_extern, weekday, time_start, time_end, confirm" .
			")" .
			"VALUES" .
			"(" .
			"{$rows_intern[0]["id_intern"]}, {$rows_extern[0]["id"]}, " .
			"'{$utl->data_client->weekday}', '{$utl->data_client->time_start}', " . 
			"'{$rows_intern[0]["time_end"]}', {$confirm}" .
			")";	
		$result_inserted = $db_query->Run($sql);
		
		$sh_time_start = date("m/d/Y h:i:s A", strtotime("{$utl->data_client->weekday} " .
			"{$rows_intern[0]["time_start"]}"));
		$sh_time_end = date("m/d/Y h:i:s A", strtotime("{$utl->data_client->weekday} " .
			"{$rows_intern[0]["time_end"]}"));
		$sh2_time_start = date("d/m/Y h:i A", strtotime("{$utl->data_client->weekday} " .
			"{$rows_intern[0]["time_start"]}"));	
			
		list(
			$day_num
		,	$weekday_num
		,	$month_num
		,	$hour
		,	$meridiem
		,	$year
		) = $utl->GetDateTimeSplit($sh_time_start);

		$file_template = Array(
			"ics_attendee" => "../../core/template/ics/attendee.txt"
		,	"mail_attendee" => "../../core/template/mail/attendee.html"
		,	"mail_organizer" => "../../core/template/mail/organizer.html"
		,	"mail_administrator" => "../../core/template/mail/administrator.html"
		);

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
		
		$data_datetime = Array(
			"day" => $day_num
		,	"weekday_label" => \Enumeration\LabelDate::weekday[$weekday_num]
		,	"month_label" => \Enumeration\LabelDate::month[$month_num]
		,	"hour" => $hour
		,	"meridiem" => $meridiem
		);
		
		$data_picture = Array(
			"calendar" => "cid:" . $utl->GetUrlRoot() . \Enumeration\Path::app_user_image .
				"calendar.png"
		);
		
		$attendee = Array(
			"gender" => $data_attendee["gender"]
		,	"fullname" => "{$data_attendee["name"]} {$data_attendee["surname"]}"
		,	"email" => $data_attendee["email"]
		);
		
		$intern = Array(
			"gender" => $data_intern["gender"]
		,	"fullname" => "{$data_intern["name"]} {$data_intern["surname"]}"
		,	"email" => $data_intern["email"]
		);

		$organizer = Array(
			"gender" => $data_organizer["gender"]
		,	"fullname" => "{$data_organizer["name"]} {$data_organizer["surname"]}"
		,	"email" => $data_organizer["email"]
		);
		
		$administrator = Array();	
		foreach($data_administrator as $row) {
			array_push($administrator, [
				"gender" => $row["gender"]
			,	"fullname" => "{$row["name"]} {$row["surname"]}"
			,	"email" => $row["email"]
			]);
		}
		
		$time_start = $utl->GetUTC($sh_time_start);
		$time_end = $utl->GetUTC($sh_time_end);
		$summary = "Turno con {$intern["gender"]} {$intern["fullname"]}";
		$description = $utl->FormatICSDescription(
			$file_template["ics_attendee"]
		,	$data_attendee
		,	$data_organizer
		,	$data_datetime	
		);
		$location = $data_intern["location"];
		
		$message_attendee = $utl->FormatHTMLBody(
			$file_template["mail_attendee"]
		,	$data_attendee
		,	$data_organizer
		,	$data_datetime
		,	$data_picture
		);
		$message_organizer = $utl->FormatHTMLBody(
			$file_template["mail_organizer"]
		,	$data_attendee
		,	$data_organizer
		,	$data_datetime
		,	$data_picture
		);
		$message_administrator = $utl->FormatHTMLBody(
			$file_template["mail_administrator"]
		,	$data_attendee
		,	$data_organizer
		,	$data_datetime
		,	$data_picture
		,	$data_administrator
		);

		$image = Array(
			"calendar" => $utl->GetUrlRoot() . \Enumeration\Path::app_user_image . 
				"calendar.png"
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

		#user41.pc@gmail.com
		foreach($administrator as $row) {
			#$mail->to = "{$row["fullname"]} <{$row["email"]}>";
			$mail->to = "{$row["fullname"]} <abcconn@outlook.com>";
			$mail->from = "Equipo de Soporte Turnos.com <soporte.turnos@gmail.com>";
			$mail->subject = "{$attendee["gender"]} {$attendee["fullname"]} " .
				"ha solicitado un turno con {$intern["gender"]} {$intern["fullname"]}";
			$mail->message = $message_administrator;
			$mail->file_content = $ics_content;
			$mail->file_type = "text/calendar";
			$mail->file_name = "turno.ics";
			$mail->image_inline = $image; 

			$result_administrator = $mail->Send();
		}
		
		#$mail->to = "{$intern["fullname"]} <{$intern["email"]}>";
		$mail->to = "{$intern["fullname"]} <abcconn@outlook.com>";
		$mail->from = "Equipo de Soporte Turnos.com <soporte.turnos@gmail.com>";
		$mail->subject = "{$attendee["gender"]} {$attendee["fullname"]} " .
			"ha solicitado un turno con usted";
		$mail->message = $message_organizer;
		$mail->file_content = $ics_content;
		$mail->file_type = "text/calendar";
		$mail->file_name = "turno.ics";
		$mail->image_inline = $image;

		$result_organizer = $mail->Send();

		#$mail->to = "{$attendee["fullname"]} <{$attendee["email"]}>";
		$mail->to = "{$attendee["fullname"]} <abcconn@outlook.com>";
		$mail->from = "Equipo de Soporte Turnos.com <soporte.turnos@gmail.com>";
		$mail->subject = "Turno con {$intern["gender"]} {$intern["fullname"]} - " .
			"{$sh2_time_start}";
		$mail->message = $message_attendee;
		$mail->file_content = $ics_content;
		$mail->file_type = "text/calendar";
		$mail->file_name = "turno.ics";
		$mail->image_inline = $image;

		$result_attendee = $mail->Send();
		
		echo $utl->buildOutput([
			[
				($result_inserted) ? \Enumeration\Result::ok : \Enumeration\Result::not_ok
			,	($result_attendee) ? \Enumeration\Result::ok : \Enumeration\Result::not_ok
			,	($result_organizer) ? \Enumeration\Result::ok : \Enumeration\Result::not_ok
			,	($result_administrator) ? \Enumeration\Result::ok : \Enumeration\Result::not_ok
			]
		]);
	
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
$mail = null;
