<?
if (ini_get('register_globals'))
	print '<h1>!!Warning - Registerglobals is on</h1>';
if (get_magic_quotes_gpc()) {
    print '<h1>!!Warning - stripslashes is on</h1>';
    function stripslashes_deep($value)
    { return;
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

set_exception_handler(create_function('$e', 'echo "A problem occured: " , $e->getMessage(), "\n";'));
#following will keep all errors off the screen
#set_exception_handler(create_function('$e', 'exit("An unknown error occurred");'));

// stuff for utf8
mb_language('uni'); mb_internal_encoding('UTF-8');
header('Content-type: text/html; charset=utf-8'); // plus we <META> in HTMP in _header.php

// this causes buffering of output so headers can be set in following code w/o errors
ob_start();

////////////////////////////////////////////////////////
//
// Global Vars
//
////////////////////////////////////////////////////////

$aryAcceptDecline=array();
$aryAcceptDecline[0]='y.png';
$aryAcceptDecline[1]='n.png';

$aryReviewStatus=array();
$aryReviewStatus[0]='Not Ready Yet';
$aryReviewStatus[1]='Open';
$aryReviewStatus[2]='Closed';
$aryReviewStatus[3]='Archived';
$aryReviewStatus[4]='Deleted';

$aryMyReviewStatus=array();
$aryMyReviewStatus[0]='Not Started';
$aryMyReviewStatus[1]='In Progress';
$aryMyReviewStatus[2]='Results Submitted';

$noauthkey = 'WQm16AhCHH5SyjL'; // used to allow login w/o pwd

require_once('_db.php');
require_once('_user.php');

$mediaURLBase = 'media/';
$symbolsENURLBase = 'media/symbols/EN/';
$symbolsWMF = $symbolsENURLBase.'wmf/';
$symbolsSVG = $symbolsENURLBase.'svg/';
$symbolsPNG = $symbolsENURLBase.'png/';
$symbolsThumb = $symbolsENURLBase.'thumb/';
$symbolsPreview = $symbolsENURLBase.'preview/';

///////////////////////////////////////////////////////

function anyRejectLic($username)
{
	//Find out if Any current licenses have not been accepted by me
	$returnval = false;

	$query = "
	SELECT
		count(*) NumRejected
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
		ifnull(t_newres.lid,'-1')='-1'
	";


	db_connect();
	$result = db_runQuery($query);	

	//$result = mysql_db_query("strstr", $query);

	if ($result) {

		//record exists
		if ($r = mysql_fetch_array($result)) {
			if ($r["NumRejected"]>0) { $returnval = true; }
		}

	}

	//mysql_free_result($result); 
	//db_freeResult($result);

	return $returnval;


}

function hasAcceptedCCLic($username)
{
	//Find out if Any current licenses have not been accepted by me
	$query = "
	SELECT
		IF(lid IS NULL, 0, 1) as acc_lic
	FROM
		t_user u
		LEFT JOIN t_user_agr_lic ual
		  ON (ual.uid=u.id  AND ual.lid = '6')
    WHERE u.username='".db_escape_string($username)."';";

	db_connect();
	$result = db_runQuery($query);	

	$returnval = false;
	if ($result)
    { 
		if ($r = mysql_fetch_array($result))
        {
			$returnval = ($r["acc_lic"] == 1);
		}

	}

	return $returnval;
}


function getMediaTags($mediaId) {

	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = "SELECT tag FROM t_tag WHERE mid='".db_escape_string($mediaId)."';";
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);


	$strTags = "";
	if ($result) {
		
		while ($r = mysql_fetch_array($result)) {
			$strTags .= $r["tag"].",";
		}

		//Clip last comma off the string (redundant at end of list)
		$strTags = mb_substr($strTags, 0, -1);
	} 

	//mysql_free_result($result); 
	db_freeResult($result);

	return $strTags;
}



function getImageResultSetAccept($rdsid,$rmid) {

	$strAccept = "1";
	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();


	$query = sprintf("SELECT decline FROM t_review_results WHERE rdsid='%s' AND rmid='%s';",
			db_escape_string($rdsid), db_escape_string($rmid));
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);


	if ($result) {
		if ($r = mysql_fetch_array($result)) {
			$strAccept = $r["decline"];
			if ($strAccept=="1") { $strAccept="0"; } else { $strAccept="1"; }
			//echo "||$strAccept||";
		}
	} 

	//mysql_free_result($result); 
	db_freeResult($result);

	//if no record, then assume accepted!
	if ($strAccept=="") { $strAccept = "1"; }

	return $strAccept;
}

function getImageResultSetComments($rdsid,$rmid) {

	$strComments = "";
	//mysql_connect() or die ("Problem connecting to DataBase");
	db_connect();	

	$query = sprintf("SELECT comments FROM t_review_results WHERE rdsid='%s' AND rmid='%s';",
 		db_escape_string($rdsid), db_escape_string($rmid));
	//$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

	if ($result) {
		if ($r = mysql_fetch_array($result)) {
			$strComments = $r["comments"];
			//echo "||$strAccept||";
		}
	} 

	//mysql_free_result($result); 
	db_freeResult($result);

	return $strComments;
}

function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
  /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
      (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
  */
  
  if (!$using_timestamps) {
    $datefrom = strtotime($datefrom, 0);
    $dateto = strtotime($dateto, 0);
  }
  $difference = $dateto - $datefrom; // Difference in seconds
  
  switch($interval) {
  
    case 'yyyy': // Number of full years

      $years_difference = floor($difference / 31536000);
      if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
        $years_difference--;
      }
      if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
        $years_difference++;
      }
      $datediff = $years_difference;
      break;

    case "q": // Number of full quarters

      $quarters_difference = floor($difference / 8035200);
      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
        $months_difference++;
      }
      $quarters_difference--;
      $datediff = $quarters_difference;
      break;

    case "m": // Number of full months

      $months_difference = floor($difference / 2678400);
      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
        $months_difference++;
      }
      $months_difference--;
      $datediff = $months_difference;
      break;

    case 'y': // Difference between day numbers

      $datediff = date("z", $dateto) - date("z", $datefrom);
      break;

    case "d": // Number of full days

      $datediff = floor($difference / 86400);
      break;

    case "w": // Number of full weekdays

      $days_difference = floor($difference / 86400);
      $weeks_difference = floor($days_difference / 7); // Complete weeks
      $first_day = date("w", $datefrom);
      $days_remainder = floor($days_difference % 7);
      $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
      if ($odd_days > 7) { // Sunday
        $days_remainder--;
      }
      if ($odd_days > 6) { // Saturday
        $days_remainder--;
      }
      $datediff = ($weeks_difference * 5) + $days_remainder;
      break;

    case "ww": // Number of full weeks

      $datediff = floor($difference / 604800);
      break;

    case "h": // Number of full hours

      $datediff = floor($difference / 3600);
      break;

    case "n": // Number of full minutes

      $datediff = floor($difference / 60);
      break;

    default: // Number of full seconds (default)

      $datediff = $difference;
      break;
  }    

  return $datediff;

}

// Steve's stuff

function is_str_uint($str)
{
	return mb_ereg("^[0-9\.]+$", $str) != 0;
}

function is_str_surname($str)
{
	return preg_match("/^([-'a-z])+$/i", $str) != 0;
}

function is_valid_email_address($email){
		$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
		$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
		$atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
			'\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
		$quoted_pair = '\\x5c[\\x00-\\x7f]';
		$domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
		$quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
		$domain_ref = $atom;
		$sub_domain = "($domain_ref|$domain_literal)";
		$word = "($atom|$quoted_string)";
		$domain = "$sub_domain(\\x2e$sub_domain)*";
		$local_part = "$word(\\x2e$word)*";
		$addr_spec = "$local_part\\x40$domain";
		return preg_match("!^$addr_spec$!", $email) ? 1 : 0;
}

function random_string($type = 'alnum', $len = 8)
{					
	switch($type)
	{
		case 'alnum'	:
		case 'numeric'	:
		case 'nozero'	:
		
				switch ($type)
				{
					case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric'	:	$pool = '0123456789';
						break;
					case 'nozero'	:	$pool = '123456789';
						break;
				}

				$str = '';
				for ($i=0; $i < $len; $i++)
				{
					$str .= mb_substr($pool, mt_rand(0, mb_strlen($pool) -1), 1);
				}
				return $str;
		  break;
		case 'unique' : return md5(uniqid(mt_rand()));
		  break;
	}
}

function checkExists($table, $field, $compared)
{
	$sql = 'SELECT  '.db_escape_string($field).
		   ' FROM '.db_escape_string($table).
		   ' WHERE '.db_escape_string($field).' = "'.db_escape_string($compared).'"';
	$query = db_runQuery($sql);
	return mysql_num_rows($query) != 0;
}	


$domain = "straight-street.com";
$addr_bare = "support@$domain";
//$addr = '"Straight Street Team" <'.$addr_bare.'>'; // this is not working < gets lost. WHY?
$addr = '"Straight Street Team" <support@straight-street.com>';

$contact_addr = 'Straight Street Ltd,
Pippins, The Orchard,
Felsted,
Essex
CM6 3DE';

$contact_tel = '01371 821501';

function htmlenc($str)
{
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

function redirectTo($file)
{
//echo '<script>document.location.href='.$file.'</script>';
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: http://$host$uri/$file");
}

function emptyCart()
{
	setcookie ("ss_cart_contents", "", time() - 3600);
	unset($_COOKIE["ss_cart_contents"]);
}

function listMediaEdit( $dbh )
{
  print '<select id="medialist">';
  foreach ($dbh->query('SELECT id, name FROM t_media ORDER BY name, id') as $row)
  {        printf('<option value="%s">%s</option>'."\n",
                     $row['id'],htmlenc($row['name']));
  }
  print '</select>';
  $page = 'ad_editMedia.php';
  $f = "var id=document.getElementById('medialist').value; location.href = '$page?cmd=edit&id='+id;";
  print '<input type="button" accesskey="E" value="Edit Media" name="edit" onclick="'.$f.'" />';
}


//ip can be string or array
function getSelectionHTML($name, $ip, $selected_id, $allow_null=False, $highlight_nosel=False, $klass=null)
{
	if ('string' == gettype($ip))
	{
		$dbh = DBCXn::get();
		$sth = $dbh->query($ip);
		$arr = array();
		foreach($sth as $row )
		{
			$arr[$row['id']] = $row['name'];
		}
	}
	else
	{
		$arr = $ip;
	}	
    
    $has_selection = ($selected_id && (array_key_exists($selected_id, $arr) || in_array($selected_id, $arr)));
    $klass = ($highlight_nosel && !$has_selection) ? "class='badselection'" : (($klass == '') ? "" : "class='$klass'");
	$s = "<select id='$name' name='$name' $klass>\n";
	if ($allow_null)
		$s .= "<option value='0'></option>\n";
	foreach ($arr as $id => $oname)
	{
		$oname = htmlenc($oname);
        $match = ($id == $selected_id || $oname == $selected_id);
		$sel = ($selected_id && $match) ? 'selected' : '';
		$s .= "<option value='$id' $sel>".htmlenc($oname)."</option>\n";
	}
	$s .= "</select>\n";
	
	return $s;
}

function getListHTML($name, $table, $select, $show_deleted=False, $allow_null=False, $sortByID=False, $highlight_nosel=False, $klass='')
{
	$name_clause = ($show_deleted) ? "IF(deleted , CONCAT('~ ', name), name) AS name" : 'name';
	$del_clause = ($show_deleted) ? '' : 'WHERE deleted <> TRUE';
    $orderby = "IF(deleted , _utf8'~', ''), ". (($sortByID) ? 't.id' : 't.name');
	$sql = "SELECT id, $name_clause FROM t_media_$table AS t $del_clause ORDER BY $orderby";
	return GetSelectionHTML($name, $sql, $select, $allow_null, $highlight_nosel, $klass).'</div>';
}

?>
