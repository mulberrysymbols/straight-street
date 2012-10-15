<?
include('_header.php');
?>

Media Types<br><br>

<ul>
<?
mysql_connect() or die ("Problem connecting to DataBase");


$query = "SELECT * FROM t_media_type;";
$result = mysql_db_query("strstr", $query);

if ($result) {

	//record exists
	while ($r = mysql_fetch_array($result)) {

		if ($r["iconpath"])
		{
			$lic_pic = $r["iconpath"];
		} else {
			$lic_pic = "/img/nologo.png"; // was nomediatype
		}
		
		$lic_cap = $r["caption"];
		$lic_txt = $r["brief"];		

		echo "
		<img src='$lic_pic' align=left>\n
		<b>$lic_cap</b><br>\n
		$lic_txt<br clear=all><br>\n
		";

	}

} else {

	//no record
	echo "No licenses found in DB at the moment...";
}

mysql_free_result($result); 
?>
</ul>

<br><br>

<?
include('_footer.php');
?>