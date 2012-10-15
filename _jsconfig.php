<?
include('_common.php');

echo "var g_bIsUserLoggedOn = ".((isUserLoggedOn()) ? 'true' : 'false')."\n";
echo "var g_bIsAdminUser = ".((isUserAtLeastAdmin()) ? 'true' : 'false')."\n";

?>