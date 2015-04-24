<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
 session_start();
 error_reporting(0);
 include('config/database.php');
 $db = new Database;
 $delete_item_name=trim(urldecode($_REQUEST['delete_item_name']));
 $delete_item_source=trim(urldecode($_REQUEST['dir']));
 $edit_page=trim(urldecode($_REQUEST['edit']));
 
 $delete_item_fullname=$delete_item_source.'/'.$delete_item_name;
 $delete_query="";
 $delete_query_status=true;
 
 //check in main carrousel
 $carrousel_check_quesry="select count(distinct(c_id)) as count from carrousel where name='$delete_item_fullname'";
 $count=0;
 $check_query_result=$db->query($carrousel_check_quesry);
 while($result_row =mysql_fetch_array($check_query_result)){
   $count=$result_row['count'];
 }
 
 
 //check in temp carrousel
 $temp_carrousel_check_query="select count(*) as count  from temp_carrousel where name='$delete_item_fullname' limit 1";
 
 $temp_count=0;
 $temp_check_query_result=$db->query($temp_carrousel_check_query);
 
 while($result_row =mysql_fetch_array($temp_check_query_result)){
   $temp_count=$result_row['count'];
 }
 if($temp_count>0)
 $temp_count=1;
 
 $total_occurance=$temp_count+$count;
 
 if($total_occurance>0){
  echo  $total_occurance;
 }else{
  echo  "0";
 }
 die;


?>