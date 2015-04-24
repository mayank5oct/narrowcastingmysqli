<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
   session_start();
   $sid=session_id();
   error_reporting(0);
   include('config/database.php');
   include('config/master_database.php');
  // echo "<pre>"; print_r($_POST); echo "</pre>";
   $db = new Database;
   $master_db = new master_Database();
   
   $folder_query = "select * from narrowcasting_folder";
   $record_set = $db->query($folder_query);
   $resultset = mysqli_fetch_array($record_set);
   $is_client = $resultset['is_client'];
   $is_master = $resultset['is_master'];
 
   $page_id=$_REQUEST['id'];
   $carrouel_name=addslashes($_REQUEST['cname']);
   if($is_master==1){
      $CountQuery = "SELECT * FROM master_carrousel_listing";
      $conn=$master_db->query($CountQuery);
      $count=mysqli_num_rows($conn);
   }else{
      $CountQuery = "SELECT * FROM carrousel_listing";
      $conn=$db->query($CountQuery);
      $count=mysqli_num_rows($conn);
      
   }
   $result_url_analytic=validate_url_in_caurrasels();
   
   if($count>=30){
      $error_message="U heeft het maximum aantal van 30 carrousels bereikt. U moet eerst een carrousel verwijderen voordat u een nieuwe kunt aanmaken.";
      echo $error_message;
      die;
   }
   else if($result_url_analytic!="success"){
      echo $result_url_analytic;
      die;
   }
   else {
     
   if($is_master==1){
   $checkname="select * from master_carrousel_listing where name='$carrouel_name'";
   $conn1=$master_db->query($checkname);
   }else{
   $checkname="select * from carrousel_listing where name='$carrouel_name'";
   $conn1=$db->query($checkname);
   }
   
    
   
   $conn1=$db->query($checkname);
   if(mysqli_num_rows($conn1)>0) {
     echo $error_message="Deze naam bestaat al. U dient een andere naam voor de carrousel te kiezen.";
     die;
   }
   else {
   if($is_master==1){
      $Query_master = "insert into master_carrousel_listing (name,status) values('$carrouel_name','1')";
   $conn_master=$master_db->query($Query_master);
   $master_carrousel_id=mysqli_insert_id($master_db->Link_ID_PREV);
   }else{
      $Query = "insert into carrousel_listing (name,status) values('$carrouel_name','1')";
      $conn=$db->query($Query);
     
      $carrousel_id = $db->give_insert_id('carrousel_listing');

      

   }
   
 
     if($carrousel_id!="" || $master_carrousel_id!='') {         
       $select_temp="select * from temp_carrousel where s_id='$sid'";
       $res_1=$db->query($select_temp);
       while($result_1=mysqli_fetch_array($res_1)){
         $name=$result_1['name'];
         $corder=$result_1['record_listing_id'];
         $secduration=$result_1['duration'];
         $enddate=$result_1['enddate'];
         if($is_master==1){
            $carrlist_query_master="insert into master_carrousel (c_id,name,duration,enddate,record_listing_id) values('$master_carrousel_id','$name','$secduration','$enddate','$corder')";
            $master_db->query($carrlist_query_master);
         }else{
             $carrlist_query="insert into carrousel (c_id,name,duration,enddate,record_listing_id) values('$carrousel_id','$name','$secduration','$enddate','$corder')";
             $db->query($carrlist_query);
         }
        
       }
      $deletequery="delete from temp_carrousel where s_id='$sid'";
      $db->query($deletequery);
      print $page_id;
      die;
     }else{
      ## error in getting id of last created carrousel(last insert id)
      if($is_master==1){
         $deletequery="delete from master_carrousel_listing where name='$carrouel_name' ORDER BY id DESC limit 1";
      $master_db->query($deletequery);
      }else{
      $deletequery="delete from carrousel_listing where name='$carrouel_name' ORDER BY id DESC limit 1";
      $db->query($deletequery);
      
      }
     echo '500';
      die;
     }
    }
   }
   
   
   
   function validate_url_in_caurrasels(){
    global $db,$sid;
    ## getting all caurrasel rows
    $selectquery="select duration,name,enddate from temp_carrousel where s_id='$sid' ORDER BY record_listing_id ASC";
    $conn=$db->query($selectquery);
    $all_carrousel_data=array();
    while($result=mysqli_fetch_array($conn)) {
       $path=$result['name'];
       $imagename=explode('/',$path);
       $imagename1=explode('.',$imagename[1]);
       if($path==$imagename[0] and $imagename[1]!='undefined') {
         $row_type="url";
       }else{
        $row_type="";
       }
      if($row_type=="url"){
       $all_carrousel_data[]=array('duration'=>$result['duration'],'data'=>$result['name'],'type'=>$row_type,'enddate'=>$result['enddate']);
      }
      else if($imagename1[1]=="jpg" or $imagename1[1]=="gif" or $imagename1[1]=="png") {
        $all_carrousel_data[]=array('duration'=>$result['duration'],'data'=>$result['name'],'type'=>'image','enddate'=>$result['enddate']);
      }else{
            ob_start();
             passthru("$ffmpeg_path -i /var/www/html/narrowcasting/$result[name] 2>&1");
            $duration = ob_get_contents();
            ob_end_clean();
            preg_match('/Duration: (.*?),/', $duration, $matches);
            
            $duration = $matches[1];
            $duration_array = split(':', $duration);
            $total_duration_insec=$duration_array[0]*3600+$duration_array[1]*60+$duration_array[2];
            $all_carrousel_data[]=array('duration'=>$total_duration_insec,'data'=>$result['name'],'type'=>'video','enddate'=>$result['enddate']);	
      }	
    }
    
    
    ## getting previous items duration for each url in timeline
    $tempduration=0;
    $url_analytic_data_innner=array();
    $counter=0;
    if(count($all_carrousel_data)<=0 or !is_array($all_carrousel_data)){
     return "success";
    }else if(count($all_carrousel_data)==1 and $all_carrousel_data[0]['type']=='url'){ // when only one item (url) is in carrousel at 1st position
       $errormsg="- Een carrousel kan niet alleen uit een URL bestaan.";
       return $errormsg;
    }
    
    $non_url_item_count=0;
    $hasError2=false;
    $hasError3=false;
    $hasError4=false;
    $hasError5=false;
    $hasError6=false;
    $errormsg=array();
    $carrousel_item_count=count($all_carrousel_data);
    //echo "<pre>"; print_r($all_carrousel_data); echo "</pre>";
    foreach($all_carrousel_data as $key=>$caurrasel_row){
     if($caurrasel_row['type']=='url'){
      if($all_carrousel_data[$key-1]['enddate']!=0){
         $errormsg[]="U kunt de afbeelding/video die voorafgaat aan een URL niet inplannen.";
         $hasError6=true;
      }
      if($all_carrousel_data[$key+1]['enddate']!=0){
         $errormsg[]="U kunt de afbeelding/video die na een URL komt niet inplannen.";
         $hasError6=true;
      }
	if(!isset($url_analytic_data_innner[$counter])){
	  $url_analytic_data_innner[$counter]=array('index'=>$key,'duration'=>$tempduration,'non_url_item_count'=>$non_url_item_count,'count_url'=>$counter+1);
	}
	else{
	  //$url_analytic_data_innner[$counter]['duration']+=$tempduration;
	}
	$tempduration=0;
        $non_url_item_count=0;
	$counter++;
     }else{
	$tempduration+=$caurrasel_row['duration'];
        $non_url_item_count++;
     }
    }
    ## no url in timeline
    if(count($url_analytic_data_innner)<=0){
	 return "success";
    }
   // echo '$count_carrousel='.$count_carrousel;
   // print_r($url_analytic_data_innner);
    
    ## generate error msg  based on  duration before each url and its position
   
   // echo "<pre>"; print_r($url_analytic_data_innner); echo "</pre>";
      
    foreach($url_analytic_data_innner as $key=>$url_row){
      if($url_row['index']==0 and $url_row['duration']==0){
         $errormsg[]="- U kunt een carrousel niet beginnen met een URL.";
      }
      else if($url_row['duration']>0 and $url_row['duration']<10){
        if(!$hasError2){
         $errormsg[]="- De afbeelding/video voor de URL moet minstens 10 seconden duren.";
         $hasError2=true;
      }
      }else if($url_row['duration']==0){
        if(!$hasError3){
          $errormsg[]="- U kunt geen twee URL's achter elkaar plaatsen.";
          $hasError3=true;
        }
      }
      else if($url_row['non_url_item_count']<2 && $url_row['count_url']>1){
        if(!$hasError4){
          $errormsg[]="- U dient minimaal 2 items tussen 2 verschillende URL's in de tijdlijn te plaatsen.";
          $hasError4=true;
        }
      }
      else if(($carrousel_item_count==($url_row['index']+1)) ){
        if(!$hasError5){
          $errormsg[]="- U kunt een carrousel niet met een URL eindigen.";
          $hasError5=true;
        }
      }
     }
    if(count($errormsg)==0){
      $msg="success";
    }else{
      $msg=implode("\n",$errormsg);
    }
    return $msg;
 } 
exit;
?>
