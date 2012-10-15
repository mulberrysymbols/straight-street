<?
require_once('_common.php');

if (!isset($_GET['file']))
{
    exit();
}

$file = $_GET['file'];

// TODO - put in try except so erros don't stop download?
db_connect();
$query = sprintf("INSERT INTO t_downloads VALUES ('', '%s','%s',NOW());",
            db_escape_string($loggedUserId), 
            db_escape_string(basename($file)));
$result = db_runQuery($query);
db_freeResult($result);

header("Location: $file");

?>