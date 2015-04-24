<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
date_default_timezone_set('Europe/Amsterdam');
#!/usr/bin/php -q
error_reporting(0);
include('config/database.php');

$skip_mediasync = $_REQUEST['skip_mediasync'];
include('start_carrousel_functions.php');
$db = new Database;
$theatreDetailsSql  = "select * from theatre where status=1 limit 1";
$theatreDetails =$db->query($theatreDetailsSql);
$theatreDetails = mysql_fetch_array($theatreDetails);

$folder_query = "select * from narrowcasting_folder";
$record_set = $db->query($folder_query);
$resultset = mysql_fetch_array($record_set);
$is_client = $resultset['is_client'];
$is_master = $resultset['is_master'];

 if($is_client==1){
	 include('syncdb.php');
 }
 
//---------code to check schedule----------------------------------------------
// $curr_date=mktime(date('H'),date('i'),0,date('n'),date('d'),date('Y'));
$curr_date='1428706800';
 $is_block=0;
 $start_script="schedule";
 
$client_schedule_query="select c.*, cs.* from client_schedule cs, clients c where cs.schedule='$curr_date' and find_in_set(c.id,cs.clients) and is_active =1 ";

 $client_schedule_conn=$db->query($client_schedule_query);
 $client_schedule_count=mysql_num_rows($client_schedule_conn);
echo "client_schedule_count : ".$client_schedule_count;
exit;
 if($client_schedule_count > 0){  
	$is_block=1;
    $update_client_content_block_query="update client_content_block_listing set status=0";
    $db->query($update_client_content_block_query);
	
  while($result=mysql_fetch_array($client_schedule_conn)) {
     $carr_id=$result['cid'];
     $new_schedule=$result['schedule'];
     $ipaddress=$result['ipaddress'];
     $ipaddress_condition=intval($ipaddress);
     
     $newmultiscreen=$result['multiscreen'];
     $new_multiscreen=substr($newmultiscreen,-1);
     if($new_multiscreen==",") {
       $multiscreen=substr($newmultiscreen,0,-1);
     } else {
       $multiscreen=$result['multiscreen'];
     }
     /**************** start of new code similar to postermodule*****************/
     //if  machine ip is selected  all or disabled and multiscreen is disabled
     if($ipaddress_condition!="" && $newmultiscreen=="") {
       // echo "<<<<<<< 1st";
        $update_query="update carrousel_listing set status=1";
        $db->query($update_query);
		
		$update_query1="update client_carrousel_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
		        $db->query($update_query1);
				
				$update_query2="update client_carrousel_listing set status=1 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id!=$carr_id";
				        $db->query($update_query2);
        
        //$update_client_content_block_query="update client_content_block_listing set status=1 where id!=$carr_id";
       // $db->query($update_client_content_block_query);
        
        $update_client_content_block_query1="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
        $db->query($update_client_content_block_query1);
        
      }//if  machine ip  selected  is all or disabled and multiscreen is selected(enabled)
      else if($ipaddress_condition!="" && $newmultiscreen!="") {
        //---------code for multiscreen check status--------------------------------
         $cpr_multiscreen=explode(",",$multiscreen);
         sort($cpr_multiscreen);
         $select_count_all="select * from combined_carrousel_details where status=0 and multiscreen!=''";
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
              $update_query1="update carrousel_listing set status=1 , multiscreen=''";// does i remove multiscreen
              $db->query($update_query1);
            
             // $update_client_content_block_query1="update client_content_block_listing set status=1 , multiscreen='' where id=$new_carr_id";// does i remove multiscreen
             // $db->query($update_client_content_block_query1);
            }else{
              sort($compare_scr);
              $compare_scr=implode(',',$compare_scr);
              $update_query2="update carrousel_listing set multiscreen='$compare_scr' where id=$new_carr_id";
              $db->query($update_query2);
              
              $update_client_content_block_query2="update client_content_block_listing set multiscreen='$compare_scr'  where id=$new_carr_id";
              $db->query($update_client_content_block_query2);
            }
         }
            //---------- change the status of current start carrousel ------------------------
         
         $update_client_content_block_query="update client_content_block_listing set status=0 ,ipaddress='$ipaddress',multiscreen='$multiscreen' where id=$carr_id";
         $db->query($update_client_content_block_query);
          //die;
       }
       else {
       // if ipaddress in selected is not
       if($ipaddress_condition!=0 and $newmultiscreen=="") {// if  machine ip is selected (not all)and multiscreen is disabled
        $select_count_all="select * from combined_carrousel_details where status=0 and (ipaddress='$ipaddress_condition' or ipaddress='0')";
        $res_count_all=$db->query($select_count_all);
        //$all_count=mysql_num_rows($res_count_all);
        while($result_count_all=mysql_fetch_array($res_count_all)) {
          $new_carr_id=$result_count_all['id'];
          $courrasel_id_address=$result_count_all['ipaddress'];
          $stop=false;
          // for $courrasel_id_address==0 how we select 1 ip from rest of ip because current we cant select a set of ips only (all or 1 is fun)....
          if(($courrasel_id_address==$ipaddress_condition) || ($courrasel_id_address===""||$courrasel_id_address==0)){
            $stop=true;
          }
          if($stop==true) {
            $update_query1="update carrousel_listing set status=1,multiscreen='' where id=$carr_id";// does i remove multiscreen
            $db->query($update_query1);
            
            
           // $update_client_content_block_query1="update client_content_block_listing set status=1,multiscreen='' where id=$new_carr_id";// does i remove multiscreen
           // $db->query($update_client_content_block_query1);
          }
        }
             //---------- change the status of current start carrousel ------------------------
      
        
        $update_client_content_block_query="update client_content_block_listing set status=0 ,ipaddress='$ipaddress_condition',multiscreen='' where id=$carr_id";
        $db->query($update_client_content_block_query);
       }
       else {// if both machine ip and multiscreen is selected
          $cpr_multiscreen=explode(",",$multiscreen);
          
          sort($cpr_multiscreen);
          $select_count_all="select * from combined_carrousel_details where (ipaddress='$ipaddress_condition' or ipaddress='0' or  ipaddress='') and status=0";
          $res_count_all=$db->query($select_count_all);
          $all_count=mysql_num_rows($res_count_all);
          //echo $all_count;die;
          $abcdef=12345;
          
          while($result_count_all=mysql_fetch_array($res_count_all)) {
           $new_carr_id=$result_count_all['id'];
           $compare_scr=explode(',',$result_count_all['multiscreen']);
           $courrasel_id_address=$result_count_all['ipaddress'];
         
            // for $courrasel_id_address==0 how we select 1 ip from rest of ip because current we cant select a set of ips only (all or 1 is fun)....
           if(($courrasel_id_address==$ipaddress_condition) || (intval($courrasel_id_address)==0)){
            for($c=0;$c<count($cpr_multiscreen);$c++) {
             if(($array_pos=array_search($cpr_multiscreen[$c],$compare_scr))!==FALSE) {
              unset($compare_scr[$array_pos]);
             }
            }
           }
           if(count($compare_scr)==0) {
            $update_query1="update carrousel_listing set status=1 , multiscreen=''";// does i remove multiscreen
            $db->query($update_query1);
            
           // $update_client_content_block_query1="update client_content_block_listing set status=1 , multiscreen='' where id=$new_carr_id";// does i remove multiscreen
          //  $db->query($update_client_content_block_query1);
            
           }else{
             $compare_scr=implode(',',$compare_scr);
             $update_query2="update carrousel_listing set multiscreen='$compare_scr'";
             $db->query($update_query2);
             
             $update_client_content_block_query2="update client_content_block_listing set multiscreen='$compare_scr'  where id=$new_carr_id";
             $db->query($update_client_content_block_query2);
             
           }
  
          }
             //---------- change the status of current start carrousel ------------------------
         
          
          $update_client_content_block_query="update client_content_block_listing set status=0 ,ipaddress='$ipaddress_condition',multiscreen='$multiscreen' where id=$carr_id";
          $db->query($update_client_content_block_query);
           
       }
       
     }
  
   /**************** end of new code similar to postermodule*****************/
  
  
 }

   
 if($db->is_html5==1){
        include('start_carrousel_apple_script_html5.php');
 }else if($theatreDetails[qlab]==3) {
   include('start_carrousel_apple_script_qlab3.php');
   }
   else {
   include('start_carrousel_apple_script.php');
   }
  
  
  
 }else{
 $Query="select * from schedule where schedule='$curr_date'";
 // $Query="select * from schedule where schedule='1424962800'";
 $conn=$db->query($Query);
 $count=mysql_num_rows($conn);
 if($count>0) {
      
  while($result=mysql_fetch_array($conn)) {
     $carr_id=$result['cid'];
     $new_schedule=$result['schedule'];
      $ipaddress=$result['ipaddress'];
     $ipaddress_condition=intval($ipaddress);
     
     $newmultiscreen=$result['multiscreen'];
     $new_multiscreen=substr($newmultiscreen,-1);
     if($new_multiscreen==",") {
       $multiscreen=substr($newmultiscreen,0,-1);
     } else {
       $multiscreen=$result['multiscreen'];
     }
 
  
     /**************** start of new code similar to postermodule*****************/
     //if  machine ip is selected  all or disabled and multiscreen is disabled
     
	 
     if($ipaddress!="" && $newmultiscreen=="") {
	      echo $ipaddress;
	      $cpr_ipaddress=explode(",",$ipaddress);
	      sort($cpr_ipaddress);
	      $select_count_all_ip="select * from carrousel_listing where status=0 and ipaddress!=''";
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
	  
	  
      }//if  machine ip  selected  is all or disabled and multiscreen is selected(enabled)
      else if($ipaddress!="" && $newmultiscreen!="") {
       
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
			  
              $update_query2="update client_content_block_listing set status=1 , multiscreen=''";// does i remove multiscreen
              $db->query($update_query2);
			  
              $update_query3="update client_carrousel_listing set status=1 , multiscreen=''";// does i remove multiscreen
              $db->query($update_query3);
            }else{
              sort($compare_scr);
              $compare_scr=implode(',',$compare_scr);
              $update_query2="update carrousel_listing set multiscreen='$compare_scr'  where id=$new_carr_id";
              $db->query($update_query2);
			  
              $update_query2="update client_content_block_listing set multiscreen='$compare_scr'";
              $db->query($update_query2);
			  
              $update_query3="update client_carrousel_listing set multiscreen='$compare_scr'";
              $db->query($update_query3);
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
        //$all_count=mysql_num_rows($res_count_all);
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
			
            $update_query1="update client_content_block_listing set status=1,multiscreen=''";// does i remove multiscreen
            $db->query($update_query1);
			
            $update_query2="update client_carrousel_listing set status=1,multiscreen=''";// does i remove multiscreen
            $db->query($update_query2);
          }
        }
             //---------- change the status of current start carrousel ------------------------
        $update_query="update carrousel_listing set status=0 ,ipaddress='$ipaddress_condition',multiscreen='' where id=$carr_id";
        $db->query($update_query);
       }
       else {// if both machine ip and multiscreen is selected
          $cpr_multiscreen=explode(",",$multiscreen);
          
          sort($cpr_multiscreen);
          $select_count_all="select * from carrousel_listing where (ipaddress='$ipaddress_condition' or ipaddress='0' or  ipaddress='') and status=0";
          $res_count_all=$db->query($select_count_all);
          $all_count=mysql_num_rows($res_count_all);
          //echo $all_count;die;
          $abcdef=12345;
          
          while($result_count_all=mysql_fetch_array($res_count_all)) {
           $new_carr_id=$result_count_all['id'];
           $compare_scr=explode(',',$result_count_all['multiscreen']);
           $courrasel_id_address=$result_count_all['ipaddress'];
         
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
			
            $update_query2="update client_content_block_listing set status=1 , multiscreen=''";// does i remove multiscreen
            $db->query($update_query2);
           }else{
             $compare_scr=implode(',',$compare_scr);
             $update_query2="update carrousel_listing set multiscreen='$compare_scr'  where id=$new_carr_id";
             $db->query($update_query2);
			 
             $update_query3="update client_content_block_listing set multiscreen='$compare_scr' ";
             $db->query($update_query3);
			 
             $update_query4="update client_carrousel_listing set multiscreen='$compare_scr' ";
             $db->query($update_query4);
           }
  
          }
             //---------- change the status of current start carrousel ------------------------
          $update_query="update carrousel_listing set status=0 ,ipaddress='$ipaddress_condition',multiscreen='$multiscreen' where id=$carr_id";
          $db->query($update_query);
           
       }
       
     }
  
   /**************** end of new code similar to postermodule*****************/
  
  
  
  
//  include('start_carrousel_functions.php');
  
if($db->is_html5==1){
       include('start_carrousel_apple_script_html5.php');
}else if($theatreDetails[qlab]==3) {
  include('start_carrousel_apple_script_qlab3.php');
  }
  else {
  include('start_carrousel_apple_script.php');
  }
     
 }
 
}// end count if condition
 }
?>
