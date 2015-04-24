<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(0);
include('config/database.php');
require('config/master_database.php');
$delete_id=$_REQUEST['id'];
$db = new Database;
$master_db = new master_Database();

 if($delete_id!="") {
	 if ($is_master==1){
  $select_query="select * from master_carrousel_listing where id='$delete_id'";
  $res=$master_db->query($select_query);
} else {
	$select_query="select * from carrousel_listing where id='$delete_id'";
	$res=$db->query($select_query);
}
  
  $result=mysql_fetch_array($res);
  $carr_name=$result['name'];
  $carr_name=str_replace(" ","_",$carr_name);
  //$carr_name=$carr_name.".scpt";
  
  #moving the file to OLD folder
   $uploadPath = 'script/old';
   if (!file_exists($uploadPath)) {
       mkdir($uploadPath, 0777, true);
   }
   
   //------------------------- code for checking eppc status for delete files --------------
    $eppc_check_query="select id,eppc_status from theatre where status=1";
    $res1=$db->query($eppc_check_query);
    $result1=mysql_fetch_array($res1);
    $theatre_id=$result1['id'];
    if($result1['eppc_status']=='0') {
      #make copy of deleting file to old folder
       copy("script/$carr_name.scpt", "script/old/$carr_name.scpt");
      #end
      unlink("script/$carr_name.scpt");
    } else {
       copy("script/$carr_name.scpt", "script/old/$carr_name.scpt");
       unlink("script/$carr_name.scpt");
     
       $eppc_count="select * from theatre_eppc where theatre_id='$theatre_id'";
        $res3=$db->query($eppc_count);
        $count1=mysql_num_rows($res3);
        for($b=1;$b<=$count1;$b++) {
          $carr_name1="$carr_name$b.scpt";
          if (file_exists("script/$carr_name1")) {
           copy("script/$carr_name1", "script/old/$carr_name1");
           unlink("script/$carr_name1");
          }
        }
    }
  if($_GET[master]==1){
     $deleteQuery = "delete from master_carrousel_listing where id='$delete_id'";
$conn=$master_db->query($deleteQuery);
  }else{
  $deleteQuery = "delete from carrousel_listing where id='$delete_id'";
  $conn=$db->query($deleteQuery);
  }
  
  if($_GET[master]==1){
  $deleteQuery1 = "delete from master_carrousel where c_id='$delete_id'";
  $conn=$master_db->query($deleteQuery1);
  }else{
   
   $deleteQuery1 = "delete from carrousel where c_id='$delete_id'";
   $conn=$db->query($deleteQuery1);
  }
  
  
  #add by Himani Agarwal on 30 Oct 2012
  #Purpose : to delete schedule for that deleted carrousel
  if($_GET[master]==1){
  $deleteQuery2 = "delete from master_schedule where cid='$delete_id'";
  $conn=$master_db->query($deleteQuery2);
  }else{
   $deleteQuery2 = "delete from schedule where cid='$delete_id'";
   $conn=$db->query($deleteQuery2);
  }
  
  
  //header('Location: welcome.php');
  echo"<script type='text/javascript'>window.location = 'welcome.php'</script>";
 }

exit; 
?>





