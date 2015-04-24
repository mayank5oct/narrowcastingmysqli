<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
include('config/database.php');
$db = new Database;
$imagepath=$_GET['path'];
$temp_mededelingen_id=trim($_GET['temp_mededelingen_id']);

$deleteQuery="delete from temp_mededelingen where id=".$temp_mededelingen_id;
if(intval($temp_mededelingen_id)!=0){
  $delete_success=$db->query($deleteQuery);    
}

$main_image_path='mededelingen/'.$imagepath;
$thumb_image_path='mededelingen/thumb/'.$imagepath;
## delete images from mededelingen directory on sql command success
if($delete_success){
  if(file_exists($main_image_path)){
       unlink($main_image_path);
  }
  if(file_exists($thumb_image_path)){
  unlink($thumb_image_path);
  }
}

echo"<script type='text/javascript'>window.location = 'mededelingen_overlay.php'</script>";

?>