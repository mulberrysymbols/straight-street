<?
include('_header.php');
include('_reporters.php');

if (isUserAtLeastPartner()) 
{
	if ($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		?>

<script type="text/javascript" src="js/cvi_busy_lib.js"></script>
<script type="text/javascript" >
if(typeof $=='undefined') {function $(v) {return(document.getElementById(v));}}
function submit(what)
{
    document.forms['rform'].report.value=what;
    ctrl = getBusyOverlay($('divtop'), {opacity:0.5, text:'Running report'}, {size:40});
    //    ctrl.remove();
    document.forms['rform'].submit();
    window.onblur=function(){ctrl.remove();};
}
</script>

<div id='divtop'>
Data is available as HTML report or <label><input type='checkbox' id='csv' name='csv' onclick="document.forms['rform'].csv.value=(this.checked)?1:0;"/>CSV file</label> 

<div class="blue_content_div" id="blue_content_div1">
<div class="innerdivspacer">

    <h3>Partner Data</h3>
		[ <a href="javascript:submit('all_media')">Symbol Metadata</a> ]

<br/>[ <a href="/viewcart.php?what=all">The symbols</a> ]

</div></div>
<br>
	
<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
</script>
		
	<form action="<?=$_SERVER['PHP_SELF']?>" name="rform" id ="rform" method="post" >
		<input type='hidden' name='report' id='report' value="1"> 
		<input type='hidden' name='csv' id='csv' value="0"> 
		<!--<input type="submit" value="Download" > -->
		
	</form>


		<?
	}
	else
	{
        $csv = (isset($_POST['csv'])) ? $_POST['csv']=='1' : false;
		$which = (isset($_POST['report'])) ? $_POST['report'] : null;
		$wide = false;
 		$getHdr = null;
		$getRow = null;
       
		if  ($which  =='all_media')
		{
			$wide = True;
			$sql = <<<EOT
			SELECT
 			v.name AS symbol
 			, v.l_id as lang
 			, REPLACE(v.name, '_', ' ') AS name
 			, v.synonym
			, g.name AS grammar
			, tmc.name  AS category
			, m.rated
			, GROUP_CONCAT(t.tag ORDER BY t.tag SEPARATOR " ") AS tags
			FROM t_media m
			LEFT JOIN t_media_category AS tmc
			 ON m.category_id = tmc.id
			LEFT JOIN t_media_tags mt
			 ON mt.mid = m.id
			LEFT JOIN t_tag t
			 ON t.id = mt.tid
			LEFT JOIN t_media_vocab v
			 ON v.m_id = m.id
			LEFT JOIN t_media_grammar g
			 ON g.id = v.g_id
			WHERE m.status_id = 4
			GROUP BY v.name, v.l_id, v.synonym, g.name, tmc.name, m.rated
			ORDER BY m.name;
EOT;
		}
				
        $reporter = ($csv) ? new CSVReporter($sql, $wide, $getHdr, $getRow) : new HTMLReporter($sql, $wide, null, $getRow);
        $reporter->run();
	}
}

include('_footer.php');
?>