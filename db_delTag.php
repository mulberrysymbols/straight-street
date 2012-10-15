<?
include('_common.php');

if (isUserAtLeastReviewer())
{

	$input_imgid = $_GET["i"];
	$input_imgtag = $_GET["t"];

	if (trim($input_imgid) && trim($input_imgtag))
	{
		db_connect();
		$query = sprintf("
		DELETE mt
		FROM t_media_tags mt
		INNER JOIN t_tag t
		 ON mt.tid = t.id 
		WHERE mt.mid = %s AND t.tag ='%s'",
		 db_escape_string($input_imgid ), db_escape_string($input_imgtag));

		$result = db_runQuery($query);
		// note we leave unused tags in t_tag
		
		$query = sprintf("SELECT GROUP_CONCAT(t.tag SEPARATOR ',') as tags
		FROM t_media_tags mt 
		INNER JOIN t_tag t
		ON t.id = mt.tid
		WHERE mt.mid = %s
		GROUP BY mt.mid;",
		 db_escape_string($input_imgid ));
		$result = db_runQuery($query);
		$r=mysql_fetch_assoc($result);
		echo $r['tags'];
	}
}
?>