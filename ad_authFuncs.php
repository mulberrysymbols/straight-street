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
        $klass = ($i != 0) ? 'class="report_cal"' : '';
        if ($heading)
        {
            $line  .= "<th $klass>" . $arLine[$i] . '</th>';
        }
        else
        {
            $line .= "<td $klass>" . $arLine[$i] . '</td>';
        }
    }
    $line .= "</tr>\n";
    print($line);
    //return fwrite($handle, $line);
}

    $sql = <<<EOT
    SELECT  f.name as function,
    IF(SUM(IF(a.id='S', 1, 0)), 'X', ' ') as Subscriber,
    IF(SUM(IF(a.id='R', 1, 0)), 'X', ' ') as Reviewer,
    IF(SUM(IF(a.id='C', 1, 0)), 'X', ' ') as Commiter,
    IF(SUM(IF(a.id='D', 1, 0)), 'X', ' ') as Developer,
    IF(SUM(IF(a.id='P', 1, 0)), 'X', ' ') as Partner,
    IF(SUM(IF(a.id='T', 1, 0)), 'X', ' ') as Trustee,
    IF(SUM(IF(a.id='A', 1, 0)), 'X', ' ') as Admin,
    IF(SUM(IF(a.id='E', 1, 0)), 'X', ' ') as Editor
    FROM t_authority AS a
    LEFT JOIN t_authority_function af
      ON af.auth_id=a.id
    LEFT JOIN t_function f
      ON f.id=af.func_id

    GROUP BY f.id
    ORDER BY f.id;
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
