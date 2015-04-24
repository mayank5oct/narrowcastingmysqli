<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
 session_start();
 error_reporting(0);
 $site_full_path="/var/www/html/narrowcasting";
 $file_name=trim($_REQUEST['filename']);
 if(file_exists("$site_full_path/export_carr/$file_name")){
  
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename("$site_full_path/export_carr/$file_name"));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize("$site_full_path/export_carr/$file_name"));
    ob_clean();
    flush();
    readfile("$site_full_path/export_carr/$file_name");
    exit;
 }else{
   echo "Geen bestand gevonden...";
   die;
 }
 

?>