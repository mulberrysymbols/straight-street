<?
include('_header.php');
$lang = getUserLangID($loggedUser);
if ($lang == '')
    $lang = 'EN';
?>

Settings

<ul>

<div class="blue_content_div" id="settings2">
<div class="innerdivspacer">

<label for="language">Language:</label>
<select id="language" onchange="setUserLanguage('<?=$loggedUserId?>', document.getElementById('language').value)">
<?
    db_connect();
    $query = "SELECT l.id, CONCAT(native_name, ' - ', name) AS name FROM t_language AS l INNER JOIN t_bundle_version bv ON bv.lang_id = l.id ORDER BY l.name;";
    $result = db_runQuery($query);
    while ($r = mysql_fetch_array($result))
    {
        echo '<option value="'.$r["id"].'">'.$r["name"]."</option>";
    }
?>
</option>

</div></div>

<script type="text/javascript">	
    function foo() 
    { 
        document.getElementById('language').value = '<?=$lang?>';
    }
    //Rounded("div#settings2","#FFFFFF","#ECECFF"); clashes with onload in IE - bah!
    window.onload = foo;
</script>

<?
include('_footer.php');
?>
