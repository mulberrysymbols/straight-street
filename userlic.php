<?
include('_header.php');

function listlic($acceptedRejected,$username)
{


	db_connect();
	//mysql_connect() or die ("Problem connecting to DataBase");

	$query = "
	SELECT
		l.id LicId,
		l.caption caption,
		l.brief brief,
		ifnull(t_newres.lid,'-1') as LicAccept,
		(SELECT id from t_user WHERE username='".db_escape_string($username)."') as UserDbId 
	FROM
		t_lic l
		LEFT OUTER JOIN 
	
		(
		SELECT
			ual.lid
		FROM
			t_user_agr_lic ual
			INNER JOIN t_user u
				ON (ual.uid=u.id AND u.username='".db_escape_string($username)."')
		) t_newres
			ON l.id=t_newres.lid
	WHERE
		ifnull(t_newres.lid,'-1')";

	if ($acceptedRejected) { $query.="<>'-1'"; } else { $query.="='-1'"; }

	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	//echo "|$query|";


	if ($result) {

		//record exists
		while ($r = mysql_fetch_array($result)) {

			//------------------------
			//Calc some stuff
			$pathtoimg = "uploaded/lic/t-".$r["LicId"].".jpg";

			if (file_exists($pathtoimg))
			{
				$lic_pic = $pathtoimg;
			} else {
				$lic_pic = "/img/nologo.png";
			}

			//------------------------
			//output
			$lic_cap = $r["caption"];
			$lic_txt = $r["brief"];		

			echo "<table class=\"userlicaccept\"><tr><td class=\"td1\">";

			echo "<img src='$lic_pic' class= 'userlicaccept' align=left>\n<b>$lic_cap</b><br>\n";
			echo $lic_txt."<br clear=all><br>\n";

			echo "</td><td class=\"td2\">";

			if ($r["LicAccept"]=='-1') {
				echo "<img src=\"/img/star.png\"><br><font color=\"FF0000\">Not&nbsp;Accepted</font><br>[ <a href=\"javascript:user_lic_Accept('1','".$r["LicId"]."','".$r["UserDbId"]."')\">Accept</a> ]";
			} else {
				echo "<img src=\"/img/star2.png\"><br><font color=\"008800\">Accepted</font><br>[ <a href=\"javascript:user_lic_Accept('0','".$r["LicId"]."','".$r["UserDbId"]."')\">Reject</a> ]";
			}

			//echo "Accepted : $strAccepted <br><br>";

			echo "</td></tr></table>";
		}
	} else {
		//no record
		//echo "No licenses found in DB at the moment...";
	}

	//mysql_free_result($result); 
	//db_freeResult($result);
}


//$AnyNonAcceptedLics = anyRejectLic($loggedUser);


//echo "||$AnyNonAcceptedLics||";

?>

Licenses 

<ul>
<?php
//==================================================
if (!isUserLoggedOn()) {
	echo "<font color='#FF0000'>You must be logged in to view this page</font>";
} else {
//==================================================
?>

Licences that you have not Accepted
<div class="red_content_div" id="content1" style="width:400px;">
<div class="innerdivspacer">

	<? listlic(0,$loggedUser); ?>

</div></div>

<br>

Licenses that you have Accepted	
<div class="blue_content_div" id="content2" style="width:400px;">
<div class="innerdivspacer">

	<? listlic(1,$loggedUser); ?>

</div></div>


</ul>

<script type="text/javascript">
	//if(!NiftyCheck()) return;
	Rounded("div#content1","#FFFFFF","#FFECEC");
	Rounded("div#content2","#FFFFFF","#ECECFF");
</script>


<?
}
include('_footer.php');
?>