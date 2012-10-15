<?
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();

function fixupVocab($dbh)
{
    $sql = "INSERT INTO t_media_vocab
SELECT 'EN', m.id, m.name, 1, 0
FROM t_media m
LEFT JOIN t_media_vocab v ON m.id = v.m_id
WHERE m.status_id = 4 AND v.m_id IS NULL";
    $st = $dbh->prepare($sql);
    $st->execute();
    $sql = "UPDATE t_media m, t_media_vocab v
SET v.name = m.name
WHERE m.id = v.m_id AND v.name <> m.name";;
    $st = $dbh->prepare($sql);
    $st->execute();
    
}
fixupVocab($dbh);

//redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1)).$nxtcmd);

?>

Vocab table has been updated.

<?
include('_footer.php');
?>
