<?
if (!isset($aryAcceptDecline))
{
	include('_common.php'); // TODO change all includes to require_once or just assume this happens
}

if (isUserLoggedOn()) { 
?>
<?
	if (isUserBlocked()) { 

		echo "<font color=\"#FF0000\">Your account has been disabled by an Administrator</font>";

	} elseif (isUserActive()) { 

		//if sname/fname/role is blank then needs updating


		db_connect();
		$query = "SELECT * FROM t_user WHERE username='".db_escape_string($loggedUser)."';";
		$result = db_runQuery($query);

		$needUpd = 0;
		if ($result) {
			if ($r = mysql_fetch_array($result)) {
				if (trim($r["fname"])=="" || trim($r["sname"])=="" || trim($r["role"])=="" ||
				    trim($r["fname"])==null || trim($r["sname"])==null || trim($r["role"])==null) {
					$needUpd = 1;
				}
			}
		}

		//db_freeResult($result);


		echo "User Links [ ";
		if ($needUpd==1) {
			echo "<img src='/img/star.png'> <a href='/userinfo.php'>My Info</a> - Needs Updating!";
		} else {
			echo "<a href='userinfo.php'>My Info</a>";
		}

		echo " - <a href='/users.php'>All Users</a>";

		//echo " - <a href='/userlic.php'>My Licenses</a>";

		echo " ]";

		$AnyNonAcceptedLics = anyRejectLic($loggedUser);
		echo " - Licenses [ <a href=\"/userlic.php\">";
		if ($AnyNonAcceptedLics) {
			echo "<img src='/img/star.png' border=0> New Licenses";
		} else {
			echo "<img src='/img/star2.png' border=0> No New Licenses";
		}



		echo "</a> ]";

	} else {

		//echo "<font color=\"#FF0000\">Your account has not yet been activated</font>";

	}
}
?>

