<?
include('_header.php');

$subject = (isset($_GET["subject"])) ? $_GET["subject"] : 'General Question';
?>

Contact

<ul>

<div class="green_content_div" id="contact2">
<div class="innerdivspacer">

	Contact details:
	<p>
    <?= str_replace("\n", '<br>', $contact_addr) ?>
	</p>
    <p>
    <?= $contact_tel ?>
	</p>
    <p>
	[ <a href="mailto:support@something.com>?subject=Support%20email%20contact">support@something.com></a> ]
    </p>

</div></div>


<div class="blue_content_div" id="contact1">
<div class="innerdivspacer" id="innycontentdiv">
	Your name :<br>
	<input type="text" id="contactName">

	<br><br>

	Your email address :<br>
	<input type="text" id="contactEmail">

	<br><br>

    Subject: <? echo $subject; ?>
    <input type="hidden" id="contactSubject" value="<? echo $subject; ?>">
    <br><br>
	
    Your Comments :<br>
	<textarea id="contactComments" rows="10" cols="38" maxlength="200"></textarea>

	<br><br>

	<input type="checkbox" id="contactAllow"> Allow moderated comments / feedback to be posted<br> on this website for public viewing
    
	<br><br>

	<input type="button" value="Send Comments" onClick="checkSendContactsEmail('innycontentdiv','contactName','contactEmail','contactComments','contactAllow', 'contactSubject');">

</div></div>


<script type="text/javascript">	
	Rounded("div#contact1","#FFFFFF","#ECECFF");
	Rounded("div#contact2","#FFFFFF","#ECFFEC");
	//Rounded("div#blue_content_div2","#FFFFFF","#ECECFF");
	//Rounded("div#blue_content_div3","#FFFFFF","#ECECFF");
	//Rounded("div#blue_content_div4","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>
