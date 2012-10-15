<?
include('_common.php');
//need to return results for:
//	1 - successful activation code
//	0 - bad activation code
//	2 - useraccount already activated! (auth<>0)


$input_email = $_GET["email"];
$input_act = $_GET["act"];

if (trim($input_email) && trim($input_act)) {




	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

//echo "|2";


	$query = "SELECT * FROM t_user WHERE email='".dc_escape_string($input_email)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	if($r = mysql_fetch_array($result)) {
		//record exists
//echo "|3";
		//echo "||".$r["email"]."||".$r["auth"]."||";
		if ($r["auth"]<>"0") {
			//already activated - fail

			echo "2";

		} else {
			//ok email exists, and account not yet activated. check authcode
			if ($input_act===$r["authcode"]) {
				//ok activate because email and act code match
				//$query = "INSERT INTO t_user (authcode)
				//VALUES ('1') WHERE email='$input_email';";
				//$result = mysql_db_query("strstr", $query);

				//only activate once username and pass have been entered into the DB!

				echo "1";
			} else {
				//auth code doesnt match - fail

				echo "0";
			}

		}

		
	} else {
		//email doesnt exist - fail!

		echo "0";

	}
	
//echo "|4";

	//mysql_free_result($result); 
	db_freeResult($result);
}

//echo "|5";

?>