<?
include('_common.php');

//first check to see if a tag search is needed. if not jsut list all as normal

$input_tagsearchstrict = (isset($_GET["s"])) ? $_GET["s"] : null;
$input_tagsearch = (isset($_GET["t"])) ? $_GET["t"] : null;
$input_enablepopup = (isset($_GET["p"])) ? $_GET["p"] : null;

$input_source = (isset($_GET["src"])) ? $_GET["src"] : null;
$input_sourceopts = (isset($_GET["so"])) ? $_GET["so"] : null;
$input_sourceopts2 = (isset($_GET["so2"])) ? $_GET["so2"] : null;

$input_callingpage = (isset($_GET["cp"])) ? $_GET["cp"] : null;

$input_highlightUserAcceptedLic = (isset($_GET["h"])) ? $_GET["h"] : null;
$input_page = (isset($_GET["pg"])) ? $_GET["pg"] : null;
$input_itemspage = (isset($_GET["ppp"])) ? $_GET["ppp"] : null;

//t = list of csv tags
//s = 1 (strict) or 0 (rough) or -1 (sparse) search
//p = 0:disable or 1:enable popups over thumbs
//src = source; 0 = live db; -1 = non-live images; 1:review set 2 - live db no rated
//so = reviewid = set id of review (only if src=1)
//so2 = reviewdatasetid = if set, review datasource id. When reviewing media, dataset id is supplied.
//cp = callingpage.
//	if '3' then DO REVIEW page calling this. Means change javascript events for each image
//h = "" 	if no border highlight on thumbnails
//  = "userid" 	if border highlight for specified user

if ($input_enablepopup=="") { $input_enablepopup = "1"; }
if ($input_source=="") { $input_source = "0"; }

//echo "|||$input_source|||";

//###########
//Code for switching sources

//leave blank for normal live search
$strSrc1 = "";
$strSrc2 = "";
$review_clause = "
inner join t_review_media rm
    on (rm.mid = m.id AND rm.rid = $input_sourceopts)
";
if ($input_source=="1") {
	//Source is Review Set

	$strSrc1 = $review_clause;
}
elseif ($input_source=="-1") {
	//Source is Non-Live
 
    if ($input_sourceopts != null)
        $strSrc1 = $review_clause;
	$strSrc2 = "
	and m.status_id <> 4
	";
}
elseif ($input_source=="0") {
	//Source is Live

    if ($input_sourceopts != null)
        $strSrc1 = $review_clause;
	$strSrc2 = "
	and m.status_id = 4
	";
}
elseif ($input_source=="2") {
	//Source is Live non rated

	$strSrc2 = "
	and m.status_id = 4 and not m.rated
	";
}



//###########



//###########
//If Tag search requested, then create a condition for the second query. If not, skip this part
//this will return a list of photo ids that match the tag search
$strTagFromClause = "";
if (trim($input_tagsearch)!='') 
{
 $arrTags = explode(',', $input_tagsearch);
 $arrEscapedTags = array_map('db_escape_string', $arrTags);
 $op = ($input_tagsearchstrict == '1') ? 'AND' : 'OR';
 $strTagJoinClause = "FIND_IN_SET('".implode("', ms.name_tags) > 0 $op FIND_IN_SET('", $arrEscapedTags)."', ms.name_tags) > 0";
  $strTagFromClause = <<<EOT
	INNER JOIN 	(SELECT	ms_m.id AS mid,
						CONCAT(REPLACE(ms_m.name, '_', ','), ',', COALESCE(GROUP_CONCAT(DISTINCT ms_t.tag SEPARATOR ','), '')) AS name_tags
				FROM t_media AS ms_m
					LEFT JOIN t_media_tags AS ms_mt
					ON ms_m.id = ms_mt.mid
					LEFT JOIN t_tag AS ms_t
					ON ms_t.id = ms_mt.tid
					GROUP BY ms_m.id ) AS ms
	  ON (m.id = ms.mid
		AND ($strTagJoinClause))
EOT;

}


//###########
//Ok now use the above search string (if req'd) in listing the images

//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();

//init query
$query = "
select SQL_CALC_FOUND_ROWS DISTINCT
	m.id picid,
	REPLACE(m.name, '_', ' ') AS name,
";

	//----
	//quick check see if user has accepted license
	//	1  = accepted
	//	0  = not accepted
	//	-1 = ignore

	if (mb_strlen(trim($input_highlightUserAcceptedLic))>0) {
	// NB this assumes 1 licence per media otherwise will get duplicate rows.
	$query .= "
		
	ifnull((SELECT 
		ual.id
	FROM
	t_user_agr_lic ual
		INNER JOIN t_lic l
			ON l.id=ual.lid
		INNER JOIN t_user u
			ON u.id=ual.uid
		INNER JOIN t_media mmm
			ON mmm.licid=l.id
	WHERE
		u.username='".db_escape_string($input_highlightUserAcceptedLic)."'
		and mmm.id=m.id
	),'0') as UserAcceptedLic,
	";

	}
	//----


		//review dataset id + mid
#		$rev_results_accept = getImageResultSetAccept($input_sourceopts2,$r["picid"]);#
#		$rev_results_comments = getImageResultSetComments($input_sourceopts2,$r["picid"]);

#$input_callingpage = '3';
#$input_sourceopts2='29';
$revSelClause = $revFromClause = $revOrderClause="";
if ($input_callingpage == '3')
{
	assert($input_sourceopts2!='');
	$revSelClause="
	,COALESCE(rr.comments,'') AS rev_results_comments,
	IF( rr.decline is NULL, 1, NOT rr.decline) AS rev_results_accept";
//	COALESCE(NOT rr.decline, 1) AS rev_results_accept";
	$revFromClause="
	LEFT JOIN t_review_results AS rr
	    ON (rr.rdsid=".db_escape_string($input_sourceopts2)." AND rr.rmid=m.id) 
    INNER JOIN t_media_category mc
        ON mc.id = m.category_id ";
    $revOrderClause = "mc.name, ";
}

$limitClause = '';
if ($input_page <> '')
{
	$ITEMS_PER_PAGE = ($input_itemspage != "") ? $input_itemspage : 30;
	$nOffset = $input_page * $ITEMS_PER_PAGE;
	$limitClause = "LIMIT $nOffset, $ITEMS_PER_PAGE";
}

$query .= "
	concat('m-',m.name,'.gif') filename_preview,
	concat('t-',m.name,'.gif') filename,
	l.caption lic_capt,
	m.status_id = 4 AS islive,
    m.rated,
	l.id lic_id,
	sp.id spon_id,
	(SELECT GROUP_CONCAT(t.tag SEPARATOR ',') FROM
		t_media_tags mt 
		INNER JOIN t_tag t
		ON t.id = mt.tid
		WHERE mt.mid = m.id
		GROUP BY mt.mid) AS tags
	$revSelClause
FROM 
	t_media m
	$revFromClause
	$strSrc1
	inner join t_lic l
		on l.id = m.licid
	$strTagFromClause
	left outer join t_sponsor sp
		on sp.id=m.sponid
WHERE
	m.status_id <> 5
	$strSrc2
ORDER BY $revOrderClause m.name	
$limitClause
";

//exit( "<pre style='text-align:left'>$query</pre>");

//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);
if ($result) {

	$imgidnum = 0;

	$js_dragdrop_str = "";

	//record exists
	if (mysql_num_rows($result) == 0 )
    {
      if (isset($input_callingpage) && $input_callingpage != '' && $input_callingpage[0] == '_')
		echo "";
      else        
                echo '&nbsp;';
    }
	else
	{ 
		echo '<div>';
		$ti = 10;
		if ($input_page <> '')
		{
			// SAL NB I'm a little worried about race condiitons as we don't have transactions
			// hopefully PHP will always call this after above SQL w/o interleaving with other requests
			$qry = db_runQuery( 'SELECT FOUND_ROWS() AS total_rows' );
			$rst = mysql_fetch_array ( $qry, MYSQL_ASSOC );
			$total_rows = $rst['total_rows'];
			$num_pages = ceil($total_rows / $ITEMS_PER_PAGE);
			echo '<p style="text-align:center">';

			if ($input_page > 0)
			{
				$prev_page = $input_page - 1;
				echo "<span class='pagnNext'>[ <a tabindex='6' href=\"javascript:doTagSearch('_$loggedUser','$ITEMS_PER_PAGE','thumbs','tagsearch',$prev_page)\"  title='Previous page' >&laquo; Previous</a> ]</span>";
			}
			else
			{
				echo" <span class='pagnDisabled'>&laquo; Previous</span>";				
			}
			$page = $input_page+1;
			echo "<span class='pagnLead'> | Page $page of  $num_pages | </span>"; 
			if ($input_page < $num_pages-1)
			{
				$next_page = $input_page + 1; 
				echo "<span class='pagnNext'>[ <a tabindex='7' href=\"javascript:doTagSearch('_$loggedUser','$ITEMS_PER_PAGE','thumbs','tagsearch',$next_page)\" title='Next page'>Next &raquo;</a> ]  </span>";
			}
			else
			{
				echo" <span class='pagnDisabled'>Next &raquo;  </span>";				
			}
			echo'&nbsp;&nbsp;&nbsp;&nbsp;</p>';
		}
		while ($r = mysql_fetch_array($result)) {
			
			if ($r["filename"])
			{
				$lic_pic = '/'.$symbolsThumb.$r["filename"];
				$lic_pic_prev = '/'.$symbolsPreview.$r["filename_preview"];
			} else {
				$lic_pic = "/img/nolog.png"; // was nomediatype.gif ???
				$lic_pic_prev = "/img/nologo.png";
			}

			$pathtolicimg = "uploaded/lic/t-".$r["lic_id"].".jpg";
			//if ($r["lic_icon"]) 
			if (file_exists($pathtolicimg))
			{
				$lic_licicon = $pathtolicimg;	//$r["lic_icon"];
			} else {
				$lic_licicon = "/img/nologo.png";
			}

			//if no sponsor, and if sponsor imae not found
			//assuming there is no NULL.jpg!!!
			$pathtosponimg = "uploaded/spon/t-".$r["spon_id"].".jpg";
			if (file_exists($pathtosponimg))
			{
				$lic_sponicon = $pathtosponimg;	
			} else {
				$lic_sponicon = "/img/nologo.png";
			}

	#		//get file tags (if any) via AJAX 
	#		$imagetags = getMediaTags($r["picid"]);

			//misc vars for the JS
			$lic_cap = $r["name"];
			//$lic_txt = $r["brief"];
			$lic_liccapt = $r["lic_capt"];

            $rated = $r['rated'];
            
			//JS code
			//echo "|||$input_enablepopup|||";
			$js_img_code = '';
			if ($input_enablepopup != "0") {
			
				if ($input_callingpage == '3') {
                    $bReadOnly=($input_enablepopup == '2');
					//$js_img_code = "onMouseOver=\"thumb_preview_doreview(true,this,'".$r["picid"]."','$lic_cap','$lic_pic_prev','$lic_liccapt','$lic_licicon','$imagetags')\" onMouseOut=\"thumb_preview_doreview(false,'','','','','','')\"";
					$js_img_code = "onMouseOver=\"thumb_preview_doreview(true,this,'".$r["picid"]."','$lic_cap','$lic_pic_prev',this.getAttribute('accept'), this.getAttribute('comments'),'$bReadOnly')\" onMouseOut=\"thumb_preview_doreview(false,'','','','','','','')\"";
					$js_img_code .= " onClick=\"thumb_preview_doreview_toggle(); return false;\"";
                    $js_img_code .= " accept=".$r['rev_results_accept'];
                    $js_img_code .= " comments='".htmlenc($r['rev_results_comments'])."'";
				}
				else 
				{
					$bEditTags = (isUserAtLeastAdmin()) ? 'true' : 'false';
					$js_img_code = "onMouseOver=\"thumb_preview(true,'".$r["picid"]."','$lic_cap','$lic_pic_prev','$lic_liccapt','$lic_licicon','$lic_sponicon',$bEditTags,$rated)\" onMouseOut=\"thumb_preview(false,'','','','','','')\"
onFocus=\"thumb_preview(true,'".$r["picid"]."','$lic_cap','$lic_pic_prev','$lic_liccapt','$lic_licicon','$lic_sponicon',$bEditTags)\"
onBlur=\"thumb_preview(false,'','','','','','','')\"";
					if (isUserLoggedOn() ) //&& $r["UserAcceptedLic"]!="0")
					{
						$picid = ($r["UserAcceptedLic"]=="0") ? '' : $r["picid"];
						$bFreeze = (isUserAtLeastAdmin()) ? 'true' : 'false';
						$js_img_code .= " onClick=\"thumb_preview_toggle('".$picid."',".$bFreeze."); return false;\"
onKeydown=\"if (event.keyCode == 13) thumb_preview_toggle('".$picid."',".$bFreeze.");\"";
					}
					else
					{
						$js_img_code .= " onClick=\"return false;\"";
					}
				}

			}

			$imgidnum = $imgidnum + 1;

			// Use diff img name depending on SOURCE
			$imgNameBit = "";
			switch ($input_source)
			{
				case '1':
					$imgNameBit = "Rev";
					break;
				case '-1':
					$imgNameBit = "NonLive";
					break;
				default:
					$imgNameBit = "Live";
			}

			//echo "||".$r["UserAcceptedLic"]."||";
			//lic accepted for image or not?
			//query field will contain:	 0 - Not Accepted
			//				>0 - Accepted (ID of image)

			$strLicAccepted = "";
			if (! isset($r["UserAcceptedLic"]))
			{
				$strLicAccepted = '';
			}
			elseif ($r["UserAcceptedLic"]=="0")
			{
				$strLicAccepted = "class=\"LicNotAccepted\"";
			}
			else
			{
				$strLicAccepted = "class=\"LicAccepted\"";
			}

			$ti++;
			echo "<img $strLicAccepted tags='".$r["tags"]."' tabindex='".$ti."' mid='".$r["picid"]."' src='$lic_pic' id='img$imgNameBit$imgidnum' alt='$lic_cap' title='$lic_cap' $js_img_code>\n";

			//add line to make the image dragable, in the javascript at the end
			// Make IMG dragable. slide item back into original position after drop

			//$js_dragdrop_str .= "
			//dragDropObj.addSource('img$imgNameBit$imgidnum',true);";

		}
		echo '</`>';
	}
//	echo "
//	<script>
//	var dragDropObj = new DHTMLgoodies_dragDrop();
//
//	// List JS code for all images
//	$js_dragdrop_str
//
//
//	// Set drop target. Call function on drop	
//	dragDropObj.addTarget('rev_thisimage','edReviewDropImage');	
//
//	dragDropObj.init();
//	</script>
//	";

} else {

	//no record
	echo "";
}

//mysql_free_result($result);
db_freeResult($result); 
?>
