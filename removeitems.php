<?php
   error_reporting(0);
   include('config/database.php');
   include('config/master_database.php');
   $db = new Database;
   $today=date('m-d-Y');
   $today=explode("-",$today);
   $today_date=mktime(0,0,0,$today[0],$today[1],$today[2]);
   $query_folder="select * from narrowcasting_folder";
   $rs=$db->query($query_folder);
   $result=mysql_fetch_array($rs,MYSQL_ASSOC);
   
   $is_client=$result['is_client'];
   $is_master=$result['is_master'];
   $is_regular=$result['is_regular'];
 //  $is_block=$is_client;
   
   $multiscreen_query="select a.label, b.multiscreen_status,b.export_status from theatre_multiscreen as a right join theatre as b on b.id=a.theatre_id where  b.status=1";

   $conn4=$db->query($multiscreen_query);
   $multiscreen_result=mysql_fetch_array($conn4);
   $multiscreen_check = $multiscreen_result['multiscreen_status'];
   
   if($is_client==1)
   {
      $remove_items_query_client="delete from client_content_block where enddate < $today_date and enddate <> 0";
      $con=$db->query($remove_items_query_client);
	  
      $remove_items_query_client1="delete from master_carrousel where enddate < $today_date and enddate <> 0";
      $con=$db->query($remove_items_query_client1);
	 
      $remove_items_query_client2="delete from carrousel where enddate < $today_date and enddate <> 0";
      $con=$db->query($remove_items_query_client2);
	  
      $remove_items_query_client3="delete from client_carrousel where enddate < $today_date and enddate <> 0";
      $con=$db->query($remove_items_query_client3);
	  
   }elseif($is_master==1){
      $remove_items_query_master="delete from master_carrousel where enddate < $today_date and enddate <> 0";
      $con=$db->query($remove_items_query_master);
   }else{
      $remove_items_query="delete from carrousel where enddate < $today_date and enddate <> 0";
      $con=$db->query($remove_items_query);
   }
  // echo "Items removed !!";
   //$schedule=
 //  get_carrousel_item_details(1);
   include('start_carrousel_functions.php');

   if($db->is_html5==1){
     
        include('start_carrousel_apple_script_html5.php');
   }
   elseif($multiscreen_check==1){
   	  include('restart_carrousel_apple_script_multiscreen.php');
   }
   
   else {
     
        include('restart_carrousel_apple_script.php');
   }
   
   
?>
