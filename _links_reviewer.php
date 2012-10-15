<?
if (isUserAtLeastReviewer() || isUserAtLeastAdmin()) { 
?>

 - Reviews [ 

<?


	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "SELECT * FROM t_review WHERE status='1';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	$found = 0;
	if ($result) {
		while ($r = mysql_fetch_array($result)) {
			$found++;
		}
	} 
	//mysql_free_result($result); 

	db_freeResult($result);

	echo "<a href=\"/reviews.php\"><img src='/img/star";
	if ($found==0) { echo "2"; }
	echo ".png' border=0> $found Open review";
	if ($found!=1) { echo "s"; } 
	echo "</a>";

?>



 ]

<?
}
?>

