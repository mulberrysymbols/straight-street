<?
include('_common.php');

$input_n = $_GET["name"];
$input_e = $_GET["email"];
$input_c = $_GET["comments"];
$input_s = $_GET["subject"];
$input_a = $_GET["allow"];


if (trim($input_n) && trim($input_e) && trim($input_c) && mb_strlen(trim($input_a))>0) {

	$recipient = "support@straight-street.com,steve@fullmeasure.co.uk";

	$sender = $input_e;
	$name = $input_n;
	//convert special chars in comments back to normal
	$comments = $input_c;
	if ($input_a=="1") {
		$allowstr = "User has selected to ALLOW any moderated use of their comments or feedback";
	} else {
		$allowstr = "User has selected to NOT ALLOW any moderated use of their comments or feedback";
	}

	$emailbody = "";
	$emailbody .= "Name : ".htmlenc($name)." \n"; // shoul really to encode as is text but just in case it gets displayed as HTML
	$emailbody .= "Email : ".htmlenc($sender)." \n";
	$emailbody .= "--- \n";
	$emailbody .= "".htmlenc($comments)." \n";
	$emailbody .= "--- \n";
	$emailbody .= "".htmlenc($allowstr)." \n";
 	$emailbody .= "--- \n";
	$emailbody .= "Remote Host : ".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";
	$emailbody .= "Remote Addr : ".$_SERVER['REMOTE_ADDR']." \n";
	$emailbody .= "User Agent : ".$_SERVER['HTTP_USER_AGENT']." \n";

	//exit( "$recipient\n$emailbody\n\nFrom: $name \nSender: $sender");	

	$subject = "Straight-Street contact: ".htmlenc($input_s);
	$headers = "MIME-Version: 1.0 \n"
		   	."Content-type: text/plain; charset=UTF-8 \n"
		   	."From: $name <$sender> \n"
			.'X-Mailer: PHP/' . phpversion() . " \n";
            
    //echo "mb_send_mail($recipient, $subject, $emailbody, $headers)";
	if (mb_send_mail($recipient, $subject, $emailbody, $headers))
    {
		echo("1");
    }
	else
    {
		echo("0");
    }
}


?>

