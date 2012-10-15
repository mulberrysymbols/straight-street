<?php
require("GTranslate.php");

$x = $_REQUEST['x'];
$sWordEn = $_REQUEST['w'];
$sLangTo = $_REQUEST['langto'];

$translate_string = $sWordEn;
try {
       $gt = new Gtranslate;
	echo $x."	".$gt->english_to_french($sWordEn);

	/**
	* Lets switch the request type to CURL
	*/
	//$gt->setRequestType('curl');

	//echo "[CURL] Translating [$translate_string] German to English => ".$gt->german_to_english($translate_string)."<br/>";

} catch (GTranslateException $ge)
 {
       echo "#ERROR:".$ge->getMessage();
 }

?>
