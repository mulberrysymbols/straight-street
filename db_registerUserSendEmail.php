<?
include('_common.php');
//=======================================================================
function generate_code($chars){
	for ($i=0;$i<=($chars-1);$i++) {
		$r0 = rand(0,1); $r1 = rand(0,2);
		if($r0==0){$r .= chr(rand(ord('A'),ord('Z')));}
		elseif($r0==1){ $r .= rand(0,9); }

		if($r1==0){ $r = mb_strtolower($r); }
	}
	return $r;
}
//=======================================================================

$input_email = $_GET["email"];

if (trim($input_email)) {

	$donewquery = false;

	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	//Check if email already exists
	$query = "SELECT * FROM t_user WHERE email='".db_escape_string($input_email)."';";
	//$result = mysql_db_query("strstr", $query); 
	$result = db_runQuery($query);
  
	if($row = mysql_fetch_array($result)) {
		//record exists

		if ($row["auth"]<>"0") {
			//already activated - fail

			echo "2";

		} else {
			//not activated - no resend, but allow continue (may be tryign to activate)

			echo "0";

		}
		
	} else {
		//record doesnt exist

		//---- Gen authcode
		$auth_code = generate_code(15);  // authcode will be 15 characters long.
		//$DateReg = date("y/m/d : H:i:s", Time());
		$DateReg = date("Y-m-d", Time());

	//do query at end of function instead - bug if here? doesnt work on pw4!!!

		//---- save in DB
		//---- (uid is db pk in t_user, so use email ad as uid meanwhile before user picks it)
		db_connect();
		//$query2 = "INSERT INTO t_user (authcode,username,pass,email,auth,datereg) VALUES ('$auth_code','$input_email','.','$input_email','0','$DateReg'); ";
		$query2 = sprintf("INSERT INTO t_user VALUES ('','%s','%s','.','$DateReg','0','%s','','','',''); ",
							db_escape_string($auth_code), db_escape_string($input_email), db_escape_string($input_u), db_escape_string($$input_email));
		//$result = mysql_db_query("strstr", $query2);
		$result2 = db_runQuery($query2);
		//db_freeResult($result2);

		//echo "|$query2|";
		//$donewquery = true;

		//---- send email
		$to = "$input_email, fred@fullmeasure.co.uk";
		$subject = "Website Activation Code";
		$body = "";
		$body .= "Dear User,\n\n";
		$body .= "Your email address has been used to activate an Account with something.com.\n";
		$body .= "If you did not cause this email to be sent, please ignore this email.\n\n";
		$body .= "Email Address : $to\n";
		$body .= "Activation Code : $auth_code\n\n";
		$body .= "Alternatively, you can click on the following link to activate your account :\n";
		$body .= "http://something.com/register.php?email=$to&code=$auth_code \n\n";
		$body .= "Many thanks,\n";
		$body .= "- Something";

		if (mb_send_mail($to, $subject, $body, "From: support@something-street.com"))
        {
			echo("1");
		} else {
			echo("9");
		}
	}
	
	//mysql_free_result($result); 
	db_freeResult($result);




	//bug before, so try run query after above one ends
	//if ($donewquery) {
	//	db_connect();
	//	$query = "INSERT INTO t_user (authcode,username,pass,email,auth,datereg) VALUES ('$auth_code','$input_email','.','$input_email','0','$DateReg'); ";
	//	$result = db_runQuery($query);
	//	db_freeResult($result);
	//}

}

?>