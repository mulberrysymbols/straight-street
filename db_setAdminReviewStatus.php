<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_rid = $_GET["rid"];
$input_status = $_GET["status"];

if (mb_strlen(trim($input_rid))>0 && mb_strlen(trim($input_status))>0) {




	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	//if record exists, then update it.
	$query = "SELECT * FROM t_review WHERE id='".db_escape_string($input_rid)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);   

	if($row = mysql_fetch_array($result)) {
		//record exists
		$query = sprintf("UPDATE t_review SET status='%s' WHERE id='%s';",
				db_escape_string($input_status), db_escape_string($input_rid));
		//$result = mysql_db_query("strstr", $query);
		$result = db_runQuery($query);
	} else {
		//record doesnt not exist
		//do nothing
	}
	
	//mysql_free_result($result); 
	db_freeResult($result);
}
}
?>