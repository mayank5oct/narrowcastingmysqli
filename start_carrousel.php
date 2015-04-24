<?php
error_reporting(E_ALL);
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(0);
include('config/database.php');
$db = new Database;
$is_block=$_REQUEST[is_block];
$skip_mediasync = $_REQUEST['skip_mediasync'];
$theatreDetailsSql  = "select * from theatre where status=1 limit 1";
$theatreDetails =$db->query($theatreDetailsSql);
$theatreDetails = mysql_fetch_array($theatreDetails);
$carr_id=$_REQUEST['id'];
$ipaddress=$_REQUEST['ipaddress'];
$editstatus=$_REQUEST['edit'];
$multiscreen=$_REQUEST['multiscreen'];

$start_script="start_carrousel";
$ipaddress=eregi_replace(',$', '', $ipaddress);
 if($_REQUEST[is_block]==1){
	include('syncdb.php');
}

//echo '<br>ipaddress='.$ipaddress;
//echo '<br>multiscreen='.$multiscreen;
  //echo '<pre>';
 // print_r($_REQUEST);
 // die;
  //----- script for deleteing previous cues from Qlab player -------------------
 //
 // exec("osascript delete_selected_cue.scpt");
 //echo 'multiscreen '.$multiscreen;
 //echo 'editstatus '.$editstatus;
 //echo 'ipaddress '.$ipaddress;
 //------------- code for running cue start by carrousal listing ---------------
 ///------------------- code for running cue start by client content listing ---------
 
 ///------------------- code for running cue start by client content listing ---------
 if($_REQUEST[is_block]==1){
  if($ipaddress=="" && $multiscreen=="") {
   $update_query="update client_content_block_listing set status=1 where id!=$carr_id";
   $db->query($update_query);
   
   $update_query1="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='' where id=$carr_id";
   $db->query($update_query1);
   
   $update_query2="update carrousel_listing set status=1";
   $db->query($update_query2);
   }else if($ipaddress=="" && $multiscreen!=""){
    $update_query="update client_content_block_listing set status=1 where id!=$carr_id and multiscreen='$multiscreen'";
    $db->query($update_query);
    
    $update_query1="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
    $db->query($update_query1);
	
    $update_query2="update carrousel_listing set status=1 and multiscreen='$multiscreen'";
    $db->query($update_query);
   }
   else if($ipaddress!="" && $multiscreen==""){
      $update_query="update client_content_block_listing set status=1 where id!=$carr_id";
      $db->query($update_query);

      $update_query1="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='' where id=$carr_id";
      $db->query($update_query1);
	  
      $update_query2="update carrousel_listing set status=1";
      $db->query($update_query2);
   }
   else if($ipaddress!="" && $multiscreen!=""){
      $update_query="update client_content_block_listing set status=1 where id!=$carr_id and multiscreen='$multiscreen'";
      $db->query($update_query);

      $update_query1="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
      $db->query($update_query1);
	  
      $update_query="update carrousel_listing set status=1 and multiscreen='$multiscreen'";
      $db->query($update_query);
   }else if($ipaddress!=0 and $multiscreen==""){# when ipaddress is not null and multiscreen  is null
    
      $select_count_all="select * from client_content_block_listing where ipaddress IN ($ipaddress) and status=0";
      $res_count_all=$db->query($select_count_all);
      $carr_count_all=mysql_num_rows($res_count_all);
      $result_count_all=mysql_fetch_array($res_count_all);
      
      $new_carr_id=$result_count_all['id'];
      if($carr_count_all>0) {
        $update_query1="update client_content_block_listing set status=1 where id=$new_carr_id";
        $db->query($update_query1);
        $update_query1="update carrousel_listing set status=1";
        $db->query($update_query1);
      } else {
        ## case of handling all screen....
        $select_label="select a.* from theatre_eppc as a , theatre as b where a.theatre_id=b.id and b.status=1";
        $res_label=$db->query($select_label);
        $label_count=mysql_num_rows($res_label);
        $select_carr="select * from carrousel_listing where status=0";
        $res_carr=$db->query($select_carr);
        $carr_count=mysql_num_rows($res_carr);
        ## if all screen is covered then stop all screen caurrasel
        if($carr_count==$label_count) {
          $update_query2="update client_content_block_listing set status=1 where ipaddress=0";
          $db->query($update_query2);  
          $update_query3="update carrousel_listing set status=1 where ipaddress=0";
          $db->query($update_query3);  
        }
        
      }
      $update_query1="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='' where id=$carr_id";
      $db->query($update_query1);

   }## when ipaddress and multiscreen is not null
   else{
    
      $select_count_all="select * from client_content_block_listing where ipaddress IN ($ipaddress) and status=0 and multiscreen='$multiscreen'";
      $res_count_all=$db->query($select_count_all);
      $carr_count_all=mysql_num_rows($res_count_all);
      $result_count_all=mysql_fetch_array($res_count_all);
      $new_carr_id=$result_count_all['id'];
      if($result_count_all){
        $update_query1="update client_content_block_listing set status=1 where id=$new_carr_id";
        $db->query($update_query1);
        $update_query2="update carrousel_listing set status=1";
        $db->query($update_query2);
      }else{
       
        $select_label="select a.* from theatre_eppc as a , theatre as b where a.theatre_id=b.id and b.status=1";
        $res_label=$db->query($select_label);
        $label_count=mysql_num_rows($res_label);
        $select_carr="select * from carrousel_listing where status=0 and multiscreen='$multiscreen'";
        $res_carr=$db->query($select_carr);
        $carr_count=mysql_num_rows($res_carr);
        if($carr_count==$label_count) {
          $update_query2="update client_content_block_listing set status=1 where ipaddress=0";
          $db->query($update_query2);  
          $update_query3="update carrousel_listing set status=1 where ipaddress=0";
          $db->query($update_query3);  
        }
       
      }
       $update_query1="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
       $db->query($update_query1);
    
    }
 }else{
  if($ipaddress=="" && $multiscreen=="") {
   $update_query="update carrousel_listing set status=1 where id!=$carr_id";
   $db->query($update_query);
   
   $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='' where id=$carr_id";
   $db->query($update_query1);
   
   $update_query2="update client_content_block_listing set status=1";
   $db->query($update_query2);
   
   $update_query3="update client_carrousel_listing set status=1";
   $db->query($update_query3); 
   
   }else if($ipaddress=="" && $multiscreen!=""){
    $update_query="update carrousel_listing set status=1 where id!=$carr_id and multiscreen='$multiscreen'";
    $db->query($update_query);
    
    $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
    $db->query($update_query1);
	
    $update_query2="update client_content_block_listing set status=1 and multiscreen='$multiscreen'";
    $db->query($update_query2);
	
    $update_query3="update client_carrousel_listing set status=1 and multiscreen='$multiscreen'";
    $db->query($update_query3);
   }
   else if($ipaddress!="" && $multiscreen==""){
    
    	      $ipaddress;
              
	      $cpr_ipaddress=explode(",",$ipaddress);
	      sort($cpr_ipaddress);
	      $select_count_all_ip="select * from carrousel_listing where  ipaddress!=''";
	      $res_count_all_ip=$db->query($select_count_all_ip);
	      $all_count_ip=mysql_num_rows($res_count_all_ip);
	      while($result_count_all_ip=mysql_fetch_array($res_count_all_ip)) {
		     $new_carr_id=$result_count_all_ip['id'];
		   $compare_ip=explode(',',$result_count_all_ip['ipaddress']);
		   $courrasel_id_address=$result_count_all_ip['ipaddress'];
		   sort($compare_ip);
       
		   for($c=0;$c<count($cpr_ipaddress);$c++) {
		    if(($array_pos_ip=array_search($cpr_ipaddress[$c],$compare_ip))!==FALSE) {
		     
		     unset($compare_ip[$array_pos_ip]);
		    }
		   }
	    
	       if(count($compare_ip)==0) {
	         $update_query1="update carrousel_listing set status=1 , ipaddress='' where id=$new_carr_id";// does i remove multiscreen
	         $db->query($update_query1);
	       }else{
	         $compare_ip=implode(',',$compare_ip);
	         $update_query2="update carrousel_listing set ipaddress='$compare_ip'  where id=$new_carr_id";
	         $db->query($update_query2);
	       }
     
	      }
    
    
 //     $update_query="update carrousel_listing set status=1, ipaddress='$compare_ip' where id=$new_carr_id";
  //    $db->query($update_query);

      $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='' where id=$carr_id";
      $db->query($update_query1);
	  
      $update_query2="update client_content_block_listing set status=1";
      $db->query($update_query2);
	  
      $update_query3="update client_carrousel_listing set status=1";
      $db->query($update_query3);
	  
   }
   else if($ipaddress!="" && $multiscreen!=""){
    
      
      $update_query="update carrousel_listing set status=1 where id!=$carr_id and multiscreen='$multiscreen'";
      $db->query($update_query);

      $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
      $db->query($update_query1);
	  
      $update_query2="update client_content_block_listing set status=1 and multiscreen='$multiscreen'";
      $db->query($update_query2);
	  
      $update_query3="update client_carrousel_listing set status=1 and multiscreen='$multiscreen'";
      $db->query($update_query3);
	  
   }else if($ipaddress!=0 and $multiscreen==""){# when ipaddress is not null and multiscreen  is null
    
      $select_count_all="select * from carrousel_listing where ipaddress IN ($ipaddress) and status=0";
      $res_count_all=$db->query($select_count_all);
      $carr_count_all=mysql_num_rows($res_count_all);
      $result_count_all=mysql_fetch_array($res_count_all);
      
      $new_carr_id=$result_count_all['id'];
      if($carr_count_all>0) {
        $update_query1="update carrousel_listing set status=1 where id=$new_carr_id";
        $db->query($update_query1);
		
        $update_query2="update client_content_block_listing set status=1";
        $db->query($update_query2);
		
        $update_query3="update client_carrousel_listing set status=1";
        $db->query($update_query3);
		
      } else {
        ## case of handling all screen....
        $select_label="select a.* from theatre_eppc as a , theatre as b where a.theatre_id=b.id and b.status=1";
        $res_label=$db->query($select_label);
        $label_count=mysql_num_rows($res_label);
        $select_carr="select * from carrousel_listing where status=0";
        $res_carr=$db->query($select_carr);
        $carr_count=mysql_num_rows($res_carr);
        ## if all screen is covered then stop all screen caurrasel
        if($carr_count==$label_count) {
          $update_query2="update carrousel_listing set status=1 where ipaddress=0";
          $db->query($update_query2);  
		  
          $update_query3="update client_content_block_listing set status=1 where ipaddress=0";
          $db->query($update_query3);  
		  
          $update_query4="update client_carrousel_listing set status=1 where ipaddress=0";
          $db->query($update_query4);  
        }
        
      }
      $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='' where id=$carr_id";
      $db->query($update_query1);
	  

   }## when ipaddress and multiscreen is not null
   else{
    
      $select_count_all="select * from carrousel_listing where ipaddress IN ($ipaddress) and status=0 and multiscreen='$multiscreen'";
      $res_count_all=$db->query($select_count_all);
      $carr_count_all=mysql_num_rows($res_count_all);
      $result_count_all=mysql_fetch_array($res_count_all);
      $new_carr_id=$result_count_all['id'];
      if($result_count_all){
        $update_query1="update carrousel_listing set status=1 where id=$new_carr_id";
        $db->query($update_query1);
		
        $update_query2="update client_content_block_listing set status=1";
        $db->query($update_query2);
		
        $update_query3="update client_carrousel_listing set status=1";
        $db->query($update_query3);
      }else{
       
        $select_label="select a.* from theatre_eppc as a , theatre as b where a.theatre_id=b.id and b.status=1";
        $res_label=$db->query($select_label);
        $label_count=mysql_num_rows($res_label);
        $select_carr="select * from carrousel_listing where status=0 and multiscreen='$multiscreen'";
        $res_carr=$db->query($select_carr);
        $carr_count=mysql_num_rows($res_carr);
        if($carr_count==$label_count) {
          $update_query2="update carrousel_listing set status=1 where ipaddress=0";
          $db->query($update_query2);  
		  
          $update_query3="update client_content_block_listing set status=1";
          $db->query($update_query3);  
		  
          $update_query4="update client_carrousel_listing set status=1";
          $db->query($update_query4);  
        }
       
      }
       $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
       $db->query($update_query1);
    
    }
 }
    /*$select_label="select a.* from theatre_eppc as a , theatre as b where a.theatre_id=b.id and b.status=1";
    $res_label=$db->query($select_label);
    $label_count=mysql_num_rows($res_label);
    
    $select_carr="select * from carrousel_listing where status=0";
    $res_carr=$db->query($select_carr);
    $carr_count=mysql_num_rows($res_carr);
    
    if($carr_count==$label_count) {
     $select_count_all="select * from carrousel_listing where ipaddress=0";
     $res_count_all=$db->query($select_count_all);
     $carr_count_all=mysql_num_rows($res_count_all);
      if($carr_count_all>0) {
        if($ipaddress==0) {
         $update_query2="update carrousel_listing set status=1 where ipaddress=0";
         $db->query($update_query2);  
        } else {
          $update_query2="update carrousel_listing set status=1 where ipaddress='$ipaddress'";
          $db->query($update_query2); 
        }
      } else {
       if($ipaddress==0) {
         $update_query2="update carrousel_listing set status=1 where id!=$carr_id";
         $db->query($update_query2);  
       } else {
        $update_query2="update carrousel_listing set status=1 where ipaddress='$ipaddress'";
        $db->query($update_query2);
      }
     }    
    } else {
      $update_query2="update carrousel_listing set status=1 where ipaddress='$ipaddress'";
      $db->query($update_query2);
    } */
 
  
  if(isset($_GET['edit']) and $_GET['edit']==1) {
   $update_query_edit="update carrousel_listing set edit_status=0 where id=$carr_id";
   $db->query($update_query_edit);
   
  }
  //echo '<br>ipaddress='.$ipaddress;
  //echo '<br>multiscreen='.$multiscreen;
 /*echo "<pre>"; print_r($theatreDetails); echo "</pre>";
 exit;*/
 $curr_date=mktime(date('H'),date('i'),0,date('n'),date('d'),date('Y'));
 $current_date=strtotime($curr_date);
 $client_schedule = "select c.is_active, cs.schedule as schedule, cs.cid as cid from client_schedule cs, clients c where find_in_set(c.id,cs.clients) and c.is_active =1 order by cs.schedule desc";
 $client_schedule_record_set=$db->query($client_schedule);
 while($client_schedule_rocord=mysql_fetch_array($client_schedule_record_set)){
   $scheduled_date=$client_schedule_rocord[schedule];
   if($scheduled_date <= $curr_date){
    $carrousel_id = $client_schedule_rocord['cid'];
    break;
   }
 }
 
 if($_REQUEST[is_block]==1) {
	 
 if(isset($carrousel_id) && $carrousel_id!=0){
  $update_client_carrousel_listing_query="update client_carrousel_listing set ";
  $update_client_carrousel_listing_query .="status=0 ";
  $update_client_carrousel_listing_query .="where id=$carrousel_id";
  $update_response=$db->query($update_client_carrousel_listing_query);
  $update_client_carrousel_listing_query2="update client_carrousel_listing set ";
  $update_client_carrousel_listing_query2 .="status=1 ";
  $update_client_carrousel_listing_query2 .="where id!=$carrousel_id";
  $update_response2=$db->query($update_client_carrousel_listing_query2);
 }
}


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
