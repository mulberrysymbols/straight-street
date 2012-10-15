<?
//----------------------
//require("GTranslate.php");
//----------------------
?>

<html>
<head>
<title>This is the test page</title>
<script type="text/javascript" src="jquery-1.3.2.js"></script>

<style>
body {
	font-family:arial;
}
div.container
{
	border:4px solid green;
}
div.container div.row
{
	border:0px solid green;
	width:400px;
}

div.container div.row div 
{
	border:1px solid blue;
	width:150px;
	float:left;
	text-align:center;
	margin:2px;
	height:26px;
	vertical-align:middle;
}
div.container div.row div.word_en 
{
	background-color:#fff;
}
div.container div.row div.word_en_sel 
{
	background-color:#afa;
}
div.container div.row div.word_tr 
{
	background-color:#ddd;
}
div.container div.row div.word_tr_changed 
{
	background-color:#ff7;
}

div.container div.row div.title_en,
div.container div.row div.title_tr
{
	font-weight:bold;
	font-size:16px;
	background-color:#555;
	color:#fff;
}
div.container div.row div input 
{
	width:100px;
}

div#previewWindow 
{
	display:none;
	position:absolute;
	top:100px;
	left:300px;
	border:4px solid #afa;
}
div#previewWindow img
{
	width:150px;
	border:1px solid green;
}

</style>

<script>
function translateTable()
{
	if (confirm('Do you wish to RE-TRANSLATE the whole table?')) {


	var now = new Date();

	//get value of hidden field, which contains the number of words in the table
	$wordCount = $('#wordcount').val();

	//loop thru objects
	for ($x=0;$x<$wordCount;$x++)
	{
		//get word on this row
		$thisWord = $('#en_'+$x).html();

		//alert($thisWord);

		//translate each word
		$.ajax({
			type: "POST",
			url: "misc_gtranslate.php",
			data: "x="+$x+"&w=" +$thisWord+ "&ms=" + now.getTime(),
			success: function(sResult){
				//on completion, put result (translation) into new cell editbox

				//*** NOTE ***
				//Due to the asynchronous nature of ajax, the results 
				//for each word request will come back out of order.
				//So each requested word is paired with an ID (1,2,3,etc)
				//and then the returned translated word also contains the same id.
				//This means that the translated word can be inserted into the 
				//correct table cell.

				// req "3 Green" ---->
				//				<---- rec'v "3 Vert"

				//split into ID and TEXT
				$aryValues=sResult.split('	');
				// [0] is ID, [1] is TEXT
				$('#tr_'+$aryValues[0]).val($aryValues[1]);

				//set class of newly translated cell to default
				//(incase a user has manually changed the value, which changes the div class)
				$('#div_tr_'+$aryValues[0]).attr('class','word_tr');
			}
		});
	}

	}

}
function showPreview($id)
{
	//set image on preview window
	$sEnWord = $('#en_'+$id).html().toLowerCase();
	$('#previewImg').attr('src',$sEnWord+'.wmf');

	//set class of english word cell, to "sel" to simulate highlighting a row
	$('#en_'+$id).attr('class','word_en_sel');

	//show preview window
	$('#previewWindow').show();
}
function hidePreview($id)
{
	//set class of english word cell back to normal
	$('#en_'+$id).attr('class','word_en');


	//hide window when textbox loses focus
	$('#previewWindow').hide();
}
function valueChanged($id)
{
	//set class of a translated cell to indicate that it's been changed manually
	$('#div_tr_'+$id).attr('class','word_tr_changed');

}



</script>


</head>

<body>

<div id="previewWindow">
<img id="previewImg">
</div>

<input type="button" value="Translate Table" onClick="translateTable();">
<br/><br/>

<div class="container" onClick="james(this);">
<div class="row">
<div class="title_en">English</div><div class="title_tr">Translation</div>
<br style="clear:both;">
</div>



<?
//----------
//Simulating a PHP fetch of data (DB) for words to be translated
//----------
//simulated list of words (in an array here)
$aryWordsEn = array ("Fruit","Apple","Hello","Pear","Banana","Car","Bag","Train","Pencil","Shoe");
$x=-1;

while ($x<count($aryWordsEn)-1) {
$x++;
//----------
?>
	<div class="row">
	<div id="en_<?=$x;?>" class="word_en"><?=$aryWordsEn[$x]?></div><div id="div_tr_<?=$x;?>" class="word_tr"><input type="text" id="tr_<?=$x;?>" onFocus="showPreview('<?=$x;?>')" onBlur="hidePreview('<?=$x;?>')" onChange="valueChanged('<?=$x;?>')"></div>
	<br style="clear:both;">
	</div>

<?
//----------
}
//----------
?>

<input type="hidden" id="wordcount" value="<?=$x+1;?>">
<br style="clear:both;">
</div>

</body>
</html>