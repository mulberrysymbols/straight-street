<?
require('_header.php');

if (!isUserAtLeastEditor())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $ids = array();
    foreach ($_POST as $key => $value)
    {
        if (!in_array($key, array('add','remove','newtags','add','status_id','media_id','media_match','tag_id','finishing_pool_id','category_id')) && $key[0] == 'r')
        {
            $id = substr($key, 1);
            $ids[] = $id;
        }
    }
    
    if (isset($_POST['addtag']))
    {
        $sql = "INSERT INTO t_tag (tag) VALUES (?)";
        $st = $dbh->prepare($sql);
    	$st->execute(array($_POST['newtag']));
    }
    elseif (isset($_POST['deltag']))
    {
        // we delete it if not referenced at all or otherwise just set the deleted field
        $sql = "DELETE FROM t_tag WHERE t_tag.id = ? AND NOT EXISTS (SELECT 'x' FROM t_media_tags AS mt WHERE mt.tid = t_tag.id);";
        $st = $dbh->prepare($sql);
        $st->execute(array($_POST['dtag_id']));
//        $sql = "UPDATE $table SET deleted = TRUE WHERE id = ?;";
//        $st = $dbh->prepare($sql);
    }
    elseif (isset($_POST['rentag']))
    {
        $sql = "UPDATE t_tag SET tag = ? WHERE t_tag.id = ?;";
        $st = $dbh->prepare($sql);
        $st->execute(array($_POST['newname'], $_POST['rtag_id']));
    }
    elseif (isset($_POST['add']))
    {
        $newtags = db_escape_string($_POST['newtags']); // only one for now
        $strInClause = str_repeat("(?, $newtags),", count($ids));
        $strInClause = substr_replace($strInClause, '', -1, 1); // lose the extra ','
        
        $sql = "INSERT IGNORE INTO t_media_tags (mid, tid) VALUES $strInClause";
        $st = $dbh->prepare ($sql);
        $st->execute($ids);
    }
    elseif (isset($_POST['remove']))
    {
        $strInClause = '('.str_repeat('?, ', count($ids)).')';
        $strInClause = substr_replace($strInClause, '', -3, 2); // lose the extra ', '
        
        $sql = "DELETE FROM t_media_tags WHERE tid = ? AND mid IN $strInClause" ;
        $st = $dbh->prepare($sql);
        array_unshift($ids, $_POST['newtags']);
        $st->execute($ids);
    }
    
    $nxtcmd = '?status_grp='.htmlenc($_POST['status_grp']).'&media_id='.htmlenc($_POST['media_id']).'&media_match='.htmlenc($_POST['media_match']).'&tag_id='.htmlenc($_POST['tag_id']).'&finishing_pool_id='.htmlenc($_POST['finishing_pool_id']).'&category_id='.htmlenc($_POST['category_id']);
    redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1)).$nxtcmd);
}    
else
{

$status_grp = (isset($_GET['status_grp'])) ? $_GET['status_grp'] : 'live';
$media_id = (isset($_GET['media_id'])) ? $_GET['media_id'] : '';
$media_match = (isset($_GET['media_match'])) ? $_GET['media_match'] : '';
$tag_id = (isset($_GET['tag_id'])) ? $_GET['tag_id'] : '0';
$finishing_pool_id = (isset($_GET['finishing_pool_id'])) ? $_GET['finishing_pool_id'] : '0';
$category_id = (isset($_GET['category_id'])) ? $_GET['category_id'] : '0';
// TODo sanitise these

?>
Manage media Tags
<div class="blue_content_div" id="mediastatus">
<div class="innerdivspacer">
	<form name="theform" id="theform" method="post" action="<?= htmlenc($_SERVER['PHP_SELF']) ?>">
<?
    print ('<input type="hidden" name="status_grp" value="'.htmlenc($status_grp).'">');
    print ('<input type="hidden" name="media_id" value="'.htmlenc($media_id).'">');
    print ('<input type="hidden" name="media_match" value="'.htmlenc($media_match).'">');
    print ('<input type="hidden" name="tag_id" value="'.htmlenc($tag_id).'">');
    print ('<input type="hidden" name="finishing_pool_id" value="'.htmlenc($finishing_pool_id).'">');
    print ('<input type="hidden" name="category_id" value="'.htmlenc($category_id).'">');
?>
		<table class="form">
		<tr>
			<td><label for="newtag">New: </label></td>
			<td><input class="edmedia" type="text" size="20" maxlength="30" name="newtag" id="newtag" value="" tabindex="1"/></td>
			<td>&nbsp;<input type="submit" accesskey ="A" name="addtag" id="addtag" value="Add"/><br/></td>
		</tr>
		<tr>
<?
    $sql = 'SELECT t.id, tag name FROM t_tag t WHERE NOT EXISTS (SELECT * FROM t_media_tags mt WHERE mt.tid = t.id) ORDER BY tag;';
    print( '<td><label for="dtag_id">Delete: </label></td><td>'.GetSelectionHTML('dtag_id', $sql, null, True)."\n</td>");
    print( '<td>&nbsp;<input type="submit" accesskey ="D" name="deltag" value="Delete"/></td>'."\n</td>");
?>
        </tr><tr>
<?
    $sql = 'SELECT t.id, tag name FROM t_tag t ORDER BY tag;';
    print( '<td><label for="rtag_id">Rename: </label></td><td>'.GetSelectionHTML('rtag_id', $sql, null, True)."\n</td>");
?>
			<td><input class="edmedia" type="text" size="20" maxlength="30" name="newname" value="" /></td>
			<td>&nbsp;<input type="submit" accesskey ="R" name="rentag" value="Rename"/></td>
		</tr>
		</table><p></p>
	</form>
    
</div></div><br/>

Apply media Tags
<div class="blue_content_div" id="mediastatus">
<div class="innerdivspacer">

<?
    print  '<form name="applyform" id="applyform" method="get" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n";
    $status_all = (($status_grp == 'all') ? 'checked' : '');
    $status_nonlive = ($status_grp == 'nonlive') ? 'checked' : '';
    $status_live = ($status_grp == 'live') ? 'checked' : '';
    print ('Restrict to ');
    print ('<input type="radio" name="status_grp" id="s_live" value="live" '.$status_live.' /><label for="s_live">Live</label>
            <input type="radio" name="status_grp" id="s_nonlive" value="nonlive" '.$status_nonlive.' /><label for="s_nonlive">Non live</label> 
            <input type="radio" name="status_grp" id="s_all" value="all" '.$status_all.' /><label for="s_all">All</label>
            ');
    print ('  media with <table><tr>');
    $sql = 'SELECT id, name FROM t_media where status_id NOT IN (1) ORDER BY name;';
    print( '<td><label for="name" >Name: </label>'.GetSelectionHTML('media_id', $sql, $media_id, True)."\n</td>");
    $sql = 'SELECT t.id, tag name FROM t_tag t WHERE EXISTS (SELECT * FROM t_media_tags mt WHERE mt.tid = t.id) ORDER BY tag;';
    print( '<td><label for="tag">Existing tag: </label>'.GetSelectionHTML('tag_id', $sql, $tag_id, True)."\n</td>");
    print( '<td><label for="finishing_pool">Finishing pool: </label>'.getListHTML('finishing_pool_id', 'finishing_pool', $finishing_pool_id, False, True, False, False)."\n</td>");
    print( '<td><label for="category">Category: </label>'.getListHTML('category_id', 'category', $category_id, False, True, False, False)."\n</td>");
   
    print( '<td><input type="submit" accesskey ="G" value="Get"/>'."\n</td>");
    print ('</tr><tr><td colspan="5"><label for="name" >Match: </label><input type="text" name="media_match" id="media_match" value="'.$media_match.'" /></td>');
    print('</tr></table>');
    print ('</form>'."\n");

    print ('<form name="theotherform" id="theotherform" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n");
   if (isset($_GET['media_id']) /*...*/)
   {
    print ('<input type="hidden" name="status_grp" value="'.htmlenc($status_grp).'">');
    print ('<input type="hidden" name="media_id" value="'.htmlenc($media_id).'">');
    print ('<input type="hidden" name="media_match" value="'.htmlenc($media_match).'">');
    print ('<input type="hidden" name="tag_id" value="'.htmlenc($tag_id).'">');
    print ('<input type="hidden" name="finishing_pool_id" value="'.htmlenc($finishing_pool_id).'">');
    print ('<input type="hidden" name="category_id" value="'.htmlenc($category_id).'">');
    print ("<table id='managemedia'><tr><td id='l'>");
    print ("<table id='statii'>");
    $status_clause = ($status_grp == 'all') ? "1=1" 
                    : (($status_grp == 'live') ? "m.status_id = 4"
                    : (($status_grp == 'nonlive') ? "m.status_id <> 4" 
                    : "1=1"));
    $match_str = ($media_match <> '') ? "%$media_match%"
                    : "";
        try
        {

        $sql = <<<EOT
        SELECT m.id, m.name, ms.name as status, concat('t-',m.name,'.gif') AS thumb, MIN(r.name) as review, mc.name as category, mfp.name as finishing_pool, GROUP_CONCAT(DISTINCT t.tag ORDER BY t.tag) as tags
        FROM t_media m
        LEFT OUTER JOIN t_review_media rm
          ON m.id = rm.mid
        LEFT OUTER JOIN t_media_status ms
          ON ms.id = m.status_id
        LEFT OUTER JOIN t_review r
          ON r.id = rm.rid
        LEFT OUTER JOIN t_media_category mc
          ON mc.id = m.category_id
        LEFT OUTER JOIN t_media_finishing_pool mfp
          ON mfp.id = m.finishing_pool_id
        LEFT OUTER JOIN t_media_tags mt
           ON mt.mid = m.id
        LEFT OUTER JOIN t_tag t
           ON t.id = mt.tid
        WHERE ($status_clause) AND 
                ((m.id = ? OR m.name LIKE ?)
                  OR (finishing_pool_id <> 0 AND finishing_pool_id = ?) 
                  OR (category_id <> 0 AND category_id = ?) 
                  OR (?<>0 AND EXISTS (SELECT * FROM t_media_tags mt2 WHERE mt2.mid = m.id AND mt2.tid=?)))
        GROUP BY m.id, m.name, ms.name , concat('t-',m.name,'.gif'), mc.name, mfp.name
EOT;

            $st = $dbh->prepare($sql);
            $st->execute(array($media_id, $match_str, $finishing_pool_id, $category_id, $tag_id, $tag_id));
            $norows = ($st->fetchColumn() == 0);
            $st->execute(array($media_id, $match_str, $finishing_pool_id, $category_id, $tag_id, $tag_id));; // nasty work arround for lack of number of rows
            $disabled = ($norows) ? 'disabled="disabled"' : '';

            print("<tr id='header'><th><input $disabled type='checkbox' checked='checked' id='all' />All</th><th>Symbol</th><th width='60%'>Name</th><th>Fin Pool</th><th>Category</th></th><th>Tags</th><th width='60%'>id<br/>review<br/>status</th></tr>\n");
            while ($row = $st->fetch(PDO::FETCH_ASSOC))
            {
                $id = htmlenc($row['id']);
                print("<tr><td><input $disabled type='checkbox' checked='checked' name='r$id' id='r$id' /></td>\n".
                        "<td><image src='media/symbols/EN/thumb/".htmlenc($row['thumb'])."' title='".htmlenc($row['name'])."' alt='".htmlenc($row['name'])."'/>".
                        "</td><td>".htmlenc($row['name'])."</td>".
                        "</td><td>".htmlenc($row['finishing_pool'])."</td>".
                        "</td><td>".htmlenc($row['category'])."</td>".
                        "</td><td>".htmlenc($row['tags'])."</td>".
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
        print ("<select $disabled name='newtags' id='newtags'>\n");
        
        $strQuery= <<<EOT
        SELECT id, tag
        FROM t_tag
        ORDER BY tag
EOT;
        $st = $dbh->prepare($strQuery);
        $st->execute();
        while ($row = $st->fetch(PDO::FETCH_ASSOC))
        {
            //print ($status.' '.$row['id'].' '.$row['status']);
            $id = htmlenc($row['id']);      // a messy mishmash encoding format this but will do for now
            $ntag = htmlenc($row['tag']);
            print ("<option value='$id'>$ntag</option>\n");
        }
        print ("</select>\n");
        
        print( "<input $disabled type='submit' name='add' id='add' accesskey='A' value='Add'/>\n");
        print( "<input $disabled type='submit' name='remove' id='remove' accesskey='R' value='Remove'/>\n");
        print('</td></tr></table>');
    }
    print ('</form>'."\n");

?>


</div></div>


<script type="text/javascript">	
    function setNewStatusState(status)
    {
       var elSetStatus = document.getElementById('setstatus');
       var elNewStatus = document.getElementById('newstatus');
       elSetStatus.disabled = false;
       elNewStatus.disabled = false;
       setStatusTransitionList(elNewStatus, status, 'newstatus');
       elSetStatus.disabled = (status == '');
       elNewStatus.disabled = (status == '');
       //document.theform.submit();
    }
    
    function onStatusChange()
    {
        var index = this.selectedIndex;
        var status = this.options[index].text;
        setNewStatusState(status);
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

String.prototype.trim = function () {
  return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
};    
    function setHandlers()
    {
        var elStatus = document.getElementById('status');
        //elStatus.addEventListener('change',onStatusChange,false)
        //onStatusChange.call(elStatus);

        var elAll = document.getElementById('all');
        if (elAll)
        {
            elAll.onclick = onAllChange;
            onAllChange.call(elAll);
        }
        
        var elAddTag = document.getElementById('addtag');
        elAddTag.onclick = function(event)
        {
            var elnewTag = document.getElementById('newtag');
            if (elnewTag.value.trim().length == 0)
            {
                alert('Empty tag or contains only spaces, so not added');
                event.preventDefault();
                return false;
            }
        }
        
        var elMedia = document.getElementById('media_id');
        elMedia.onfocus = function(event)
        {
        }
    }

	Rounded("div#mediastatus","#FFFFFF","#ECECFF");
    window.onload=setHandlers;
</script>

<?

include('_footer.php');
}
?>
