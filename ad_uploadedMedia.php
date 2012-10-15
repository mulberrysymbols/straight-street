<?
include('_header.php');
if (isUserAtLeastAdmin()) {

$input_revid = isset($_GET["id"]) ? $_GET["id"] : '';
echo "<input type='hidden' id='currRevIdOnPage' value='$input_revid'>";
$objectCounter = 0;
?>

Admin - Uploaded Media<br><br>

<ul>

<div class="blue_content_div" id="blue_content_div1" style="width:400px;">
<div class="innerdivspacer">
	This page inserts all newly uploaded media into the Database to a non-live record with status Dev<br><br>

	Automated part:<br>
	1 - find all wmf objects in uploaded directory to create unique local media object (eg car.wmf)<br>
	2 - find corresponding images to link to (eg t-car.gif, m-car.gif, car.gif)<br>
	3 - merge into one object ready for approval<br><br>

	Manual part:<br>	
	1 - Drag elements into media object (eg large, small, thumbnail)<br>
	2 - accept and add items into the DB<br>
	3 - Create non-wmf unique media object (eg wav)<br><br>
</div></div>

<br>

Symbols (by WMF)

<div class="blue_content_div" id="blue_content_div2">
<div class="innerdivspacer">

<table class="uploadedmedia">

<?
//=====================================================================
//Init
//=====================================================================
$strDisplayFound = "Y";
$strDisplayNotFound = "N";
$strNonLiveMedia = "media/unsorted/";
$strReplaceSpaceWithThis = "_";


//=====================================================================
//Read through contents of the dir and put into an array
//=====================================================================
if ($dh = opendir($strNonLiveMedia)) {
	$files = array();
	while (($file = readdir($dh)) !== false) {
		if (mb_substr($file,-4)==".wmf") {
			//array_push($files, str_Replace(" ","$strReplaceSpaceWithThis",$file));
			array_push($files,$file);
		}
	}
	closedir($dh);
}

db_connect();
$query = "SELECT * FROM t_lic;";
$result = db_runQuery($query);
$lics = array();
if ($result) {
	while ($r = mysql_fetch_assoc($result))
	{
		$lics[$r["id"]] = $r["caption"];
	}
}
db_freeResult($result);

function file_to_symbol_name($file)
{
	global $strReplaceSpaceWithThis;
	return str_replace(' ', $strReplaceSpaceWithThis, mb_substr($file,0,-4));
}

$symbols=array();
foreach ($files as $file)
{
	$symbols[] = file_to_symbol_name($file);
}
$escaped_symbols = @array_map(db_escape_string, $symbols); // need to suppress notify for db_escape_string

db_connect();
$symbols_clause =  "m.name IN ('".implode("','", $escaped_symbols)."')";
$query = "SELECT name, IF(status_id = 1, '1', '0') as can_import FROM t_media AS m WHERE $symbols_clause AND m.mtype = 1;";
$result = db_runQuery($query);
$db_symbols = array_fill_keys(array_values($symbols), -1);	// not in db
if ($result) {
	while ($r = mysql_fetch_assoc($result))
	{
		$db_symbols[$r['name']] = $r['can_import'];
	}
}
db_freeResult($result);
//=====================================================================
//sort the array
//=====================================================================
natsort($files);


//=====================================================================
//output the array
//=====================================================================


foreach ($files as $file) {

//
//}
//
//if ($handle = opendir($strNonLiveMedia)) {
//	//echo "Directory handle: $handle\n";
//	//echo "Files:\n";
//
//	/* This is the correct way to loop over the directory. */
//	while (false !== ($file = readdir($handle))) {
//		//if ((is_dir($file)) && ($file <>"..") &&($file<>".")) { 
//
//		if (mb_substr($file,-4)==".wmf") {
//
			$objectCounter++;

			echo "
			<tr>
			<td>$file<br>
			<img class=\"uploadedmedia\"src='".$strNonLiveMedia."t-".mb_substr($file,0,-4).".gif'>
			";

			
			$fileexist1 = file_exists($strNonLiveMedia.$file);
			$fileexist2 = file_exists($strNonLiveMedia."t-".mb_substr($file,0,-4).".gif");
			$fileexist3 = file_exists($strNonLiveMedia."m-".mb_substr($file,0,-4).".gif");
			//$fileexist4 = file_exists($strNonLiveMedia.mb_substr($file,0,-4).".gif");

			$strfileexist1 = "<font color=\"#FF0000\">$strDisplayNotFound</font>";
			if ($fileexist1) {
				$strfileexist1 = "<font color=\"#008800\">$strDisplayFound</font>";
			}
			$strfileexist2 = "<font color=\"#FF0000\">$strDisplayNotFound</font>";
			if ($fileexist2) {
				$strfileexist2 = "<font color=\"#008800\">$strDisplayFound</font>";
			}
			$strfileexist3 = "<font color=\"#FF0000\">$strDisplayNotFound</font>";
			if ($fileexist3) {
				$strfileexist3 = "<font color=\"#008800\">$strDisplayFound</font>";
			}

			?>

			</td>

			<td>
			WMF:<? echo $strfileexist1; ?><br>
			t-GIF:<? echo $strfileexist2; ?><br>
			m-GIF:<? echo $strfileexist3; ?><br>
			</td>

			<td><center>
			<?
			if ($fileexist1 && $fileexist2 && $fileexist3) {
				echo "<font color=\"#008800\">Set Complete</font>";
			} else {
				echo "<font color=\"#FF0000\">Set Incomplete</font>";
			}
			?>
			</center></td>

			<td>
			<?
			if ($fileexist1 && $fileexist2 && $fileexist3) {
			?>	
			<select id="cbo<? echo $objectCounter; ?>">
			<!--<option value="">* Select</option>-->
			<?
				foreach ($lics as $id => $caption)
				{
				?>
				<option value="<? echo $id; ?>"><? echo $caption; ?></option>
				<?
				}
				//mysql_free_result($result); 
				db_freeResult($result);
			?>
			</select>
			<?
			} else {
				echo "&nbsp;";
			}
			?>
			</td>

			<td id="td<? echo $objectCounter; ?>"><center>
			<?
			if ($fileexist1 && $fileexist2 && $fileexist3) 
			{	
				//TODO fix this mess
				$sym_status = $db_symbols[file_to_symbol_name($file)];
				if ($sym_status == 1)
 				  $str = "[ <a href=\"javascript:importSymToDB('".mb_substr($file,0,-4)."',document.getElementById('cbo$objectCounter').value,'$objectCounter','$strReplaceSpaceWithThis')\">Import images</a> ]";
				elseif ($sym_status == 0)
				  $str = "Symbol is live";
				else
				  $str = "No such symbol";
				echo $str;
			} else {
				echo "&nbsp;";
			}
			?>
			</center></td>

			</tr>
			<?
//		}
//		//}
//	}
//
//	closedir($handle);
}
?>
</table>
</div></div>

<br>

Photos (by JPG)
<div class="blue_content_div" id="blue_content_div3">
<div class="innerdivspacer">

<table class="uploadedmedia">

<?

//$strNonLiveMedia = "media/unsorted/";

if ($handle = opendir($strNonLiveMedia)) {
	//echo "Directory handle: $handle\n";
	//echo "Files:\n";

	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		//if ((is_dir($file)) && ($file <>"..") &&($file<>".")) { 

		if (mb_substr($file,-4)==".jpg") {

			$objectCounter++;

			echo "
			<tr>
			<td>$file<br>
			<img class=\"uploadedmedia\"src='".$strNonLiveMedia."t-".mb_substr($file,0,-4).".gif'>
			";

			
			$fileexist1 = file_exists($strNonLiveMedia.$file);
			$fileexist2 = file_exists($strNonLiveMedia."t-".mb_substr($file,0,-4).".jpg");
			$fileexist3 = file_exists($strNonLiveMedia."m-".mb_substr($file,0,-4).".jpg");


			$strfileexist1 = "<font color=\"#FF0000\">$strDisplayNotFound</font>";
			if ($fileexist1) {
				$strfileexist1 = "<font color=\"#008800\">$strDisplayFound</font>";
			}
			$strfileexist2 = "<font color=\"#FF0000\">$strDisplayNotFound</font>";
			if ($fileexist2) {
				$strfileexist2 = "<font color=\"#008800\">$strDisplayFound</font>";
			}
			$strfileexist3 = "<font color=\"#FF0000\">$strDisplayNotFound</font>";
			if ($fileexist3) {
				$strfileexist3 = "<font color=\"#008800\">$strDisplayFound</font>";
			}


			?>

			</td>

			<td>
			JPG : <? echo $strfileexist1; ?><br>
			t-JPG : <? echo $strfileexist2; ?><br>
			m-JPG : <? echo $strfileexist3; ?><br>
			</td>

			<td><center>
			<?
			if ($fileexist1 && $fileexist2 && $fileexist3) {
				echo "<font color=\"#008800\">Set Complete</font>";
			} else {
				echo "<font color=\"#FF0000\">Set Incomplete</font>";
			}
			?>
			</center></td>

			<td>
			<?
			if ($fileexist1 && $fileexist2 && $fileexist3) {
			?>	
			<select id="cbo<? echo $objectCounter; ?>">
			<option value="">* Select</option>
			<?
				//mysql_connect() or die ("Problem connecting to DataBase");
				db_connect();
				$query = "SELECT * FROM t_lic;";
				//$result = mysql_db_query("strstr", $query);
				$result = db_runQuery($query);
				if ($result) {
					while ($r = mysql_fetch_array($result)) {
						?>
						<option value="<? echo $r["id"]; ?>"><? echo $r["caption"]; ?></option>
						<?
					}
				}
				//mysql_free_result($result); 
				db_freeResult($result);
			?>
			</select>
			<?

			} else {
				echo "&nbsp;";
			}
			?>
			</td>

			<td><center>
			<?
			if ($fileexist1 && $fileexist2 && $fileexist3) {
				echo "[ <a href=\"#\">Import to DB</a> ]";
			} else {
				echo "&nbsp;";
			}
			?>
			</center></td>

			</tr>
			<?
		}
		//}
	}

	closedir($handle);
}
?>
</table>
</div></div>

<br>

Sounds (by Wav)
<div class="blue_content_div" id="blue_content_div4">
<div class="innerdivspacer">

<table class="uploadedmedia">

<?

//$strNonLiveMedia = "media/unsorted/";

if ($handle = opendir($strNonLiveMedia)) {
	//echo "Directory handle: $handle\n";
	//echo "Files:\n";

	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		//if ((is_dir($file)) && ($file <>"..") &&($file<>".")) { 

		if (mb_substr($file,-4)==".wav") {

			$objectCounter++;

			echo "
			<tr>
			<td>$file<br>
			<img class=\"uploadedmedia\" src='img/ico_wav.jpg'>
			";

			
			?>

			</td>

			<td>
			&nbsp;
			</td>

			<td>&nbsp;</td>

			<td>
			<select id="cbo<? echo $objectCounter; ?>">
			<option value="">* Select</option>
			<?
				//mysql_connect() or die ("Problem connecting to DataBase");
				db_connect();
				$query = "SELECT * FROM t_lic;";
				//$result = mysql_db_query("strstr", $query);
				$result = db_runQuery($query);
				if ($result) {
					while ($r = mysql_fetch_array($result)) {
						?>
						<option value="<? echo $r["id"]; ?>"><? echo $r["caption"]; ?></option>
						<?
					}
				}
				//mysql_free_result($result); 
				db_freeResult($result);
			?>
			</select>
			</td>

			<td><center>
			<?
			
				echo "[ <a href=\"#\">Import to DB</a> ]";
			
			?>
			</center></td>

			</tr>
			<?
		}
		//}
	}

	closedir($handle);
}
?>
</table>
</div></div>

<br>

Sounds (by mp3)
<div class="blue_content_div" id="blue_content_div4">
<div class="innerdivspacer">

<table class="uploadedmedia">

<?

//$strNonLiveMedia = "media/unsorted/";

if ($handle = opendir($strNonLiveMedia)) {
	//echo "Directory handle: $handle\n";
	//echo "Files:\n";

	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		//if ((is_dir($file)) && ($file <>"..") &&($file<>".")) { 

		if (mb_substr($file,-4)==".mp3") {

			$objectCounter++;

			echo "
			<tr>
			<td>$file<br>
			<img class=\"uploadedmedia\" src='img/ico_mp3.jpg'>
			";

			
			?>

			</td>

			<td>
			&nbsp;
			</td>

			<td>&nbsp;</td>
			
			<td>
			<select id="cbo<? echo $objectCounter; ?>">
			<option value="">* Select</option>
			<?
				//mysql_connect() or die ("Problem connecting to DataBase");
				db_connect();
				$query = "SELECT * FROM t_lic;";
				//$result = mysql_db_query("strstr", $query);
				$result = db_runQuery($query);
				if ($result) {
					while ($r = mysql_fetch_array($result)) {
						?>
						<option value="<? echo $r["id"]; ?>"><? echo $r["caption"]; ?></option>
						<?
					}
				}
				//mysql_free_result($result); 
				db_freeResult($result);
			?>
			</select>
			</td>

			<td><center>
			<?
			
				echo "[ <a href=\"#\">Import to DB</a> ]";
			
			?>
			</center></td>

			</tr>
			<?
		}
		//}
	}

	closedir($handle);
}
?>
</table>
</div></div>


</ul>

<script type="text/javascript">
	//if(!NiftyCheck()) return;
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
	Rounded("div#blue_content_div2","#FFFFFF","#ECECFF");
</script>

<?
}
include('_footer.php');
?>