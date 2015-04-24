<?php
error_reporting(E_ALL);
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(0);
include('config/database.php');
$db = new Database;

$id=$_GET['id'];
$path=$_GET['path'];
$cid=$_GET['cid'];
$enddate_string=str_replace('-','/',$_GET['enddate']);
$enddate=strtotime($enddate_string);
$overlay_path="overlay_video/".$path;
$overlay = 'overlay';
if($id!="" && $overlay_path!=""){
 if(isset($_GET['enddate']) ){
 
  //$date=explode("-",$_GET['enddate']);
  
 // $enddate=time(0,0,0,$date[0],$date[1],$date[2]);
  //echo "enddate: ".$enddate=strtotime($_GET['enddate']);
   $update_query="update temp_carrousel set name='$overlay_path', enddate='$enddate' where id=$id";
 }else{
  $update_query="update temp_carrousel set name='$overlay_path' where id=$id";
 }
 
 $db->query($update_query);
}

if($cid!="") {
  header("Cache-Control: no-cache, must-revalidate");
  header("Location: edit.php?id=$cid&status=$_GET[status]&is_block=$_GET[is_block]&key=$overlay", true, 302);
  die;

 } else {
   header("Cache-Control: no-cache, must-revalidate");
   header("Location:create_carrousel.php?status=$_GET[status]&key=$overlay", true, 302);
   die;
 }

/*
 if($cid!="") {
  echo"<script type='text/javascript'>window.location = 'edit.php?id=$cid&status=2&key=$overlay'</script>"; 
 } else {
  echo"<script type='text/javascript'>window.location = 'create_carrousel.php?status=2&key=$overlay'</script>";
 }
 
*/
