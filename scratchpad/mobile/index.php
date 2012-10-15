<!DOCTYPE html> 
<html>
<head>
<title>Mobile Symbol Search</title>

	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a1/jquery.mobile-1.0a1.min.css" />
	<script src="http://code.jquery.com/jquery-1.4.3.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0a1/jquery.mobile-1.0a1.min.js"></script>

<script>
$(document).ready(function(){

	$("#myform").submit(function(){

		$.ajax({
		   type: "POST",
		   url: "_db_mobSearch.php",
		   data: "t="+$("input#txtsearch").val(),
		   success: function(msg){
		     $("div#thumbs").html(msg);
		   }
		 });


      		return false;// stop default form posting action
	});
   
});
</script>

<style>
div#thumbs {
	text-align:center;
	font-weight:bold;
	overflow:hidden;

}
div#thumbs img {
	width:40px;
	height:40px;
	float:left;
	margin:5px;
	border:1px solid #aaa;
}
</style>
</head>

<body>

<!-- Start of first page -->
<div data-role="page" id="foo">

	<div data-role="header">
		<h1>Straight Street</h1>
	</div><!-- /header -->

	<div data-role="content">	

		<form id="myform" action="index.php" method="GET" onsubmit="testtest()">
		<div data-role="fieldcontain">
		    <label for="search">Symbol Search:</label>
		    <input type="search" name="txtsearch" id="txtsearch" value="" />
		</div>
		</form>

		<div id="thumbs"></div>
	
	</div><!-- /content -->

	<div data-role="footer">
		<h4>&nbsp;</h4>
	</div><!-- /header -->
</div><!-- /page -->

</body>
</html>
