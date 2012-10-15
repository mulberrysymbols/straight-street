<?
include('_common.php');
if (isUserAtLeastAdmin()) {

$input_g = $_POST["group"];
$input_b = $_POST["body"];
$input_c = $_POST["client"];
$input_h = $_POST["html"];

function getGroupRecipients($group)
{
	if ($group == '998')
	{
		return array('support@straight-street.com');
	}
	else if ($group == '999')
	{
		return array('steve@fullmeasure.co.uk');
	}
	
	db_connect();

	$query = "SELECT * FROM t_user AS u
            WHERE u.cancontact=1 
            AND EXISTS (SELECT * FROM t_user_authority AS ua WHERE ua.user_id = u.id AND ua.authority_id = '".db_escape_string($group)."');";

    //$result = mysql_db_query("strstr", $query);
	$result = db_runQuery($query);

    $arRecipients = array();
	if ($result)
    {
		while ($r = mysql_fetch_array($result))
        {
			if (mb_strlen($r["email"])>0) 
            {
                $arRecipients[] = $r["email"];
            }
		}
	}
    
	db_freeResult($result);

    return $arRecipients;
}

if (mb_strlen(trim($input_g))>0 && (mb_strlen(trim($input_b)))>0)
{
    $groupName = ($input_g == '998' || $input_g == '999') ? 'Test group email' : $ar_authorities[$input_g];
    $arRecipients = getGroupRecipients($input_g);

	//--------
    if ($input_c == 'true')
    {
        $s = 'mailto:'.$addr_bare.'?subject=Group%20email%20to%20'.$groupName.'&body=';
        $s .= '@@@' . join(',', $arRecipients); // so client can slpit and count address safely
        $r = $s;
    }
    else
    {
        $subject = "[$groupName] Straight-Street Group Email";
        $name = "Straight-Street";
        $body = $input_b;

        if ($input_h == 'true')
        {
            $prefix = 'This is a MIME encoded multipart message';
            $boundary = "MULTIPART_BOUNDARY_".md5(date('r', time()));
            $mimetype = "Content-Type: multipart/alternative; boundary=\"".$boundary."\"";
            $textpart = "This email is HTML formatted but your emial program is only showing plain text.\r\n";
            $body = 
"$prefix

--$boundary
Content-Type: text/plain; charset=utf-8
Content-Transfer-Encoding: 8bit

$textpart

--$boundary
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: 8bit

$body

--$boundary--";
        }
        else
        {
            $mimetype = "Content-type: text/plain; charset=UTF-8 \n";
        }     

        $headers = "MIME-Version: 1.0 \n"
                   .$mimetype." \n"
                   ."From: $addr \n"
                   ."Reply-To: $addr \n"
                   .'X-Mailer: PHP/' . phpversion() . " \n";
        
        $r = True;
        foreach ($arRecipients as $to)
        {
            //echo "$input_c\n$to\n$subject\n$body\n$headers";	
            $r &= mail($to, $subject, $body, $headers);
            //$r &= mb_send_mail("$to", "$subject", "$body", "$headers");
        }
    }
    echo $r;
}
}
/*
Notes:
"Subject: =?UTF-8?B?".base64_encode($subject)."?="
"Subject: =?UTF-8?Q?".imap_8bit($subject)."?="  // better for some clients and use htmlentities
mb_encode_mimeheader()
*/
?>

