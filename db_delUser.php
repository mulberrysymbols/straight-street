<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_id = $_GET["id"];

//echo "1";

if (trim($input_id)) {

//echo "2";



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

	$query = "DELETE FROM t_user_agr_lic WHERE uid='".db_escape_string($input_id)."';";
	$result = db_runQuery($query);
    $query="DELETE rr FROM t_review_dataset rd INNER JOIN t_review_results rr  ON rr.rdsid = rd.rid WHERE rd.userid='".db_escape_string($input_id)."';";
	$result = db_runQuery($query);
    $query = "DELETE FROM t_review_dataset WHERE userid='".db_escape_string($input_id)."';";
	$result = db_runQuery($query);
	$query = "DELETE FROM t_user WHERE id='".db_escape_string($input_id)."';";
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}
}
?>