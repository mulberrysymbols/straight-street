<?
include('_header.php');

if (isUserAtLeastAdmin()) {

?>

Admin - New Media<br><br>

<ul>

<?
//mysql_connect() or die ("Problem connecting to DataBase");


?>

<div class="blue_content_div" id="blue_content_div1">
<div class="innerdivspacer">

<script>function setReviewID(rid)
{
	var objReviewID = document.getElementById('currRevIdOnPage');
	objReviewID.value = rid;
}
</script>

Show media in Review: 
<select onChange='setReviewID(this.options[this.selectedIndex].value); update_pop_thumbs_lnl()'>
<?php
$query = 
"SELECT id, name 
FROM t_review 
WHERE status in (2)
ORDER BY id DESC;";
$result = db_runQuery($query);
echo "$result";
if ($result)
{
    $first = '';
	//record exists
	while ($r = mysql_fetch_assoc($result))
    {   
        if ($first == '')
        {
            $first = $r["id"];
        }
		echo "  <option value='".$r["id"]."'>".htmlenc($r["name"])."</option>\n";
    }
}
?>
  <option value=''>[All Media]</option>
</select>
<input type='hidden' id='currRevIdOnPage' value='<?php echo $first;?>'>
<br/><br/>

<div id="media_container">
<!--	some text<br>goes here<br>just to make<br>some space for now
-->
	<table class="sort_livenonlive">
	<tr>
		<th>Non-Live Media</th>
		<th>All Live Media</th>
	</tr>
	<tr>
		<td>
			<div class="white_content_div" id="white_content_div1">
			<div class="innerdivspacer" id="media_nonlive">
		</td>
		<td>
			<div class="white_content_div" id="white_content_div2">
			<div class="innerdivspacer" id="media_live">
		</td>
	</tr>
	</table>

</div>

</div>
</div>

<!-- Update All Divs with correct images now -->
<script>update_pop_thumbs_lnl()</script>

<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
	Rounded("div#white_content_div1","#ECECFF","#FFFFFF");
	Rounded("div#white_content_div2","#ECECFF","#FFFFFF");

</script>

<!--input type="button" value="image 1" onClick="testtesttest();"-->
<!--input type="button" value="all images" onClick="runLoadImageDragDropCode();"->

<br><br>
<!--img id="img_trash" src="/img/trash.jpg"-->

</ul>
<?
}
include('_footer.php');
?>