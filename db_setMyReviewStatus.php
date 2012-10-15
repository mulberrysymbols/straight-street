<?
include('_common.php');

$input_rid = $_GET["rid"];
$input_status = $_GET["status"];
$input_uid = $_GET["uid"];

echo "0";

if (mb_strlen(trim($input_rid))>0 && mb_strlen(trim($input_status))>0 && mb_strlen(trim($input_uid))>0) {



	echo "1";
	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	//if record exists, then update it. if not then add it
	$query = sprintf("SELECT * FROM t_review_dataset WHERE rid='%s' AND userid='%s';",
						db_escape_string($input_rid), db_escape_string($input_uid));

	//$result = mysql_db_query("strstr", $query);   
	$result = db_runQuery($query);

	if($row = mysql_fetch_array($result)) {
		//record exists
		echo "2";
		$query = sprintf("UPDATE t_review_dataset SET status='%s' WHERE rid='%s' AND userid='%s';",
				db_escape_string($input_status), db_escape_string($input_rid), db_escape_string($input_uid));
		//$result = mysql_db_query("strstr", $query);
		$result = db_runQuery($query);
		
	} else {
		echo "3";
		//record doesnt not exist
		$query = sprintf("INSERT INTO t_review_dataset VALUES ('','%s','%s','%s');",
				db_escape_string($input_rid), db_escape_string($input_uid), db_escape_string($input_status));
		//$result = mysql_db_query("strstr", $query);
		$result = db_runQuery($query);
	}
	
	echo "4";
	//mysql_free_result($result); 
	db_freeResult($result);
}

?>