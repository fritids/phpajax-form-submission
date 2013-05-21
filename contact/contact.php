<?php
// put form in debug mode
define("DEBUG_MODE", true);

// form will be mailed to this address
// @note should probably move the following to a settings file
define("CLIENT_EMAIL","TODO@TO");

// remove the # from the following line if FROM email is different than client's email
# define("FROM_EMAIL","TODO@FROM");

// cc's rep in debug mode
define("REP_EMAIL","TODO@CCTO");

// cc's developer in debug mode
define("DEV_EMAIL", "TODO@CCDEV");

// used in email subject and from name
define("SITE_NAME", "TODO@SITENAME");

// subject of the email if not supplied on the page
define("DEFAULT_SUBJECT", "form submitted from your site");

#!    __________________
// DO NOT EDIT BELOW / /
//                  V V
include_once('mail.php');
include_once('template.php');

function is_ajax()
{
	if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] )
				&& strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest')
	{	return true; } else {	return false;	}
}

if($_POST){

		$to = array();
		// set up recipients
		if(defined('CLIENT_EMAIL'))
		{
			if(defined('DEBUG_MODE') && DEBUG_MODE === true)
			{
				// do nothing
			} else {
				if(strstr(CLIENT_EMAIL, ','))
				{
					$client_emails = explode(',', CLIENT_EMAIL);
					foreach($client_emails as $email)
					{
						array_push($to, $email);
					}
				} else {
					array_push($to,CLIENT_EMAIL);
				}
			}
		}

		if(defined('DEBUG_MODE') && DEBUG_MODE === true)
		{
			if( defined('REP_EMAIL') ){ array_push($to, REP_EMAIL); }
			if( defined('DEV_EMAIL') ){ array_push($to, DEV_EMAIL); }
		}

		// from
		$from = $_POST['name']."<".$_POST['email'].">";


# if sending confirmation email to client
# set up from email

		if(defined(CLIENT_EMAIL))
		{
			if(defined(FROM_EMAIL))
			{
				$from = SITE_NAME . "<".FROM_EMAIL.">";
			}
			else
			{
				$from = SITE_NAME . "<".CLIENT_EMAIL.">";
			}
		}
		else
		{
			$from = "No-Reply <nobody@postcardmania.com>";
		}

		// subject
		if(!empty($_POST['subject']))
		{
			$subject = $_POST['subject'];
		} else {
			if(defined('DEFAULT_SUBJECT')){
				$subject = DEFAULT_SUBJECT;
			} else { $subject = "Howdy!"; }
		}
		// append site name
		if(!empty($site_name))
		{
			$subject .= ' - '.$site_name;
		}

		// post variables
		unset($_POST['submit']);  // unwanted fields

		// run template
		$Emailer = new Emailer($from, $subject, $to);
		$Template = new EmailTemplate('../templates/email/default.php');
		$valid = $Emailer->validate($_POST,array('email' => 'email','name' => 'name','phone' => 'phone'));
		if($valid === true)
		{
			$Template->__set("postVars",$_POST);
			$Emailer->SetTemplate($Template); //Email runs the compile
			$response = $Emailer->send();
		} else {
			$response = $valid;
		}

} else {
	// form not submitted
	$response = array(
		"status" => "error",
		"message" => "invalid request."
	);
}

if(is_ajax()){
	if($response) {
		echo json_encode($response);
	}	else {
		$response = array(
			"status" => "error",
			"message" => "unknown."
		);
		echo json_encode($response);
	}
} else {

?>
  <h1><?=$response['status'];?></h1>
  <p><?=$response['message'];?></p>
  <?php
}