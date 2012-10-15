<?php
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

?>

Delete Users

<ul>

<div class="blue_content_div" id="sec1">
<div class="innerdivspacer">

<?
function write_line($arLine, $heading)
{
    $line = "<tr>";
    for ($i=0; $i<sizeof($arLine); $i++)
    {
        $klass = (in_array($i, Array(2,3,4,5))) ? 'class="report_ral"' : '';
        if ($heading)
        {
            $line  .= "<th $klass>" . $arLine[$i] . '</th>';
        }
        else
        {
            $line .= "<td $klass>" . $arLine[$i] . '</td>';
        }
    }
    if (!$heading)
    {
        if ($arLine['can_del'] == '0')
        {
            $clck = "onclick=\"delUserConfirm(".$arLine['id'].", '".$arLine['username']." (".$arLine['name'].")')\"";
        }
        else
        {
            $clck = "onclick=\"delUser(".$arLine['id'].")\"";
        }
        $btn = "<input type=\"button\" value=\"Del\" $clck />";
        $line .= "<td>$btn</td>";
    }
    $line .= "</tr>\n";
    print($line);
    //return fwrite($handle, $line);
}


    $sql = <<<EOT
    SELECT  u.username, 
            CONCAT(u.fname, ' ', u.sname) AS name,
            u.id,
            IFNULL(ual.nlics, 0) AS lics,
            IFNULL(rd.nrevs, 0) AS revs,
            IF(ual.nlics IS NULL AND rd.nrevs IS NULL, 1, 0) as can_del
    FROM t_user AS u
    LEFT JOIN (SELECT uid, count(lid) AS nlics FROM t_user_agr_lic GROUP BY uid ) AS ual
      ON ual.uid=u.id
    LEFT JOIN (SELECT userid, count(rid) AS nrevs FROM t_review_dataset GROUP BY userid ) AS rd
      ON rd.userid=u.id

    ORDER BY u.username, u.id;
EOT;


db_connect();
    $result = db_runQuery($sql);
    if (!$result)
    {
       die("query failed" );
    }

    print "<table class='report_table'";
    $row = mysql_fetch_assoc($result);
    write_line(array_keys($row), true);
    mysql_data_seek($result, 0);

    while ($row = mysql_fetch_array($result))
    {
        @write_line($row, false);
    }
    print "</table>";


?>

</div></div>

<script type="text/javascript">	
	Rounded("div#sec1","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>
