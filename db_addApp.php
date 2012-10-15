<?
include('_common.php');
if (isUserAtLeastAdmin()) {



function ChangeSpecialCharsBack($s) {

	$myStr = $s;
	$myStr = str_replace("***nl***","\n",$myStr);
	$myStr = str_replace("***am***","&",$myStr);
	$myStr = str_replace("***eq***","=",$myStr);
	$myStr = str_replace("***pl***","+",$myStr);
	$myStr = str_replace("***qu***","?",$myStr);

	return $myStr;
}

$input_id = $_GET["id"];
$input_n = $_GET["n"];
$input_b = $_GET["b"];
$input_i = $_GET["i"];

$input_f = $_GET["f"];
$input_s = $_GET["s"];
$input_o = $_GET["o"];

if (trim($input_n) && trim($input_b) && trim($input_i)) {

	$input_i = ChangeSpecialCharsBack($input_i);
	$input_f = ChangeSpecialCharsBack($input_f);
	$input_s = ChangeSpecialCharsBack($input_s);
	$input_o = ChangeSpecialCharsBack($input_o);




	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "SELECT * FROM t_app WHERE id='".db_escape_string($input_id)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//if record exists - UPDATE fields - else INSERT fields
	if ($result) {
		if ($r = mysql_fetch_array($result)) {
			//UPDATE
			$query2 = sprintf("UPDATE t_app SET name='%s',brief='%s',info='%s',features='%s',sysreq='%s',other='%s' WHERE id='%s';",
						db_escape_string($input_n), db_escape_string($input_b), db_escape_string($input_i), db_escape_string($input_f),
						db_escape_string($input_s), db_escape_string($input_o), db_escape_string($input_id));

			//$result2 = mysql_db_query("strstr", $query2);
			$result2 = db_runQuery($query2);
			//mysql_free_result($result2);
			db_freeResult($result2);

		} else {
			//INSERT
			$query2 = sprintf("INSERT INTO t_app VALUES ('','%s','%s','%s','1','0','%s','%s','%s');",
						db_escape_string($input_n), db_escape_string($input_b), db_escape_string($input_i), db_escape_string($input_f),
						db_escape_string($input_s), db_escape_string($input_o));
			//$result2 = mysql_db_query("strstr", $query2);
			$result2 = db_runQuery($query2);
			//mysql_free_result($result2);
			db_freeResult($result);
		}
	}

	//mysql_free_result($result); 
	db_freeResult($result);

}
}
?>