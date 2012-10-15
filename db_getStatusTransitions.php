<?
include('_common.php');

//first check to see if a tag search is needed. if not jsut list all as normal

$dbh = DBCXn::get();

$status = (isset($_GET['status'])) ? $_GET['status'] : null;
if (!$status)
{
    echo '';
    exit();
}

$strQuery= <<<EOT
    SELECT id, sto AS status FROM (
    SELECT status AS sfrom, 'Dev' AS sto, Dev AS alow, (SELECT id FROM t_media_status WHERE name = 'Dev') AS id FROM t_media_status_transitions 
    UNION
    SELECT status, 'Uploaded', Uploaded, (SELECT id FROM t_media_status WHERE name = 'Uploaded') AS id  FROM t_media_status_transitions
    UNION
    SELECT status, 'Review', Review, (SELECT id FROM t_media_status WHERE name = 'Review') AS id  FROM t_media_status_transitions
    UNION
    SELECT status,  'Live', Live, (SELECT id FROM t_media_status WHERE name = 'Live') AS id  FROM t_media_status_transitions
    UNION
    SELECT status, 'Rejected', Rejected, (SELECT id FROM t_media_status WHERE name = 'Rejected') AS id  FROM t_media_status_transitions) AS transitions
    WHERE sfrom = ? AND alow = '1'
    ORDER BY id
EOT;
    $st = $dbh->prepare($strQuery);
    $st->execute(array($status));
    while ($row = $st->fetch(PDO::FETCH_ASSOC))
    {
        $id = htmlenc($row['id']);      // a messy mishmash encoding format this but will do for now
        $nstatus = htmlenc($row['status']);
        print("$id,$nstatus;");
    }

//mysql_free_result($result);
db_freeResult($result); 
?>
