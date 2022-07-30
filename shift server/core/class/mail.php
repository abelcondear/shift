<?php
namespace Mailing;

include_once "enumeration.php";

class Mail {
	public $to;
	public $subject;
	public $from;
	public $message;
	public $image_inline;
	public $file_content;
	public $file_size;
	public $file_type;
	
	private $headers;
	private $body;
	
	public function __construct() {
		//TODO
	}	

	public function Send() {
		$boundary = md5(time());
		$cid = sha1(date('r', time()));
		
		//headers
		$this->headers = "MIME-Version: 1.0\r\n";
		$this->headers .= "From: {$this->from}\r\n";
		$this->headers .= "Reply-To: {$this->from}\r\n";
		$this->headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
		$this->headers .= "Content-Transfer-Encoding: 7bit\r\n"; 
		$this->headers .= "This is a MIME encoded message.\r\n"; 

		//html message
		$this->body = "--{$boundary}\r\n"; 
		$this->body .= "Content-type: text/html; charset=UTF-8\r\n"; 
		$this->body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$this->body .= "{$this->message}\r\n";  

		//image inline 
		$this->body .= "--{$boundary}\r\n";
		$this->body .= "Content-Type: {$this->image_inline["calendar"]["file_type"]}; " .
			"name=\"{$this->image_inline["calendar"]["file_name"]}\"\r\n";
		$this->body .= "Content-Transfer-Encoding: base64\r\n";		
		$this->body .= "Content-ID: <{$this->image_inline["calendar"]["file_name"]}>\r\n\r\n";		
		$this->body .= chunk_split(base64_encode(file_get_contents($this->image_inline["calendar"]["file_name"])));
		
		if ($this->file_content) {
			//file attachment 
			$this->body .= "--{$boundary}\r\n"; 
			$this->body .= "Content-Type: {$this->file_type}; name={$this->file_name}\r\n"; 
			$this->body .= "Content-Transfer-Encoding: base64\r\n"; 
			$this->body .= "Content-Disposition: attachment\r\n\r\n"; 
			$this->body .= chunk_split(base64_encode($this->file_content));
		}
		
		$this->body .= "--{$boundary}--";
		
		$result = mail($this->to, $this->subject, $this->body, $this->headers); 
	  
		if($result) { 
		   return \Enumeration\Result::success; 
		} else { 
			return \Enumeration\Result::failed;
		}     			
	}
		
	public function __destruct() {
		//TODO
	}	
}