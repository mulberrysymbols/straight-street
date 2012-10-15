<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_mid = $_GET["mid"];

if (trim($input_mid)) {

	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

	$query = "UPDATE t_media SET rated=1 WHERE id='".db_escape_string($input_mid)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}
}
?>

