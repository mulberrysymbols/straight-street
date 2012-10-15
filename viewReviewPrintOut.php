<?
include('_header.php');

$input_revid = $_GET["id"];
echo "<input type='hidden' id='currRevIdOnPage' value='$input_revid'>";

$IMAGEROOT = $symbolsPreview;

?>

View Review Printout version<br><br>
<a href="viewReview.php?id=<? echo $input_revid;?>">&lt;-- Back</a>

<ul>

<?
//-------------------------- 
// Review Summary
//-------------------------- 

db_connect() or die ("Problem connecting to DataBase");

//----
//How many users are reviwers?
$u_reviewer_count = 0;
$query = "select count(*) ucount 
           FROM t_user AS u
           WHERE EXISTS (SELECT * FROM t_user_authority AS ua WHERE ua.user_id = u.id AND ua.authority_id = 'R');";
$result = db_runQuery($query);
if ($result) {
	if ($r = mysql_fetch_array($result)) {
		$u_reviewer_count = $r["ucount"];		
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

$result = db_runQuery($query);
$revmcount = 0;
//echo "$result";

if ($result) {


	//record exists
	if ($r = mysql_fetch_array($result)) {

		$revmcount = $r["mcount"];		
		echo "<b>".htmlenc($r["rname"])."</b> ( ".$r["rstatus"]." ) <br>";
		echo "<b>$revmcount</b> items in Review<br>";


	}

}

?>

<p>
The following pages will be ready for you to print out to perform a manual review.<br>
Please circle "Accept" or "Decline" and then add any comments if you so wish.</p>

<p>
Once you have completed the review, please post your results to the following address:</p>
<ul>
<p>
Media Reviews<br>
<?= str_replace("\n", '<br>', $contact_addr) ?>
</p>
</ul>

<?
//-----------------------------
// Review content
//-------------------------- 

$query = "
SELECT 
	m.id mid,
	m.name mname,
	mp.filename fname
FROM 
	t_review_media rm
	INNER JOIN t_media m
		ON rm.mid = m.id
	INNER JOIN t_media_path mp
		ON mp.mid = m.id
WHERE 
	rm.rid='".db_escape_string($input_revid)."'
	AND mp.type='1'
ORDER BY 
	rm.id DESC;
";

$result = db_runQuery($query);

//echo "$result";

if ($result) {

	echo "
	<p style='page-break-after:always'></p>
	<table border=1 class='print'>

	<tr>
		<th>#</th>
		<th>Media</th>
		<th>Accept?</th>
		<th>Comments?</th>
	</tr>
	";
	$x = 0;
	//record exists
	while ($r = mysql_fetch_array($result)) {
		$x += 1;

/*		if ($x % 2 == 0) {
			echo "
			<tr><td colspan=4><p style='page-break-after:always'>...</p></td></tr>
			";
		}
*/
		echo "<tr >\n";	

		//---------
		// pic etc
		//---------
		echo "<td>$x&nbsp;of&nbsp;$revmcount</td>\n";
		echo "<td>".htmlenc($r["mname"])."<br><img src='".$IMAGEROOT.htmlenc($r["fname"])."' ></td>\n";
		//echo "<td>".$r["mid"]."</td>\n";


		//---------
		// review results
		//---------
		echo "<td>";
		echo "<img src='/img/".$aryAcceptDecline[0]."'>&nbsp;&nbsp;Accept<br><br>";
		echo "<img src='/img/".$aryAcceptDecline[1]."'>&nbsp;&nbsp;Decline<br><br>";
		echo "</td>";

		//---------
		// review comments
		//---------
		echo "<td width=300>&nbsp;";
		echo "</td>";

		echo "</tr>\n\n";


		
	}

	echo "</table>";

}

//mysql_free_result($result); 

?>


</ul>
<?
include('_footer.php');
?>