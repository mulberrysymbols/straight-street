<html>
<head>
<title>This is the test page</title>
<script type="text/javascript" src="jquery-1.3.2.js"></script>

<style>
body {
	font-family:arial;
}
div 
{
	border:2px solid blue;
}
div.container
{
	border:4px solid red;
}
div.container div.title
{
	font-size:20px;
	color:blue;
}
.text 
{
	font-weight:bold;
}
</style>


<script>
function james($objDiv)
{
	//alert('hello');
	alert('content of clicked object is:'+$objDiv.outerHTML);
}
function flintstone()
{
	//alert('hello');
	var now = new Date();
	var mycounter = $('#countervalue').val()

	$.ajax({
		type: "POST",
		url: "dosomestuff.php",
		data: "counter=" +mycounter+ "&ms=" + now.getTime(),
		success: function(sResult){
			//if do reload then we lose the current pic page (goes back to #1!)
			//location.reload();
			//alert(sResult);

			$('#hereplease').html(sResult);
			
			
		}
	});




}

</script>


</head>

<body>

<div class="container" onClick="james(this);">
	<div class="title" onClick="james(this);">Hello this is some text</div>
	<div class="text">Here's some more text</div>
	<div class="text">Here's some more text</div>
</div>

<?
//----------------
$x=0;
while ($x<10) 
{
	$x++;
	echo "			<div class=\"text\">PHP Gen'd Text</div>\n";
}
//----------------
?>


<span id="object1" class="text" onClick="james(this);">More text</span>

<div class="title" onClick="james(this);">ANOTHER TITLE</div>

<br/>

<input type="text" id="countervalue" value="10">
<input type="button" value="Ajax me!" onClick="flintstone()">

<div id="hereplease">blah</div>

</body>
</html>