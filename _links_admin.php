<?
if (isUserAtLeastAdmin()) { 
?>
Admin Links [ 
 <a href="/ad_admin.php">Website Admin</a>
 - <a href="/ad_groupemails.php">Group Emails</a>
 - <a href="/ad_reports.php">Reports</a>
]

<?
}
elseif (isUserAtLeastEditor()) { 
?>
Editing Links [ 
 <a href="/ad_admin.php">Website Editing</a>
 ]

<?
}
?>
