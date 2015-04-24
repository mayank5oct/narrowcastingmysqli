<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
date_default_timezone_set('Europe/Amsterdam');
require('config/database.php');
 $db = new Database;
if($_REQUEST['id']!="") {
  $carr_id=$_REQUEST['id'];
  $carr_sch=$_REQUEST['sdate'];
  $carr_hr=$_REQUEST['shour'];
  $carr_sch=explode("-",$carr_sch);
  $sch_date=mktime($carr_hr,0,0,$carr_sch[0],$carr_sch[1],$carr_sch[2]);
  
  $update_status=$_REQUEST['id1'];
  if($update_status==1) {
   $select_query="select * from carrousel_listing where schedule='$sch_date'";
  } else {
   $select_query="select * from carrousel_listing where schedule='$sch_date' and id!='$carr_id'"; 
  }
  $conn1=$db->query($select_query);
  $count1=mysql_num_rows($conn1);
  if($count1>0) {
   $carrousel_list.="<table><tr><td><span style='color:#c30f0f;'>Er is al een andere carrousel ingepland voor deze tijd.</span></td></tr></table>";
  } else {
   $update="update carrousel_listing set schedule='$sch_date' where id=$carr_id";
   $db->query($update);
   $carrousel_list.="<table><tr><td><span style='color:#c30f0f;'>De programmering is succesvol opgeslagen.</span></td></tr></table>";
  }
}
 //echo $carrousel_list;
 //exit;
 
 echo"<script type='text/javascript'>window.location = 'welcome.php'</script>";
 exit;

?>