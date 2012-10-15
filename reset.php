<?php
$_in_account_page=True;
require('_header.php');

    function validateForm(&$error)
    {
        if (mb_strlen($_POST['password']) < 4)
            $error[] = 'Please enter a Password of at least 4 characters.';		// should we test pwds more, we do want to allow strong pwds
	elseif ($_POST['password'] <> $_POST['password_confirmed'])
            $error[] = 'The second password does not match the first.';
	return count($error) == 0;
    }
 	
	$error = array();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') // from form
	{
		db_connect();
		
		if (validateForm($error)) 
		{
            // TODO will always reset authcode and auth
			$sql = "UPDATE t_user SET authcode='', auth=1, pass='".md5(db_escape_string($_POST['password']))."' WHERE ID='".db_escape_string($_POST['ID'])."'";
			$update = db_runQuery($sql) or die(mysql_error());
            setLoggedUser($_POST['username']);
			echo '<p>Your password has been changed and you are logged in. You may <a href="index.php">use this site</a>.</p>.</p>';
			exit();
		}
	}
	
	// following either GET from email or POST from Form
	if ($_REQUEST['ID'] != '' && is_str_uint($_REQUEST['ID']) 
		&& mb_strlen($_REQUEST['key']) == 15 && ctype_alnum($_REQUEST['key']))
	{
		db_connect();

		$sql = "SELECT ID, username, auth FROM t_user WHERE ID = '".db_escape_string($_REQUEST['ID'])."'"
				." AND (auth = -2 OR authcode = '".db_escape_string($_REQUEST['key'])."')";
		$rs = db_runQuery($sql) or die(mysql_error());
		if(mysql_num_rows($rs) == 1)
		{
			$row = mysql_fetch_assoc($rs);
			if($row['auth'] == "0" || $row['auth'] == "-1")
			{
				$msg = '<p>This account is not available.</p>';
			}
			else
			{
				if (count($error))
				{
					print '<div class="formError"><p>';
					foreach ($error as $err)
					{
						print $err.'<br/>';
					}
					print '</p></div>';
				}
				if (isset($msg))
				{
					echo $msg;
				}
				else
				{ 
			?>
			<p>Please enter your new password.<br/>
			<div class="blue_content_div" id="blue_content_div1">
			<div class="innerdivspacer">
				<form action="<?=$_SERVER['PHP_SELF']?>" name="resetform" method="post">
					<table class="form">
						<input type="hidden" name="ID" value="<?php echo $_REQUEST['ID']; ?>" />
						<input type="hidden" name="username" value="<?php echo $row['username']; ?>" />
						<input type="hidden" name="key" value="<?php echo $_REQUEST['key']; ?>" />
						<tr><td><label for="password">Password:</label></td><td><input type="password" id="password" name="password" size="20" maxlength="20" value="" /></td></tr>
						<tr><td><label for="password_confirmed">Confirm password:</label></td><td><input type="password" id="password_confirmed" name="password_confirmed" size="20" maxlength="20" value="" /></td></tr>
					</table>
					[ <a href='javascript:document.resetform.submit();'>Set password</a> ]
				</form>
			</div></div>
			<?
				}
			}
		}
		else {
		
			echo "<p>Could not reset password.</p><p>Please <a href='/contact.php?subject=Reset%20password%20issue'>contact us</a></p>";
		}
	}
	else 
	{
		echo "<p>Could not reset password as the link appears to be invalid. Please <a href='/contact.php?subject=Reset%20password%20link%20issue'>contact us</a></p>";
	}
?>

<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>
