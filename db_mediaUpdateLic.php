<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_m = $_GET["m"];
$input_l = $_GET["l"];

if (mb_strlen(trim($input_m))>0 && mb_strlen(trim($input_l))>0) {



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	//if ($input_l=='0') { $input_l=''; }
	
	$query = sprintf("UPDATE t_media SET licid='%s' WHERE id='%s';",
					db_escape_string($input_l), db_escape_string($input_m));
	
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}
}
?>

