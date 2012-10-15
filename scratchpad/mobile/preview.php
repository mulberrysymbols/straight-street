<?
$sPicName = "Name";
$sPicFilename = "";
$bSearchError = true;

//----------------------------

include('../_common.php');
db_connect();
$sPicId = $_GET["pid"];

$query = " 

SELECT 
	m.id,
	m.name,
	mp.filename
FROM
	t_media m
	INNER JOIN t_media_path mp
		ON m.id=mp.mid
WHERE
	m.id='" . $sPicId . "' 
	AND mp.type=3

ORDER BY
	m.name ASC

";
$result = db_runQuery($query);
if ($result) {
	//record doesn't exist
	if (mysql_num_rows($result) > 0 ) {
		//record exists
		if ($r = mysql_fetch_array($result)) {
			$sPicName = $r["name"];
			$sPicFilename = "/media/symbols/EN/preview/" . $r["filename"];
			$bSearchError = false;
		}
	}
}
db_freeResult($result); 
//----------------------------


?><!DOCTYPE html> 
<html>
<head>
<title>Mobile Symbol Search</title>

	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a1/jquery.mobile-1.0a1.min.css" />
	<script src="http://code.jquery.com/jquery-1.4.3.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0a1/jquery.mobile-1.0a1.min.js"></script>

</head>

<body>

<!-- Start of first page -->
<div data-role="page" id="mydialog"  data-back-btn-text="previous">

	<div data-role="header">
		<h1>Preview</h1>
	</div><!-- /header -->

	<div data-role="content">	
		<center>
		Add this to your Grid?<br /><br />

		<? if ($bSearchError) { ?>

			<p>Error Loading Preview Image</p>

		<? } else { ?>

			<img src="<?=$sPicFilename?>" width="200" height="200" />

		<? } ?>

		<a href="#" data-role="button" onclick="close()">Add to Grid</a>

		</center>
	</div><!-- /content -->

	<!--div data-role="footer">
		<h4>&nbsp;</h4>
	</div--><!-- /header -->
</div><!-- /page -->

</body>
</html>
