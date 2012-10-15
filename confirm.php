<?php
$_in_account_page=True;
require('_header.php');

	if ($_GET['ID'] != '' && is_str_uint($_GET['ID']) 
		&& mb_strlen($_GET['key']) == 15 && ctype_alnum($_GET['key']))
	{
		db_connect();

		$sql = "SELECT ID, cancontact FROM t_user WHERE ID = '".db_escape_string($_GET['ID'])."'"
				." AND authcode = '".db_escape_string($_GET['key'])."'";
		$rs = db_runQuery($sql) or die(mysql_error());
		if(mysql_num_rows($rs) == 1)
		{
			$row = mysql_fetch_assoc($rs);
/*			if($row['authcode'] == '')
			{
				$msg = '<p>This account already has an confirmed email address.</p>';
			}
			else*/
			{
				$update = db_runQuery("UPDATE t_user SET authcode='', cancontact=1 WHERE ID='".db_escape_string($row['ID'])."'") or die(mysql_error());
				$msg = '<p>Your email confirmation is now complete and you will receive occaisional notification emails from us.</p>';
			}
		}
		else {
		
			$msg = "<p>Could not complete email confirmation.</p><p>Please <a href='/contact.php?subject=Email confirmation %20issue'>contact us</a></p>";
		}
	}
	else 
	{
		$msg = "<p>Could not complete email confirmation as the link appears to be invalid. Please <a href='/contact.php?subject=Activation%20link%20issue'>contact us</a>.</p>";
	}

	echo $msg;
?>

<?
include('_footer.php');
?>
