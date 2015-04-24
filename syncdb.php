<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(1);
require_once('config/database.php');
$db = new Database;
$connection=mysql_connect($db->remote_db_host,$db->remote_db_user,$db->remote_db_password);

// BEGIN SYNC MASTER_CARROUSEL TO MASTER_CARROUSEL.CSV
if($connection){
$sql_master_carrausel =
   "SELECT *
    FROM master_carrousel";
$pathToCsv_master_carrausel = '/var/www/html/narrowcasting/mysqldump/master_carrousel.csv';

$command_master_carrausel = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
    $db->remote_db_host,
    $db->remote_db_user,
    $db->remote_db_password,
    $db->remote_db_database,
    $sql_master_carrausel,
    $pathToCsv_master_carrausel);
//echo $command_master_carrausel;

exec($command_master_carrausel);

// END SYNC MASTER_CARROUSEL TO MASTER_CARROUSEL.CSV

// BEGIN SYNC MASTER_CARROUSEL_LISTING TO MASTER_CARROUSEL_LISTING.CSV

$sql_master_carrausel_listing =
   "SELECT * FROM master_carrousel_listing";
$pathToCsv_master_carrausel_listing = '/var/www/html/narrowcasting/mysqldump/master_carrousel_listing.csv';

$command_master_carrausel_listing = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
    $db->remote_db_host,
    $db->remote_db_user,
    $db->remote_db_password,
    $db->remote_db_database,
    $sql_master_carrausel_listing,
    $pathToCsv_master_carrausel_listing);

	//echo $command_master_carrausel_listing;

exec($command_master_carrausel_listing);

// END SYNC MASTER_CARROUSEL_LISTING TO MASTER_CARROUSEL_LISTING.CSV

// BEGIN SYNC MASTER_SCHEDULE TO MASTER_SCHEDULE.CSV

$sql_master_schedule =
   "SELECT * FROM master_schedule";
$pathToCsv_master_schedule = '/var/www/html/narrowcasting/mysqldump/master_schedule.csv';

$command_master_schedule = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
    $db->remote_db_host,
    $db->remote_db_user,
    $db->remote_db_password,
    $db->remote_db_database,
    $sql_master_schedule,
    $pathToCsv_master_schedule);

	//echo $command_master_schedule;

exec($command_master_schedule);

// END SYNC MASTER_SCHEDULE TO MASTER_SCHEDULE.CSV

// BEGIN SYNC URS TO URLS.CSV

$sql_urls =
   "SELECT * FROM urls";
$pathToCsv_urls = '/var/www/html/narrowcasting/mysqldump/urls.csv';

$command_urls = sprintf("/Applications/xampp/xamppfiles/bin/mysql --host=%s -u %s  --password=%s -D %s -e '%s' > %s",
    $db->remote_db_host,
    $db->remote_db_user,
    $db->remote_db_password,
    $db->remote_db_database,
    $sql_urls,
    $pathToCsv_urls);

	//echo $command_urls;

exec($command_urls);

// END SYNC URLS TO URLS.CSV


// BEGIN TRANSFER FROM CSV'S TO LOCAL CLIENT TABLES

$truncate_sql_client_master_carrausel="truncate table client_carrousel";
$db->query($truncate_sql_client_master_carrausel);
   $sql_client_master_carrausel =
   "LOAD DATA LOCAL INFILE '$pathToCsv_master_carrausel'
    REPLACE INTO TABLE `client_carrousel`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_master_carrausel); // Using your favourite database adapter


$truncate_sql_client_master_carrausel_listing="truncate table client_carrousel_listing";
$db->query($truncate_sql_client_master_carrausel_listing);
    $sql_client_master_carrausel_listing =
   "LOAD DATA LOCAL INFILE '$pathToCsv_master_carrausel_listing'
    REPLACE INTO TABLE `client_carrousel_listing`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_master_carrausel_listing); // Using your favourite database adapter

$truncate_sql_client_master_schedule="truncate client_schedule";
$db->query($truncate_sql_client_master_schedule);
   $sql_client_master_schedule =
   "LOAD DATA LOCAL INFILE '$pathToCsv_master_schedule'
    REPLACE INTO TABLE `client_schedule`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_client_master_schedule); // Using your favourite database adapter

$truncate_sql_urls="truncate urls";
$db->query($truncate_sql_urls);
   $sql_urls =
   "LOAD DATA LOCAL INFILE '$pathToCsv_urls'
    REPLACE INTO TABLE `urls`
    CHARACTER SET 'utf8'
    IGNORE 1 LINES";
$db->query($sql_urls); // Using your favourite database adapter

// echo "Database synced with remote";
$carrousel_listing_query="select * from carrousel_listing where status=0";
$carrousel_listing_rs=$db->query($carrousel_listing_query);
$carrousel_listing_count=mysql_num_rows($carrousel_listing_rs);
if($carrousel_listing_count == 0){
 $curr_date=mktime(date('H'),date('i'),0,date('n'),date('d'),date('Y'));
 $current_date=strtotime($curr_date);
 $client_schedule = "select c.is_active, cs.schedule as schedule, cs.cid as cid from client_schedule cs, clients c where find_in_set(c.id,cs.clients) and c.is_active =1 order by cs.schedule desc";
 $client_schedule_record_set=$db->query($client_schedule);
 while($client_schedule_rocord=mysql_fetch_array($client_schedule_record_set)){
   $scheduled_date=$client_schedule_rocord[schedule];
   if($scheduled_date <= $curr_date){
    $carrousel_id = $client_schedule_rocord['cid'];
    break;
   }
 }
  
 if(isset($carrousel_id) && $carrousel_id!=0){
  $update_client_carrousel_listing_query="update client_carrousel_listing set ";
  $update_client_carrousel_listing_query .="status=0 ";
  $update_client_carrousel_listing_query .="where id=$carrousel_id";  
  $update_response=$db->query($update_client_carrousel_listing_query);
  $update_client_carrousel_listing_query2="update client_carrousel_listing set ";
  $update_client_carrousel_listing_query2 .="status=1 ";
  $update_client_carrousel_listing_query2 .="where id!=$carrousel_id";
  $update_response2=$db->query($update_client_carrousel_listing_query2);
  $update_client_content_block_query="update client_content_block_listing set ";
  $update_client_content_block_query .="status=0 ";
  $update_response3=$db->query($update_client_content_block_query);
 }
}
}else{
     die('Could not connect: ' . mysql_error());
}

?>
