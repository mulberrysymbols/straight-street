<?php
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

?>

Media admin

<ul>

<div class="blue_content_div" id="sec1">
<div class="innerdivspacer">

 <ul>
     <li><a href="/ad_addMedia.php?cmd=add">Add Media</a></li>
     <li>
<?
    $dbh = DBCXn::get();
    listMediaEdit($dbh);
?></li>
     <li><a href="/ad_uploadedMedia.php">Uploaded Media</a></li>
     <li><a href="/ad_newMedia.php">Live / non-Live Media</a></li>
 </ul>

</div></div>

<script type="text/javascript">	
	Rounded("div#sec1","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>
