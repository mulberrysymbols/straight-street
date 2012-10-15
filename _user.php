<?php

// TODO ID is the primary key so we should use that for queries or else change PK to be username as we constrain that to be unique as well

function mk_cookie_hash($str)
{
	return md5($str.'John ch3v16');		// makes it hard to crack the cookie
}

function setLoggedUser($username)
{
	// NB must be called b4 any html is output.
	$cookie_val = $username.','.mk_cookie_hash($username);
	setcookie('ss_userid', $cookie_val);	// never expires
	$_COOKIE['ss_userid'] = $username; // so effective immediately
	emptyCart();
}

$g_logtimeout = '00:25:00';		// hh:mm:ss

function isUserInactive($username)
{
	global $g_logtimeout;
	db_connect();
	$sql = "SELECT u.ID, IF((NOT EXISTS(SELECT * FROM t_user_authority ua where ua.user_id=u.id and authority_id IN ('A', 'R', 'E'))) AND TIMEDIFF(NOW(), ifnull(last_access, NOW())) > '$g_logtimeout', 'yes', 'no') AS expired"
        ." FROM t_user u WHERE u.username = '".db_escape_string($username)."'";
    $rs = db_runQuery($sql) or die(mysql_error());
	if (mysql_num_rows($rs) == 1)
	{
		$row = mysql_fetch_assoc($rs);
		if ($row['expired'] == 'no')
		{
			$sql = "UPDATE t_user SET last_access = NOW()"
				  ." WHERE ID = {$row['ID']}";
			$rs = db_runQuery($sql) or die(mysql_error());
			return False;
		}
	}
	return True;
}


function getLoggedUser()
{
	if (! isset($_COOKIE["ss_userid"]))
	{
		logoutUser();
		return '';
	}
	list($cookie_username, $cookie_hash) = mb_split(',', $_COOKIE["ss_userid"]);
	if (mk_cookie_hash($cookie_username) != $cookie_hash)	// check for corrupt cookie
		return '';
	if (isUserInactive($cookie_username))
	{
		header("Location: login.php?expired=true");
		logoutUser();
		return '';
	}
	else
	{
		return $cookie_username;
	}
}

function logoutUser()
{
	setcookie ("ss_userid", "", time() - 3600);
	unset($_COOKIE["ss_userid"]);
	emptyCart();
}

$loggedUser= getLoggedUser();
$loggedUserAuth = (int)getUserAuth($loggedUser);
$loggedUserId = getUserId($loggedUser);

function isUserLoggedOn() {
	GLOBAL $loggedUser;
	return ($loggedUser!=""); 
}
function isUserAtLeastAdmin() {
	GLOBAL $loggedUserId;
	GLOBAL $loggedUser;
    return $loggedUser!="" && array_key_exists('A', getUserAuthorities($loggedUserId));
}
function isUserAtLeastEditor() {
	GLOBAL $loggedUserId;
	GLOBAL $loggedUser;
    return $loggedUser!="" && array_key_exists('E', getUserAuthorities($loggedUserId));
}
function isUserAtLeastReviewer() {
	GLOBAL $loggedUserId;
	GLOBAL $loggedUser;
    return $loggedUser!="" && array_key_exists('R', getUserAuthorities($loggedUserId));
}
function isUserAtLeastPartner() {
	GLOBAL $loggedUserId;
	GLOBAL $loggedUser;
    return $loggedUser!="" && array_key_exists('P', getUserAuthorities($loggedUserId));
}
/*function isUserAtLeastContributor() {
	GLOBAL $loggedUserId;
    return array_key_exists('C', getUserAuthorities($loggedUserId));
	GLOBAL $loggedUser;
}*/
function isUserAtLeastSubscriber() {
	GLOBAL $loggedUserId;
	GLOBAL $loggedUser;
    return $loggedUser!="" && array_key_exists('S', getUserAuthorities($loggedUserId));
}
function isUserBlocked() {
	GLOBAL $loggedUser;
	return ((int)getUserAuth($loggedUser)==-1);
}
function isUserActive() {
	GLOBAL $loggedUser;
	return ((int)getUserAuth($loggedUser)>0); // TODO always true now as author is -1 or 1
}

function getField($fieldname,$table,$cond_field,$cond_val) {

	// ***
	// Get $fieldname from $table where $cond_field = $cond_val
	// ***

	//mysql_connect() or die ("Problem connecting to DataBase");

	db_connect();
	db_runQuery("SET NAMES 'utf8'");	
	$query = "SELECT ".$fieldname." FROM ".$table." WHERE ".$cond_field."='".db_escape_string($cond_val)."';";
	//$result = mysql_db_query("strstr", $query);

	$result = db_runQuery($query);

	//echo $query;

	if ($result) {
		
		$aryreturnval = mysql_fetch_array($result);		
		return $aryreturnval[$fieldname];

	} else {
	
		return "";

	}

	//mysql_free_result($result); 
	db_freeResult($result);

}

$ar_authorities=array();
$query = "SELECT id, name FROM t_authority ORDER BY display_ord;";
$result = db_runQuery($query);
if ($result)
{
	//record exists
    while ($r = mysql_fetch_assoc($result))
    {
        $ar_authorities[$r['id']] = $r['name'];
	}
}
//mysql_free_result($result); 
db_freeResult($result);

function getUserAuthorities($userid)
{
  $sql ='SELECT  
    SUM(IF(ua.authority_id=\'S\', 1, 0)) as S,
    SUM(IF(ua.authority_id=\'A\', 1, 0)) as A,
    SUM(IF(ua.authority_id=\'E\', 1, 0)) as E,
    SUM(IF(ua.authority_id=\'P\', 1, 0)) as P,
    SUM(IF(ua.authority_id=\'C\', 1, 0)) as C,
    SUM(IF(ua.authority_id=\'R\', 1, 0)) as R,
    SUM(IF(ua.authority_id=\'D\', 1, 0)) as D,
    SUM(IF(ua.authority_id=\'T\', 1, 0)) as T
    FROM t_user_authority AS ua
    WHERE ua.user_id = '.$userid.'
    GROUP BY ua.user_id';
    $result2 = db_runQuery($sql);
    $r2 = mysql_fetch_assoc($result2);
    db_freeResult($result2);
    $ar = array();
    global $ar_authorities;
    foreach ($r2 as $auth => $enabled)
    {
        if ($enabled <> 0)
            $ar[$auth] = $ar_authorities[$auth];
    }
    return $ar;
}   

$site_version = getField('version','t_web_app',"1","1");

function getUserFname($uid) {
	return getField("fname","t_user","username",$uid);
}
function getUserSname($uid) {
	return getField("sname","t_user","username",$uid);
}
function getUserAuth($uid) {
	return getField("auth","t_user","username",$uid);
}
function getUserId($uid) {
	return getField("id","t_user","username",db_escape_string($uid));
}
function getUserLangID($uid) {
	return getField("language_id","t_user","username",db_escape_string($uid));
}
function isUserEmailConfirmed($uid) {
	return (getField("authcode","t_user","username",db_escape_string($uid)) == '');
}


?>