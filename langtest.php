<?
    header('Content-type: text/html; charset=UTF-8') ;
?>
<html>
<header>
<meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
</header>
<body>
<?
    print '<p>Amharic  	አማርኛ</p><br/>';

    include('_db.php');
	db_connect();
    db_runQuery("SET NAMES 'utf8'");
    $query = "SELECT native_name FROM t_language where ID = 'AM';";
	$result = db_runQuery($query);
    $r = mysql_fetch_array($result);
    print 'zzz'.$r['native_name'];
?>
</body>
</html>
    