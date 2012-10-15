<?
include('_common.php');
$input_email = $_GET["email"];

if (trim($input_email)) {


	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	//Check if record exists
	$query = "SELECT * FROM t_user WHERE email='".db_escape_string($input_email)."';";
	
	//$result = mysql_db_query("strstr", $query);   
	$result = db_runQuery($query);

	if($row = mysql_fetch_array($result)) {
		//record exists

		if ($row["username"] == $row["email"]) 
		{
			echo "1";
		} else {
			echo "0";
		}
	}
	
	//mysql_free_result($result); 
	db_freeResult($result);
}

?>