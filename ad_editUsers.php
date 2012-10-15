<?
include('_header.php');
if (isUserAtLeastAdmin()) {

$ar_authorities_d = $ar_authorities;
$ar_authorities_d['U'] = 'Unauthenticated';
$ar_authorities_d['X'] = 'Disabled';

$show=(isset($_GET['show'])) ? $_GET['show'] : 'S';
if (!array_key_exists($show, $ar_authorities_d))
{
    $show = 'S';
}

if(isset($_GET['action']) && ($_GET['action'] == 'unsetAuth' || $_GET['action'] == 'setAuth') && isset($_GET['user']) )
{
    $username = db_escape_string($_GET['user']);
    $usernameh = htmlenc($_GET['user']);

    db_connect();

    $show = 'U';

    if ($_GET['action'] == 'unsetAuth')
    {
        $with = 'may now login without';
        $set = '-2';
    }
    elseif ($_GET['action'] == 'setAuth')
    {
        $with = 'must login with';
        $set = '1';
    }
    else
    {
        exit();
    }
    
    $sql = "UPDATE t_user set auth = $set WHERE username = '$username'";
    $rs = db_runQuery($sql) or die(mysql_error());

    print("User '$usernameh' $with password'");
    print("<br><br>[ <a href='".$_SERVER['PHP_SELF']."?show=$show'>Back to users</a> ]'");
    exit();
}
else
{
    $prompt = ($show == 'U') ? '\'Require user "\' + auser + \'" to login by entering their password?\''
                         : '\'Alow user "\' + auser + \'"to login once without entering their password?\'';
    $action = ($show == 'U') ? 'setAuth' : 'unsetAuth';
    print "<script>
                function alterAuth(auser)
                {
                    if (confirm($prompt))
                    {
                        location.replace('".$_SERVER['PHP_SELF']."?action=$action&user='+auser);
                    }
                }
            </script>";

}

function listRecords($query,$aryAuthLevels,$showActCode) {

    //mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

	$result = db_runQuery($query);

	if ($result) {

		echo "
		<table class=\"ad_userlist\">
		<tr>	<th>DbId</th>
			<th>Username / Role</th>
			<th>Name / Email</th>
			<th>DateReg</th>
			<th>Contact</th>
			<th>Authorities</th>";

		if ($showActCode) {
			echo "	<th>Activation Code</th>";
		}

		echo "
		</tr>";

		//record exists
		while ($r = mysql_fetch_array($result)) {
		
			//$jsedit = "";
			//$jsedit = " onClick=\"editField(this);\" ";

			echo "<tr>";
            $nocontact = ($r['cancontact']) ? '' : 'No';
			echo "
			<td>".$r["id"]."</td>
			<td><div style='text-align:left;'><a href='#/ru' onclick='alterAuth(\"".htmlenc($r["username"])."\")' title='Alter user authentication'>".htmlenc($r["username"])."</a></div><br/>
			<div style='text-align:right;'>".htmlenc($r["role"])."</div></td>
			<td><div style='text-align:left;'>".htmlenc($r["name"])."</div><br/>
			<div style='text-align:right;'>".htmlenc($r["email"])."</div></td>
			<td>".$r["datereg"]."<br/> (".$r['daysreg']."&nbsp;days)</td>
			<td>".$nocontact."</td>
			<td> ";

            echo '<table id="authorities"><tr>';
			foreach ($aryAuthLevels as $authLevel => $authLevelName)
			{
                $cid = 'cb_'.$r["id"].'_'.$authLevel;
                echo "<td><label for =\"$cid\">$authLevel</label></td>";
			}
            echo "</tr>\n<tr>";
            $aa = getUserAuthorities($r['id']); // NB this requires update if  authorities change
			foreach ($aryAuthLevels as $authLevel => $authLevelName)
			{
                $cid = 'cb_'.$r["id"].'_'.$authLevel;
                $chkd = (array_key_exists($authLevel, $aa)) ? 'checked="checked"' : '';
                echo "<td><input type=\"checkbox\" id=\"$cid\" name=\"$cid\" value=\"\" $chkd onclick=\"setUserAuthority('".$r["id"]."','$authLevel',this.checked)\"/></td>";
            }
            echo '</tr></table>';

			echo " </td>";


			if ($showActCode) {
				echo "<td>".$r["authcode"]."</td>";
			}

			echo "</tr>";
		}

		echo "</table>";

	} 

	//mysql_free_result($result); 
	db_freeResult($result);
}

?>

Admin - Users

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
    //if ($tot)
        echo ": <b>$tot</b>";
    echo " ] ";
}

//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();


$query = "
SELECT
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'S') as cS,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'A') as cA,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'E') as cE,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'P') as cP,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'C') as cC,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'R') as cR,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'D') as cD,
	(SELECT count(user_id) FROM `t_user_authority` WHERE authority_id = 'T') as cT,
	(SELECT count(id) FROM `t_user` WHERE auth = -2) as cU,
	(SELECT count(id) FROM `t_user` WHERE auth = -1) as cX,
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
    SELECT u.id, u.username, CONCAT(u.fname,' ',u.sname) AS name, DATE_FORMAT(u.datereg, '%Y-%m-%d') AS datereg, DATEDIFF(CURDATE(),u.datereg) AS daysreg, u.role, u.email, u.cancontact, u.authcode
    FROM t_user u 
";

$name = $ar_authorities_d[$show];
if ($show == "U") 
{
    $sql .="
    WHERE auth = -2
    ORDER BY datereg ASC;";
    $showAuthCode = true;
}
elseif ($show == "X") 
{
    $sql .="
    WHERE auth = -1
    ORDER BY datereg ASC;";
    $showAuthCode = false;
}
else
{
    $sql .= "
      INNER JOIN t_user_authority ua 
        ON ua.user_id = u.id AND ua.authority_id ='".db_escape_string($show)."' 
    ORDER BY datereg ASC;";
    $showAuthCode = false;
}

echo $name;
echo "s<br>
<div class=\"blue_content_div\" id=\"blue_content_div1\">
<div class=\"innerdivspacer\">
";
listRecords($sql, $ar_authorities, $showAuthCode);
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