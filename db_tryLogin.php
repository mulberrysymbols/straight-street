<?
include('_common.php');

$input_uid = $_GET["uid"];
$input_pass = $_GET["pass"];



//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();


$query = "SELECT * FROM t_user WHERE username='".db_escape_string($input_uid)."';";
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

if ($result) {

	//record exists
	while ($r = mysql_fetch_array($result)) {
		$pass = $r["pass"];
	}

	//echo $pass;

	//got password
	if ($pass == $input_pass) {
		echo "1";
	} else {
		echo "0";
	}

} else {

	//no record
	echo "0";
}

//mysql_free_result($result); 
db_freeResult($result);


?>