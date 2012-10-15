<?php
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();

function listUserRename( $dbh )
{
  print 'Rename <select id="userlist" name="from">';
  foreach ($dbh->query('SELECT id, username, CONCAT(fname,\' \',sname) AS name FROM t_user ORDER BY username, id') as $row)
  {        printf('<option value="%s">%s</option>'."\n",
                     $row['id'], htmlenc($row['username']).' &nbsp;&nbsp;('.htmlenc($row['name']).')' );
  }
  print '</select> ';
  print 'To <input class="edmedia" type="text" size="20" maxlength="30" name="to" value="" />';
  print '<input type="submit" accesskey="R" value="Rename Media" name="rename" />';
}

?>

Rename Media

<div class="blue_content_div" id="sec1">
<div class="innerdivspacer">

<?php
if  (isset($_POST['from']) && isset($_POST['to']))
{
    $id = $_POST['from'];
    $to = $_POST['to'];

    try
    {
        $query = "SELECT id, username, CONCAT(fname,' ',sname) AS name 
                    FROM t_user
                    WHERE id=? ";
        $st = $dbh->prepare($query);
        $st->execute(array($id));
        $row = $st->fetch(PDO::FETCH_ASSOC);
     } 
     catch (Exception $e)
     {
         print $e->getMessage();
         $row = array();
     }
     $st->closeCursor();

	$query = "UPDATE t_user SET username = ? WHERE id = ?";
    $st = $dbh->prepare($query);
    $st->execute(array($to, $id));
    $st->closeCursor();
    print 'User "'.htmlenc($row['username']).'"  ('.htmlenc($row['name']).') renamed to "'.htmlenc($to)."\"<br/>\n";
}
else
{
    print  '<form name="theform" id="theform" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n";
    print listUserRename($dbh);
    print '</form>';
}

?>

</div></div>

<script type="text/javascript">	
	Rounded("div#sec1","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>
