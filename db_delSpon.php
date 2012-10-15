<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_id = $_GET["id"];

//echo "1";

if (trim($input_id)) {

//echo "2";



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "DELETE FROM t_sponsor WHERE id='".db_escape_string($input_id)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}
}
?>