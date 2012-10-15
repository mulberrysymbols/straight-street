<?
include('_header.php');
if (isUserAtLeastAdmin()) {

$input_id = (isset($_GET["id"])) ? $_GET["id"] : '';
?>

Edit Applications

<ul>

<div class="green_content_div" id="apphelp">
<div class="innerdivspacer">

	Each application should have a thumbnail image, and large image<br><br>

	<b>Thumbnail</b> image should be stored in /uploaded/apps/t-<u>ID</u>.jpg.<br><br>

	<b>Larger</b> image should be stored in /uploaded/apps/<u>ID</u>.jpg.<br><br>

	(where <u>ID</u> is the record ID in the Database)<br><br>
	
	Keep text simple.

</div></div>

<?



	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "SELECT * FROM t_app WHERE id='".db_escape_string($input_id)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	$item0 = "";
	$item1 = "";
	$item2 = "";
	$item3 = "";
	$item4 = "";
	$item5 = "";
	$item6 = "";

	if ($result) {
		if ($r = mysql_fetch_array($result)) {
			$item0 = $r["id"];
			$item1 = $r["name"];
			$item2 = $r["brief"];
			$item3 = $r["info"];
			$item4 = $r["features"];
			$item5 = $r["sysreq"];
			$item6 = $r["other"];
			//$item1 = $r[""];

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
		echo "Currently Editing<br>Application ID ".$input_id."<br>[ <a href=\"?\">Add New</a> ]";
	} else {
		echo "Add new<br>Application";
	}
	?>
	</p>

	<input type="hidden" id="appid" value="<? echo $item0; ?>">

	Application Name:<br>
	<input type="text" id="appname" value="<? echo $item1; ?>"><br><br>

	Brief:<br>
	<input type="text" id="appbrief" value="<? echo $item2; ?>"><br><br>

	More Info:<br>
	<textarea id="appinfo" rows="5" cols="38"><? echo $item3; ?></textarea><br><br>

	Features:<br>
	<textarea id="appfeat" rows="5" cols="38"><? echo $item4; ?></textarea><br><br>

	System Requirements:<br>
	<textarea id="appsysreq" rows="5" cols="38"><? echo $item5; ?></textarea><br><br>

	Other Info:<br>
	<textarea id="appother" rows="5" cols="38"><? echo $item6; ?></textarea><br><br>

	<input type="button" value="Save Information" onClick="addNewApp('appid','appname','appbrief','appinfo','appfeat','appsysreq','appother');">

	<?
	if (trim($input_id)) {
		?>
		<input type="button" value="Delete Application" onClick="delApp('','appid');">
		<?
	} 
	?>

</div></div>

<br>

Current Applications
<div class="blue_content_div" id="app2">
<div class="innerdivspacer">

	<table>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>Brief</th>
		<th>More Info</th>
		<th>Thumbnail found</th>
		<th>Image found</th>
		<th>&nbsp;</th>
	</tr>

	<?
	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "SELECT * FROM t_app;";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	if ($result) {
		while ($r = mysql_fetch_array($result)) {

		//echo "|/uploaded/apps/t-".$r["id"].".jpg|";

		if (file_exists("uploaded/apps/t-".trim($r["id"]).".jpg")) {
			$foundImage1str = "<font color=\"#008800\">Found</font>";
		} else {
			$foundImage1str = "<font color=\"#FF0000\">Not Found</font>";
		}

		if (file_exists("uploaded/apps/".trim($r["id"]).".jpg")) {
			$foundImage2str = "<font color=\"#008800\">Found</font>";
		} else {
			$foundImage2str = "<font color=\"#FF0000\">Not Found</font>";
		}

	?>

	<tr>
		<td><? echo $r["id"];?></td>
		<td><? echo $r["name"];?></td>
		<td><? echo mb_substr($r["brief"],0,20)."...";?></td>
		<td><? echo mb_substr($r["info"],0,20)."...";?></td>
		<td><? echo $foundImage1str;?></td>
		<td><? echo $foundImage2str;?></td>
		<td>[ <a href="?id=<? echo $r["id"]; ?>">Edit</a> ]</td>
		<td>[ <a href="javascript:delApp('<? echo $r["id"]; ?>','')">Delete</a> ]</td>




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
