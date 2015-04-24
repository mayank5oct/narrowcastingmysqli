<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
include('config/database.php');
$db = new Database;

$path=$_GET['path'];
$temp_overlay_id=$_GET['temp_overlay_id'];
$oldpath=$_GET['oldpath'];
$cid=$_GET['cid'];
$id=$_GET['id'];
$type=$_GET['type'];
$image_path=explode(".",$path);

$deleteQuery="delete from temp_overlay where id=".$temp_overlay_id;
if(intval($temp_overlay_id)!=0){
  $delete_success=$db->query($deleteQuery);    
}
// from create_overlay
if($type==1) {
  if($delete_success){
    $deleteimage="./overlay_video/thumb/".$image_path[0].'.jpg';
    $deletetotalimage="./overlay_video/totalthumb/".$image_path[0].'.jpg';
    $deletevideo="./overlay_video/".$path;
    unlink($deletevideo);
    unlink($deleteimage);
    unlink($deletetotalimage);
  }
  echo"<script type='text/javascript'>window.location = 'create_overlay.php?cid=$cid&path=$oldpath&id=$id&status=$_GET[status]&is_block=$_GET[is_block]&show_cal=$_GET[show_cal]'</script>";
 } else {
  if($delete_success){
    $deleteimage="./overlay_video/".$path;
    $deletethumbimage="./overlay_video/thumb/".$path;
    $deletetotalthumbimage="./overlay_video/totalthumb/".$path;
    unlink($deleteimage);
    unlink($deletethumbimage);
    unlink($deletetotalthumbimage);
  }
  echo"<script type='text/javascript'>window.location = 'createimage_overlay.php?cid=$cid&path=$oldpath&id=$id&status=$_GET[status]&is_block=$_GET[is_block]&show_cal=$_GET[show_cal]'</script>";
 }

?>
