<?
include('_header.php');
if (isUserAtLeastAdmin()) {

$input_id = $_GET["id"];
?>

Edit Sponsors

<ul>

<div class="green_content_div" id="apphelp">
<div class="innerdivspacer">

	Each Sponsor should have a thumbnail image<br><br>

	<b>Thumbnail</b> image should be stored in /uploaded/spon/t-<u>ID</u>.jpg.<br><br>

	(where <u>ID</u> is the record ID in the Database)<br><br>
	
	Keep text simple.

</div></div>

<?



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "SELECT * FROM t_sponsor WHERE id='".db_escape_string($input_id)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	$item0 = "";
	$item1 = "";
	$item2 = "";
	$item3 = "";

	if ($result) {
		if ($r = mysql_fetch_array($result)) {
			$item0 = $r["id"];
			$item1 = $r["caption"];
			$item2 = $r["brief"];
			$item3 = $r["url"];
		}
	}
	//mysql_free_result($result); 
	db_freeResult($result);

?>


<div class="blue_content_div" id="app1">
<div class="innerdivspacer">

	<p style="float:right; text-align:right">
	<?
	if (trim($input_id)) {
		echo "Currently Editing<br>Sponsor ID ".$input_id."<br>[ <a href=\"?\">Add New</a> ]";
	} else {
		echo "Add new<br>Sponsor";
	}
	?>
	</p>

	<input type="hidden" id="appid" value="<? echo $item0; ?>">

	Sponsor Name:<br>
	<input type="text" id="appname" value="<? echo $item1; ?>"><br><br>

	Sponsor Brief:<br>
	<input type="text" id="appbrief" value="<? echo $item2; ?>"><br><br>

	Sponsor URL:<br>
	<input type="text" id="appurl" value="<? echo $item3; ?>"><br><br>

	<input type="button" value="Save Information" onClick="addNewSpon('appid','appname','appbrief','appurl');">

	<?
	if (trim($input_id)) {
		?>
		<input type="button" value="Delete Sponsor" onClick="delSpon('','appid');">
		<?
	} 
	?>

</div></div>

<br clear="all">

Current Sponsors
<div class="blue_content_div" id="app2">
<div class="innerdivspacer">

	<table>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>Brief</th>
		<th>URL</th>
		<th>Thumbnail found</th>
		<th>&nbsp;</th>
	</tr>

	<?
	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "SELECT * FROM t_sponsor;";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	if ($result) {
		while ($r = mysql_fetch_array($result)) {

		if (file_exists("uploaded/spon/t-".trim($r["id"]).".jpg")) {
			$foundImage1str = "<font color=\"#008800\">Found</font>";
		} else {
			$foundImage1str = "<font color=\"#FF0000\">Not Found</font>";
		}

	?>

	<tr>
		<td><? echo $r["id"];?></td>
		<td><? echo $r["caption"];?></td>
		<td><? echo mb_substr($r["brief"],0,20)."...";?></td>
		<td><? echo mb_substr($r["url"],0,20)."...";?></td>
		<td><? echo $foundImage1str;?></td>
		<td>[ <a href="?id=<? echo $r["id"]; ?>">Edit</a> ]</td>
		<td>[ <a href="javascript:delSpon('<? echo $r["id"]; ?>','')">Delete</a> ]</td>
	</tr>


	<?
		}
	}
	//mysql_free_result($result); 
	db_freeResult($result);
	?>
	</table>

</div></div>


<script type="text/javascript">	
	Rounded("div#app1","#FFFFFF","#ECECFF");
	Rounded("div#app2","#FFFFFF","#ECECFF");
	Rounded("div#apphelp","#FFFFFF","#ECFFEC");
</script>

<?
}
include('_footer.php');
?>
