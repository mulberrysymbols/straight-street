<?
include('_common.php');

$input_a = $_GET["a"];
$input_l = $_GET["l"];
$input_u = $_GET["u"];

//echo "1";

if (mb_strlen(trim($input_a))>0 && mb_strlen(trim($input_l))>0 && mb_strlen(trim($input_u))>0) {

//echo "2";



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	if ($input_a=="1") {
		//add record 
		$query = sprintf("INSERT INTO t_user_agr_lic VALUES ('','%s','%s');",
					db_escape_string($input_u), db_escape_string($input_l));
		//$result = mysql_db_query("strstr", $query);
		$result = db_runQuery($query);
	} else {
		//remove record
		$query = sprintf("DELETE FROM t_user_agr_lic WHERE lid='$input_l' AND uid='$input_u';",
					db_escape_string($input_l), db_escape_string($$input_u));
		//$result = mysql_db_query("strstr", $query);
		$result = db_runQuery($query);
	}

	//mysql_free_result($result); 
	db_freeResult($result);
}

?>