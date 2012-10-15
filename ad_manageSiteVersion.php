<?php
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();

function getVersion( $dbh )
{
  $st = $dbh->query('SELECT version FROM t_web_app;');
  $row = $st->fetch(PDO::FETCH_ASSOC);
  $ver = htmlenc($row['version']);
  print "<p>Webite Version is <span>$ver</span><p>";
  print 'Change to: <input class="edmedia" type="text" size="10" maxlength="10" name="ver" value="'.$ver.'" /></td>';
  print '<input type="submit" accesskey="R" value="Set version" name="Save" />';
}

?>

Manage versions

<div class="blue_content_div" id="sec1">
<div class="innerdivspacer">

<?php
if  (isset($_POST['ver']))
{
    $ver = $_POST['ver'];

	$query = "UPDATE t_web_app SET version = ?";
    $st = $dbh->prepare($query);
    $st->execute(array($ver));
    $st->closeCursor();
    print 'Version set to '.htmlenc($ver)."<br/>\n";
    redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1))); //refresh version in menu bar
}
else
{
    print  '<form name="theform" id="theform" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n";
    print getVersion($dbh);
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
