<?php
$_in_account_page=True;
require('_header.php');

    function validateForm(&$error)
{
        if ($_POST['what'] == 'password')
        {
            if (isset($_POST['username']) && $_POST['username'] != '')
            {
                if (!checkExists('t_user', 'username', $_POST['username']))
                    $error[] = 'Sorry that username could not be found.';
                unset($_POST['email']); // ignore
            }
        }
       else if ($_POST['what'] == 'username' || ($_POST['what'] == 'password' && isset($_POST['email'])))
        {
            if (!isset($_POST['email']) || $_POST['email'] == '' || !is_valid_email_address($_POST['email']))
                $error[] = 'Please enter a valid email address.';
            elseif (!checkExists('t_user', 'email', $_POST['email']))
                $error[] = 'Sorry that email could not be found .';
            $_POST['username'] = ''; // only one or other
        }
        
        return count($error) == 0;
    }
 	
	$error = array();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') // from form
	{
		db_connect();
		
		if (validateForm($error)) 
		{
            $sqlWhere = ' WHERE '
                             . ((isset($_POST['email']) && $_POST['email'] <> '') ? "email='".db_escape_string($_POST['email'])."'" : 'FALSE')
                             .' OR '
                             . ((isset($_POST['username']) && $_POST['username'] <> '') ? "username='".db_escape_string($_POST['username'])."'" : 'FALSE');
            
			$sql = "UPDATE t_user "
				." SET authcode='".random_string('alnum',15)."'"
				.$sqlWhere.";";
			$rs = db_runQuery($sql) or die('Error: '.mysql_error());
			
			// double check UPDATE
			$sql = "SELECT ID, email, sname, fname, authcode, auth, username FROM t_user "
                    .$sqlWhere.";";
			$rs = db_runQuery($sql) or die(mysql_error());
			if (mysql_num_rows($rs) == 1)
			{
				$row = mysql_fetch_assoc($rs);
                
				if($row['auth'] == "-1" || $row['auth'] == "0")
				{
					$msg = '<p>This account is not available.</p>';
				}
				else
				{
					if ($_POST["what"] == 'username')
					{
						$subject = "Straight Street username reminder";
						$message = <<<EOT
Hi {$row['fname']} {$row['sname']},

A request has been made for a reminder of the Straight Street username for the account associated with this e-mail address.

Your username is:    {$row['username']} .
	
If you did not make this request, you may safely ignore this email.
If you need help, please contact the Support Team directly at: $addr_bare .
EOT;
					}
					elseif ($_POST["what"] == 'password')
					{
						$subject = "Straight Street password reset request";
						$message = <<<EOT
Hi {$row['fname']} {$row['sname']},

A request has been made to change the Straight Street password for the account associated with this e-mail address.

To change the password please click on the following link.

http://$domain/reset.php?ID={$row['ID']}&key={$row['authcode']}

In most e-mail programs, this will appear as a blue link which you can click on. If that doesn't work you can cut-and-paste the link into the address bar at the top of your Web browser window.

If you did not make this request, you may safely ignore this email.
If you need help, please contact the Support Team directly at: $addr_bare .
EOT;
					}
					else
					{
						die('Unknown request');
					}
					$headers = "MIME-Version: 1.0\r\n"
							   ."Content-type: text/plain; charset-utf-8 \r\n"
							   ."From: $addr_bare\r\n" . "Reply-To: $addr_bare\r\n". 'X-Mailer: PHP/' . phpversion() . "\r\n";

					if (mb_send_mail($row['email'], $subject, $message, $headers))
					{
						if ($_POST["what"] == 'username')
						{
							$msg = <<<EOT
<p>An email has been sent to your email address. It may take a few minutes to arrive -- please be patient.</p>
<p>If you use web-based email or have 'junk mail' filters, you may wish to check your spam mail folders, in odd cases the message may get confused as spam.</p>
<p>[ <a href='/'>Return to home page</a> ]</p>
EOT;
						}
						elseif ($_POST["what"] == 'password')
						{
							$msg = <<<EOT
<p>An email has been sent to your email address. It may take a few minutes to arrive -- please be patient.</p>
<p>If you use web-based email or have 'junk mail' filters, you may wish to check your spam mail folders, in odd cases the message may get confused as spam.</p>
<p>You must click on the link within the email we've just sent you to change you password.</p>
EOT;
						}
					}
					else 
					{
						$msg = "<div class='formError'><p>The email could not be sent.</p><p>Please contact us at $addr_bare.</p></div>";
					}
				}
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
        $what = (isset($_GET['what']) ? $_GET['what'] : 
                    (isset($_POST['what']) ? $_POST['what'] : '')); // php has rubbish presidence here
		if ($what == 'password')
		{
			?>
			<p>Please enter your username or email address.<br/>
			We will send you an email reminder of your password.</p>
			<div class="blue_content_div" id="blue_content_div1">
			<div class="innerdivspacer">
				<form action="<?=$_SERVER['PHP_SELF']?>" name="theform" id="theform" method="post">
					<input type="hidden" name="what" value="<?php echo $what; ?>" />
					<table class="form">
						<tr><td><label for="username">Username:</label></td><td><input type="text" id="username" name="username" size="20" maxlength="50" value="<?php if (isset($_POST['username'])) { echo htmlenc($_POST['username']); } ?>" /></td></tr>
						<tr><td colspan="2">&nbsp;&nbsp;or</td></tr>
						<tr><td><label for="email">Email:</label></td><td><input type="text" id="email" name="email" size="50" maxlength="50" value="<?php if (isset($_POST['email'])) { echo htmlenc($_POST['email']); } ?>" /></td></tr>
					</table>
					[ <a href='javascript:document.theform.submit();'>Request password</a> ]
				</form>
			</div></div>
			<?
		}
		else if ($what == 'username')
		{
			?>
			<p>Please enter your email address.<br/>
			We will send you an email reminder of your username.</p>
			<div class="blue_content_div" id="blue_content_div1">
			<div class="innerdivspacer">
				<form action="<?=$_SERVER['PHP_SELF']?>" name="theform" id="theform" method="post">
					<input type="hidden" name="what" value="<?php echo $what; ?>" />
					<table class="form">
						<tr><td><label for="email">Email:</label></td><td><input type="text" id="email" name="email" size="50" maxlength="50" value="<?php if (isset($_POST['email'])) { echo htmlenc($_POST['email']); } ?>" /></td></tr>
					</table>
					[ <a href='javascript:document.theform.submit();'>Request username</a> ]
				</form>
			</div></div>
			<?php
		}
		else
		{
		}
	}
?>

<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>

<script type="text/javascript">	
	document.theform.username.focus();
</script>
