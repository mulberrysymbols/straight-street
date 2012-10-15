<?
include('_header.php');
?>

User Info<br><br>

<ul>

<?
//==================================================
if (!isUserLoggedOn()) {
	echo "<font color='#FF0000'>You must be logged in to view this page</font>";
} else {
//==================================================


$clean = array();

   function validateForm(&$error)
    {
        if (mb_strlen($_POST['fname']) < 1  /*or !ctype_alpha($_POST['fname'])*/)
            $error[] = 'Please enter a Firstname using letters.';
        if (mb_strlen($_POST['sname']) < 1  /*or !is_str_surname($_POST['sname'])*/)
            $error[] = 'Please enter a Surname using letters and - and \'.';
        $pwlen = mb_strlen($_POST['password']);
        if ($pwlen > 0)
        {
            if ($pwlen < 4)
                $error[] = 'Please enter a Password of at least 4 characters. Please re-enter';		// should we test pwds more, we do want to allow strong pwds
        elseif ($_POST['password'] <> $_POST['password2'])
                $error[] = 'The second password does not match the first. Please re-enter.';
        }
        /*if ($_POST['email'] =='' || !is_valid_email_address($_POST['email']))
            $error[] = 'Please enter a valid email address.';
        if (checkExists('t_user', 'email', $_POST['email']))
            $error[] = 'Sorry that email is already in use.';*/
    	return count($error) == 0;
    }

    db_connect();
	$error = array();
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        
        $clean['fname'] = isset($_POST['fname']) ? $_POST['fname'] : '';
        $clean['sname'] = isset($_POST['sname']) ? $_POST['sname'] : '';
        $clean['role'] = isset($_POST['role']) ? $_POST['role'] : '';
        $clean['email'] = isset($_POST['email']) ? $_POST['email'] : '';
        $clean['password'] = isset($_POST['password']) ? $_POST['password'] : '';
        $clean['cancontact'] = isset($_POST['cancontact']) ? '1' : '0';
		if (validateForm($error)) 
		{
            $pwd_clause = 
            
            $sql = sprintf("UPDATE t_user SET fname='%s', sname='%s', role='%s', cancontact=%s %s WHERE username = '%s';",
                    db_escape_string($clean['fname']),
                    db_escape_string($clean['sname']),
                    db_escape_string($clean['role']),
                    db_escape_string($clean['cancontact']),
                    ($clean['password']) ? ",pass='".db_escape_string(md5($clean['password']))."'" : "",
                    db_escape_string($loggedUser));
			$rs = db_runQuery($sql) or die('Error: '.mysql_error());
            $msg = "Your info has been saved.".(($clean['password']) ? " You new password has been set." : "");
            
        }
    }
    else
    {
    }

    $query = "SELECT id, username, datereg, fname, sname, role, email, cancontact, authcode FROM t_user WHERE username='".db_escape_string($loggedUser)."';";
    $rs = db_runQuery($query) or die(mysql_error());
    if (mysql_num_rows($rs) == 1)
    {
        $row = mysql_fetch_assoc($rs);
        $clean['id'] = htmlenc($row['id']);
        $clean['username'] = htmlenc($row['username']);
        $clean['datereg'] = htmlenc($row['datereg']);
        $clean['authcode'] = htmlenc($row['authcode']);
    	if (!count($error))
        {
            $clean['fname'] = htmlenc($row['fname']);
            $clean['sname'] = htmlenc($row['sname']);
            $clean['role'] = htmlenc($row['role']);
            $clean['cancontact'] = htmlenc($row['cancontact']);
            $clean['email'] = htmlenc($row['email']);
        }
    }
    else {
        $error[] = "An unexpected error has occured.<br/>Please contact us at $addr_bare";
    }
	if (count($error) || $msg)
	{
		print '<div class="formError"><p>';
        if ($msg) { print($msg); }
		foreach ($error as $err)
		{
			print $err.'<br/>';
		}
		print '</p></div>';
	}
?>
<div class="blue_content_div" id="blue_content_div1">
<div class="innerdivspacer" >
    <form  action="<?=$_SERVER['PHP_SELF']?>" name="infoform" id ="infoform" method="post">
	<table class="form">
		<tr>
			<td><label for="fname">Forename:</label></td>
			<td><input type="text" id="fname" name="fname" size="20" maxlength="20" value="<?=$clean['fname']?>" tabindex="1"/></td>
		</tr>
		<tr>
			<td><label for="sname">Surname:</label></td>
			<td><input type="text" id="sname" name="sname" size="20" maxlength="20" value="<?=$clean['sname']?>" tabindex="2"/></td>
		</tr>
		<tr>
			<td><label for="role">Role:</label></td>
			<td><input type="text" id="role" name="role" size="40" maxlength="40" value="<?=$clean['role']?>" tabindex="3"/></td>
		</tr>
		<tr>
			<td><label for="password">Password:</label></td>
			<td><input type="password" id="password" name="password" size="20" maxlength="20" value="" tabindex="4"/></td>
		</tr>
        <tr>
			<td><label for="password2">Confirm password:</label></td>
			<td><input type="password" id="password2" name="password2" size="20" maxlength="20" value="" tabindex="5"/></td>
		</tr>
        <tr>
			<td><label for="email">Email:</label></td>
			<td><?=$clean['email']?></td>
        </tr>
        <tr>
            <? /*$auth = ($clean['authcode'] == '');
               if (!$auth)
               {?>
			<td></td><td><div class='formError'>You have not yet confirmed your email address.<br/>Please look for the email we sent you when you registered and click the link in it.</div></td>.
               <? } else */{ $check = ($clean['cancontact']) ? 'checked="checked"' : ''; ?>
			<td></td><td><input type="checkbox" name="cancontact" id="cancontact" <?=$check?>>From Time to time we will send an email whenever new symbols are added.
            <br/>Please untick the box if you don't wish to receive any communication from us.</td>
            <?}?>
        </tr>
	</table>
	<input type='submit' value='Save' tabindex="6" /> 
    </form>
	</div></div>


<div class="clearer">&nbsp;</div>
<br><br>

    <div class="blue_content_div" id="blue_content_div2">
	<div class="innerdivspacer">
	<?
	echo "UserID: ".htmlenc($clean["id"])."<br>";
	echo "Username: ".htmlenc($clean["username"])."<br>";
	$daysMember = (datediff('d', $clean["datereg"], date("j F Y"), false) + 1);
	echo "Member since: ".$clean["datereg"]." (".$daysMember." days)<br>";
	echo "Authorities:";
    $ar = getUserAuthorities($clean["id"]);
    echo(implode(' ', $ar));

	?>
	</div></div>
    

<?
//mysql_free_result($result); 
db_freeResult($result);
?>


</ul>

<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
	Rounded("div#blue_content_div2","#FFFFFF","#ECECFF");
</script>

<?
}
include('_footer.php');
?>