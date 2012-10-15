<?
include('_common.php');

$input_rdsid = $_GET["rdsid"];
$input_rmid = $_GET["rmid"];
$input_accept = $_GET["accept"];
$input_comments = $_GET["c"];

//limit comments to 200 chars
$input_comments = mb_substr($input_comments,0,199);


//(input is ACCEPT, but DB is a DECLINE - so negation of the var needed here)




if (mb_strlen(trim($input_rdsid))>0 && mb_strlen(trim($input_rmid))>0 && mb_strlen(trim($input_accept))>0 && mb_strlen(trim($input_comments))>0) {

	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	//If records exist, then just update rather than add
	$query = sprintf("SELECT * FROM t_review_results WHERE rdsid='%s' AND rmid='%s';",
				db_escape_string($input_rdsid), db_escape_string($input_rmid));
	//$result = mysql_db_query("strstr", $query); 
	$result = db_runQuery($query);  

	if($row = mysql_fetch_array($result)) {
		//record exists - update


		$query = sprintf("UPDATE t_review_results SET decline='".!$input_accept."', comments='%s' WHERE rdsid='%s' AND rmid='%s';",
					db_escape_string($input_comments), db_escape_string($input_rdsid), db_escape_string($input_rmid));

		//$result = mysql_db_query("strstr", $query);
		$result = db_runQuery($query);
		
	} else {
		//add only if record doesnt already exist

		$query = sprintf("INSERT INTO t_review_results VALUES ('','%s','%s','".!$input_accept."','%s');",
					db_escape_string($input_rdsid), db_escape_string($input_rmid), db_escape_string($input_comments));
		//echo "||$query||";
		//$result = mysql_db_query("strstr", $query);
		$result = db_runQuery($query);
	}
	
	//mysql_free_result($result); 
	db_freeResult($result);
}

?>