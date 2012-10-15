<?
include('_common.php');

if (isUserAtLeastReviewer())
{

	$input_imgid = $_GET["i"];
	$input_imgtag = $_GET["t"];

	if (trim($input_imgid) && trim($input_imgtag))
	{
		db_connect();
		$query = "
		INSERT into t_tag (tag)
		SELECT '".db_escape_string($input_imgtag)."' 
		FROM DUAL 
		WHERE NOT EXISTS (SELECT 'x' FROM t_tag WHERE tag = '".db_escape_string($input_imgtag)."');";
		$result = db_runQuery($query);

		$query = "SELECT id AS tid FROM t_tag WHERE tag = '".db_escape_string($input_imgtag)."';";
		$result = db_runQuery($query);
		$r=mysql_fetch_assoc($result);
		$tid = $r['tid'];

		$query = sprintf("
		INSERT into t_media_tags (tid, mid)
		SELECT %s, %s
		FROM DUAL 
		WHERE NOT EXISTS (SELECT 'x' FROM t_media_tags WHERE tid = %s AND mid = %s);",
			db_escape_string($tid), db_escape_string($input_imgid), db_escape_string($tid), db_escape_string($input_imgid));
		
		$result = db_runQuery($query);
		
		// return new tags list
		$query = "
		SELECT GROUP_CONCAT(t.tag SEPARATOR ',') as tags
		FROM t_media_tags mt 
		INNER JOIN t_tag t
		  ON t.id = mt.tid
		WHERE mt.mid = ".db_escape_string($input_imgid)."
		GROUP BY mt.mid;";
		$result = db_runQuery($query);
		$r=mysql_fetch_assoc($result);
		echo $r['tags'];
	}
}
?>