<?php

 function get_carrousel_item_details($carr_id){

    global $db;
    global $is_block;

    if($is_block==1){

    $selectquery="select * from client_content_block where c_id='$carr_id' order by record_listing_id asc";
    }else{
     
      $selectquery="select * from carrousel where c_id='$carr_id' order by record_listing_id asc";
     
    }
  
    $conn=$db->query($selectquery);
    $all_carrousel_data=array();
    $previous_id=0;
    $distance_from_url=0;
    $post_url_item=false;
    $position=1;
    while($result=mysql_fetch_array($conn)) {
        
       $path=$result['name'];
       $imagename=explode('/',$path);
       $imagename1=explode('.',$imagename[1]);
       if($path==$imagename[0] and $imagename[1]!='undefined') {
         $row_type="url";
       }else{
        $row_type="";
       }
      if($row_type=="url"){
       $post_url_item=true;
       $distance_from_url=0;
       $all_carrousel_data[$result['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>$result['duration'],'type'=>$row_type,'name'=>$result['name'],'position'=>$position);
      }
      else if($imagename1[1]=="jpg" or $imagename1[1]=="gif" or $imagename1[1]=="png") {
        ($post_url_item==true)?($distance_from_url=$distance_from_url+1):"";
        $all_carrousel_data[$result['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>$result['duration'],'type'=>'image','name'=>$result['name'],'distance_from_url'=>$distance_from_url,'position'=>$position);	
      }else{
        ($post_url_item==true)?($distance_from_url=$distance_from_url+1):"";
        $all_carrousel_data[$result['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>0,'type'=>'video','name'=>$result['name'],'distance_from_url'=>$distance_from_url,'position'=>$position);	
      }
      
      if($all_carrousel_data[$result['id']]['previous_id']!=0){
       $all_carrousel_data[$all_carrousel_data[$result['id']]['previous_id']]['next_id']=$result['id'];
      }
      
      
      $previous_id=$result['id'];
      $position++;
    }

    return $all_carrousel_data;
  }
  
   function combined_carrousel_details(){

   global $db;
   global $is_block;
   $client_carrousel_data=array();
   $client_content_block_data=array();
   $carrousel_item_details=array();
   $previous_id=0;
   $distance_from_url=0;
   $post_url_item=false;
   $position=1;
   $client_block_position = 1;
   $select_client_listing="select ccl.*, cc.* from client_carrousel_listing ccl, client_carrousel cc where ccl.id=cc.c_id and ccl.status=0 order by cc.record_listing_id";
   $select_client_listing_rs=$db->query($select_client_listing);
   $client_listing_total_records=mysql_num_rows($select_client_listing_rs);
   while($select_client_listing_recordset=mysql_fetch_array($select_client_listing_rs)){
     
    $path=$select_client_listing_recordset['name'];
    $imagename=explode('/',$path);
    $imagename1=explode('.',$imagename[1]);
    if($path==$imagename[0] and $imagename[1]!='undefined') {
        $row_type="url";
     }else{
        $row_type="";
     }
     if($row_type=="url"){
       $post_url_item=true;
       $distance_from_url=0;
       $client_carrousel_data[$select_client_listing_recordset['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>$select_client_listing_recordset['duration'],'type'=>$row_type,'name'=>$select_client_listing_recordset['name'],'position'=>$position,'status'=>$select_client_listing_recordset['status'],'ipaddress'=>$select_client_listing_recordset['ipaddress'],'multiscreen'=>$select_client_listing_recordset['multiscreen'],'record_listing_id'=>$select_client_listing_recordset['record_listing_id']);
     }else if($imagename1[1]=="jpg" or $imagename1[1]=="gif" or $imagename1[1]=="png") {
        ($post_url_item==true)?($distance_from_url=$distance_from_url+1):"";
        $client_carrousel_data[$select_client_listing_recordset['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>$select_client_listing_recordset['duration'],'type'=>'image','name'=>$select_client_listing_recordset['name'],'distance_from_url'=>$distance_from_url,'position'=>$position,'status'=>$select_client_listing_recordset['status'],'ipaddress'=>$select_client_listing_recordset['ipaddress'],'multiscreen'=>$select_client_listing_recordset['multiscreen'],'record_listing_id'=>$select_client_listing_recordset['record_listing_id']);	
      }else{
        ($post_url_item==true)?($distance_from_url=$distance_from_url+1):"";
        $client_carrousel_data[$select_client_listing_recordset['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>0,'type'=>'video','name'=>$select_client_listing_recordset['name'],'distance_from_url'=>$distance_from_url,'position'=>$position,'status'=>$select_client_listing_recordset['status'],'ipaddress'=>$select_client_listing_recordset['ipaddress'],'multiscreen'=>$select_client_listing_recordset['multiscreen'],'record_listing_id'=>$select_client_listing_recordset['record_listing_id']);	
      }
      
      if($client_carrousel_data[$select_client_listing_recordset['id']]['previous_id']!=0){
       $client_carrousel_data[$client_carrousel_data[$select_client_listing_recordset['id']]['previous_id']]['next_id']=$select_client_listing_recordset['id'];
      }
      
      $previous_id=$select_client_listing_recordset['id'];
      $position++;
   }
   $select_client_content_block_listing="select ccl.*, cc.* from client_content_block_listing ccl, client_content_block cc where ccl.id=cc.c_id and ccl.status=0 order by cc.record_listing_id";
   $select_client_content_block_listing_rs=$db->query($select_client_content_block_listing);
   
   $record_listing_id=$client_listing_total_records + 1;
   while($select_client_content_block_listing_recordset=mysql_fetch_array($select_client_content_block_listing_rs)){
    //$client_content_block_data[]=$select_client_content_block_listing_recordset[name];
    $path=$select_client_content_block_listing_recordset['name'];
    $imagename=explode('/',$path);
    $imagename1=explode('.',$imagename[1]);
    if($path==$imagename[0] and $imagename[1]!='undefined') {
        $row_type="url";
     }else{
        $row_type="";
     }
     if($row_type=="url"){
       $post_url_item=true;
       $distance_from_url=0;
       
       $client_content_block_data[$select_client_content_block_listing_recordset['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>$select_client_content_block_listing_recordset['duration'],'type'=>$row_type,'name'=>$select_client_content_block_listing_recordset['name'],'position'=>$position,'status'=>$select_client_content_block_listing_recordset['status'],'ipaddress'=>$select_client_content_block_listing_recordset['ipaddress'],'multiscreen'=>$select_client_content_block_listing_recordset['multiscreen'],'record_listing_id'=>$record_listing_id);
     }else if($imagename1[1]=="jpg" or $imagename1[1]=="gif" or $imagename1[1]=="png") {
        ($post_url_item==true)?($distance_from_url=$distance_from_url+1):"";
	
        $client_content_block_data[$select_client_content_block_listing_recordset['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>$select_client_content_block_listing_recordset['duration'],'type'=>'image','name'=>$select_client_content_block_listing_recordset['name'],'distance_from_url'=>$distance_from_url,'position'=>$position,'status'=>$select_client_content_block_listing_recordset['status'],'ipaddress'=>$select_client_content_block_listing_recordset['ipaddress'],'multiscreen'=>$select_client_content_block_listing_recordset['multiscreen'],'record_listing_id'=>$record_listing_id);	
      }else{
        ($post_url_item==true)?($distance_from_url=$distance_from_url+1):"";
        $client_content_block_data[$select_client_content_block_listing_recordset['id']]=array('previous_id'=>$previous_id,'next_id'=>0,'duration'=>0,'type'=>'video','name'=>$select_client_content_block_listing_recordset['name'],'distance_from_url'=>$distance_from_url,'position'=>$position,'status'=>$select_client_content_block_listing_recordset['status'],'ipaddress'=>$select_client_content_block_listing_recordset['ipaddress'],'multiscreen'=>$select_client_content_block_listing_recordset['multiscreen'],'record_listing_id'=>$record_listing_id);	
      }
      
      if($client_content_block_data[$select_client_content_block_listing_recordset['id']]['previous_id']!=0){
       $client_content_block_data[$client_content_block_data[$select_client_content_block_listing_recordset['id']]['previous_id']]['next_id']=$select_client_content_block_listing_recordset['id'];
      }
      
      $previous_id=$select_client_content_block_listing_recordset['id'];
      $position++;
    $record_listing_id++;
    
   }
   
   $count_first_array=count($client_carrousel_data);
   $count_second_array=count($client_content_block_data);
   $a=1;
   foreach($client_content_block_data as $key=>$val){      
      if($a==1){
	$next_array_start_id = $key;
	 break;
      }
      $a++;
   }
   
   $b=1;
   foreach($client_carrousel_data as $key=>$val){
      if($b==$count_first_array){
	 $client_carrousel_data[$key]['next_id']= $next_array_start_id;
	 break;
      }
      $b++;
   }
   
   $carrousel_item_details=$client_carrousel_data+$client_content_block_data;
   /*echo "<pre>"; print_r($carrousel_item_details); echo "</pre>";
   exit;*/
  $combined_select_query = "select * from combined_carrousel_details";
  $combined_rs=$db->query($combined_select_query);
  $combined_count=mysql_num_rows($combined_rs);
  if($combined_count > 0){
   $combined_delete_query = "delete from combined_carrousel_details";
   $db->query($combined_delete_query);
  }
 
   foreach($carrousel_item_details as $key=>$val){      
      $combined_insert_query="insert into combined_carrousel_details set ";
      $combined_insert_query .="id = '".$key."',";
      $combined_insert_query .="previous_id = '".$val['previous_id']."',";
      $combined_insert_query .="next_id = '".$val['next_id']."',";
      $combined_insert_query .="duration = '".$val['duration']."',";
      $combined_insert_query .="type = '".$val['type']."',";
      $combined_insert_query .="name = '".$val['name']."',";
      $combined_insert_query .="distance_from_url = '".$val['distance_from_url']."',";
      $combined_insert_query .="status = '".$val['status']."',";
      $combined_insert_query .="ipaddress = '".$val['ipaddress']."',";
      $combined_insert_query .="multiscreen = '".$val['multiscreen']."',";
      $combined_insert_query .="record_listing_id = '".$val['record_listing_id']."',";      
      $combined_insert_query .="position = '".$val['position']."'";      
      $db->query($combined_insert_query);      
   }

   return $carrousel_item_details;
  }
?>

