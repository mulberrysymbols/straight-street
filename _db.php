<?php
// following file must contain 3 defines for DB access as follows
//
//define('DB_DSN', '<DSN>');  // eg mysql:unix_socket=/var/lib/mysql/mysql.sock;dbname=mydb
//define('DB_USER', '<USER>');
//define('DB_PWD', '<PASSWORD>');
require '_dbconsts.php';

// static class  to manage a single database connection
class DBCxn {
    const dsn = DB_DSN;
    const user = DB_USER;
    const pwd = DB_PWD;
//    const socket = '/var/lib/mysql/mysql.sock';
    const host = 'localhost';
    const db = '<FIXME:dbname>';
    const driverOpts = null;
    const errMode = PDO::ERRMODE_EXCEPTION;
    
    // Internal variable to hold the connection
    private static $pdb;
    // No cloning or instantiating allowed
    final private function __construct() { }
    final private function __clone() { }
    
    public static function get() {
        // Connect if not already connected
        if (is_null(self::$pdb)) {
		try 
		{
			self::$pdb = new PDO(self::dsn, self::user, self::pwd, self::driverOpts);
			if (!is_null(self::errMode))
			{
				self::$pdb->setAttribute( PDO::ATTR_ERRMODE, self::errMode );
                self::$pdb->query("SET NAMES 'utf8'");
			}
		}
		catch (PDOException $e) 
		{
//			echo 'Database connection failed: ' . $e->getMessage();
			exit();
			throw new Exception('Database connection failed');
		}
        }
        // Return the connection
        return self::$pdb;
    }
    
    public static function get_enum_values($table, $column)
	{
		try
		{
			// TODo find out home to bindtable name w/o quotes
			$sth=self::$pdb->prepare("SHOW COLUMNS FROM $table LIKE ?");
				    $sth->execute(array($column));
				    $sth->execute();
				    $row = $sth->fetch(PDO::FETCH_ASSOC);
				   preg_match_all("/'(.*?)'/", $row['Type'], $matches);
				   $arryEnum= $matches[1];
				   return $arryEnum;
		 } 
		catch (Exception $e)
		{
			print "Couldn't get enum: " . htmlenc($e->getMessage());
		}
	}

}

function db_connect() {
	if (!($link = mysql_connect(DBCxn::host,DBCxn::user,DBCxn::pwd) ))
	{
		throw new Exception("Unable to connect to the Database.");
	}
	mysql_select_db(DBCxn::db);

//	$link = mysql_connect($dbhost,$dbuser,$dbpwd);
//	if (!$link) { echo "<b><font color=\"#FF0000\">Problem Connecting to the Database!</font></b>"; };

	return $link;
}

function db_disconnect() 
{
    mysql_close();
}

function db_runQuery($query) {
    return mysql_db_query(DBCxn::db, $query);
}

//passed by reference
function db_freeResult(&$result) {
	//mysql_free_result($result);
}

function db_escape_string($str)
{
	return mysql_real_escape_string($str);
}

?>