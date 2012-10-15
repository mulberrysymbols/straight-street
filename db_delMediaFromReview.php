<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_mid = $_GET["mid"];
$input_rid = $_GET["rid"];

if (trim($input_rid) && trim($input_mid)) {



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = sprintf("DELETE FROM t_review_media WHERE rid='%s' AND mid='%s';",
					db_escape_string($input_rid), db_escape_string($input_mid));
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}
}
?>