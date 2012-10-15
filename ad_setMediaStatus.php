<?
require('_header.php');

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();

function removeSymbolsFromReview($dbh, $strInClause, $ids)
{
    $sql = "DELETE FROM t_review_media WHERE mid IN $strInClause";
    $st = $dbh->prepare($sql);
    $st->execute($ids);
    $sql = "DELETE FROM t_review_results 
            WHERE EXISTS (SELECT * FROM t_review_dataset rd
                            WHERE rd.id = rdsid AND rmid IN $strInClause)";
                            print $sql;
    $st = $dbh->prepare($sql);
    $st->execute($ids);
    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $ids = array();
    foreach ($_POST as $key => $value)
    {
        if (!in_array($key, array('newstatus','status','review')) && $key[0] == 'r')
        {
            $id = substr($key, 1);
            $ids[] = $id;
        }
    }
    $strInClause = '('.str_repeat('?, ', count($ids)).')';
    $strInClause = substr_replace($strInClause, '', -3, 2); // lose the extra ', '
    
    $newstatus = $_POST['newstatus'];
    
    $sql = "UPDATE t_media SET status_id = ? WHERE id IN $strInClause ORDER BY name;";
    $st = $dbh->prepare($sql);
    array_unshift($ids, $_POST['newstatus']);
    $st->execute($ids);
    array_shift($ids);

    if ($newstatus == '3') // review
    {
        removeSymbolsFromReview($dbh, $strInClause, $ids);

        $new_review = $_POST['new_review'];
        
        $strValuesClause = str_repeat('(?,?),', count($ids));
        $strValuesClause = substr_replace($strValuesClause, '', -1, 1); // lose the extra ', '
        $sql = "INSERT into t_review_media (mid, rid) VALUES $strValuesClause";
        $values = array();
        foreach($ids as $id)
        {
            $values[] = $id;
            $values[] = $new_review;
        }
        $st = $dbh->prepare($sql);
        $st->execute($values);
        print $sql;
        print_r($values);
    }
    elseif ($newstatus == '2') // uploaded
    {
        removeSymbolsFromReview($dbh, $strInClause, $ids);
    }
    
    $nxtcmd = '?status='.htmlenc($_POST['status']).'&review='.htmlenc($_POST['review']);
    redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1)).$nxtcmd);
}    
else
{

$status = (isset($_GET['status'])) ? $_GET['status'] : '0';
$review = (isset($_GET['review'])) ? $_GET['review'] : '0';
// TODo sanitise these

?>
Set Media status
<ul>

<div class="blue_content_div" id="mediastatus">
<div class="innerdivspacer">

<?
    print  '<form name="theform" id="theform" method="get" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n";
    print( 'Restrict media to those with <label for="status">Status: </label>'.getListHTML('status', 'status', $status, False, True, True)."\n");
    $sql = 'SELECT id, name FROM t_review where status IN (1, 2) ORDER BY name;';
    print( 'and/or in <label for="review">Review: </label>'.GetSelectionHTML('review', $sql, $review, True)."\n");
    print( '<input type="submit" accesskey ="G" value="Get"/>'."\n");
    print ('</form>'."\n");

    print ('<form name="theotherform" id="theotherform" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n");
   if (isset($_GET['status']) && isset($_GET['review']))
   {
    print ('<input type="hidden" name="status" value="'.htmlenc($status).'">');
    print ('<input type="hidden" name="review" value="'.htmlenc($review).'">');
    print ("<table id='managemedia'><tr><td id='l'>");
    print ("<table id='statii'>");

        try
        {

        $sql = <<<EOT
        SELECT m.id, m.name, ms.name as status, concat('t-',m.name,'.gif') AS thumb, MIN(r.name) as review
        FROM t_media m
        LEFT OUTER JOIN t_review_media rm
          ON m.id = rm.mid
        LEFT OUTER JOIN t_media_status ms
          ON ms.id = m.status_id
        LEFT OUTER JOIN t_review r
          ON r.id = rm.rid
        WHERE ((0 = ?) OR (status_id = ?)) AND ((0=?) OR (r.id = ?))
        GROUP BY m.id, m.name, ms.name , concat('t-',m.name,'.gif')
EOT;

            $st = $dbh->prepare($sql);
            $st->execute(array($status, $status, $review, $review));
            $norows = ($st->fetchColumn() == 0);
            $st->execute(array($status, $status, $review, $review)); // nasty work arround for lack of number of rows
            $disabled = ($status=='0' || $norows) ? 'disabled="disabled"' : '';

            print("<tr id='header'><td><input $disabled type='checkbox' checked='checked' id='all' />All</td><td></td><td width='60%'></td><td width='60%'></td><td></td></tr>\n");
            while ($row = $st->fetch(PDO::FETCH_ASSOC))
            {
                $id = htmlenc($row['id']);
                print("<tr><td><input $disabled type='checkbox' checked='checked' name='r$id' id='r$id' /></td>\n".
                        "<td><image src='$symbolsThumb".htmlenc($row['thumb'])."' title='".htmlenc($row['name'])."' alt='".htmlenc($row['name'])."'/>".
                        "</td><td>".htmlenc($row['name'])."</td>".
                        "<td>".$id."<br/>".
                        " ".htmlenc($row['review'])."<br/>".
                        " ".htmlenc($row['status'])."</td>".
                        '<td><a class="edlist" href="'.htmlenc('ad_editMedia.php?cmd=edit&id='.$row['id']).'"><img src="img/b_edit.png" title="edit '.$row['name'].'" alt="a pencil"></a>'.
                        "</tr>\n");
            }
 
         } 
         catch (Exception $e)
         {
             print $e->getMessage();
             exit();
         }
        print ('</table>');
        print ('</td><td id="r">');
        print ("<select $disabled name='newstatus' id='newstatus'>\n");
        
        $strQuery= <<<EOT
        SELECT transitions.id, transitions.sto AS status FROM (
        SELECT status AS sfrom, 'Dev' AS sto, Dev AS alow, (SELECT id FROM t_media_status WHERE name = 'Dev') AS id FROM t_media_status_transitions mst
        UNION
        SELECT status, 'Uploaded', Uploaded, (SELECT id FROM t_media_status WHERE name = 'Uploaded') AS id  FROM t_media_status_transitions
        UNION
        SELECT status, 'Review', Review, (SELECT id FROM t_media_status WHERE name = 'Review') AS id  FROM t_media_status_transitions
        UNION
        SELECT status,  'Live', Live, (SELECT id FROM t_media_status WHERE name = 'Live') AS id  FROM t_media_status_transitions
        UNION
        SELECT status, 'Rejected', Rejected, (SELECT id FROM t_media_status WHERE name = 'Rejected') AS id  FROM t_media_status_transitions) AS transitions
        INNER JOIN t_media_status status ON status.name = transitions.sfrom
         WHERE status.id = ? and transitions.alow = 1
        ORDER BY id
EOT;
        $st = $dbh->prepare($strQuery);
        $st->execute(array($status));
        while ($row = $st->fetch(PDO::FETCH_ASSOC))
        {
            //print ($status.' '.$row['id'].' '.$row['status']);
            $id = htmlenc($row['id']);      // a messy mishmash encoding format this but will do for now
            $nstatus = htmlenc($row['status']);
            print ("<option value='$id'>$nstatus</option>\n");
        }
        print ("</select>\n");
        
        $sql = 'SELECT id, name FROM t_review where status IN (0) ORDER BY name;';
        print( '<div>'.GetSelectionHTML('new_review', $sql, $review, False)."</div>\n");
       
        print( "<input $disabled type='submit' id='setstatus' accesskey='S' value='Set Status'/>\n");
        print('</td></tr></table>');
    }
    print ('</form>'."\n");

?>


</div></div>


<script type="text/javascript">	
    function onNewStatusChange()
    {
        var status_id = this.value;
        var elReviewName = document.getElementById('new_review');
        if (elReviewName)
            elReviewName.disabled = ((status_id != "3") ? true : false);
    }
    
    function onAllChange()
    {
        var chckd = this.checked;
        var elStatii = document.getElementById('statii');
        for (i=1; i<elStatii.rows.length; i++)
        {
            var row = elStatii.rows[i];
            row.cells[0].childNodes[0].checked = chckd;
        }
    }
    
    function setHandlers()
    {
        var elNewStatus = document.getElementById('newstatus');
        if (elNewStatus)
        {
            //elNewStatus.addEventListener('change',onNewStatusChange,false)
            elNewStatus.onchange = onNewStatusChange;
            onNewStatusChange.call(elNewStatus);
        }

        var elAll = document.getElementById('all');
        if (elAll)
        {
            elAll.onclick = onAllChange;
            onAllChange.call(elAll);
        }
    }

	Rounded("div#mediastatus","#FFFFFF","#ECECFF");
    window.onload=setHandlers;
</script>

<?

include('_footer.php');
}
?>
