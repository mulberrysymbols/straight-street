<?
include('_common.php');
if (isUserAtLeastAdmin()) {

$input_n = $_GET["n"];

if (trim($input_n)) {

	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();
	$query = "INSERT INTO t_review VALUES ('','".db_escape_string($input_n)."','0');";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);

}
}
?>