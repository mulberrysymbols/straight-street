<?
include('_header.php');
if (isUserAtLeastAdmin()) {

$input_revid = $_GET["id"];
echo "<input type='hidden' id='currRevIdOnPage' value='$input_revid'>";

?>

Admin - Edit Review<br><br>
<a href="reviews.php">&lt;-- Back</a>

<ul>



<?


//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();


$query = "SELECT * FROM t_review WHERE id='".db_escape_string($input_revid)."';";
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);


//echo "$result";

if ($result) {

	//record exists
	while ($r = mysql_fetch_array($result)) {
		
		echo "<b>".$r["name"]."</b> <br>";

		$status = $r["status"];
		$strstatus = $aryReviewStatus[$status];

		echo "Status : $strstatus<br><br>";

		echo "Set Review Status: 
		[ <a href=\"javascript:setAdminReviewStatus('$input_revid','0')\">Not Ready</a> | 
		  <a href=\"javascript:setAdminReviewStatus('$input_revid','1')\">Open</a> | 
		  <a href=\"javascript:setAdminReviewStatus('$input_revid','2')\">Complete</a> | 
		  <a href=\"javascript:setAdminReviewStatus('$input_revid','3')\">Archived</a> |
          <a href=\"javascript:setAdminReviewStatus('$input_revid','4')\">Deleted</a> ]<br><br>";

		$allowDragDrop = "0";
		if ($status=="0") { $allowDragDrop="1"; }


		//$jsedit = "";
		//$jsedit = " onClick=\"editField(this);\" ";

	}

}




//mysql_free_result($result);
db_freeResult($result); 

?>


</ul>
<?
}
include('_footer.php');
?>