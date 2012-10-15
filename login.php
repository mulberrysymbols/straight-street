<?php
$_in_account_page=True;
require('_header.php');

$clean = array();
$clean['username'] = isset($_POST['username']) ? $_POST['username'] : '';
$clean['password'] = isset($_POST['password']) ? $_POST['password'] : '';
//$clean['expired'] = isset($_GET['expired']) ? $_GET['expired'] : '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if ($clean['username'] != '' /*&& $clean['password'] != ''*/)
		{
			db_connect();
			
			$sql = sprintf("SELECT ID, username, auth FROM t_user WHERE username = '%s' AND (auth = -2 OR pass = '%s');",
						    db_escape_string($clean['username']),
						    db_escape_string(md5($clean['password'])));
			$rs = db_runQuery($sql) or die(mysql_error());
			if (mysql_num_rows($rs) == 1)
			{
				$row = mysql_fetch_assoc($rs);
				if($row['auth'] == 0)
				{
					$error = 'Your account has not been activated.<br/>Please open the email that we sent and click on the activation link';
				}
				if($row['auth'] == -1)
				{
					$error = 'Your account has been blocked.<br/>Please <a href="/contact.php?subject=Account%20blocked">contact us</a>';
				}
				else
				{
                    if ($row['auth'] == -2)
                        redirectTo("reset.php?ID=".$row['ID']."&key=$noauthkey");
                    else
                    {
                        setLoggedUser($clean['username']);
                        redirectTo("index.php");
                    }
					// reset any authcode to stop links in emails
					$sql = sprintf("UPDATE t_user SET last_access = NOW() WHERE ID='%s';",
								db_escape_string($row['ID']));
					$update = db_runQuery($sql) or die(mysql_error());
				}
			}
			else {		
				$error = 'Login failed. Please check your username and password';		
			}
		}
		else {
			$error = 'Please enter both your username and password to access your account';
		}
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['expired']))
		$error = '<p>You have been logged out due to inactivity. Please log in again</p>';
	

if (isset($error))
{
	print <<< HTML
<div class='formError'>
	<p>$error</p>
</div>
HTML;
}

?>
<div style='width:100%'>
<div class="blue_content_div" id="blue_content_div1" style='float: left;width:55%'>
<div class="innerdivspacer" style="height:11em">
    <p><b>Exisiting Users:</b> please log in to your account<br/></p>
    <form action="<?=$_SERVER['PHP_SELF']?>" name="loginform" id ="loginform" method="post">
	<table class="form">
		<tr>
			<td><label for="username">Username:</label></td>
			<td><input type="text" id="username" name="username" size="20" maxlength="50" value="" tabindex="1"/></td>
			<td>&nbsp;</td><td>[ <a tabindex="4" href='forgotton.php?what=username'>Forgotton your username?</a> ]</td>
		</tr>
		<tr>
			<td><label for="password">Password:</label></td>
			<td><input type="password" id="password" name="password" size="20" maxlength="20" value="" tabindex="2"/></td>
			<td>&nbsp;</td><td>[ <a tabindex="5" href='forgotton.php?what=password'>Forgotton your password?</a> ]</td>
		</tr>
	</table>
	<input type='submit' value='Login' tabindex="3" /> 
</form>
</div></div>
<div class="blue_content_div" id="blue_content_div2" style='float: right; width:44%'>
<div class="innerdivspacer" style="height:11em">
	<p><b>New Users:</b> if you do not have an account...</p>
	[ <a href='account.php'>register</a> ] a new account.
</div></div>
<div class="clearer">&nbsp;</div>
</div>

<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
	Rounded("div#blue_content_div2","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>

<script type="text/javascript">	
	document.loginform.username.focus();
</script>
