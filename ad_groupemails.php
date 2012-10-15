<?
include('_header.php');
if (isUserAtLeastAdmin()) {

?>
<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>

Group Emails

<ul>

<div class="green_content_div" id="contact2">
<div class="innerdivspacer">

	Send a HTML formatted email from "support@" to a group of users with the subject "[<GroupName>] Straight-Street Group Email".<br><br>

	To maintain user email-privacy, all users' are sent an individual email.<br><br>

	<b>Please note:</b>	Keep simple and send a test email first.
</div></div>

	<br>

<div class="blue_content_div" id="contact1">
<div class="innerdivspacer" id="innycontentdiv">

	<br>Contact Group:

	<select id="GroupEmail">
	<option value="X">* Select *</option>
	<?
	$x=0;
	foreach ($ar_authorities as $authLevel => $authLevelName)
	{
	    echo "<option value=\"$authLevel\"> $authLevelName</option>\n";
	}
	?>
    <option value="998">[Garry]</option>
    <option value="999">[Steve]</option>
	</select>
    
<?php /*    <input type="button" value="Open in email client" onClick="checkSendGroupEmail('GroupEmail','GroupEmailBody', true);">*/?>

	<br><br>

	<div >Message to send to Group:
	<a style="float:right;" href="http://docs.cksource.com/CKEditor_3.x/Users_Guide" target="_blank">Editor user's guide (new window)</a></div>

	<div style="clear:both;">
	<textarea id="GroupEmailBody" xrows="10" xcols="40"></textarea>
 	</div><br>

	<input type="button" value="Send Group Email" onClick="checkSendGroupHTMLEmail('GroupEmail','GroupEmailBody');">

</div></div>


<script type="text/javascript">	
	Rounded("div#contact1","#FFFFFF","#ECECFF");
	Rounded("div#contact2","#FFFFFF","#ECFFEC");
    CKEDITOR.replace( 'GroupEmailBody',
            {
                toolbar : 'Full',
                entities : 'false',
                templates_files : ['/editor_templates.js'],
            } );
</script>

<?
}
include('_footer.php');
?>
