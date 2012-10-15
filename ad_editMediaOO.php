<?php
require('_header.php');

print "</div></div>
<div class='fullwidth'>";

if (!isUserAtLeastAdmin())
{
	include('_footer.php');
	exit();
}

$dbh = DBCXn::get();


function mmb_trim($string, $charlist='\\\\s', $ltrim=true, $rtrim=true)
//mb_trim is missing from php
{
    $both_ends = $ltrim && $rtrim;

    $char_class_inner = preg_replace(
        array( '/[\^\-\]\\\]/S', '/\\\{4}/S' ),
        array( '\\\\\\0', '\\' ),
        $charlist
    );

    $work_horse = '[' . $char_class_inner . ']+';
    $ltrim && $left_pattern = '^' . $work_horse;
    $rtrim && $right_pattern = $work_horse . '$';

    if($both_ends)
    {
        $pattern_middle = $left_pattern . '|' . $right_pattern;
    }
    elseif($ltrim)
    {
        $pattern_middle = $left_pattern;
    }
    else
    {
        $pattern_middle = $right_pattern;
    }

    return preg_replace("/$pattern_middle/usSD", '', $string);
} 

class EditField
{
    // TODO prolly make abstract
    protected $name;
    protected $value;
    protected $state;
    
    public function __construct($_name, $_value, $_state)
    {
        $this->name = $_name;
        $this->value = $_value;
        $this->state = $_state;
    }
    
    protected function getStateClass()
    {
        return 'edmedia';
    }
    
    public function getHTML()
    {   
        return 'No HTML redendition>';
    }
}

class TextEditField extends EditField
{
    protected $size;
    protected $maxlength;

    public function __construct($_name, $_value, $_state, $_size=15, $_maxlength=30)
    {
        parent::__construct($_name, $_value, $_state);
        $this->size = $_size;
        $this->maxlength = $_maxlength;
    }

    public function getHTML()
    {   
        $s = '<input class="'.$this->getStateClass().'" type="text" size="'.htmlenc($this->size).
             '" maxlength="'.htmlenc($this->maxlength).
             '" name="'.htmlenc($this->name).
             '" value="'.htmlenc($this->value).'"/>';
        return $s;
    }
}

class DateEditField extends TextEditField
{
    public function __construct($_name, $_value, $_state, $_size=15, $_maxlength=30)
    {
        parent::__construct($_name, $_value, $_state, $_size, $_maxlength);
        $date = $_value;
        if (preg_match('/^\s*(\d\d?)[^\w](\d\d?)[^\w](\d{1,4}\s*$)/', $date, $match))
        {  // PHP assumes us style date mm/dd/yyyy
            $date = $match[2] . '/' . $match[1] . '/' . $match[3]; 
        }
        if ($date)
        {
            $date = date('Y-m-d', strtotime($date));
        }
        $this->value = $date;
    }
}

class ListEditField extends EditField
{
    protected $table;

    public function __construct($_name, $_table, $_value, $_state)
    {
        parent::__construct($_name, $_value, $_state);
        $this->table = $_table;
    }

    public function getHTML($show_deleted=False, $allow_null=False, $sortByID=False, $highlight_nosel=False)
    {
        $name_clause = ($show_deleted) ? "IF(deleted , CONCAT('~ ', name), name) AS name" : 'name';
        $del_clause = ($show_deleted) ? '' : 'WHERE deleted <> TRUE';
        $orderby = "IF(deleted , _utf8'~', ''), ". (($sortByID) ? 't.id' : 't.name');
        $sql = "SELECT id, $name_clause FROM t_media_$this->table AS t $del_clause ORDER BY $orderby";
        return GetSelectionHTML($this->name, $sql, $this->value, $allow_null, $highlight_nosel);
    }
}


function getEditHTML($name, $value, $size=15, $maxlength=30)
{
	$name = htmlenc($name);
	$value = htmlenc($value);
	$s = '<input class="edmedia" type="text" size="'.$size.'" maxlength="'.$maxlength.'" name="'.$name.'" value="'.htmlenc($value).'"/>';
	return $s;
}

function getEditDateHTML($name, $value, $size=15, $maxlength=30)
{
	$name = htmlenc($name);
	$date = $value;
	if (preg_match('/^\s*(\d\d?)[^\w](\d\d?)[^\w](\d{1,4}\s*$)/', $date, $match))
	{  // PHP assumes us style date mm/dd/yyyy
  		$date = $match[2] . '/' . $match[1] . '/' . $match[3]; 
	}
    if ($date)
    {
	    $date = date('Y-m-d', strtotime($date));
    }
	$value = htmlenc($date);
	$s = '<input class="edmedia" type="text" size="'.$size.'" maxlength="'.$maxlength.'" name="'.$name.'" value="'.htmlenc($value).'"/>';
	return $s;
}

function getEditNameHTML($name, $value, $showexist=False, $size=15, $maxlength=30)
{	
	$name = htmlenc($name);
	$value = htmlenc($value);
    $nameexists = False;
    if ($showexist)
    {
        $query = 'SELECT count(*) AS found FROM `t_media` WHERE name = ?';
        global $dbh;
        $st = $dbh->prepare($query);
        $st->execute(array($value));
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $nameexists = $row['found'];
    }
    $class = 'edmedia'.(($nameexists) ? ' badselection' : '');
	$s = '<input class="'.$class.'" type="text" size="'.$size.'" maxlength="'.$maxlength.'" name="'.$name.'" value="'.htmlenc($value).'"/>';
	return $s;
}

function getEditListHTML($name)
{
	$name = htmlenc($name);
	$s = '<br/><a class="edlist" href="'.htmlenc($_SERVER['PHP_SELF']).'?cmd=edlist&list='.$name.'"><img src="img/b_edit.png" title="edit '.$name.' list" alt="a pencil"></a>';
	return $s;
}

function pc_build_query($db,$key_field,&$fields,&$values,$table)
{
	$a = array('1', '2');
	$values_ = array();
	if (isset($values[$key_field]) && mb_strlen($values[$key_field]))
	{
		$update_fields = array();
		foreach ($fields as $field)
		{	
			if ($field != $key_field && array_key_exists($field, $values))
			{
				$update_fields[] = "$field = ?";
				$values_[] = $values[$field];
			}
		}
		// Add the key field's value to the $values array
		$values_[] = $values[$key_field];
		$st = $db->prepare("UPDATE $table SET " .
		   implode(',', $update_fields) .
		   " WHERE $key_field = ?");
	} 
	else 
	{
		// Start values off with a unique ID
		// If your DB is set to generate this value, use NULL instead
		$values_[] = 'NULL';
		$placeholders = array('?');
		foreach ($fields as $field)
		{
			// One placeholder per field
			$placeholders[] = '?';
			// Assume the data is coming from a form
			$values_[] = $values[$field];
		}
        
		$st = $db->prepare("INSERT INTO $table ($key_field," .
				   implode(',',$fields) . ', mtype) VALUES ('.
				   implode(',',$placeholders) .', 1)');
	}
	$st->execute($values_);
	return $st;
}

function print_row($r, $values, $edit, $nosel)
{
    // note if this is too slow will need to optimise all those per row queries
    $show_deleted = $edit;
    print '<tr>';
    if ($edit)
    {
        print '<td>'.$values['id'].'</td>'."\n";
    }
  $values['name'] = new TextEditField($r.'_name', $values['name'], null);
  $values['original_name'] = new TextEditField($r.'_original_name', $values['original_name'], null);
  $values['creation_date'] = new DateEditField($r.'_creation_date', $values['creation_date'], null);
  $values['category_id'] = new ListEditField($r.'_category_id', 'category', $values['category_id'], null);
  $values['designers_ref_id'] = new ListEditField($r.'_designers_ref_id', 'designers_ref', $values['designers_ref_id'], null);
  $values['author_id'] = new ListEditField($r.'_author_id', 'person', $values['author_id'], null);
  $values['wordlist_id'] = new ListEditField($r.'_wordlist_id', 'wordlist', $values['wordlist_id'], null);
  $values['finishing_pool_id'] = new ListEditField($r.'_finishing_pool_id', 'finishing_pool', $values['finishing_pool_id'], null);
  $values['finisher_id'] = new ListEditField($r.'_finisher_id', 'person', $values['finisher_id'], null);
  
  
//    print '<td>'.getEditNameHTML($r.'_name', $values['name'], !$edit).'</td>'."\n".
//        '<td>'.getEditDateHTML($r.'_creation_date', $values['creation_date']).'</td>'."\n".
//        '<td>'.getEditHTML($r.'_original_name', $values['original_name']).'</td>'."\n".
//        '<td>'.getListHTML($r.'_category_id', 'category', $values['category_id'], $show_deleted, False, False, $nosel).'</td>'."\n".
//        '<td>'.getListHTML($r.'_designers_ref_id', 'designers_ref', $values['designers_ref_id'], $show_deleted, False, False, $nosel).'</td>'."\n".
//        '<td>'.getListHTML($r.'_author_id', 'person', $values['author_id'], $show_deleted, False, False, $nosel).'</td>'."\n".
//        '<td>'.getListHTML($r.'_wordlist_id', 'wordlist', $values['wordlist_id'], $show_deleted, False, False, $nosel).'</td>'."\n".
//        '<td>'.getListHTML($r.'_finishing_pool_id', 'finishing_pool', $values['finishing_pool_id'], $show_deleted, True, False, $nosel).'</td>'."\n".
//        '<td>'.getListHTML($r.'_finisher_id', 'person', $values['finisher_id'], $show_deleted, True, False, $nosel).'</td>'."\n";

    print '<td>'.$values['name']->getHTML().'</td>'."\n".
        '<td>'.$values['original_name']->getHTML().'</td>'."\n".
        '<td>'.$values['creation_date']->getHTML().'</td>'."\n".
        '<td>'.$values['category_id']->getHTML($show_deleted, False, False, $nosel).'</td>'."\n".
        '<td>'.$values['designers_ref_id']->getHTML($show_deleted, False, False, $nosel).'</td>'."\n".
        '<td>'.$values['author_id']->getHTML($show_deleted, False, False, $nosel).'</td>'."\n".
        '<td>'.$values['wordlist_id']->getHTML($show_deleted, False, False, $nosel).'</td>'."\n".
        '<td>'.$values['finishing_pool_id']->getHTML($show_deleted, True, False, $nosel).'</td>'."\n".
        '<td>'.$values['finisher_id']->getHTML($show_deleted, True, False, $nosel).'</td>'."\n";

    if ($edit)
    {
  $values['status_id'] = new ListEditField($r.'_status_id', 'status', $values['status_id'], null);
  $values['version_id'] = new ListEditField($r.'_version_id', 'version', $values['version_id'], null);
  $values['comment'] = new TextEditField($r.'_comment', $values['comment'], null, $maxlength=50);

    print '<td>'.$values['status_id']->getHTML($show_deleted).'</td>'."\n".
                '<td>'.$values['version_id']->getHTML($show_deleted, True).'</td>'."\n".
                '<td>'.$values['comment']->getHTML().'</td>'."\n";
    }
    else
    {
        print '<input type="hidden" name="'.$r.'_status_id" value="1">';
        print '<input type="hidden" name="'.$r.'_version_id" value="">';
        print '<input type="hidden" name="'.$r.'_comment" value="">';
        print '<td>Dev</td>'."\n".
                '<td>n/a</td>'."\n".
                '<td>n/a</td>'."\n";
    }
    print '</tr>'."\n";
}

function print_form($dbh, $allValues, $edit, $id, $nosel)
{
    $rows = count($allValues);
    print  '<form name="theform" id="theform" method="post" action="' . htmlenc($_SERVER['PHP_SELF']) . '">'."\n".
        ' <input type="hidden" name="cmd" value="save">'."\n".
        ' <input type="hidden" name="rows" value="'.$rows.'">'."\n";
    if ($edit && $id)
    {
        printf('<input type="hidden" name="id" value="%d">', $id);
        print    '<div id="edmselect">'; listMediaEdit($dbh); print"</div>\n";
    }
    if ($rows)
    {
        print
            ' <table id="edmedia" summary="Editing media data">'."\n".
            '  <thead> <tr>'."\n";
        if ($edit)
        {
            print '<th>id</th>';
        }
        print '     <th>Name</th><th>Created</th><th>Original Name</th>
                <th>Category'.getEditListHTML('category').'</th><th>Designers ref'.getEditListHTML('designers_ref').'</th>
                <th>Author'.getEditListHTML('person').'</th><th>Wordlist'.getEditListHTML('wordlist').'</th>
                <th>Finishing Pool'.getEditListHTML('finishing_pool').'</th><th>Finisher'.getEditListHTML('person').'</th>
                <th>Status'.getEditListHTML('status', $sortByID=True).'</th><th>Version'.getEditListHTML('version').'</th><th>Comments</th>'."\n".
            '  </tr> </thead><tbody>'."\n";

        foreach($allValues as $r => $values)
        {   print_row($r+1, $values, $edit, $nosel);
        }
        print '<tr><td colspan="12">'."\n";
        print	'<input type="submit" accesskey ="S" value="Save"/>';
        if(!$edit)
        {
            print '<input type="submit" accesskey="A" value="Another row" name="add"/>';
        }
        print   '</td></tr>'."\n".
            '</tbody></table>';
    }
   print '</form>'."\n";
}

function error($txt)
{
    print "<div class='formError'>$txt</div>";
}

$fields = array('id', 'name','creation_date', 'original_name', 'category_id','designers_ref_id', 'author_id',
			'wordlist_id', 'finishing_pool_id', 'finisher_id', 'status_id', 'version_id', 'comment' );

// ? doesn't seem to nest properly
if (isset($_REQUEST['add']))
  $cmd = 'add';
elseif (isset($_REQUEST['edit']))
  $cmd = 'edit';
elseif (isset($_REQUEST['batch']))
  $cmd = 'batch';
elseif (isset($_REQUEST['cmd']))
  $cmd = $_REQUEST['cmd'];
else 
  $cmd = 'show';

$id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : '';

if  (isset($_POST['rows']) && isset($_POST['add']))
  $rows = $_POST['rows'] + 1;
elseif (isset($_POST['rows'])) 
  $rows = $_POST['rows'];
else
  $rows = 1;

print '<div class="blue_content_div" id="blue_content_div1" width="100%" >
<div class="innerdivspacer" >';

$list = (isset($_REQUEST['list'])) ? $_REQUEST['list'] : '';

function write_csv_line($handle, $arLine)
{
  $arLine = str_replace('"', '""', $arLine);
  $line = "\"" . join('","', $arLine) . "\"\r\n";
  print($line).'<br>';
  return fwrite($handle, $line);
}

function write_csv($file)
{
    $file_handle = fopen($file, "r");

    while (!feof($file_handle) )
    {
        $line_of_text = fgetcsv($file_handle, 1024);
        if ($line_of_text)
        {
            foreach ($line_of_text as $column)
            {
                print $column. ', ';
            }
            print '<br/>';
        }
    }

    fclose($file_handle);
}

function file_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
} 

function print_csv_loader($mode)
{
    print '<form name="anotherform" id="anotherform"  enctype="multipart/form-data" action="' . htmlenc($_SERVER['PHP_SELF']) . '" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
    <input name="uploadedfile" type="file" />
    <input type="submit" name = "batch" value="Load from File" />
    <input type="hidden" name="batchmode" value='.$mode.' />
    </form>';
}

switch ($cmd)
{
    case 'batch':
    {
        if((empty($_FILES["uploadedfile"])) || ($_FILES['uploadedfile']['error'] != 0))
        {
            error("Error: ".file_upload_error_message($_FILES['uploadedfile']['error']));
            break;
        }

        $filename = basename($_FILES['uploadedfile']['name']);
        $ext = substr($filename, strrpos($filename, '.') + 1);
        print( 'File Name: '.$filename.' Type: '.$_FILES["uploadedfile"]["type"].' Size:'. $_FILES["uploadedfile"]["size"].'<br>');
        if (($ext != "csv") || 
            !in_array($_FILES["uploadedfile"]["type"], array("application/vnd.ms-excel","text/csv","text/plain","text/comma-separated-values")) ||
            ($_FILES["uploadedfile"]["size"] > 350000))
        {
            error("Error: Only .csv files under 350Kb are accepted for upload");
            break;
        }            
        $newname = dirname(__FILE__).'/tmp/'.$filename;
        if (file_exists($newname))
        {
            error("Warning: File ".$_FILES["uploadedfile"]["name"]." already exists on server - replacing");
            unlink($newname);
        }
        
        if (!move_uploaded_file($_FILES['uploadedfile']['tmp_name'],$newname))
        {
            error("Error: A problem occurred during file upload!");
            break;
        }
        
        $allValues = array();
        $file_handle = fopen($newname, "r");

        // skip first row == names
        $columns = fgetcsv($file_handle, 1024);

        while (!feof($file_handle) )
        {
            $columns = fgetcsv($file_handle, 1024);
            if ($columns)
            {
                $values = array();
                //assume csv columns in same order as 
   				foreach ($columns as $c => $column)
                {
                    $values[$fields[$c]] = mmb_trim($column);
                }
                $values["name"] = str_replace(' ', '_', $values["name"]);
				$allValues[] = $values;
            }
        }
        fclose($file_handle);
        unlink($newname);

		$edit = (isset($_REQUEST['batchmode']) && $_REQUEST['batchmode'] == 'edit');
        if ($edit)
        {
            foreach($allValues as $v => $values)
            {
                $query = 'SELECT ' . implode(',',$fields).' FROM t_media WHERE id = ? and name = ?';
                $st = $dbh->prepare($query);
                $st->execute(array($values['id'], $values['name']));
                $row = $st->fetch(PDO::FETCH_ASSOC);
        		$st->closeCursor();
                if ($row==null)
                {
                    print 'no:'.$values['id'].' '.$values['name'] ;print("<br>");
                }
                else
                {
                    foreach ($row as $col => $value)
                    {
                        if ($values[$col] == '')
                            $values[$col] = $value;
                    }
//                    print_r($row);print("<br>");
//                    print_r($values);print("<br>");
                    $allValues[$v] = $values;
                }
            }
        }

		$edit = (isset($_REQUEST['batchmode']) && $_REQUEST['batchmode'] == 'edit');
		print(($edit) ? 'Admin - batch edit media' : 'Admin - batch add media');
        print_form($dbh, $allValues, $edit, null, $edit);
             
        break;
    }
    case 'batchedit':
       print '<p>GARRY - DO NOT USE YET</p>';
       print_csv_loader('edit');
       break;
	case 'edit':
		try
		{
            $query = 'SELECT ' . implode(',',$fields).' FROM t_media WHERE id = ?';
		    $st = $dbh->prepare($query);
		    $st->execute(array($id));
		    $row = $st->fetch(PDO::FETCH_ASSOC);
		 } 
		 catch (Exception $e)
		 {
		     print $e->getMessage();
		     $row = array();
		 }
		$st->closeCursor();
		// drop through
	case 'add':
		print(($cmd == 'add') ? 'Admin - add media' : 'Admin - edit media');
		$edit = ('edit' == $cmd) ;

        $allValues = array();
        if (!$edit || $id)
        {
            foreach (range(1, $rows)  as $r)
            {
                $values = array();
                foreach ($fields as $field)
                {
                    if ($edit) 
                        $values[$field] = $row[$field];
                    else
                        $values[$field] = (isset($_POST[$r.'_'.$field])) ? $_POST[$r.'_'.$field] : (($r > 1 && isset($_POST[($r-1).'_'.$field])) ? $_POST[($r-1).'_'.$field] : '');
                    $values[$field] = mmb_trim($values[$field]);
                }
                $allValues[] = $values;
            }
        }
        print_form($dbh, $allValues, $edit, $id, False);
        
        if(!$edit)
        {
        	print_csv_loader('add');
        }

		break;
	case 'save':
		if ($_SERVER['REQUEST_METHOD'] != 'POST')
			break;
		try
		{
			foreach (range(1, $rows)  as $r)
			{
				$values = array();
				foreach ($fields as $field)
				{
					$values[$field] = mmb_trim($_POST[$r.'_'.$field]);
				}
				$values["name"] = str_replace(' ', '_', $values["name"]);
				$values['id'] = $id;
				$st = pc_build_query($dbh,'id',$fields,$values,'t_media');
                
			}
			print 'Added info.';
		} 
		catch (Exception $e)
		{
			print "Couldn't add info: " . htmlenc($e->getMessage());
		}
		print '<hr>';
        $nxtcmd = ((isset($id) && $id != '') ? '?cmd=edit&id='.htmlenc($id) : '?cmd=add');
		redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1)).$nxtcmd);
		break;
 	case 'edlist':
?>
	<div width='50%' style='margin-left:20%;text-align:left'>
	<form name="theform" id="theform" method="post" action="<?= htmlenc($_SERVER['PHP_SELF']) ?>">
	<p>Edit <?=htmlenc($list)?> list:</p>
		<input type="hidden" name="cmd" value="savelist">
		<input type="hidden" name="list" value="<?=$list?>">
		<table class="form">
		<tr>
			<td><label for="newitem">New item: </label></td>
			<td><input class="edmedia" type="text" size="20" maxlength="30" name="newitem" value="" tabindex="1"/></td>
			<td>&nbsp;<input type="submit" accesskey ="A" name="addlist" value="Add"/><br/></td>
		</tr>
		<tr>
			<td><label for="category_id"><?print($list)?>: </label></td>
			<td><?print(getListHTML($list.'_id', $list, null, $show_deleted=True));?></td>
			<td><input class="edmedia" type="text" size="20" maxlength="30" name="newname" value="" /></td>
			<td>&nbsp;<input type="submit" accesskey ="R" name="renlist" value="Rename"/></td>
			<td>&nbsp;<input type="submit" accesskey ="D" name="dellist" value="Delete"/></td>
		</tr>
		<tr><td colspan="3">&nbsp;&nbsp;<a href="<?=$_SERVER['PHP_SELF']."?cmd=show"?>" accesskey="L">List All</a></td>
</tr>
		</table><p></p>
	</form>
	</div>
<?
		break;
 	case 'savelist':
		if ($_SERVER['REQUEST_METHOD'] != 'POST')
			break;
		try
		{
//			$list = $dbh->quote($list);
//			$table = 't_media_'.mb_substr($list, 1, -1);
			$table = 't_media_'.$list;
			print $table;
			if (isset($_POST['addlist']))
			{
                $sql = "INSERT INTO $table (name) VALUES (?)";
				$st = $dbh->prepare($sql);
				$values = array($_POST['newitem']);
			}
			elseif (isset($_POST['dellist']))
			{
				$iid = $_POST[$list."_id"];
				// we delete it if not referenced at all or otherwise just set the deleted field
				$sql = "DELETE FROM $table WHERE $table.id = ? AND NOT EXISTS (SELECT 'x' FROM t_media AS tm WHERE tm.${list}_id = ?);";
				$st = $dbh->prepare($sql);
				$values = array($iid,$iid);
    			$st->execute($values);
				$sql = "UPDATE $table SET deleted = TRUE WHERE id = ?;";
				$st = $dbh->prepare($sql);
				$values = array($iid);
			}
			elseif (isset($_POST['renlist']))
			{
				$iid = $_POST[$list."_id"];
				$sql = "UPDATE $table SET name = ? WHERE $table.id = ?;";
				$st = $dbh->prepare($sql);
				$values = array($_POST['newname'], $iid);
            }
            else 
            {
            }
			$st->execute($values);
		}
		catch (Exception $e)
		{
			print "Couldn't update list info: " . htmlenc($e->getMessage());
		}
	    //print $sql;
		redirectTo(htmlenc(substr($_SERVER['PHP_SELF'], 1)).'?cmd=edlist&list='.$list);
		break;
	case 'show':
	default:
		$page = htmlentities($_SERVER['PHP_SELF']);
		print '<a href="'.$page.'?cmd=add">Add New</a>';
		print '<hr>';
		foreach ($dbh->query('SELECT id, name FROM t_media ORDER BY NAME, id') as $row)
		{
			 printf('<a href="%s?cmd=edit&id=%s">%s</a> ',
				   $page,$row['id'],htmlenc($row['name']));
		}
		break;
}
?>
</div></div>
</div>
<div><div>
<script type="text/javascript">	
	Rounded("div#blue_content_div1","#FFFFFF","#ECECFF");
//    document.theform.r1_name.focus();
</script>

<?
include('_footer.php');
//INSERT INTO t_media (id,name,creation_date,original_name,category_id,designers_ref_id,author_id,wordlist_id,finishing_pool_id,finisher_id,status_id,version_id,comment,mtype) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
?>



