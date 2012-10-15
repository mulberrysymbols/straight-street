<?
include('_common.php');
?>

<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="Expires" CONTENT="Tue, 04 Dec 1993 21:29:02 GMT"/>
<meta name="google-site-verification" content="T4-hEZdWODzMXHB9ll6fCdYleDCyUhhXKhL9jleuNb8" />
<title>Straight-Street</title>
<!-- My AJAX -->
<script type="text/javascript" language="javascript" src="/_jsconfig.php"></script>
<script type="text/javascript" language="javascript" src="/_ajax.js"></script>
<!-- Drag/Drop elements -->
<script type="text/javascript" src="/js/drag-drop-custom.js"></script>
<!-- My CSS -->
<link href='/styles.css' rel='stylesheet' type='text/css'>
<link href='/ui.css' rel='stylesheet' type='text/css'>
<!-- NIFTY Corners -->
<link rel="stylesheet" type="text/css" href="nifty/niftyCorners.css">
<link rel="stylesheet" type="text/css" href="nifty/niftyPrint.css" media="print">
<script type="text/javascript" src="nifty/nifty.js"></script>

<script type="text/javascript">
	window.onload=function()
	{
		//if(!NiftyCheck()) return;
		//Rounded("table#header_toolbar","#377CB1","#9BD1FA");
		//Rounded("div#header_top","#377CB1","#FF0000");
		//Rounded("div#siteBody","#D1D1D1","#FFFFFF");
		//Rounded("div#footer","#D1D1D1","#ECECFF");
		//Rounded("div#footer","#D1D1D1","#E0F4FC");
		//Rounded("div#footer","#D1D1D1","#FFFFFF");
	}
</script>
<meta name="google-site-verification" content="etnUtyH-Rgd_ZUi3jOCRxRL9LTe213wFJptyI0U6jZ4" />
</head>

<body>
<center>

<div class="header_top" id="header_top">
<table id="nav"><tr>

	<td class="nav-home"><table class="innylink"><tr><td><a href="/"><img src="/img/ss03.jpg" title='Go to Straight Street home page' alt='Straight Street logo and home link' border=0></a></td></tr></table></td>
<?php 
if (!isset($_in_account_page) && !isset($_in_help_content_page))
{
?>
	<td class="nav-others"><table class="innylink"><tr><td><a href="/gallery.php" >Get<br>Symbols</a></td></tr></table></td>
	<td class="nav-others"><table class="innylink"><tr><td><a href="/apps.php">Get<br>Programs</a></td></tr></table></td>
	<td class="nav-others"><table class="innylink"><tr><td><a href="/dev.php" >Developers</a></td></tr></table></td>
	<td class="nav-others"><table class="innylink"><tr><td><a href="/contrib.php" >Contributors</a></td></tr></table></td>
	<td class="nav-others"><table class="innylink"><tr><td><a href="/settings.php">Settings</a></td></tr></table></td>
	<td class="nav-others"><table class="innylink"><tr><td><a href="/help.php">Help</a></td></tr></table></td>
<?php 
}
?>

</tr></table>
</div>

<!--div style="clear: right;">&nbsp;</div-->

<div class="header_top_base" id="header_top_base">
<!-- spacer - long thin div for fader bg img -->
&nbsp;
</div>

<!--div class="siteNonBody"-->

<?
if (!isset($_in_help_page) && !isset($_in_help_content_page))
{
?>
    <table class="header_welcomebar" id="header_toolbar">
    <?
    if (!isset($_in_account_page))
    {
        // See if logged in?
        if ($loggedUser=="") { 
            $strHTML1 = "You are not logged in.";
            $strHTML2 = '[ <a href="login.php">My Account</a> ]';
    /*		"
            <input id='uname' class='nice' style=\"width:75px;\"> <input type='password' id='upass' class='nice' style=\"width:75px;\">
            [ <a href=\"javascript:tryLogin('uname','upass','uremember');\">Login</a> ]<!--input id='uremember' type='checkbox'-->
            [ <a href='/register.php'>Register</a> ]
            ";
    */
        } else {
            $fname = htmlenc(getUserFname($loggedUser));
            $sname = htmlenc(getUserSname($loggedUser));
            $uname = htmlenc($loggedUser);
            
            $strHTML1 = "Welcome $fname $sname, you are logged in as $uname. "; 
            $strHTML2 = "[ <a href='javascript:logout();'>logout</a> ]";
            if (isUserAtLeastAdmin()) 
            {
                $version = htmlenc($site_version);
                $strHTML2 .= "&nbsp;&nbsp;&nbsp;Website Version $version";
            }
/*            elseif (!isUserEmailConfirmed($loggedUser))
            {
                $strHTML2 .= ' <div class="formError">You have not yet confirmed your email address - see [<a href="userinfo.php">My info</a>].</div>';
            }*/
        }

        //============================================================= All User Alevels TOOLBAR

        echo "<tr><td class='welcome'>$strHTML1$strHTML2</td></tr>";
        echo "<tr><td class='welcome'>";
            include('_links_admin.php');
            echo "<br/>";
            include('_links_partner.php');
            echo "<br/>";
            include('_links_user.php');
            include('_links_reviewer.php');
        //	include('_links_contrib.php');
            echo "</td></tr>";

        //=============================================================
    }
    else
    {
        echo '<td class="welcome">User account</td></tr>';
    }
}
?>

</table>

<!--/div-->

<div class="siteBody" id="siteBody">
<?
if (!isset($_in_help_page))
{
    print ('<div class="sitebodyinnerspacer"><br><br>');
}
?>


