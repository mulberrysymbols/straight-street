<?
include('_header.php');
if (isUserAtLeastAdmin()) {

$input_id = $_GET["id"];

if (trim($input_id)) {
?>

Admin - Edit Media

<ul>

<div class="green_content_div" id="apphelp">
<div class="innerdivspacer">

	Use the Gallery to find media, and then select [Edit] to arrive here<br><br>

	Edit Media Licenses or Sponsors<br><br>

	Media does not have to have a sponsor...

</div></div>


<div class="blue_content_div" id="app1">
<div class="innerdivspacer">

<?



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "
SELECT 
	m.id mid,
	m.name mname,
	(m.status_id = 4) AS islive,
	(m.status_id = 5) AS isdeleted,
	(m.rated) AS israted,
	CONCAT('$symbolsPreview', mp.filename) imgpath
FROM 
	t_media m
	INNER JOIN t_media_path mp
		ON mp.mid=m.id
WHERE 
	m.id='".db_escape_string($input_id)."'
	and mp.type='3'
";

	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	if ($result) {
		if ($r = mysql_fetch_array($result)) {

			echo "<b>".htmlenc($r["mname"])."</b> (".$r["mid"].")<br><br>";
			echo "<img src=\"".$r["imgpath"]."\">";

?>


	<br clear="all">
	
	 <p><a href='ad_addMedia.php?cmd=edit&id=<?=$r["mid"]?>'>Edit media details</a></p>

	<p>
	  <input id='emdeleted' type='checkbox' <? if ($r['isdeleted']) echo 'checked'; ?>>Deleted
	  <input type="button" value="Save Change" onClick="media_UpdateDeleted(<?= $input_id; ?>, document.getElementById('emdeleted').checked)">
	</p>

	<p>
	  <input id='emrated' type='checkbox' <? if ($r['israted']) echo 'checked'; ?>>Rated
	  <input type="button" value="Save Change" onClick="media_UpdateRated(<?= $input_id; ?>, document.getElementById('emrated').checked)">
	</p>
    
	License for Media:<br>

	<?
	$query2 = "
	SELECT 
		l.id,
		l.caption,
		licid	 
	FROM 
		t_lic l
                INNER JOIN (SELECT m.licid FROM t_media m WHERE m.id='".db_escape_string($input_id)."') m ON TRUE 
		
	ORDER BY 
		caption ASC";

	//$result2 = mysql_db_query("strstr", $query2);
	$result2 = db_runQuery($query2);
	?>
        <select id="mlic">
        <?
	if ($result2) {
		while ($r2 = mysql_fetch_array($result2)) {
	
			echo "	<option ";
			if ($r2["id"]==$r2["licid"]) { echo "selected='selected'"; }
			echo " value=\"".$r2["id"]."\">".$r2["caption"]."</option> \n";

		}
	}
	?>
	</select>
	<input type="button" value="Save Change" onClick="media_UpdateLic('<? echo $input_id; ?>',document.getElementById('mlic').value)">

	<br><br>

	Sponsor for Media:<br>

	<select id="mspon">
	<option value="null">None</option>
	<?
	$query2 = "
	SELECT 
		s.id,
		s.caption,
		(SELECT m.sponid FROM t_media m WHERE m.id='".db_escape_string($input_id)."' sponid	 
	FROM 
		t_sponsor s
		
	ORDER BY 
		caption ASC";

	//$result2 = mysql_db_query("strstr", $query2);
	$result2 = db_runQuery($query2);

	if ($result2) {
		while ($r2 = mysql_fetch_array($result2)) {
	
			echo "	<option ";
			if ($r2["id"]==$r2["sponid"]) { echo "SELECTED"; }
			echo " value=\"".$r2["id"]."\">".htmlenc($r2["caption"])."</option> \n";

		}
	}
	?>
	</select>
	<input type="button" value="Save Change" onClick="media_UpdateSpon('<? echo $input_id; ?>',document.getElementById('mspon').value)">


<?
		} else {
			echo "No Media found with that ID";

		}
	}
	//mysql_free_result($result); 
	db_freeResult($result);

?>

</div></div>

<script type="text/javascript">	
	Rounded("div#app1","#FFFFFF","#ECECFF");
	//Rounded("div#app2","#FFFFFF","#ECECFF");
	Rounded("div#apphelp","#FFFFFF","#ECFFEC");
</script>

<?
}
}
include('_footer.php');
?>
