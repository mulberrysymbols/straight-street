<?
include('_header.php');

if (!isUserAtLeastReviewer() && !isUserAtLeastAdmin()) {
	echo "You need to be a registered Reviewer to access this page";
} else {
?>

	<div class="green_content_div" id="list_of_reviewers">
	<div class="innerdivspacer">

	<b>List of Reviewers</b><br><br>

	<?




	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();

	$query = "SELECT username AS name 
    FROM t_user u
    WHERE EXISTS (SELECT * FROM t_user_authority AS ua WHERE ua.user_id = u.id AND ua.authority_id = 'R')
    ORDER BY u.username;";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	if ($result) {
		while ($r = mysql_fetch_array($result)) {
			echo "<img src='/img/person.png'> ".$r["name"]."<br>";
		}
	}
	?>

	</div>
	</div>
Reviews

<ul>

<?
if (isUserAtLeastAdmin()) {
?>
<input id="txtReview"> ( <a href="javascript:addReview('txtReview');">Create Review</a> )<br><br>
<?
}

db_connect();
//mysql_connect() or die ("Problem connecting to DataBase");


$query = "
SELECT 
	id,
	name,
	status, 
	(SELECT count(*) FROM t_review_media rm WHERE rid=t.id) as revcount ,
	(SELECT status FROM t_review_dataset rds WHERE rds.rid=t.id AND rds.userid='".db_escape_string($loggedUserId)."') as statusdataset,
	(status=0) as isPreOpen,
	(status=1) as isOpen
	# isOpen is used to ORDER BY, so order is correct, but OPEN is ontop! (status=1 - is open)
FROM 
	t_review t 
ORDER BY 
	isPreOpen DESC,
	isOpen DESC,
	status ASC,
    name DESC,
	id DESC;
";
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

//echo "$result";

$x=0;
if ($result) {

	//record exists
	while ($r = mysql_fetch_array($result)) {
		
		//---------------------------------------
		// Get some data and stuff
		//---------------------------------------

		//How many datasets have been started etc?
		$u_reviewer_count = 0;
		$escaped_id = db_escape_string($r["id"]);
		$query2 = "
		SELECT	(SELECT count(*) c FROM t_review_dataset WHERE rid='".$escaped_id."' AND status='1') as nInProgress,
			(SELECT count(*) c FROM t_review_dataset WHERE rid='".$escaped_id."' AND status='2') as nCompleted
		FROM 	t_review_dataset";

		$r_nInProgress = 0;
		$r_nCompleted = 0;
		$result2 = mysql_db_query("strstr", $query2);
		$result2 = db_runQuery($query2);

		if ($result2) {
			if ($r2 = mysql_fetch_array($result2)) {
				$r_nInProgress = $r2["nInProgress"];		
				$r_nCompleted = $r2["nCompleted"];		
			}
		}

		//Review Status
		$status = $r["status"];
		$strstatus = $aryReviewStatus[$status];

		//personal status of this review
		$statusdataset = $r["statusdataset"];
		if ($statusdataset=="") { $statusdataset = '0'; }
		$strstatusdataset = $aryMyReviewStatus[$statusdataset];

		//---------------------------------------
		// Output
		//---------------------------------------

		$x = $x + 1;


		//pick class based on open status
		//	$aryReviewStatus[0]='Not Ready Yet';
		//	$aryReviewStatus[1]='Review Open';
		//	$aryReviewStatus[2]='Review Closed';
		//	$aryReviewStatus[3]='Archived';
		switch($status) {
			case 0:
				$strdivclass = "yellow_content_div";
				break;
			case 1:
				$strdivclass = "green_content_div";
				break;
			case 2:
				$strdivclass = "red_content_div";
				break;
			case 3:
				$strdivclass = "blue_content_div";
				break;
		}


		
		//=======
		//Different layout if archived
	
		if ($status=="4" && !isUserAtLeastAdmin()) {
            // don't show
        } else {

            echo "
            <div class=\"$strdivclass\" id=\"review_summary_box$x\" style=\"width:400px;\">
            <div class=\"innerdivspacer\">
            ";
            
            if ($status=="4") {
                echo "<b>Deleted</b> - ".htmlenc($r["name"])." (".$r["revcount"]." items) - [ <a href=\"viewReview.php?id=".$r["id"]."\">View</a> ]";
                echo "[ <a href='ad_editReview.php?id=".$r["id"]."'>Edit</a> ]";	
            
            } elseif ($status=="3") {

                echo "<b>Archived</b> - ".htmlenc($r["name"])." (".$r["revcount"]." items) - [ <a href=\"viewReview.php?id=".$r["id"]."\">View</a> ]";
                if (isUserAtLeastAdmin()) {
                    echo "[ <a href='ad_editReview.php?id=".$r["id"]."'>Edit</a> ]";	
                }

            } else {

                echo "<img src='/img/star";
                if ($r["status"]!='1') { echo "2"; }
                echo ".png' align=right>";

                echo "<b>".$r["name"]."</b> - ".$r["revcount"]." items<br>";

                echo "
                Status : <b>$strstatus</b><br>
                <hr><ul>
                In Progress : $r_nInProgress<br>
                Completed : $r_nCompleted<br>
                Your Status : $strstatusdataset
                </ul><hr>
                ";
            
                if (!isUserAtLeastAdmin())
                {
    //			if ($statusdataset=="2" || $r["status"]!='1') {
                    if (/*$statusdataset!="1" ||*/ $r["status"]!='1') {
                        echo "[ Do review ]";
                    } else {
                        echo "[ <a href='doReview.php?id=".$r["id"]."'>Do review</a> ]";
                    }
                }
                echo "[ <a href='viewReview.php?id=".$r["id"]."'>View Summary</a> ]";

                if (isUserAtLeastAdmin()) {
                    echo "[ <a href='ad_editReview.php?id=".$r["id"]."'>Edit</a> ]";	
                }
            }
            //=======

            echo "</div></div>";
            echo "<br>&nbsp;\n\n";
        }
		//=====================================

	}

}

//mysql_free_result($result); 
db_freeResult($result);

?>


<!--/table-->
<?
if ($x>0) {
	echo "\n\n<script type=\"text/javascript\">\n";

	for ($i=1; $i<=$x; $i++) {
		echo "	Rounded(\"div#review_summary_box$i\",\"#FFFFFF\",\"#ECECFF\");\n";
	}

	echo "	Rounded(\"div#list_of_reviewers\",\"#FFFFFF\",\"#ECFFEC\")";
	echo "</script>";
}
?>

</ul>
<?
}
include('_footer.php');
?>