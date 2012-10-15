<html>
<head>
<title>This is the test page</title>


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
</script>



</head>

<body>

<div class="container" onClick="james(this);">
	<div class="title" onClick="james(this);">Hello this is some text</div>
	<div class="text">Here's some more text</div>
	<div class="text">Here's some more text</div>
</div>

<span id="object1" class="text" onClick="james(this);">More text</span>

<div class="title" onClick="james(this);">ANOTHER TITLE</div>



</body>
</html>