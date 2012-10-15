<?php
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();

function addVersion( $dbh )
{
  print '<select id="userlist" name="lang">';
  foreach ($dbh->query('SELECT l.id, CONCAT(l.name, \' - \', l.native_name) AS name FROM t_language l WHERE l.id NOT IN (SELECT lang_id FROM t_bundle_version) ORDER BY l.name') as $row)
  {
//  printf( '<option value="%s">%s</option>'."\n",
  //                   $row['id'], htmlenc($row['name']).' &nbsp; - &nbsp;'.htmlenc($row['native_name']) );

  print( '<option value="'.$row['id'].'">'.htmlenc($row['name']).'</option>' );
    }
  print '</select> ';
 print '<input type="submit" accesskey="A" value="Add" name="add" />';
}

?>

Add language 

<div class="blue_content_div" id="sec1">
<div class="innerdivspacer">

<?php
if  (isset($_POST['add']) && isset($_POST['lang']))
{
    $lang= $_POST['lang'];

    $query = "INSERT INTO t_bundle_version (lang_id, version) VALUES( ?, '');";
    $st = $dbh->prepare($query);
    $st->execute(array($lang));
    $st->closeCursor();
    redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1)));
}
else
{
    print  '<form name="theform" id="theform" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n";
    print addVersion($dbh);
    print '</form>';
}

?>


</div></div>

<br/>Manage language bundle versions

<div class="blue_content_div" id="sec2">
<div class="innerdivspacer">

<?php
function getVersions( $dbh )
{
  print('<table>');
  foreach ($dbh->query('SELECT l.id, CONCAT(l.name, \' - \', l.native_name) AS name, bv.version 
                        FROM t_language AS l 
                        INNER JOIN t_bundle_version bv 
                            ON bv.lang_id = l.id
                        ORDER BY l.name;') as $row)
  { 
      printf('<tr><td><input class="edmedia" type="text" size="5" maxlength="5" name="lver_%s" value="%s"/></td>'.
                '<td></td><td>%s</td><td>%s</td></tr>',
                 $row['id'], $row['version'], $row['id'], $row['name']);
  }
  print '<tr><td colspan="4"><input type="submit" accesskey="S" value="Save" name="Save" /></td></tr>';
  print('</table>');
}

if  (isset($_POST['Save']))
{
    $values = array();
    $sql = 'UPDATE t_bundle_version SET version = ? WHERE lang_id = ?;';
    $st = $dbh->prepare($sql);
    foreach($_POST as $name => $ver)
    {
        if (substr($name, 0, 4) == 'lver')
        {
            $id = substr($name, -2, 2);
            $st->execute(array($ver, $id));
        }
    }
    $st->closeCursor();
    redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1)));
}
else
{
    print  '<form name="theform2" id="theform2" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n";
    print getVersions($dbh);
    print '</form>';
}

?>

</div></div>

<script type="text/javascript">	
	Rounded("div#sec1","#FFFFFF","#ECECFF");
	Rounded("div#sec2","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>
