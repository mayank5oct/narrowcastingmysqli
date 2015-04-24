<?php
include('config/database.php');
$db = new Database;


  $carr_id=$_REQUEST['id'];
  $ipaddress_condition=intval(trim($_REQUEST['ipaddress']));
  $ipaddress=trim($_REQUEST['ipaddress']);
  $editstatus=$_REQUEST['edit'];
  $newmultiscreen=trim($_REQUEST['multiscreen']);
  $new_multiscreen=substr($newmultiscreen,-1);
  if($new_multiscreen==",") {
   $multiscreen=substr($newmultiscreen,0,-1);
  } else {
    $multiscreen=$_REQUEST['multiscreen'];
  }

  //----- script for deleteing previous cues from Qlab player -------------------
 //
 // exec("osascript delete_selected_cue.scpt");
 //------------- code for running cue start by carrousal listing ---------------
 //echo "<<<<<<< 1st".$ipaddress."HHHHH".$newmultiscreen;
  if($ipaddress=="" && $newmultiscreen=="") {
   //echo "LLLLLLLLLLL";
  }else{
   
   // echo "LLLLLLLOOOOOOOOOOOLLLL<br>";
  }
  if($ipaddress_condition==0){
  //print_r($newmultiscreen."KKKKK");
  }
  if($ipaddress_condition==0 && $newmultiscreen=="") {//if  machine ip is selected  all or disabled and multiscreen is disabled
  // echo "<<<<<<< 1st";
   $update_query="update carrousel_listing set status=1 where id!=$carr_id";
   $db->query($update_query);
   $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
   $db->query($update_query1);
   }
   else if($ipaddress_condition==0 && $newmultiscreen!="") {//if  machine ip  selected  is all or disabled and multiscreen is selected(enabled)
     //---------code for multiscreen check status--------------------------------
     $cpr_multiscreen=explode(",",$multiscreen);
     sort($cpr_multiscreen);
     $select_count_all="select * from carrousel_listing where status=0 and multiscreen!=''";
     $res_count_all=$db->query($select_count_all);
     $all_count=mysql_num_rows($res_count_all);
     while($result_count_all=mysql_fetch_array($res_count_all)) {
     
      $new_carr_id=$result_count_all['id'];
      $compare_scr=explode(',',$result_count_all['multiscreen']);
      $courrasel_id_address=$result_count_all['ipaddress'];
      sort($compare_scr); 
      for($c=0;$c<count($cpr_multiscreen);$c++) {
       if(($array_pos=array_search($cpr_multiscreen[$c],$compare_scr))!==FALSE) {
        unset($compare_scr[$array_pos]);
       }
      }
      if(count($compare_scr)==0) {
        $update_query1="update carrousel_listing set status=1 , multiscreen='' where id=$new_carr_id";// does i remove multiscreen
        $db->query($update_query1);
      }else{
        $compare_scr=implode(',',$compare_scr);
        $update_query2="update carrousel_listing set multiscreen='$compare_scr'  where id=$new_carr_id";
        $db->query($update_query2);
      }
     
     }
        //---------- change the status of current start carrousel ------------------------
     $update_query="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
     $db->query($update_query);
      //die;
    }
    else {
    // if ipaddress in selected is not
    if($ipaddress_condition!=0 and $newmultiscreen=="") {// if  machine ip is selected (not all)and multiscreen is disabled
     $select_count_all="select * from carrousel_listing where status=0 and (ipaddress='$ipaddress_condition' or ipaddress='0')";
     $res_count_all=$db->query($select_count_all);
     $all_count=mysql_num_rows($res_count_all);
     while($result_count_all=mysql_fetch_array($res_count_all)) {
      $new_carr_id=$result_count_all['id'];
      $courrasel_id_address=$result_count_all['ipaddress'];
      $stop=false;
      // for $courrasel_id_address==0 how we select 1 ip from rest of ip because current we cant select a set of ips only (all or 1 is fun)....
      if(($courrasel_id_address==$ipaddress_condition) || ($courrasel_id_address===""||$courrasel_id_address==0)){
        $stop=true;
      }
      if($stop==true) {
        $update_query1="update carrousel_listing set status=1,multiscreen='' where id=$new_carr_id";// does i remove multiscreen
        $db->query($update_query1);
      }
    }
          //---------- change the status of current start carrousel ------------------------
     $update_query="update carrousel_listing set status=0 ,ipaddress='$ipaddress_condition',multiscreen='' where id=$carr_id";
     $db->query($update_query);
    }
    else {// if both machine ip and multiscreen is selected
     $cpr_multiscreen=explode(",",$multiscreen);
     sort($cpr_multiscreen);
     $select_count_all="select * from carrousel_listing where (ipaddress='$ipaddress_condition' or ipaddress='0') and status=0";
     $res_count_all=$db->query($select_count_all);
     $all_count=mysql_num_rows($res_count_all);
     //echo $all_count;die;
     while($result_count_all=mysql_fetch_array($res_count_all)) {
      $new_carr_id=$result_count_all['id'];
      $compare_scr=explode(',',$result_count_all['multiscreen']);
      $courrasel_id_address=$result_count_all['ipaddress'];
      sort($compare_scr);
       // for $courrasel_id_address==0 how we select 1 ip from rest of ip because current we cant select a set of ips only (all or 1 is fun)....
      if(($courrasel_id_address==$ipaddress_condition) || (intval($courrasel_id_address)==0)){
       for($c=0;$c<count($cpr_multiscreen);$c++) {
        if(($array_pos=array_search($cpr_multiscreen[$c],$compare_scr))!==FALSE) {
         unset($compare_scr[$array_pos]);
        }
       }
      }
      if(count($compare_scr)==0) {
       $update_query1="update carrousel_listing set status=1 , multiscreen='' where id=$new_carr_id";// does i remove multiscreen
       $db->query($update_query1);
      }else{
        $compare_scr=implode(',',$compare_scr);
        $update_query2="update carrousel_listing set multiscreen='$compare_scr'  where id=$new_carr_id";
        $db->query($update_query2);
      }
      
     }
        //---------- change the status of current start carrousel ------------------------
     $update_query="update carrousel_listing set status=0 ,ipaddress='$ipaddress_condition',multiscreen='$multiscreen' where id=$carr_id";
     $db->query($update_query);
      
    }
    
  }
  //exit;
  if(isset($_GET['edit']) and $_GET['edit']==1) {
   $update_query_edit="update carrousel_listing set edit_status=0 where id=$carr_id";
   $db->query($update_query_edit);
  }
  
 
   $theatre_multiscreen="select multiscreen_status from theatre where status=1 limit 1";
   $theatre_multiscreen_handler=$db->query($theatre_multiscreen);
   $result_multiscreen=mysql_fetch_array($theatre_multiscreen_handler);
  // echo "Test";
  // exit;
 
    //require('restart_carrousel_apple_script_multiscreen.php');
    include('start_carrousel_functions.php');

 if($db->is_html5==1){
        include('start_carrousel_apple_script_html5.php');
 }else if($theatreDetails[qlab]==3) {
   include('start_carrousel_apple_script_qlab3.php');
   }
   else {
   include('start_carrousel_apple_script.php');
   }
 
   
 

?>
