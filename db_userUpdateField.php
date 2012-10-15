<?
include('_common.php');

$input_UID = $_GET["uid"];
$input_field = $_GET["field"];
$input_val = $_GET["val"];

if (trim($input_UID) && trim($input_field) && trim($input_val)) {



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

	if ($input_field == 'pass')
		$input_val = md5($input_val);
	else
		$input_val = db_escape_string($input_val);
		
	//Set field val - if doesnt exist will be ignored anyway
	$query = sprintf("UPDATE t_user SET %s='%s WHERE username='%s';",
					db_escape_string($input_field), db_escape_string($input_val), db_escape_string($input_UID));
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}

?>