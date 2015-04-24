<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(1);
require_once('config/database.php');
$db = new Database;
$connection=mysql_connect($db->remote_db_host,$db->remote_db_user,$db->remote_db_password);

// BEGIN SYNC COLOR_SCHEME TO COLOR_SCHEME.CSV
if($connection){
	
$sql_color_scheme =
   "SELECT *
    FROM color_scheme";
$pathToCsv_color_scheme = '/var/www/html/narrowcasting/mysqldump/color_scheme.csv';

$command_color_scheme = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
	$db->remote_db_host,
	$db->remote_db_user,
	$db->remote_db_password,
	$db->remote_db_database,
    $sql_color_scheme,
    $pathToCsv_color_scheme);
	
exec($command_color_scheme);

// END SYNC COLOR_SCHEME TO COLOR_SCHEME.CSV


// BEGIN SYNC DELAY_MEDEDELINGEN TO DELAY_MEDEDELINGEN.CSV

$sql_delay_mededelingen =
   "SELECT *
    FROM delay_mededelingen";
$pathToCsv_delay_mededelingen = '/var/www/html/narrowcasting/mysqldump/delay_mededelingen.csv';

$command_delay_mededelingen = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
	$db->remote_db_host,
	$db->remote_db_user,
	$db->remote_db_password,
	$db->remote_db_database,
    $sql_delay_mededelingen,
    $pathToCsv_delay_mededelingen);

exec($command_delay_mededelingen);

// END SYNC DELAY_MEDEDELINGEN TO DELAY_MEDEDELINGEN.CSV


// BEGIN SYNC MEDEDELINGEN TO MEDEDELINGEN.CSV

$sql_mededelingen =
   "SELECT *
    FROM mededelingen";
$pathToCsv_mededelingen = '/var/www/html/narrowcasting/mysqldump/mededelingen.csv';

$command_mededelingen = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
	$db->remote_db_host,
	$db->remote_db_user,
	$db->remote_db_password,
	$db->remote_db_database,
    $sql_mededelingen,
    $pathToCsv_mededelingen);

exec($command_mededelingen);

// END SYNC MEDEDELINGEN TO MEDEDELINGEN.CSV


// BEGIN SYNC THEATRE TO THEATRE.CSV

$sql_theatre =
   "SELECT *
    FROM theatre";
$pathToCsv_theatre = '/var/www/html/narrowcasting/mysqldump/theatre.csv';

$command_theatre = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
	$db->remote_db_host,
	$db->remote_db_user,
	$db->remote_db_password,
	$db->remote_db_database,
   $sql_theatre,
   $pathToCsv_theatre);

exec($command_theatre);

// END SYNC THEATRE TO THEATRE.CSV


// BEGIN SYNC THEATRE_EPPC TO THEATRE_EPPC.CSV

$sql_theatre_eppc =
   "SELECT *
    FROM theatre_eppc";
$pathToCsv_theatre_eppc = '/var/www/html/narrowcasting/mysqldump/theatre_eppc.csv';

$command_theatre_eppc = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
	$db->remote_db_host,
	$db->remote_db_user,
	$db->remote_db_password,
	$db->remote_db_database,
    $sql_theatre_eppc,
    $pathToCsv_theatre_eppc);

exec($command_theatre_eppc);

// END SYNC THEATRE_EPPC TO THEATRE_EPPC.CSV


// BEGIN SYNC THEATRE_MULTISCREEN TO THEATRE_MULTISCREEN.CSV

$sql_theatre_multiscreen =
   "SELECT *
    FROM theatre_multiscreen";
$pathToCsv_theatre_multiscreen = '/var/www/html/narrowcasting/mysqldump/theatre_multiscreen.csv';

$command_theatre_multiscreen = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
	$db->remote_db_host,
	$db->remote_db_user,
	$db->remote_db_password,
	$db->remote_db_database,
    $sql_theatre_multiscreen,
    $pathToCsv_theatre_multiscreen);

exec($command_theatre_multiscreen);

// END SYNC THEATRE_MULTISCREEN TO THEATRE_MULTISCREEN.CSV


// BEGIN SYNC THEATRE_TEMPLATE TO THEATRE_TEMPLATE.CSV

$sql_theatre_template =
   "SELECT *
    FROM theatre_template";
$pathToCsv_theatre_template = '/var/www/html/narrowcasting/mysqldump/theatre_template.csv';

$command_theatre_template = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
	$db->remote_db_host,
	$db->remote_db_user,
	$db->remote_db_password,
	$db->remote_db_database,
    $sql_theatre_template,
    $pathToCsv_theatre_template);

exec($command_theatre_template);

// END SYNC THEATRE_TEMPLATE TO THEATRE_TEMPLATE.CSV


// BEGIN TRANSFER FROM CSV'S TO LOCAL CLIENT TABLES

$truncate_sql_client_color_scheme="truncate table color_scheme";
$db->query($truncate_sql_client_color_scheme);
	$sql_client_color_scheme =
   "LOAD DATA LOCAL INFILE '$pathToCsv_color_scheme'
    REPLACE INTO TABLE `color_scheme`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_color_scheme);


$truncate_sql_client_delay_mededelingen="truncate table delay_mededelingen";
$db->query($truncate_sql_client_delay_mededelingen);
$sql_client_delay_mededelingen =
   "LOAD DATA LOCAL INFILE '$pathToCsv_delay_mededelingen'
    REPLACE INTO TABLE `delay_mededelingen`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_delay_mededelingen);


$truncate_sql_client_mededelingen="truncate table mededelingen";
$db->query($truncate_sql_client_mededelingen);
$sql_client_mededelingen = 
   "LOAD DATA LOCAL INFILE '$pathToCsv_mededelingen'
    REPLACE INTO TABLE `mededelingen`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_mededelingen);


$truncate_sql_client_theatre="truncate table theatre";
$db->query($truncate_sql_client_theatre);
$sql_client_theatre = 
   "LOAD DATA LOCAL INFILE '$pathToCsv_theatre'
    REPLACE INTO TABLE `theatre`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_theatre);


$truncate_sql_client_theatre_eppc="truncate table theatre_eppc";
$db->query($truncate_sql_client_theatre_eppc);
$sql_client_theatre_eppc = 
   "LOAD DATA LOCAL INFILE '$pathToCsv_theatre_eppc'
    REPLACE INTO TABLE `theatre_eppc`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_theatre_eppc);


$truncate_sql_client_theatre_multiscreen="truncate table theatre_multiscreen";
$db->query($truncate_sql_client_theatre_multiscreen);
$sql_client_theatre_multiscreen = 
	"LOAD DATA LOCAL INFILE '$pathToCsv_theatre_multiscreen'
    REPLACE INTO TABLE `theatre_multiscreen`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_theatre_multiscreen);

$truncate_sql_client_theatre_template="truncate table theatre_template";
$db->query($truncate_sql_client_theatre_template);
$sql_client_theatre_template = 
   "LOAD DATA LOCAL INFILE '$pathToCsv_theatre_template'
    REPLACE INTO TABLE `theatre_template`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_theatre_template);

echo "Database synced with remote";
}else{
     die('Could not connect: ' . mysql_error());
}



?>