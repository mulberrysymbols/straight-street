<?
//TODO - when we do security sweep make more secure by not putting all the files in the POST body.

$bAll = (isset($_GET['what']) && ($_GET['what'] == 'all'));

// called from form below
function myPreAddCallBack($p_event, &$p_header)
{
	// replace '_' with ' '
	$info = pathinfo($p_header['stored_filename']);
	$filename= str_replace('_', ' ', $info['dirname'].'/'.$info['basename']);
	$p_header['stored_filename'] = $filename;
	return 1;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	require_once('_common.php');
	$filename = './tmp/mulberry_symbols_EN_wmf_'.mb_strtolower($loggedUser).'.zip';
	$imgRoot = "./".$symbolsWMF;
	$files = split(';', $_POST['files']);
    array_walk($files, create_function('&$item, $key, $prefix', '$item =  $prefix.$item;'), $imgRoot);
    array_unshift($files, 'uploaded/bundles/LICENSE.txt'); 
    
	//set_time_limit(240);
	require_once('zipper/pclzip.lib.php');
	$archive = new PclZip("$filename");
	$v_list = $archive->create($files,
		     PCLZIP_CB_PRE_ADD, 'myPreAddCallBack',
		     PCLZIP_OPT_REMOVE_ALL_PATH,
		     PCLZIP_OPT_ADD_PATH, 'Mulberry_symbols');
	if ($v_list == 0)
	{
        die("Error : ".$archive->errorInfo(true));
	}
	header("Location: download.php?file=".urlencode($filename));

}
else
{
	include('_header.php');
	$MaxThumbsPerPage = 100;

	$cartCookie = (isset($_COOKIE["ss_cart_contents"])) ? $_COOKIE["ss_cart_contents"] : '';
	//cart cookie will contain semicolon-separated list of img IDs
	//$cartCookie = "";

	//echo "Cart Cookie:$cartCookie<br><br>";

	if (!isUserLoggedOn()) {
		echo "You must be logged on to see this page";
	} else {

if (!$bAll)
{
	?>

	Items to download:

	<div class="green_content_div" id="green_content_div1">
	<div class="innerdivspacer">

		<div id="thumbs">

		<?
		//===============================================
		$aryItems = explode("-",$cartCookie);

		//echo "|$cartCookie|".count($aryItems)."|<br><br>";
		//foreach ($aryItems as $Item) {
		//	echo "($Item)";
		//}

		if (count($aryItems)==0 || count($aryItems)==1) {

			echo "<p>There are no items to download</p>";

		} else {

			//List all items in cookie
			//by creating a query string 
			//
			// turn this "...45-34-75-235-65-46-...-"
			// into this "select * from t_media where mid in ('34','235','5345'...)"

			$query = "";
			$query = db_escape_string($cartCookie);
			$query = str_replace("-","','",$query);
			$query = "
	SELECT m.id, m.name, concat('$symbolsThumb','t-',m.name,'.gif') AS icon, concat(m.name,'.wmf') AS media
	FROM t_media m 
	WHERE m.id in ('".$query."')";

			//echo "||$query||";

			$result = db_runQuery($query);
			$files = array();
			if ($result) {
				while ($r = mysql_fetch_assoc($result)) {

					echo "<img src=\"".$r['icon']."\" title='".htmlenc($r['name'])."' class=\"\">";
					$files[] = $r['media'];
				}
			}
            $files = implode(';', $files);
			db_freeResult($result);
			//now download button

			?>

			<br><br>
			<form action="<?=$_SERVER['PHP_SELF']?>" name="dlform" id ="dlform" method="post">
				<input type='hidden' name='files' value="<?php echo "$files" ?>"> 
				<!--<input type="submit" value="Download" > -->
				[ <a href="javascript:document.forms['dlform'].submit()">Download items</a> ]
				[ <a href="javascript:emptyCart();">Remove all items to download</a> ]
				<p>Note: your items are packaged in a zip archive for convenience.<br/>
				On Windows it will appear as a compressed folder which works much like a normal folder, or you can right-click and 'Extract All FIles'.</p>
			</form>
	<!--		<a href="<? //echo $filename; ?>"><img id="dl" src="/img/dl04.jpg" title="Download" alt="Download" border=0></a>-->

			<?

		

		}


		//===============================================
		?>

		</div>

	</div></div>

</div>
</div>
	<script type="text/javascript">	
		Rounded("div#green_content_div1","#FFFFFF","#ECFFEC");
		Rounded("div#green_content_div2","#FFFFFF","#ECFFEC");
		Rounded("div#CartDownloadButton","#ECFFEC","#FFFFFF");
	</script>

<div class="cart_divider">
&nbsp;Or, choose one of the options below to download an entire symbol set...
</div>

<?
}
else
{
print ("</div></div>
<div class=\"cart_divider\">
&nbsp;Choose one of the options below to download an entire symbol set...
</div>");
}
function get_filesize($file_path)
{
    if (!file_exists($file_path))
    {
        return 'File not found';
    }
    $file_size = array_reduce ( array (" B", " KB", " MB"), 
                                create_function ('$a,$b', 'return is_numeric($a)?($a>=1024?$a/1024:number_format($a,2).$b):$a;' ),
                                filesize ($file_path));
	return $file_size;
}

$lang = getUserLangID($loggedUser);
$lic_accepted = hasAcceptedCCLic($loggedUser);
function getURL($suffix, $type)
{
    global $lic_accepted;
    if ($lic_accepted)
    {
        return getFile($suffix, $type);
    }
    else
    {
        return "#";
    }
}

function get_bundle_version($lang)
{
    $query = "
	SELECT version
    FROM t_bundle_version 
    WHERE lang_id = '".db_escape_string($lang)."';";
    
    $result = db_runQuery($query);
    if ($result) 
    {
        $r = mysql_fetch_assoc($result);
        $ver = $r['version'];
        return $ver;
    }
    return '';
}


function getFile($suffix, $type)
{
    global $lang;
    $base = "Mulberry";
    $mlang = ($suffix == "") ? $lang : 'EN';
    $version = get_bundle_version($mlang);
    //$prefix = ($type == "exe") ? 'Setup' : '';
    $path="uploaded/bundles/";
//    return $path.$prefix.$base.$lang.$version.$suffix.'.'.$type;
    return $path.$base.$mlang.$version.$suffix.'.'.$type;
}

function getLangName($langID)
{
	db_connect();
    $query = "SELECT id, CONCAT(native_name, '&nbsp;-&nbsp;', name) AS name FROM t_language WHERE id = '$langID';";
	$result = db_runQuery($query);
    if ($result) 
    {
        $r = mysql_fetch_assoc($result);
        $name = $r['name'];
        return $name;
    }
    return '';
}
$langName = getLangName(getUserLangID($loggedUser));
?>

<div class='sitebody'>
<div class='bundle_container'>
<? if (!$lic_accepted)
{
?>
  <span style="display:inline" id='licence_not_accepted'>&nbsp;Cannot download as Licence not accepted.</span>
<?
}
?>
<div class='bundle'>
	<a class='colour' href="<?echo 'download.php?file='.urlencode(getURL("","exe"))?>" style='white-space:nowrap;'>WMF colour (<?=$langName?>)</br><img src='img/bundle_big_colour.png'/></a>
	<br/>(installer - <? echo get_filesize(getFile("","exe"));?>)<br/><span class='recommend'>* Ideal for SALTs *</span>
</div>
<div class='bundle'>
</div>
<div class='bundle'>
	<a class='bandw' href="<?echo 'download.php?file='.urlencode(getURL("_bw","exe"))?>"> WMF blk &amp; wht<span >&nbsp;&nbsp;&nbsp;</span><br/><img src='img/bundle_small_bandw.png'/></a>
	<br/>(installer - <? echo get_filesize(getFile("_bw","exe"));?>)
</div>
<div class='bundle'>
	<a class='colour' href="<?echo 'download.php?file='.urlencode(getURL("_svg","zip"))?>"> SVG colour<span >&nbsp;&nbsp;&nbsp;</span><br/><img src='img/bundle_small_colour.png'/></a>
	<br/>(.zip - <? echo get_filesize(getFile("_svg","zip"));?>)
</div>
<div class='clearer'>
</div>

<?  
	}
	include('_footer.php');
}
?>
