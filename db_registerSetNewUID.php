<?
include('_common.php');
$input_email = $_GET["email"];
$input_newUID = $_GET["uid"];

if (trim($input_email) && trim($input_newUID)) {


	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	//Check if record exists
	$query = "SELECT * FROM t_user WHERE email='".db_escape_string($input_email)."';";
	//$result = mysql_db_query("strstr", $query); 
	$result = db_runQuery($query);
  
	if($row = mysql_fetch_array($result)) {
		//record exists

		//check if username in use
		$query2 = "SELECT * FROM t_user WHERE username='".db_escape_string($input_newUID)."';";
		//$result2 = mysql_db_query("strstr", $query2);
		$result2 = db_runQuery($query2);
   
		if($row2 = mysql_fetch_array($result2)) {
			//record exists - disallow

			echo "0";

		} else { 
			//username not in use by anyone, so allow

			echo "1";

			//change uid in DB
			$query3 = sprinf("UPDATE t_user SET username='%s' WHERE email='%s';",
						db_escape_String($input_newUID),db_escape_String($input_email));
			//echo $query3;
			//$result3 = mysql_db_query("strstr", $query3);
			$result3 = db_runQuery($query3);

		}
		
	} else {
		//record doesnt exist

		echo "0";
	}
	
	//mysql_free_result($result); 
	db_freeResult($result);
}

?>