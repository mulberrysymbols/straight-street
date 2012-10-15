<?
include('_common.php');
if (isUserAtLeastAdmin()) {

//echo "1";

$input_k = db_escape_string($_GET["keyword"]);
$input_l = db_escape_string($_GET["lid"]);
$input_nonSpaceChar = db_escape_string($_GET["nsc"]);
$symbol = str_replace(" ","$input_nonSpaceChar",$input_k);

if (trim($input_k) && trim($input_l) && trim($input_nonSpaceChar)) {

	//DB STUFF

	//echo "2";

	db_connect();

	//get media id
	$query = "SELECT id FROM t_media WHERE name='$symbol';";
	$result = db_runQuery($query);
	if ($result) {
		if ($r = mysql_fetch_array($result)) {
			$symbol_id = $r["id"];
		}
	}

	//Add rec to T_MEDIA
	$query = "UPDATE t_media SET licid = $input_l, status_id = 2 WHERE id = $symbol_id";
    $result = db_runQuery($query);

	//Add recs to T_MEDIA PATHS
	$query = "INSERT INTO t_media_path VALUES ('','$symbol_id','0','$symbol.wmf','$symbol');";
	$result = db_runQuery($query);


	$query = "INSERT INTO t_media_path VALUES ('','$symbol_id','1','t-$symbol.gif','');";
	$result = db_runQuery($query);


	$query = "INSERT INTO t_media_path VALUES ('','$symbol_id','3','m-$symbol.gif','');";
	$result = db_runQuery($query);

	
	db_freeResult($result);	


	//MOVE FILES???

	$newLoc = "media";
	$oldLoc = "media/unsorted";

	//echo "|$oldLoc/$input_k.wmf | $newLoc/$input_k.wmf|";


	//rename oldimg with newimg
	// [oldimg] - any _'s are converted to spaces, to check if any have been used in the filename
	// [newimg] - any spaces are converted to _'s, so there are no spaces in the DB


	rename("$oldLoc/".$input_k.".wmf","$symbolsWMF/".$symbol.".wmf");
	rename("$oldLoc/t-".$input_k.".gif","$symbolsThumb/t-".$symbol.".gif");
	rename("$oldLoc/m-".$input_k.".gif","$symbolsPreview/m-".$symbol.".gif");

	//echo "<b>Import completed</b>";
	echo "1";

}
}
?>