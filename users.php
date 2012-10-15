<?
include('_header.php');
if (!isUserLoggedOn()) {
	echo "<font color='#FF0000'>You must be logged on to see this page</font>";
} else {


$ar_authorities_d = $ar_authorities;

$show=(isset($_GET['show'])) ? $_GET['show'] : 'S';
if ($show <> 'L' && !array_key_exists($show, $ar_authorities_d))
{
    $show = 'S';
}

function listRecords($query)
{
    //mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

	$result = db_runQuery($query);

	if ($result) 
    {
		echo "
		<table class=\"ad_userlist\">
		<tr>			
            <th>Name</th>
			<th>Role</th>
		</tr>";

		//record exists
		while ($r = mysql_fetch_array($result))
        {
			$strName = $r["name"];
			if (trim($strName)=="") { $strName = "* None *"; }
			$strRole = $r["role"];

			//output
			echo "<tr>";
			echo "<td>".htmlenc($strName)."</td>".
			"<td>".htmlenc($strRole)."</td>";
            echo "</tr>";
        }   
		echo "</table>";
	} 

	//mysql_free_result($result); 
	db_freeResult($result);
}

?>

Registered Users

<ul>

<?
//===========================
//STATS

function showLink($name, $id, $id_showing, $tot) 
{
    echo "[ ";
    if ($id != $id_showing)
    {
        echo '<a href="'.$_SERVER['PHP_SELF']."?show=$id\">$name</a>";
    }
    else
    {
        echo "$name";
    }
    if ($tot)
        echo ": <b>$tot</b>";
    echo " ] ";
}

//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();

$loggedInClause="TIMEDIFF(NOW(), ifnull(u.last_access, NOW())) <= '$g_logtimeout'";

$query = "
SELECT
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'S') as cS,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'A') as cA,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'P') as cP,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'C') as cC,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'R') as cR,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'D') as cD,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'T') as cT,
    (SELECT count(id) FROM `t_user` AS u WHERE $loggedInClause) as cLoggedIn,
	(SELECT count(*) from t_user) as cTotal
";

//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

if ($result) {
	//record exists
	if ($r = mysql_fetch_array($result)) {
		echo "
		<div class=\"green_content_div\" id=\"green_content_div1\">
		<div class=\"innerdivspacer\">";
		echo "Total: <b>".$r["cTotal"]."</b> <br>";

        foreach ($ar_authorities_d as $id => $name)
        {
            showLink($name, $id, $show, $r["c".$id]);
        }
		echo "<br/>";
        showLink('Logged In', 'L', $show, $r['cLoggedIn']);
		echo "</div></div>";
	}
}
//mysql_free_result($result); 
db_freeResult($result);
//===========================

?>
<br><br>

<?

//===========================
//Loop thru each status array and run query to list each auth

$sql="
    SELECT u.username AS name, CONCAT(u.fname,' ',u.sname) AS realname, 
    #DATE_FORMAT(u.datereg, '%Y-%m-%d') AS datereg, DATEDIFF(CURDATE(),u.datereg) AS daysreg,
    u.role
    FROM t_user u";

if ($show == "L") 
{
    $name = 'Logged In';
    $sql .= "
    WHERE $loggedInClause";
}
else
{
    $name = $ar_authorities_d[$show].'s';
    $sql .= "
      INNER JOIN t_user_authority ua 
        ON ua.user_id = u.id AND ua.authority_id ='".db_escape_string($show)."' 
    ORDER BY datereg ASC;";
}

echo $name;
echo "<br>
<div class=\"blue_content_div\" id=\"blue_content_div1\">
<div class=\"innerdivspacer\">
";
listRecords($sql);
echo "
</div>
</div>
<br>
";

?>

<script type="text/javascript">	
	Rounded("div#green_content_div1","#FFFFFF","#ECFFEC");
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
</script>


</ul>
<?
}
include('_footer.php');
?>