<?
include('_header.php');
if (isUserAtLeastAdmin()) {

?>

Group Emails

<ul>

<div class="green_content_div" id="contact2">
<div class="innerdivspacer">

	Send an email to a Group of users.<br><br>

	Email is Addressed from "support@".<br><br>

	To maintain user email-privacy, all users' are sent an individual email.<br><br>

	Subject is "[GroupName] Straight-Street Group Email".<br><br>

	<b>Please note:</b><br><br>

	Keep text simple.

</div></div>


<div class="blue_content_div" id="contact1">
<div class="innerdivspacer" id="innycontentdiv">

	Contact Group:
	<br>

	<select id="GroupEmail">
	<option value="X">* Select *</option>
	<?
	$x=0;
	foreach ($ar_authorities as $authLevel => $authLevelName)
	{
	    echo "<option value=\"$authLevel\"> $authLevelName</option>\n";
	}
	?>
	</select>
    
    <input type="button" value="Open in email client" onClick="checkSendGroupEmail('GroupEmail','GroupEmailBody', true);">

	<br><br>

	Message to send to Group:<br>

	<textarea id="GroupEmailBody" rows="10" cols="40"></textarea>

	<br><br>

	<input type="button" value="Send Group Email" onClick="checkSendGroupEmail('GroupEmail','GroupEmailBody', false);">


</div></div>


<script type="text/javascript">	
	Rounded("div#contact1","#FFFFFF","#ECECFF");
	Rounded("div#contact2","#FFFFFF","#ECFFEC");
</script>

<?
}
include('_footer.php');
?>
