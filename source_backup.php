<html>

<head>
<title>Site backup</title>
</head>

<body>

<?php
function dirList ($directory) 
{
    $results = array();
    if ($handler = opendir($directory))
    {
      while (false != ($file = readdir($handler)))
      {
	// if $file isn't this directory or its parent, 
        // add it to the results array
	if (!is_dir($file)) //        if ($file != '.' && $file != '..')
            $results[] = $file;
      }
      closedir($handler);
    }
    return $results;
}
		
  print "<p>Downloading archive...</p>\r\n";

  require_once('_common.php');
  $name = 'ss_backup_'.date('ymd');
  $tmpDir='tmp/';
  $filename = $tmpDir.$name.".zip";

/* can't get exec to work on any thing except pwd - wait for new server
$user = 'sspaxton';
$dbName = 'sspaxton';
$password='ssmysqlpw';

  $sqlFile=$tmpDir.$name.'.sql';
  $creatBackup = "mysqldump -u ".$user." --password=".$password." ".$dbName." > ".$sqlFile;
//$backupFile = $dbname . date("Y-m-d-H-i-s") . '.gz';
//$command = "mysqldump --opt -h $dbhost -u $dbuser -p $dbpass $dbname | gzip > $backupFile";
system('/usr/local/bin/mysqldump 2>&1');
exit(0);
*/
  $files = implode(',', dirList('.'));
  $files .= ',css,js,nifty,'.$sqlFile;
  //set_time_limit(240);
  require_once('zipper/pclzip.lib.php');
  $archive = new PclZip("$filename");
  $v_list = $archive->create("$files",
	     PCLZIP_OPT_REMOVE_PATH, 'tmp',
	     PCLZIP_OPT_ADD_PATH, "$name");
  if ($v_list == 0)
  {
    die("Error : ".$archive->errorInfo(true));
  }

//echo $filename;
echo '<p><b>Don\'t forget to backup the DB</b></p>';
  header("Location: /$filename");
  
/*
  print <<<EOT
<script language="javascript">
location.replace("$filename");
</script> 
EOT;
*/
  
?> 

</body>
</html>
