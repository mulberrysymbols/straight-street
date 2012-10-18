<?php

require('_dbconsts.php');

function random_str($length="8")
{
	$set = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9");
	$str = '';
	for($i = 1; $i <= $length; ++$i)
	{
		$ch = mt_rand(0, count($set)-1);
		$str .= $set[$ch];
	}
	return $str;
}

// Added a myBB user directly into it's DB
// NB this code is specific to a particular version of myBB
function addForumUser($uname, $password, $email, $ip)
{
	if (!($link = mysql_connect('localhost',DB_USER,DB_PW) ))
	{
		throw new Exception("Unable to connect to the Database.");
	}
	mysql_select_db('straight_street_3', $link);
	
	$salt = random_str(8);
	$login_key = random_str(50);
	$pass = md5(md5($salt).md5($password));
	$lip = ip2long($ip);
	$NOW = time();
	$fcrisql = "INSERT INTO mybb_users (`username`, `password`, `salt`, `loginkey`, `email`, `usergroup`, `regdate`, `lastactive`, `lastvisit`, `icq`, `birthdayprivacy`, 
	`allownotices`, `receivepms`, `pmnotice`, `threadmode`, `showsigs`, `showavatars`, `showquickreply`, `showredirect`, `timezone`, `dstcorrection`, `returndate`, `pmfolders`, `regip`, `longregip`, `lastip`, `longlastip`) 
	VALUES ('$uname', '$pass', '$salt', '$login_key', '$email', '2', $NOW, $NOW, $NOW, 0, 'all', 1, 1, 1, 'linear', 1, 1, 1, 1, 0, 2, 0, '1**$%%$2**$%%$3**$%%$4**', '$ip', $lip, '$ip', $lip)";
	
	$fcrinsert = mysql_query($fcrisql, $link) or die("Can't create forum user: " . mysql_error());
	mysql_close($link);
}

?>