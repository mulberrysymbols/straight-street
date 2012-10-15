<?
include('_header.php');

$MaxThumbsPerPage = 70;

$showcart = (isUserLoggedOn() && !isUserAtLeastAdmin()); 

?>

<!--a href="types.php">View Media Types</a><br><br-->
<center>

<div class="blue_content_div" id="gal_toolbar_div">
<div class="innerdivspacer">

	Find : 
	<?
	//===============================================
	$link = db_connect();
	$query = "SELECT * FROM t_media_type;";
	$result = db_runQuery($query);

	if ($result) {
		while ($r = mysql_fetch_array($result)) {

			echo "<input type=\"checkbox\" ";
			if (mb_strtolower($r["caption"])=="symbol") { echo "CHECKED"; }
			echo ">";
			echo $r["caption"]." ";


		}
	}
	db_freeResult($result);
	//===============================================
	?>

	<br/><input id="noRated" type="checkbox">Exclude rated media

	<br><br>
	<form id='searchform' name='searchform' onSubmit="doTagSearch('_<? echo $loggedUser; ?>','<? echo $MaxThumbsPerPage; ?>','thumbs','tagsearch', 0); return false;">
		<input tabindex='1' id="tagsearch" type="text">
		<input tabindex='2' type="submit" value="Search" >
		<input tabindex='3' type="reset" value="Clear Search" onClick="clearSearch()">
		<br>

		Search for : 
		<input tabindex='3' type="radio" id="rdoSearch2" name="searchtype"  CHECKED> <strong>One or more</strong> of these words
		<input tabindex='4' type="radio" id="rdoSearch1" name="searchtype"> <strong>All</strong> of these words
		<input style='display:none' type="radio" id="rdoSearch3" name="searchtype"> <!--Any Letters-->
	</form>
</div></div>

<? if ($showcart) { ?>
<?
$lic_accepted = hasAcceptedCCLic($loggedUser);
if (!$lic_accepted)
{
?>
<div class="green_content_div" id="cart_div2">
<div class="innerdivspacer">
    <span style="display:inline" id='licence_not_accepted'>Cannot download or select as Licence is not accepted</span>
</div></div>
<?
}
else
{
?>
		<!--<table><tr><td id='count'><a href="viewcart.php?what=all"><img src="/img/cart.png" alt="Download Symbols" title="Download Symbols" border="0"></a></td><td><a href="viewcart.php?what=all">Download the entire Mulberry symbol set</a></td></tr></table>-->
      <a href="viewcart.php?what=all"><img src='img/download_all.png' id="cart_div" alt='Download the entire Mulberry symbol set' title='Download the entire Mulberry symbol set'/></a>

<div class="green_content_div" id="cart_div2">
<div class="innerdivspacer">
	<table>
	<tr><td>
		<table><tr><td id='count'><a href="viewcart.php?what=selection"><img src="/img/cart.png" alt="Download Symbols" title="Download Symbols" border="0"></a><br/>(<span id="tdCartItems">0</span>)</td>
                    <td><a href="viewcart.php?what=selection">Download all the symbols you've collected so far</a></td></tr></table>
	</td></tr>
	</table>
</div></div>

<? }} ?>


<br clear="all"><br>

<div class='note1' id='instructions'>&nbsp;</div>
<div class="blue_content_div" id="blue_content_div3">
<div class="innerdivspacer">
	<div id="thumbs">
	</div>
</div></div>

</center>


<br clear="all">

<input type="hidden" id="preview_image_id" value="">
<div id="preview">
	<div id="preview_caption">...</div>
	<img id="preview_image">
	<img id="preview_lic_icon">
	<img id="preview_rated_icon" src="img/pg.png" />
	<!--<img id="preview_spon_icon">-->
	<div id="preview_tags">...
	<?
	if (isUserAtLeastAdmin()) {
	?>
	<br/>[ <a href="javascript:ad_edMedia('preview_image_id');">Edit Media</a> ]
	<?
	}
	?>
	</div>


</div>

<script type="text/javascript">	
	clearSearch();
	document.searchform.tagsearch.focus();
	Rounded("div#gal_toolbar_div","#FFFFFF","#ECECFF");
	Rounded("div#blue_content_div3","#FFFFFF","#ECECFF");

<? if ($showcart) { ?>
	updateGalleryCartNumItems();
	Rounded("div#cart_div2","#FFFFFF","#ECFFEC");
<? } ?>
	
</script>

<?
include('_footer.php');
?>
