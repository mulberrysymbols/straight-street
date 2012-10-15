<?php
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();

function listMediaRename( $dbh )
{
  print 'Rename <select id="medialist" name="from">';
  foreach ($dbh->query('SELECT id, name FROM t_media ORDER BY NAME, id') as $row)
  {        printf('<option value="%s">%s</option>'."\n",
                     $row['id'],htmlenc($row['name']));
  }
  print '</select> ';
  print 'To <input class="edmedia" type="text" size="20" maxlength="30" name="to" value="" /></td>';
  print '<input type="submit" accesskey="R" value="Rename Media" name="rename" />';
}

function renfile($pfrom, $pto)
{
    if (file_exists($pfrom))
    {
        rename($pfrom,$pto);
        print 'File renamed '.htmlenc($pfrom).' to '.htmlenc($pto)."<br/>\n";
    }
    else
    {
        print 'File not found '.htmlenc($pfrom)."<br/>\n";
    }
}

function rensymbol($dbh, $id, $from, $to, $type)
{
	$query = "UPDATE t_media_path SET filename = ? WHERE mid = ? AND type = ?";
    $st = $dbh->prepare($query);
    $st->execute(array($to, $id, $type));
    $st->closeCursor();
   
    global $symbolsWMF, $symbolsSVG, $symbolsPNG, $symbolsThumb, $symbolsPreview;
    $p=array("0" => "orange",);
    if ($type == 0)
    {
    	foreach (array($symbolsWMF/*, $symbolsSVG, $symbolsPNG*/) as $p)
    	{
    		renfile("$p$from", "$p$to");
    	}
    }  
    elseif ($type == 1)
    {
	  	renfile("$symbolsThumb$from", "$symbolsThumb$to");
    }  
    elseif ($type == 3)
    {
	  	renfile("$symbolsPreview$from", "$symbolsPreview$to");
    }
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
        $query = "SELECT m.id, m.name, ms.name AS status,
                    mp0.filename as from0, 
                    mp1.filename as from1,  
                    mp3.filename as from2, 
                    REPLACE(mp0.filename, CONCAT(m.name, '.'), CONCAT(?, '.')) AS to0, 
                    REPLACE(mp1.filename, CONCAT(m.name, '.'), CONCAT(?, '.')) AS to1, 
                    REPLACE(mp3.filename, CONCAT(m.name, '.'), CONCAT(?, '.')) AS to2
                    FROM t_media m
                    LEFT JOIN t_media_path mp0
                      ON mp0.mid = m.id AND mp0.type = 0
                    LEFT JOIN t_media_path mp1
                      ON mp1.mid = m.id AND mp1.type = 1
                    LEFT JOIN t_media_path mp3
                      ON mp3.mid = m.id AND mp3.type = 3
                    LEFT JOIN t_media_status ms
                      ON ms.id = m.status_id
                    WHERE m.id=? ";
        $st = $dbh->prepare($query);
        $st->execute(array($to, $to, $to, $id));
        $row = $st->fetch(PDO::FETCH_ASSOC);
     } 
     catch (Exception $e)
     {
         print $e->getMessage();
         $row = array();
     }
     $st->closeCursor();

    print 'Renaming '.htmlenc($row['name']).' to '.htmlenc($to)." - status: ".$row['status']."<br/>\n";

	$query = "UPDATE t_media SET name = ? WHERE id = ?";
    $st = $dbh->prepare($query);
    $st->execute(array($to, $id));
    $st->closeCursor();
    print "Symbol renamed<br/>\n";

	if ($row['status'] != 'Dev')
    {
        rensymbol($dbh, $id, $row['from0'], $row['to0'], 0);
        rensymbol($dbh, $id, $row['from1'], $row['to1'], 1);
        rensymbol($dbh, $id, $row['from2'], $row['to2'], 3);
    }
}
else
{
    print  '<form name="theform" id="theform" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n";
    print listMediaRename($dbh);
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
