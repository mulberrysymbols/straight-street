<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_m = $_GET["m"];
$input_s = $_GET["s"];

if (mb_strlen(trim($input_m))>0 && mb_strlen(trim($input_s))>0) {



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	if ($input_s=='0') { $input_s=''; }
	
	$query = sprintf("UPDATE t_media SET sponid='%s' WHERE id='%s';",
						db_escape_string($input_s), db_escape_string($input_m));
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//mysql_free_result($result); 
	db_freeResult($result);
}
}
?>

