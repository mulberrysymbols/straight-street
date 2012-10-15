<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_mid = $_GET["mid"];

if (trim($input_mid)) {



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "UPDATE t_media SET m.status_id = 4 WHERE id='".db_escape_string($input_mid)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}
}
//UPDATE contacts SET first = '$ud_first', last = '$ud_last', web = '$ud_web' WHERE id = '$ud_id'
?>

