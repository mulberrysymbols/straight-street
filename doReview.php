<?
include('_header.php');

$input_revid = $_GET["id"];
echo "<input type='hidden' id='currRevIdOnPage' value='$input_revid'>";

$IMAGEROOT = $symbolsPreview

?>

Review Media<br>
<br><br>
<a href="reviews.php">&lt;-- Back</a>

<?
//-------------------------- 
// Review Summary
//-------------------------- 



//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();


//----------------------------------------------
//first get review status for curr user + review.

$query = "
SELECT id rdsid, status rdsstatus FROM t_review_dataset rds WHERE rds.rid='$input_revid' AND rds.userid='$loggedUserId'
";

$currRevStatus = "0";
$currRevDataSetId = "0";
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

if ($result) {
	//record exists
	if ($r = mysql_fetch_array($result)) {
		$currRevStatus = $r["rdsstatus"];;
		$currRevDataSetId = $r["rdsid"];
	}
}

//echo "|<br>|$query|<br>|$currRevStatus|<br>|";

//----------------------------------------------
//then get curr reveiw data

$query = "
SELECT 
	r.id rid,
	r.name rname,
	r.status rstatus,
	(SELECT count(*) FROM t_review_media rm WHERE rm.rid='$input_revid') mcount
FROM 
	t_review r
	INNER JOIN t_review_media rm
		ON r.id = rm.rid
WHERE
	r.id='$input_revid';
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
		echo "<b>".htmlenc($r["rname"])."</b>";
		echo "<br>Status : ".$aryMyReviewStatus[$currRevStatus];

		switch(trim($currRevStatus))
		{
			case '0':
				echo " - [ <a href=\"javascript:setMyReviewStatus('$input_revid','1','$loggedUserId')\">Start Review</a> ]";
				break;
			case '1':
				echo " - [ <a href=\"javascript:setMyReviewStatus('$input_revid','2','$loggedUserId')\">Complete Review</a> ]";
				break;
			case '2':
				//echo "[ Review Submitted ]";

		}


		echo " <br><br> <b>$revmcount</b> items in Review<br>";
		//echo "";

		//echo "";
	}

}

?>

<br clear="all">

<input type='hidden' id='currRevDataSetId' value='<? echo $currRevDataSetId; ?>'>
<input type="hidden" id="preview_image_id" value="">
<div id="previewforreview">
	<div id="preview_caption">...</div>
	<img id="preview_image"/><br>
	<table border=0><tr>
		<td>	
			<img src='/img/<?echo $aryAcceptDecline[0]?>'><input id="radioAccept" checked='checked' type="radio" name="group1"><br>
			<img src='/img/<?echo $aryAcceptDecline[1]?>'><input id="radioDecline" type="radio" name="group1">
		</td>
		<td>
			<textarea id="comments" cols="20" rows="3"></textarea>
		</td>
		<td>
			<input type="button" value="Save" id='save' onClick="saveDoReviewMediaItem('currRevDataSetId','preview_image_id','radioAccept','comments');">
		</td>
	</tr></table>
</div>

<div id="doReviewImageContainer">
</div>

<script language="javascript" type="text/javascript">
var comments = document.getElementById('comments');
addEvent(comments, 'keydown', function(){limitTextAreaText(comments, 500);}, false);
addEvent(comments, 'keyup', function(){limitTextAreaText(comments, 500);}, false);
</script>


<br clear="all">

<?
//Only allow div to show if review is "in progress"
$revstat = trim($currRevStatus);
if ($revstat=="1" || $revstat=="2") {
	echo "<script>update_pop_thumbs_dorev('$input_revid','$revstat');</script>";
} else {
	echo "<script>update_pop_thumbs_dorev('$input_revid','0');</script>";
}
?>



<?
db_freeResult($result);
include('_footer.php');
?>