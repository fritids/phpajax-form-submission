<?php
class Emailer
{
		protected $headers;
    protected $recipients = array();
		protected $subject;
    public $EmailContents;
    public $EmailTemplate;

    public function __construct($from = NULL, $subject = NULL, $to = NULL)
    {
				if(!is_null($from))
				{
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
					$headers .= "From: " . $from . "\r\n";
					$this->headers = $headers;
				}
				
				if(!is_null($subject))
				{
					$this->subject = $subject;
				}

        if(!is_null($to))
        {
            if(is_array($to)){
                foreach($to as $_to){ 
									$this->recipients[$_to] = $_to; 
								}
            } else {
                $this->recipients = $to; //1 Recip
            }
        }
    }

    public function SetTemplate(EmailTemplate $EmailTemplate)
    {
        $this->EmailTemplate = $EmailTemplate;
				$this->EmailContents = $EmailTemplate->compile();
    }
		
		public function send()
		{	
			if(isset($this->EmailContents,$this->headers,$this->subject,$this->recipients)){
				
				$to = "";
				foreach($this->recipients as $email)
				{
					$to .= $email . ",";
				}
				
				if(strrpos($to,",")+1 == strlen($to)){
					// remove trailing comma
					$to = substr($to,0,strrpos($to,","));
				
				}
				$sent = mail($to,$this->subject,$this->EmailContents,$this->headers);
				if($sent){
					return array(
						"status" => "success",
						"to" => $to,
						"subject" => $this->subject,
						"contents" => $this->EmailContents,//$this->EmailContents,
						"headers" => $this->headers,
						"message" => "Your message was sent!"
					);
				} else { 
					return array(
						"status" => "error",
						"to" => $to,
						"subject" => $this->subject,
						"contents" => "test contents",//$this->EmailContents,
						"headers" => $this->headers,
						"message" => "there was an error sending your message"
					);
				}	
			} else {
				return array(
					"status" => "error",
					"message" => "missing required information"
				);
			}

		}

		private function check_name($name){
			if( preg_match("/^[-_a-zA-Z' ]*$/", $firstname) ) {
				return true;
			} else { return false; }
			
			return true;
		}
	
		private function check_phone($phone){
			
			if( preg_match("/^([1]-)?[0-9]{3}[-\.]?[0-9]{3}[-\.]?[0-9]{4}$/i", $phone) ) {
				return true;
			} else { return false; }
			
			return true;
		}
	
		private function check_email($email) {
			// First, we check that there's one @ symbol, 
			// and that the lengths are right.
			if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
				// Email invalid because wrong number of characters 
				// in one section or wrong number of @ symbols.
				return false;
			}
			// Split it into sections to make life easier
			$email_array = explode("@", $email);
			$local_array = explode(".", $email_array[0]);
			for ($i = 0; $i < sizeof($local_array); $i++) {
				if
		(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",$local_array[$i])) {
					return false;
				}
			}
			// Check if domain is IP. If not, 
			// it should be valid domain name
			if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
				$domain_array = explode(".", $email_array[1]);
				if (sizeof($domain_array) < 2) {
						return false; // Not enough parts to domain
				}
				for ($i = 0; $i < sizeof($domain_array); $i++) {
					if
		(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$",
		$domain_array[$i])) {
						return false;
					}
				}
			}
			return true;
		}
		
		private function is_valid($data,$type)
		{
//			echo "is valid? data: ".$data." type: ".$type. "<br>";
			if( trim($data) == "" )
			{
				return false;
			} else {
				switch($type)
				{
					case "email":
						return $this->check_email($data);
						break;
					case "name":
						return $this->check_name($data);
						break;
					case "phone":
						return $this->check_phone($data);
				}
			}
			return false;
		}
		
		public function validate($data,$fields)
		{
//			print_r($data);
			$errarr = array();
			foreach($fields as $key => $type)
			{
//				echo "key: ".$key." type: ".$type."<br>";
				if(array_key_exists($key,$data))
				{
//					echo "array key ".$key." exists in data<br>";
					if(!$this->is_valid($data[$key],$type))
					{
						array_push($errarr,"'".$data[$key]."' is not a valid ".$type.". <br>");
					}
				}
			}

			if(count($errarr)>1)
			{
				$message = "";
				foreach($errarr as $error)
				{
					$message .= $error;
				}
				$response = array(
					"status" => "error",
					"message" => $message
				);
				return $response;
			} else {
				return true;
			}

		}
}
