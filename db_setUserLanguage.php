<?
include('_common.php');

$input_uid = db_escape_string($_GET["uid"]);
$input_langid = $_GET["langid"];


if (mb_strlen(trim($input_uid))>0 && mb_strlen(trim($input_langid))>0 ) {

	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

    $query = sprintf("UPDATE t_user SET language_id='%s' WHERE id='%s';",
            db_escape_string($input_langid), db_escape_string($input_uid));
    $result = db_runQuery($query);

    $query = "SELECT l.id, l.name, l.flag_id FROM t_user AS u INNER JOIN t_language AS l ON u.language_id = l.id WHERE u.id='$input_uid';";
	$result = db_runQuery($query);
    if($result)
    {
        $r = mysql_fetch_array($result);
        echo $r["name"].",".$r["flag_id"];
    }
//    db_freeResult($result);
}

?>