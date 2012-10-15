<?
include('../_common.php');
db_connect();
$sSearch = $_POST["t"];


$query = " 

SELECT 
	m.id,
	m.name,
	mp.filename
FROM
	t_media m
	INNER JOIN t_media_path mp
		ON m.id=mp.mid
WHERE
	m.name like '%" . $sSearch . "%' 
	AND mp.type=1

ORDER BY
	m.name ASC

";

//echo "|||" . $query . "|||";


$result = db_runQuery($query);
if ($result) {

	//record doesn't exist
	if (mysql_num_rows($result) == 0 ) {
        	echo "NONE FOUND";
	} else { 
		//record exists
		while ($r = mysql_fetch_array($result)) {
			echo "<a href=\"preview.php?pid=" . $r["id"] . "\" data-rel=\"dialog\" onclick=\"#\"><img src='/media/symbols/EN/thumb/" . $r["filename"] . "' /></a>";

		}
	}

} else {
	echo "NO RECORDS";
}

db_freeResult($result); 
?>
