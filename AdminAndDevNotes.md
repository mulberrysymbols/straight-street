#Administration and development notes

# Server requirements #

The code requires a LAMP (PHP) stack so basic cheap shared web hosting will suffice as long as it provides (S)FTP and phpMyAdmin access.

It is known to work on
  * Apache httpd 2.2.17
  * MYSQL 5.0.51a
  * PHP 5.3.6
  * phpMyAdmin 3.3.10

## Server config ##

The usual good PHP and MySQL security practices should be followed (eg register\_globals off). See file `/.htaccess` for details of the configuration. Code access to email will need to be enabled for some features.

# Code and media #

There are a lot of files under `/media` so it will take some time to checkout from svn.

Install the source to the web server root `/` (eg /var/www). It may work in other locations but has not been tested.

Most dependencies (see file `NOTICE.txt`) have been included in the source as the code has not been integrated with newer versions and the licences permit redistribution of source. They should be removed over time and the code made to work with the latest versions. The file `/js/cvi_busy_lib.js` (version 1.3) cannot be redistributed so you need to install it from [busy.netzgesta.de](http://busy.netzgesta.de).

You need to grant write permission for the PHP code to `/tmp` so it can create custom symbol set archive files for download.

# Database setup #

File `/database/straight_street-structure.sql` will create the schema. You will probably wanted to edit it to change the db name from straight\_street. File `/database/straight_street-data.sql` contains the data from the live site, though any user data has been removed and a single user with unspecified password has been left for reference. You can inport these either with mysql command line or phpmyadmin web UI e.g
```
mysql -uroot -pPWD < database/straight_street-structure.sql
mysql -uroot -pPWD < database/straight_street_data.sql 
```

# Config / fixups #

The code needs to be configured to connect to the database. Edit file `_dbconsts.php` and set db DSN, user & password and edit file `_db.php` to set the db name - look for the FIXME comments. You'll also need to create the MYSQL user; eg
```
$ mysql> GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES ON database1.* TO 'yourusername'@'localhost' IDENTIFIED BY 'yourpassword';
```


Several places need the domain name and email address. This needs improvement but for now look for the FIXME in `_common.php` and search for `something.com`

There is simple integration with MyBB so that a new user automatically get registered in MyBB. This call (in `acount.php`) has been commented out to remove an unwanted dependency. As the code (in `_forumuser.php`) directly updates the MyBB database rather than linking it depends on the version of myBB used and will likely need changing before it can be used.