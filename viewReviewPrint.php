<?
include('_header.php');
if (isUserAtLeastReviewer()) {


$input_revid = $_GET["id"];
echo "<input type='hidden' id='currRevIdOnPage' value='$input_revid'>";

$IMAGEROOT = $symbolsPreview;

?>

<a href="reviews.php">&lt;-- Back</a>

<?
//-------------------------- 
// Review Summary
//-------------------------- 


//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();

//----
//How many users are reviwers?
$u_reviewer_count = 0;
$query = "select count(*) ucount 
        FROM t_user AS u
        WHERE EXISTS (SELECT * FROM t_user_authority AS ua WHERE ua.user_id = u.id AND ua.authority_id = 'R');";
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

if ($result) {
	if ($r = mysql_fetch_array($result)) {
		$u_reviewer_count = $r["ucount"];		
	}
}
//----

//----
//How many datasets have been started etc?
$u_reviewer_count = 0;
$query = "
SELECT
	(SELECT count(*) c FROM t_review_dataset WHERE rid='".db_escape_string($input_revid)."' AND status='1') as nInProgress,
	(SELECT count(*) c FROM t_review_dataset WHERE rid='".db_escape_string($input_revid)."' AND status='2') as nCompleted
FROM 
	t_review_dataset
";

$r_nInProgress = 0;
$r_nCompleted = 0;
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

if ($result) {
	if ($r = mysql_fetch_array($result)) {
		$r_nInProgress = $r["nInProgress"];		
		$r_nCompleted = $r["nCompleted"];		
	}
}
//----

$query = "
SELECT 
	r.id rid,
	r.name rname,
	r.status rstatus,
	(SELECT count(*) FROM t_review_media rm WHERE rm.rid='".db_escape_string($input_revid)."') mcount
FROM 
	t_review r
	INNER JOIN t_review_media rm
		ON r.id = rm.rid
WHERE
	r.id='".db_escape_string($input_revid)."';

";

//echo "$query";

//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);
$revmcount = 0;
//echo "$result";

if ($result) {


	//record exists
	if ($r = mysql_fetch_array($result)) {

		$revmcount = $r["mcount"];		
		echo "<b>".htmlenc($r["rname"])."</b><br>";
		echo "Current Review Status : ".$aryReviewStatus[$r["rstatus"]]."<br><br>";

		echo "<i>(Only submitted results will be included in the following stats)</i><br>";
		echo "Completed Reviews : $r_nCompleted<br>In Progress : $r_nInProgress<br><br>";

		echo "<b>$revmcount</b> items in Review<br>";
		echo "Number of Reviewers : $u_reviewer_count<br><br>";

		//echo "";
	}

}

?>


<?
//-----------------------------
// Review content
//-------------------------- 

$query = "
SELECT 
m.id mid,
	m.name mname,
	mp.filename fname,
	mpt.filename tfname
FROM 
	t_review_media rm
	INNER JOIN t_media m
		ON rm.mid = m.id
	INNER JOIN t_media_path mpt
		ON mpt.mid = m.id AND mpt.type='1'
	INNER JOIN t_media_path mp
		ON mp.mid = m.id AND mp.type='3'
    INNER JOIN t_media_category mc
        ON mc.id = m.category_id
WHERE 
	rm.rid='".db_escape_string($input_revid)."'
	
#ORDER BY 
ORDER BY mc.name, m.name	
#	rm.id DESC;
";

//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

//echo "$result";

if ($result) {

	?>
	<div class="zblue_content_div" id="blue_content_div1">
	<div class="zinnerdivspacer">
	<?


    echo 	"<table id='review' class=\"review_content\" cellspacing='0'>";

/*	echo "
	<table class=\"review_content\">
	<tr>
		<!--<th>#</th>-->
		<th>Media</th>
		<th>Reviews</th>
		<th>Comments</th>
	</tr>
	";*/
	$x = 0;
	//record exists
	while ($r = mysql_fetch_array($result)) {
		$stipe_colour = ($x % 2) ? '#ffffff' : '#eeeeee';
		$x += 1;
		echo "<tr style='background-color:$stipe_colour;'>\n";	

		//---------
		// pic etc
		//---------
		echo "<td>$x: ".htmlenc($r["mname"])."<br/>";
        echo "<img src='".$IMAGEROOT.htmlenc($r["tfname"])."'></td>\n";
		//echo "<td>$x&nbsp;of&nbsp;$revmcount</td>\n";
		//echo "<td>".$r["mid"]."</td>\n";

		//---------
		// review results
		//---------

		$query2 ="
        SELECT 
            SUM(IF( rr.decline=1, 1, 0)) as nDecline,
            SUM(IF( COALESCE(rr.decline,0)=0, 1, 0)) as nAccept
        FROM t_review_media rm
            INNER JOIN t_review_dataset rds
            ON rds.rid = rm.rid
        LEFT JOIN t_review_results rr 
            ON rr.rdsid = rds.id AND rr.rmid = rm.mid
		WHERE 
			rm.mid='".db_escape_string($r["mid"])."' 
			AND rm.rid='".db_escape_string($input_revid)."' 
			AND rds.status in ('1', '2');
		";
		//echo "$query2<br>";

		//$result2 = mysql_db_query("strstr", $query2);
		$result2 = db_runQuery($query2);

		$nAccept = 0;
		$nDecline = 0;

		if ($result2) 
		{
			if ($r2 = mysql_fetch_array($result2)) 
			{
				$nAccept = $r2["nAccept"];
				$nDecline = $r2["nDecline"];
			}
		}

		echo "<td>";
		echo "Accept:&nbsp;$nAccept<br>";
		echo "Decline:&nbsp;$nDecline";
		echo "</td>";
		echo "<td>";
		echo "&nbsp";
		echo "</td>";

		//---------
		// review comments
		//---------

		$query2 = "
		SELECT DISTINCT
			rr.decline udecline,
			rds.userid uid,
			rr.comments ucom,
			u.fname ufname,
			u.sname usname,
            u.username
		FROM 
			t_review_results rr
			INNER JOIN t_review_dataset rds
				ON rds.id = rr.rdsid
			INNER JOIN t_user u
				ON rds.userid = u.id
		WHERE
			rds.rid = '".db_escape_string($input_revid)."'
			AND rr.rmid = '".db_escape_string($r["mid"])."'
			AND rr.comments != ''
			AND rds.status in ('1', '2')
		";
		//echo "$query2";

		//$result3 = mysql_db_query("strstr", $query2);
		$result3 = db_runQuery($query2);

		echo "<td>";
		$dispRes=0;
		if ($result3) 
		{
			while ($r2 = mysql_fetch_array($result3)) 
			{
				if (!$dispRes)
				{
					//open the white div (only once)
					?>
					<div class="zwhite_content_div" id="white_content_div1">
					<div class="zinnerdivspacer">
					<?

					$dispRes=1;
				}
                $arAcceptDeclineText = array('Accept', 'Decline');
				echo htmlenc($r2["username"]).": ".$arAcceptDeclineText[$r2["udecline"]].": ";
				echo "<i>".htmlenc($r2["ucom"])."</i><br>";
			}
		}

		if ($dispRes)
		{
			//only run this (close div) if it was first displayed
			?>
			</div></div>
			<?
		}
		echo "</td>";
		echo "</tr>\n\n";
	}

	echo "</table>";


	?>
	</div></div><br><br>
	<?

}

//mysql_free_result($result); 
db_freeResult($result);

?>


<script>
addEvent(window, 'load', function(){window.print();})
</script>
<?
}
include('_footer.php');
?>