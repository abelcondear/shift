<?php
namespace Common;

include_once "enumeration.php";

class Config {
	public $cnf_db;
	
	public function __construct($env) {
		if ($env == \Enumeration\Environment::development) {
			$this->cnf_db = [
				"servername" => "localhost"
			,	"schema" => "shift"	
			,	"username" => "root"
			,	"password" => "root"	
			,	"port" => "3306"
			,	"charset" => "utf8"
			];
		} elseif ($env == \Enumeration\Environment::preproduction) {
			$this->cnf_db = [
				"servername" => "localhost"
			,	"schema" => "hexa19_shift"	
			,	"username" => "hexa19"
			,	"password" => "hexAueTRDCsf032020"	
			,	"port" => "3306"
			,	"charset" => "utf8"			
			];		
		} elseif ($env == \Enumeration\Enviroment::production) {
			$this->cnf_db = [
				"servername" => ""
			,	"schema" => ""	
			,	"username" => ""
			,	"password" => ""	
			,	"port" => ""
			,	"charset" => ""			
			];			
		}	
	}
	
	public function GetConfigDatabase() {
		return $this->cnf_db;
	}
	
	public function __destruct() {
		$this->cnf_db = null;
	}	
}

class DataSubmitted {
	public function __construct($data_client) {
		foreach($data_client as $key => $value) {
			$this->{$key} = $value;
		}
	}
	
	public function __destruct() {
		//TODO
	}	
}

class Utils {
	public $data_client;
	public $data_server;
	
	public function __construct() {
		$arguments = func_get_args();
		$count_arguments = func_num_args();
		
		if ($count_arguments >= 1) {
			$this->data_client = new DataSubmitted($arguments[0]);
		}
		
		if ($count_arguments > 1) {
			$this->data_server = new DataSubmitted($arguments[1]);	        
		}
	}
	
	public function GetCurrentUrl() {
		return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https":"http") . 
			"://{$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}";
	}
	
	public function GetUrlRoot() {
		$current_url = self::GetCurrentUrl();
		
		return substr(
			$current_url
		,	0
		,	strpos(
				$current_url
			,	"/"
			,	strpos(
					$current_url
				,	"//"
				) + strlen("//")
			) + 1
		);
	}
	
	public function GetPageNameFrom() {
		$url_referer = $this->data_server->HTTP_REFERER;

		$referer = substr($url_referer, strrpos($url_referer, "/") + 1, strlen($url_referer) - strrpos($url_referer, "/")); 
		$page_name = substr($referer, 0, strrpos($referer, "."));		
		
		return $page_name;
	}

	public function GetUTC($datetime) {
		date_default_timezone_set("America/Argentina/Buenos_Aires");
		
		$tz_from = 'America/Argentina/Buenos_Aires';
		$tz_to = 'UTC';
		$format = 'Ymd\THis\Z';

		$dt = new \DateTime($datetime, new \DateTimeZone($tz_from));
		$dt->setTimeZone(new \DateTimeZone($tz_to));
		
		return $dt->format($format);
	}

	public function GetDateTimeSplit($datetime) {
		date_default_timezone_set("America/Argentina/Buenos_Aires");
		
		$tz_from = 'America/Argentina/Buenos_Aires';
		$dt = new \DateTime($datetime, new \DateTimeZone($tz_from));
		$format = "j-w-n-g-A-Y";

		return explode("-", $dt->format($format));
	}
	
	public function GetDateOfWeek($date) {
		date_default_timezone_set("America/Argentina/Buenos_Aires");
		
		$dayofweek = date('w', strtotime($date));
		$curdayofweek = date('w');
		
		$stop_date = date('Y-m-d H:i:s');

		if ($dayofweek != $curdayofweek) {
			while($dayofweek != $curdayofweek) {			
				$stop_date = strtotime($stop_date . ' +1 day');	
				$curdayofweek = date('w', $stop_date);
				$stop_date = date('Y-m-d H:i:s', $stop_date);
			}		
		}
			
		$newdate = date('Y-m-d', strtotime($stop_date));
		
		return $newdate;
	}

	public function FormatDate($data) {
		list($day, $month, $year) = explode("/", $data);
		return "$year-$month-$day";
	}
	
	public function TimeMinutesAdd($time, $minutes) {
		$date = date('H:i:s', strtotime("{$time}"));
		return date('H:i:s', strtotime($date . " +{$minutes} minutes"));		
	}
	
	public function buildOutput($rows) {
		$output = "";
		
		foreach ($rows as $row) {
			foreach($row as $value) {
				$output .= "\"" . $value . "\",";			
			}
			$output = substr($output, 0, -1);
			$output .= "\r\n";
		}
		
		return $output;
	}

	public function FormatHTMLBody(
		$filename
	,	$data_attendee
	,	$data_intern
	,	$data_datetime
	,	$data_picture
	,	$data_organizer = Array()
	) 
	{
		$body = file_get_contents($filename);
		
		$body = str_replace(
			"{{content-id}}"
		,	$data_picture["calendar"]
		,	$body
		);
		$body = str_replace(
			"{{attendee-gender}}"
		,	$data_attendee["gender"]
		,	$body
		);
		$body = str_replace(
			"{{attendee-name}}"
		,	"{$data_attendee["name"]} {$data_attendee["surname"]}"
		,	$body
		);
		$body = str_replace(
			"{{intern-gender}}"
		,	$data_intern["gender"]
		,	$body
		);
		$body = str_replace(
			"{{intern-name}}"
		,	"{$data_intern["name"]} {$data_intern["surname"]}"
		,	$body
		);
		$body = str_replace(
			"{{weekday-label}}"
		,	$data_datetime["weekday_label"]
		,	$body
		);
		$body = str_replace(
			"{{day}}"
		,	$data_datetime["day"]
		,	$body
		);
		$body = str_replace(
			"{{month-label}}"
		,	$data_datetime["month_label"]
		,	$body
		);
		$body = str_replace(
			"{{hour}}"
		,	$data_datetime["hour"]
		,	$body
		);
		$body = str_replace(
			"{{meridiem}}"
		,	$data_datetime["meridiem"]
		,	$body
		);
		$body = str_replace(
			"{{location}}"
		,	$data_intern["location"]
		,	$body
		);	

		if (sizeof($data_organizer) > 0) {
			$body = str_replace(
				"{{administrator-gender}}"
			,	$data_organizer["gender"]
			,	$body
			);
			$body = str_replace(
				"{{administrator-name}}"
			,	"{$data_organizer["name"]} {$data_organizer["surname"]}" 
			,	$body
			);		
		}
		
		return $body;
	}

	public function FormatICSDescription(
		$filename
	,	$data_attendee
	,	$data_organizer
	,	$data_datetime
	) 
	{
		$description = file_get_contents($filename);

		$description = str_replace(
			"{{attendee-gender}}"
		,	$data_attendee["gender"]
		,	$description
		);
		$description = str_replace(
			"{{attendee-name}}"
		,	"{$data_attendee["name"]} {$data_attendee["surname"]}"
		,	$description
		);
		$description = str_replace(
			"{{organizer-gender}}"
		,	$data_organizer["gender"]
		,	$description
		);
		$description = str_replace(
			"{{organizer-name}}"
		,	"{$data_organizer["name"]} {$data_organizer["surname"]}"
		,	$description
		);
		$description = str_replace(
			"{{weekday-label}}"
		,	$data_datetime["weekday_label"]
		,	$description
		);
		$description = str_replace(
			"{{day}}"
		,	$data_datetime["day"]
		,	$description
		);
		$description = str_replace(
			"{{month-label}}"
		,	$data_datetime["month_label"]
		,	$description
		);
		$description = str_replace(
			"{{hour}}"
		,	$data_datetime["hour"]
		,	$description
		);
		$description = str_replace(
			"{{meridiem}}"
		,	$data_datetime["meridiem"]
		,	$description
		);
		$description = str_replace(
			"{{location}}"
		,	$data_organizer["location"]
		,	$description
		);	
		$description = str_replace(
			"\r\n"
		,	"\\n"
		,	$description
		);	
		
		return $description;
	}
	
	public function CreateICS(
		$time_start
	,	$time_end
	,	$summary
	,	$description
	,	$location
	,	$attendee
	,	$organizer
	) 
	{
		$uid = strtoupper(
			md5(uniqid(mt_rand(), true)) . 
			md5(uniqid(mt_rand(), true)) . 
			md5(uniqid(mt_rand(), true))
		);
		
		$body = "BEGIN:VCALENDAR\r\n" . 
		"PRODID:-//Events Calendar//iCal4j 1.0//EN\r\n" . 
		"CALSCALE:GREGORIAN\r\n" . 
		"VERSION:2.0\r\n" . 
		"BEGIN:VEVENT\r\n" . 
		"DTSTAMP:{$time_start}\r\n" . 
		"DTSTART:{$time_start}\r\n" . 
		"DTEND:{$time_end}\r\n" . 
		"SUMMARY:{$summary}\r\n" . 
		"DESCRIPTION:{$description}\r\n" . 
		"LOCATION:{$location}\r\n" . 
		"TZID:America/Argentina/Buenos_Aires\r\n" . 
		"UID:{$uid}\r\n" . 
		"ATTENDEE;ROLE=REQ-PARTICIPANT;CN={$attendee[0]["fullname"]}:" . 
			"mailto:{$attendee[0]["email"]}\r\n" . 
		"ATTENDEE;ROLE=REQ-PARTICIPANT;CN={$attendee[1]["fullname"]}:" . 
			"mailto:{$attendee[1]["email"]}\r\n" . 
		"ORGANIZER;ROLE=CHAIR;CN={$organizer["fullname"]}:mailto:{$organizer["email"]}\r\n" . 
		"END:VEVENT\r\n" . 
		"END:VCALENDAR";

		return $body;
	}
		
	public function __destruct() {
		//TODO
	}
}

