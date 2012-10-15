<?
include('_common.php');
if (isUserAtLeastAdmin()) {


$input_uid = $_GET["uid"];
$input_aid = $_GET["authority"];
$input_set = $_GET["set"];

if (mb_strlen(trim($input_uid))>0 && mb_strlen(trim($input_aid))>0 && mb_strlen(trim($input_set))>0)
{

	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

    if ($input_set == 'true')
    {
		$query = sprintf("
		INSERT into t_user_authority (user_id, authority_id)
		SELECT %s, '%s'
		FROM DUAL 
		WHERE NOT EXISTS (SELECT 'x' FROM t_user_authority WHERE user_id = %s AND authority_id = '%s');",
			db_escape_string($input_uid), db_escape_string($input_aid), db_escape_string($input_uid), db_escape_string($input_aid));
    }
    else
    {
        $query = sprintf("
        DELETE FROM t_user_authority WHERE user_id = %s AND authority_id = '%s' ;",
			db_escape_string($input_uid), db_escape_string($input_aid));
    }

    $result = db_runQuery($query);    
}
}
?>