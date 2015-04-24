<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(0);
$cid=$_GET['id'];
$page=$_GET['page'];

if(isset($cid) && !empty($cid)) {
  header("Cache-Control: no-cache, must-revalidate");
  header("Location: edit.php?id=$cid&status=$_GET[status]&is_block=$_GET[is_block]", true, 302);
  die;

 }else if(isset($page) && !empty($page)){
  header("Cache-Control: no-cache, must-revalidate");
  header("Location: mededelingen_overlay.php?is_block=$_GET[is_block]", true, 302);
  die;
 }
 else {
   header("Cache-Control: no-cache, must-revalidate");
   header("Location:create_carrousel.php", true, 302);
   die;
 }

