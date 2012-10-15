<?
include('_header.php');

$input_id = (isset($_GET["id"])) ? $_GET["id"] : '';
?>

Computer Programs<br><br>

<ul>

    <p style='color:red'>This page is currently under development.</p>
<?
/*
if (trim($input_id)) {
	echo "<a href=\"?\">&lt;-- Back</a><br>&nbsp;";
} else {
	echo "This is a list of programs available for download";
}
    echo "<p style='color:red'>Please note that this site and all downloads are not yet ready for public use.</p>";
?>


<div class="blue_content_div" id="apps1">
<div class="innerdivspacer">

<?




//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();



$query = "SELECT * FROM t_app ";
if (trim($input_id)) {
	$query .= "WHERE id='"db_escape_String($input_id)."' ";
}
$query .= "ORDER BY showfirst DESC, name ASC"; 
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

?>
<table>
<?


if ($result) {

	$x = 0;
	//record exists
	while ($r = mysql_fetch_array($result)) {

		if (trim($input_id)) {
			//------------------------------------	
			//Specific App

			$x=1;

			if (file_exists("uploaded/apps/".$r["id"].".jpg"))
			{
				$lic_pic = "/uploaded/apps/".$r["id"].".jpg";
			} else {
				$lic_pic = "/img/nologo.png";
			}

			?>
			<div class="white_content_div_apps_info" id="white<? echo $x; ?>">
			<div class="innerdivspacer">
			<?
			echo "	<img src=\"$lic_pic\" width=375 height=285>\n";
			?>
			</div></div>
			<?

			//echo "<img src=\"$lic_pic\" width=375 height=285 align=right>";
			echo "<p class=\"appstext_info\"><b>".$r["name"]."</b> - [ <a href=\"uploaded/apps/".$r["name"].".exe\">Download</a> ]<br><br>\n";
			echo "<u>Brief:</u><br>".$r["brief"]."<br><br>\n";
			echo "<u>More Info:</u><br>".str_replace("\n","<br>",$r["info"])."</p>\n";

			echo "<br clear=\"all\"><br>";
			echo "<u>Features:</u><br>".$r["features"]."<br><br>\n";
			echo "<u>System Requirements:</u><br>".$r["sysreq"]."<br><br>\n";
			echo "<u>Other Information:</u><br>".$r["other"]."<br><br>\n";
			//echo "<br clear=\"all\">";


		} else {
			//------------------------------------
			//Listing all apps

			$x++;

			if (file_exists("uploaded/apps/t-".$r["id"].".jpg"))
			{
				$lic_pic = "/uploaded/apps/t-".$r["id"].".jpg";
			} else {
				$lic_pic = "/img/nologo.png";
			}


			?>
			<div class="white_content_div_apps" id="white<? echo $x; ?>">
			<div class="innerdivspacer">
			<?
			echo "	<a href=\"?id=".$r["id"]."\"><img src=\"$lic_pic\"  width=\"175\" height=\"137\" border=0></a>\n";
			?>
			</div></div>
			<?


			echo "<p class=\"appstext\"><b>".$r["name"]."</b><br><br>\n";
			echo $r["brief"]."<br><br>\n";
			echo "[ <a href=\"?id=".$r["id"]."\">More Info</a> ] [ <a href=\"uploaded/apps/".$r["name"].".exe\">Download</a> ]</p>";

			?>
			
			<?

			echo "<br clear=all><br><br>\n\n";

			//------------------------------------
		}

	}

} else {

	//no record
	echo "No Applications found in Database at the moment...";
}

//mysql_free_result($result); 
db_freeResult($result);
?>

</table>

</div></div>

</ul>

<br><br>

<script type="text/javascript">

	Rounded("div#apps1","#FFFFFF","#ECECFF");

<?
	if ($x>0) {
		for ($i=1; $i<=$x; $i++) {
			echo "	Rounded(\"div#white$i\",\"#ECECFF\",\"#FFFFFF\"); \n";
		}
	}
	?>


</script>


<?
*/
echo('</ul>');
include('_footer.php');
?>
