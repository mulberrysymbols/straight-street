<?
include('_common.php');

$input_newUID = $_GET["uid"];
$input_newPass = $_GET["pass"];

if (trim($input_newPass) && trim($input_newUID)) {



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

	//Check if record exists
	$query = "SELECT * FROM t_user WHERE username='".db_escape_string($input_newUID)."';";
	//$result = mysql_db_query("strstr", $query);   
	$result = db_runQuery($query);
	

	if($row = mysql_fetch_array($result)) {
		//record exists

		//Set password + set activated!
		$query2 = "UPDATE t_user SET pass='".md5($input_newPass)."', auth='1' WHERE username='".db_escape_string($input_newUID)."';";
		//$result2 = mysql_db_query("strstr", $query2);
		$result2 = db_runQuery($query2);

		echo "1";

	} else { 
		//username not in use

		echo "0";
	}
	
	//mysql_free_result($result); 
	db_freeResult($result);
}
?>