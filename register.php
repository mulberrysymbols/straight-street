<?
include('_header.php');

$input_email = $_GET["email"];
$input_actcode = $_GET["code"];

$takeEmail = 0;
if ($input_email && $input_actcode) {
	//only allow the email to be auto-used if there is an act code with it (in the url)
	$takeEmail = 1;
}


?>

<script>document.getElementById('header_toolbar').style.display='none';</script>

	<div class="green_content_div" id="list_of_reviewers">
	<div class="innerdivspacer">

	<b>Registration</b><br>

	<ul>
	<li>Send activation code to email</li>
	<li>Activate User Account</li>
	<li>Enter User Account details</li>
	<li>Ready to login!</li>

	</ul>

	</div>
	</div>
Register

<ul>
<input type="hidden" id="dbEmail" value="<? if ($takeEmail) { echo "$input_email"; } ?>">
<input type="hidden" id="dbActCode" value="<? if ($takeEmail) { echo "$input_actcode"; } ?>">

<div id="section1">

	<h2>1.</h2>
	Enter your email address<br>

	<div class="blue_content_div" id="register1">
	<div class="innerdivspacer" id="regemailinner">

	<?
	if ($takeEmail) {
		echo "Email Address : $input_email<br>[If this is not your email address, please <a href='/register.php'>click here</a>]";

	} else {
	?>
		<input id="txtEmail"> ( <a href="javascript:reg_sendActCodeToEmail('txtEmail','dbEmail','regemailinner');">Send Activation Code</a> )
	<?
	} 
	?>

	</div></div>

</div>

<div id="section2" style="display:none;">

	<h2>2.</h2>
	Enter the activation code<br>

	<div class="blue_content_div" id="register2">
	<div class="innerdivspacer" id="actcodeinner">

	<?
	if ($takeEmail) {
		//try to auto-activate
		echo "Code Entered<br>";
		echo "<script>reg_checkActCode('dbActCode','dbEmail','actcodeinner')</script>";


	} else {
	?>
		<input id="txtAct"> ( <a href="javascript:reg_checkActCode('txtAct','dbEmail','actcodeinner');">Check Activation Code</a> )
	<?
	} 
	?>

	</div></div>

</div>

<div id="section3" style="display:none;">

	<h2>3.</h2>
	Success! Please pick a Username for your Account<br>

	<div class="blue_content_div" id="register3">
	<div class="innerdivspacer" id="actcodeinner">

	<input type="hidden" id="newUIDSetValue" value="">

	<table class="regusernamepass">
	
	<tr id="RowCurrUID">	<td>Current&nbsp;Username</td>
		<td colspan="2"><div id="currUID">&nbsp;</div></td>
	</tr>
	<tr id="RowNewUID">	<td>Username</td>
		<td><input id="newUID" type="text" maxlength="15"></td>
		<td><input type="button" value="Set Username" onClick="reg_checkNewUid('newUID','newUIDSetValue','newUIDMsg','dbEmail');"></td>
	</tr>
	<tr>	<td>&nbsp;</td>
		<td><div id='newUIDMsg'></div></td>
	</tr>

	</table>	

	</div></div>

	<br><br>

	Please pick a password to use.<br>

	<div class="blue_content_div" id="register3b">
	<div class="innerdivspacer" id="actcodeinner">

	<table class="regusernamepass">
	<tr>	<td>Password</td>
		<td><input id="newPass1" type="password" maxlength="15"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>	<td>Retype Password</td>
		<td><input id="newPass2" type="password" maxlength="15"></td>
		<td><input type="button" value="Set Password" onClick="reg_checkNewPass('newPass1','newPass2','newUIDSetValue','newPassMsg','dbEmail');"></td>
	</tr>
	<tr>	<td>&nbsp;</td>
		<td colspan="2"><div id='newPassMsg'></div></td>
	</tr>

	</table>	

	</div></div>

</div>

<!-- is this needed? isnt it run anyway from the ajax? -->
<script>
	reg_getCurrUID('dbEmail','currUID','newUIDSetValue');
	reg_checkIfNeedInputNewUID('dbEmail','RowNewUID','RowCurrUID');
</script>



<div id="section4" style="display:none;">

	<h2>4.</h2>
	Success!<br>

	<div class="blue_content_div" id="register4">
	<div class="innerdivspacer" id="actcodeinner">

	You are now ready to Login.<br><br>

	Login in using the form at the top of the page. <br><br>Then add extra details to your account by going to
	<a href="/userinfo.php">My Info</a>

	</div></div>

</div>

<script type="text/javascript">
	Rounded("div#list_of_reviewers","#FFFFFF","#ECFFEC");
	Rounded("div#register1","#FFFFFF","#ECECFF");
	//Rounded("div#register2","#FFFFFF","#ECECFF");
</script>


</ul>
<?
include('_footer.php');
?>