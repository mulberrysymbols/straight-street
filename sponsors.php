<?
include('_header.php');
?>

Sponsors of SS<br><br>

<ul>

<div class="blue_content_div" id="blue_content_div1">
<div class="innerdivspacer">

	<!-- Have to put into table as IE is NON-CONFORMING TO STANDARDS and buggy with 'Floated' text. ARGH MICROSOFT!!! -->
	<table class="normalsmalltable">

<?


db_connect();
//mysql_connect() or die ("Problem connecting to DataBase");


$query = "SELECT * FROM t_sponsor;";
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

if ($result) {

	//record exists
	while ($r = mysql_fetch_array($result)) {

		$pathtoimg = "uploaded/spon/t-".$r["id"].".jpg";

		if (file_exists($pathtoimg))
		{
			$spon_pic = $pathtoimg;
		} else {
			$spon_pic = "/img/nologo.png";
		}


		if ($r["url"]!='') {
			$strLink = "[ <a href=\"".$r["url"]."\" target=\"_new\">Go To ".$r["caption"]."</a> ]";
		} else {
			$strLink = "<i>No Link Given</i>";
		}

		$spon_cap = $r["caption"];
		$spon_txt = $r["brief"];		

		echo "
		<tr>
		<td><img src='$spon_pic' width=40></td>\n
		<td><b>$spon_cap</b> - $strLink</b><br>\n$spon_txt\n</td>
		</tr>
		";

	}

} else {

	//no record
	echo "No licenses found in DB at the moment...";
}



//mysql_free_result($result); 
db_freeResult($result);
?>


	</table>

</div></div>

</ul>

<br><br>

<script type="text/javascript">
	//Rounded("div#list_of_reviewers","#FFFFFF","#ECFFEC");
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
	//Rounded("div#register2","#FFFFFF","#ECECFF");
</script>

<?
include('_footer.php');
?>