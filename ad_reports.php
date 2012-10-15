<?
include('_header.php');
include('_reporters.php');

if (isUserAtLeastAdmin()) 
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
<label><input type='checkbox' id='csv' name='csv' checked='checked' onclick="document.forms['rform'].csv.value=(this.checked)?1:0;"/>CSV file</label> 

<div class="blue_content_div" id="blue_content_div1">
<div class="innerdivspacer">

    <h3>User activity reports</h3>
		[ <a href="javascript:submit('downloads')">Who has downloaded?</a> ]
		<br>
		[ <a href="javascript:submit('api_hits')">Who has used the API??</a> ]

</div></div>
<br>

<div class="blue_content_div" id="blue_content_div2">
<div class="innerdivspacer">		

    <h3>Media reports</h3>
		[ <a href="javascript:submit('all_media')">All media</a> ]
		<br>
		[ <a href="javascript:submit('all_symbols_pdf_live')">All Live Symbols PDF</a> ]
		[ <a href="javascript:submit('all_symbols_pdf')">All Symbols PDF</a> ] - warning slow and resource intensive
		<br>
		[ <a href="javascript:submit('tags_list')">Symbol tags - list</a> ]
		<br>
		[ <a href="javascript:submit('tags_rows')">Symbol tags - rows</a> ]
		<br>
		[ <a href="javascript:submit('del_symb')">Rejected symbols</a> ]
		<br>
		[ <a href="javascript:submit('cross_check')">Symbol cross check</a> ]

</div></div>
<br>

<div class="blue_content_div" id="blue_content_div3">
<div class="innerdivspacer">		

	<h3>Review reports</h3>
		[ <a href="javascript:submit('review_participants')">Who took part in Reviews</a> ]
		
<br>
	
</div></div>
</div>
<br>	
	
<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
	Rounded("div#blue_content_div2","#FFFFFF","#ECECFF");
	Rounded("div#blue_content_div3","#FFFFFF","#ECECFF");
</script>
		
	<form action="<?=$_SERVER['PHP_SELF']?>" name="rform" id ="rform" method="post" >
		<input type='hidden' name='report' id='report' value="1"> 
		<input type='hidden' name='csv' id='csv' value="1"> 
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
            
        if (mb_substr($which, 0, 15) == 'all_symbols_pdf')
        {   $host  = $_SERVER['HTTP_HOST'];
            $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $qs = (mb_substr($which, -4) == 'live') ? 's=live' : 's=all';
            header("Location: http://$host$uri/ad_pdfreports.php?$qs");
            exit();
        }
                
		elseif  (mb_substr($which,0, 5)  == 'tags_')
		{
			if (mb_substr($which,-4) == 'rows')
			{
				$grp_clause = '	GROUP BY m.id, t.tag';
			}
			else
			{
				$grp_clause = '	GROUP BY m.id';
			}
			
			$sql = <<<EOT
			SELECT
  			REPLACE(m.name, '_', ' ') AS name
			, m.id
			, tms.name AS status
			, IFNULL(GROUP_CONCAT(t.tag ORDER BY t.tag SEPARATOR ', '), '') tags
			FROM t_media m
			LEFT JOIN t_media_status AS tms
			 ON m.status_id = tms.id
			LEFT JOIN t_media_tags mt
			 ON mt.mid = m.id
			LEFT JOIN t_tag t
			 ON t.id = mt.tid
			WHERE m.status_id <> 5
			$grp_clause
			ORDER BY m.name;
EOT;
		}
		elseif  ($which  =='del_symb')
		{
			$sql = <<<EOT
			SELECT
  			REPLACE(m.name, '_', ' ') AS name
			, m.id
			, tms.name AS status
			, IFNULL(GROUP_CONCAT(t.tag ORDER BY t.tag SEPARATOR ', '), '') tags
			FROM t_media m
			LEFT JOIN t_media_status AS tms
			 ON m.status_id = tms.id
			LEFT JOIN t_media_tags mt
			 ON mt.mid = m.id
			LEFT JOIN t_tag t
			 ON t.id = mt.tid
			WHERE m.status_id = 5
			GROUP BY m.id, t.tag
			ORDER BY m.name;
EOT;
}
		elseif  ($which  =='all_media')
		{
			$wide = True;
			$sql = <<<EOT
			SELECT
			m.id
  			, REPLACE(m.name, '_', ' ') AS name
			, m.creation_date
			, m.original_name
            , g.name AS grammar
			, tmc.name  AS category
			, m.rated
			, tmdr.name AS designers_ref
			, tma.name AS author
			, tmw.name AS word_list
			, tmfp.name AS finishing_pool
			, tmf.name AS finisher
			, tms.name AS status
			, tmv.name AS version
			, m.comment AS comment
			, tr.name AS review
			FROM t_media m
			LEFT JOIN t_media_category AS tmc
			 ON m.category_id = tmc.id
			LEFT JOIN t_media_designers_ref AS tmdr
			 ON m.designers_ref_id = tmdr.id
			LEFT JOIN t_media_person AS tma
			 ON m.author_id = tma.id
			LEFT JOIN t_media_wordlist AS tmw
			 ON m.wordlist_id = tmw.id
			LEFT JOIN t_media_finishing_pool AS tmfp
			 ON m.finishing_pool_id = tmfp.id
			LEFT JOIN t_media_person AS tmf
			 ON m.finisher_id = tmf.id
			LEFT JOIN t_media_status AS tms
			 ON m.status_id = tms.id
			LEFT JOIN t_media_version AS tmv
			 ON m.version_id = tmv.id
			LEFT JOIN t_review_media AS trm
			 ON trm.mid = m.id
			LEFT JOIN t_review AS tr
			 ON tr.id = trm.rid
			LEFT JOIN t_media_vocab v
			 ON v.m_id = m.id
			LEFT JOIN t_media_grammar g
			 ON g.id = v.g_id

			ORDER BY m.name;
EOT;
		}
		
		elseif ($which  == 'downloads')
		{
			$sql = <<<EOT
			SELECT
            u.username,
			u.id as user_id,
  			d.file,
            d.`when`
			FROM t_downloads d
			INNER JOIN t_user AS u
			 ON d.userid = u.id
			ORDER BY d.`when` DESC, u.username, d.file;
EOT;
		}
		elseif  ($which  =='api_hits')
		{
			$sql = <<<EOT
			SELECT
  			appid
			, clientip 
			, count
			FROM t_api_log al
			ORDER BY appid, clientip;
EOT;
		}
		elseif ($which  == 'review_participants')
		{
			$sql = <<<EOT
			SELECT r. name , u.username, rds.status
			FROM t_review r, t_review_dataset rds
			INNER JOIN t_user u
			WHERE r.id = rds.rid 
				AND (r.status =2 OR r.status =1) 
				AND u.id=rds.userid
			ORDER BY r.name 
EOT;
		}
		elseif ($which  == 'cross_check')
		{
			$sql = <<<EOT
			SELECT
 			m.name AS name
 			, '' AS thumb
			, '' AS preview
 			, '' AS wmf
			, '' AS svg
			, '' AS png
			FROM t_media m
			LEFT JOIN t_media_status AS tms
			 ON m.status_id = tms.id
			WHERE m.status_id = 4
			ORDER BY m.name, tms.name;
EOT;
			
			function getRow($result)
			{	
				
				if (!($row = mysql_fetch_assoc($result)))
					return False;
					
				global $symbolsThumb, $symbolsPreview, $symbolsWMF, $symbolsSVG, $symbolsPNG; 	
				$file = $row['name'];
				$row['thumb'] =  (file_exists($symbolsThumb.'t-'.$file.'.gif')) ? '' : 'X';
				$row['preview'] = (file_exists($symbolsPreview.'m-'.$file.'.gif')) ? '' : 'X';
				$row['wmf'] =  (file_exists($symbolsWMF.$file.'.wmf')) ? '' : 'X';
				$row['svg'] =  (file_exists($symbolsSVG.$file.'.svg')) ? '' : 'X';
				$row['png'] =  (file_exists($symbolsPNG.$file.'.png')) ? '' : 'X';
				
				if ($row['thumb'] == '' && $row['preview'] == '' && $row['wmf'] == '' && $row['svg'] == '' && $row['png'] == '')
					return array(); // don't show
					
				return $row;
			}
			
			$getRow = 'getRow';
		}
		
        $reporter = ($csv) ? new CSVReporter($sql, $wide, $getHdr, $getRow) : new HTMLReporter($sql, $wide, null, $getRow);
        $reporter->run();
	}
}

include('_footer.php');
?>