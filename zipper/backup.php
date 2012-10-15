<html>

<head>
<title>Site backup</title>
</head>

<body>

<?php
  // clean up old zips
   $files = glob('./ssbackup*.zip');
   foreach($files as $file)
   {
      if(is_file($file))
      {
         unlink($file);
      }
    }
  $tmpname = '/tmp/ssbackup_'.date('ymd').'.zip';
  $filename = 'ssbackup_'.date('ymd').'.zip';
  
  print "<p>Creating archive $filename ...</p>\r\n";
  flush();
  
  set_time_limit(240);
  include_once('./pclzip.lib.php');
  $archive = new PclZip("$tmpname");
  $v_list = $archive->create('..',
		     PCLZIP_OPT_REMOVE_PATH, '..',
		     PCLZIP_OPT_ADD_PATH, 'straight-street');
  if ($v_list == 0) {
    die("Error : ".$archive->errorInfo(true));
  }
  
  copy($tmpname, "./".$filename);
  unlink($tmpname);
  print "<p>Downloading archive...</p>\r\n";
  
  print <<<EOT
<script language="javascript">
location.replace("$filename");
</script  header("Location: $filename");
EOT;
  
?> 

</body>
</html>