<?php
$_in_account_page=True;
require('_header.php');
require('_forumuser.php');

    function validateForm(&$error)
    {
        if (mb_strlen($_POST['fname']) < 1  /*or !ctype_alpha($_POST['fname'])*/)
            $error[] = 'Please enter a Firstname using letters.';
        if (mb_strlen($_POST['sname']) < 1  /*or !is_str_surname($_POST['sname'])*/)
            $error[] = 'Please enter a Surname using letters and - and \'.';
        if (mb_strlen($_POST['username']) < 4  /*or !ctype_alnum($_POST['username'])*/)
            $error[] = 'Please enter a Username using at least 4 letters or numbers.';
        if (mb_strlen($_POST['password']) < 4)
            $error[] = 'Please enter a Password of at least 4 characters.';		// should we test pwds more, we do want to allow strong pwds
	elseif ($_POST['password'] <> $_POST['password_confirmed'])
            $error[] = 'The second password does not match the first.';
        if ($_POST['email'] =='' || !is_valid_email_address($_POST['email']))
            $error[] = 'Please enter a valid email address.';
        if ($_POST['passcode'] != 'Human')
            $error[] = 'You must enter the required phrase to prove that you are a real live person.';
        if (checkExists('t_user', 'username', $_POST['username']))
            $error[] = 'Sorry that username is already in use.';
        if (checkExists('t_user', 'email', $_POST['email']))
            $error[] = 'Sorry that email is already in use.';
	return count($error) == 0;
    }
 	
	$error = array();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') // from form
	{
		db_connect();
		
		if (validateForm($error)) 
		{
			$sql = "INSERT INTO t_user (fname, sname, username, pass, email, authcode, auth, datereg, role, cancontact) VALUES ("
						."'".db_escape_string($_POST['fname'])."'"
						.", '".db_escape_string($_POST['sname'])."'"
						.", '".db_escape_string($_POST['username'])."'"
						.", '".db_escape_string(md5($_POST['password']))."'"
						.", '".db_escape_string($_POST['email'])."'"
						.", '"/*.random_string('alnum',15).*/."'"
						.", "."1"	// activated
						.", '".date("Y-m-d", Time())."'"
						.", ''"		// Unspecified - not sure if NULL is safe in later processing
						.", "."1"	// cancontact
						.")";
			$rs = db_runQuery($sql) or die('Error: '.mysql_error());
			
			// double check UPDATE
			$sql = "SELECT ID, email, sname, fname, authcode, username FROM t_user WHERE username = '".db_escape_string($_POST['username'])."'";
			$rs = db_runQuery($sql) or die(mysql_error());
			if (mysql_num_rows($rs) == 1)
			{
                $row = mysql_fetch_assoc($rs);

                $query = sprintf("INSERT INTO t_user_authority VALUES ('','%s','%s');",
                            db_escape_string($row['ID']), db_escape_string('S'));
                $result = db_runQuery($query) or die(mysql_error());

                $query = sprintf("INSERT INTO t_user_agr_lic VALUES ('','%s','%s');",
                            db_escape_string($row['ID']), db_escape_string('6'));
                $result = db_runQuery($query) or die(mysql_error());

                setLoggedUser($row['username']);
					// reset any authcode to stop links in emails
                $sql = sprintf("UPDATE t_user SET last_access = NOW() WHERE ID='%s';",
                            db_escape_string(db_escape_string($row['ID'])));
                $update = db_runQuery($sql) or die(mysql_error());

/*				$headers = "MIME-Version: 1.0 \n"
						   ."Content-type: text/plain; charset=UTF-8 \n"
						   ."From: $addr \n"
                           ."Reply-To: $addr \n"
                           .'X-Mailer: PHP/' . phpversion() . " \n";
				$subject = "Straight Street account activation";
				$message = <<<EOT
Hi {$row['fname']} {$row['sname']},

A new Straight Street account has been created using your e-mail address.

You must confirm this email address in order that you can receive occasional notification emails from us.

To confirm please click on the following link.

http://$domain/confirm.php?ID={$row['ID']}&key={$row['authcode']}

In most e-mail programs, this will appear as a blue link which you can click on. If that doesn't work you can cut-and-paste the link into the address bar at the top of your Web browser window.

If you did not make this request, you may safely ignor this email.
If you need help, please contact the Support Team directly at: $addr_bare.

Thank you for joining Straight Street, and welcome.
EOT;
                $to = $row['email'].",$addr,steve@fullmeasure.co.uk";
				if (mb_send_mail($to, $subject, $message, $headers))
				*/{
					$msg = <<<EOT
<p>Welcome. You are now loggined in and may <a href="index.php">use this site</a>.</p>
EOT;
					addForumUser($row['username'], $_POST['password'], $row['email'], $_SERVER['REMOTE_ADDR']);

				}
/*				else {
					$msg = "<div class='formError'><p>Your account has been created but the confirmation email could not be sent.</p><p>Please contact us at $addr_bare.</p></div>";
				}*/
			}
			else {
				$error[] = "An unexpected error has occured.<br/>Please contact us at $addr_bare";
			}
		}
	}

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
<p>Please enter your details. All fields are required.<br/>
Note: You must supply an email address in order to receive notifications but this is not required to complete registration.</p>
<div class="blue_content_div" id="blue_content_div1">
<div class="innerdivspacer">
	<form action="<?=$_SERVER['PHP_SELF']?>" name="accountform" method="post">
		<table class="form">
			<tr><td><label for="fname">First name:</label></td><td><input type="text" id="fname" name="fname" size="20" maxlength="20" value="<?php if (isset($_POST['fname'])) { echo htmlenc($_POST['fname']); } ?>" /> (letters only)</td></tr>
			<tr><td><label for="sname">Surname:</label></td><td><input type="text" id="sname" name="sname" size="20" maxlength="20" value="<?php if (isset($_POST['sname'])) { echo htmlenc($_POST['sname']); } ?>" /> (letters and - and &apos;)</td></tr>
			<tr><td><label for="username">Username:</label></td><td><input type="text" id="username" name="username" size="20" maxlength="50" value="<?php if (isset($_POST['username'])) { echo htmlenc($_POST['username']); } ?>" /> (at least 4 letters or numbers)</td></tr>
			<tr><td><label for="password"><label for="fname">Password:</label></td><td><input type="password" id="password" name="password" size="20" maxlength="20" value="" /> (at least 4 characters)</td></tr>
			<tr><td><label for="password_confirmed">Confirm password:</label></td><td><input type="password" id="password_confirmed" name="password_confirmed" size="20" maxlength="20" value="" /></td></tr>
			<tr><td><label for="email">Email:</label></td><td><input type="text" id="email" name="email" size="50" maxlength="50" value="<?php if (isset($_POST['email'])) { echo htmlenc($_POST['email']); } ?>" /></td></tr>
			<tr><td></td><td><input type="checkbox" disabled="disabled" checked="checked"/>I accept the Creative Commons licence to access the Mulberry symbol set. <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(read more or unaccept once registration completed) </td></tr>
			<tr><td><label for="passcode">Pass code:</label></td><td><input type="text" id="passcode" name="passcode" size="10" maxlength="10" value="<?php if (isset($_POST['passcode'])) { echo htmlenc($_POST['passcode']); } ?>"/> (Please type the word 'Human') </td></tr>
		</table>
        <input type='submit' value='Register' />
		<!--[ <a href='javascript:document.accountform.submit();'>Register</a> ]-->
	</form>
</div></div>
<?
	}
?>

<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>

<script type="text/javascript">	
	document.accountform.fname.focus();
</script>
