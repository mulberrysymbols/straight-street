<?
include('_header.php');
?>

Licenses used by SS<br><br>

<ul>

<div class="blue_content_div" id="blue_content_div1">
<div class="innerdivspacer">

	<!-- Have to put into table as IE is NON-CONFORMING TO STANDARDS and buggy with 'Floated' text. ARGH MICROSOFT!!! -->
	<table class="normalsmalltable">

<?




//mysql_connect() or die ("Problem connecting to DataBase");
db_connect();

$query = "SELECT * FROM t_lic;";
//$result = mysql_db_query("strstr", $query);
$result = db_runQuery($query);

if ($result) {

	//record exists
	while ($r = mysql_fetch_array($result)) {

		$pathtoimg = "uploaded/lic/t-".$r["id"].".jpg";

		if (file_exists($pathtoimg))
		{
			$lic_pic = $pathtoimg;
		} else {
			$lic_pic = "/img/nologo.png";
		}

		$lic_cap = $r["caption"];
		$lic_txt = $r["brief"];		

		echo "
		<tr>
		<td><img src='$lic_pic'></td>\n
		<td><b>$lic_cap</b><br>\n$lic_txt\n</td>
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