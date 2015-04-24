<?php
error_reporting(E_ALL);
//header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
 
$ffmpeg_path = $db->ffmpeg_path;
 
$main_query="";
 
  $fade_in_time = $db->fade_in_time;
   $fade_out_time = $db->fade_out_time;
   $total_fade_time = $db->total_fade_time;
   
   $theatre_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
   $conn2=$db->query($theatre_query);
    while($result2=mysql_fetch_array($conn2)) {
     $eppc_status=$result2['eppc_status'];
 }
	
 
   
   $carr_list_query_a="select * from carrousel_listing where status=0";
   $carr_list_query_b="select * from client_content_block_listing where status=0";

   $carr_list_query_rs=$db->query($carr_list_query_a);
   $num_rows_a = mysql_num_rows($carr_list_query_rs);
   if($num_rows_a > 0){
    $carr_list_rs = $db->query($carr_list_query_a);
   }else{
    $carr_list_rs = $db->query($carr_list_query_b);
   }
   
   
  
  while($record_data=mysql_fetch_array($carr_list_rs, MYSQL_ASSOC)){
  $carr_id=$record_data['id'];
  
  $script_content="";
  
  $ip_address_query="select ipaddress from carrousel_listing where id='$carr_id'";
  $ip_address_query_rs=$db->query($ip_address_query);
  $result_select=mysql_fetch_array($ip_address_query_rs);
  $ipaddress=$result_select['ipaddress'];
  echo $ipaddress;
 
  
  if($num_rows_a > 0 ){
	
  $select_query="select name from carrousel_listing where id='$carr_id'";
  }else{
   $select_query="select name from client_content_block_listing where id='$carr_id'";
  }
  $sel_res=$db->query($select_query);
  $result_select=mysql_fetch_array($sel_res);
  $carrouel_name=$result_select['name'];
  if($is_client==1){
	  if($num_rows_a > 0 ){
  $carrousel_item_details=get_carrousel_item_details($carr_id);// getting basic details of each carrousel item(type,previousitem, next item)
}
else {

	$carrousel_item_details = combined_carrousel_details();
}
  // $start_script="start_carrousel";
   
     if(isset($skip_mediasync) && $skip_mediasync!=1){
	 include('mediasync.php');
	 }
   elseif ($start_script=="schedule") {
     include('mediasync.php'); }
      
   }else{
     
  $carrousel_item_details=get_carrousel_item_details($carr_id);// getting basic details of each carrousel item(type,previousitem, next item)
  
  }
  
  
  
$is_url=0;
foreach($carrousel_item_details as $key=>$val){
   if($val['type']=='url'){
      $is_url=1;
      break;
   }
}

  

  if($ipaddress=="") {
	if($is_url==1){
        $script_content.="tell application \"Finder\" to open \"Macintosh HD:Applications:XAMPP:xamppfiles:htdocs:narrowcasting:clean_qlab_html_qlab2.app\" \n
                           delay 4 \n
                          repeat \n
				 if application \"clean_qlab_html_qlab2\" is not running then exit repeat \n
				 delay 1 \n
			 end repeat \n";
		 }
		 else {
	         $script_content.="tell application \"Finder\" to open \"Macintosh HD:Applications:XAMPP:xamppfiles:htdocs:narrowcasting:clean_qlab_qlab2.app\" \n
	                            delay 4 \n
	                           repeat \n
	 				 if application \"clean_qlab_qlab2\" is not running then exit repeat \n
	 				 delay 1 \n
	 			 end repeat \n";
		 }
        
        $script_content.="tell application id \"com.figure53.qlab.2\" \n
                         tell front workspace\n";
        
        $script_content.="make type \"Cue List\" \n";// for webpages applescripts
        
        $script_content.="set q name of last cue list to \"Startcues\" \n
			  make type \"Cue List\" \n
			  set q name of last cue list to \"Media\" \n
                          set mediaCueList to \"Media\" \n
                          set startCueList to \"Startcues\" \n";
        $b=1;
   if($num_rows_a > 0 ){
 $script_query="select * from carrousel where c_id='$carr_id' order by record_listing_id asc";
	}else{
	 	$script_query = "select * from combined_carrousel_details order by record_listing_id asc";
	}
        $res1=$db->query($script_query);
        $count_carr=mysql_num_rows($res1);
        
        if($count_carr==1) {
          $result1=mysql_fetch_array($res1);
          $carr_name=$result1['name'];
          $name1=explode(".",$result1['name']);
          $name2=explode("/",$result1['name']);
          
          $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                            make type \"Video\" \n
                            set newCue to last item of (selected as list) \n
                            set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\" \n
                            set file target of newCue to newFileTarget \n
                            set q name of newCue to \"".$name2[1]."\" \n ";
                            
         if($name1[1]!="jpg" && $name1[1]!="png" && $name1[1]!="gif") {
           $script_content.="set infinite loop of newCue to true \n";
          }            
           $script_content.="start cue \"1\" \n
	                    end tell \n
	                   end tell ";
        } else {
        
        $b_bottom=4;
        
        while($result1=mysql_fetch_array($res1)) {
        $carr_item_id=$result1['id'];
        $carr_name=$result1['name'];
        $name1=explode(".",$result1['name']);
        $name2=explode("/",$result1['name']);
        $next_item_id=$carrousel_item_details[$carr_item_id]['next_id'];
        if(isset($carrousel_item_details[$next_item_id]['distance_from_url']) && $carrousel_item_details[$next_item_id]['distance_from_url']==2){
          $cue_increment=6;
        }else{
          $cue_increment=4;
        }
        
        if($carrousel_item_details[$carr_item_id]['type']=='url' && $carrousel_item_details[$carr_item_id]['position']==2){
          $b_bottom=6;
        }
       
        
         if($name1[1]=="jpg" || $name1[1]=="png" || $name1[1]=="gif") {
          $script_duration=$result1['duration'];
          $newduration1=$script_duration-$fade_out_time;
          $newduration2=$script_duration+$fade_out_time;
          $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                            make type \"Video\"\n
                            set newCue to last item of (selected as list)\n
                            set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\"\n
                            set file target of newCue to newFileTarget\n
                            set q name of newCue to \"".$name2[1]."\" \n
                            set continue mode of newCue to auto_continue \n
                            set opacity of newCue to \"0\"
                          
                            make type \"Animation\" \n
			    set newCue to last item of (selected as list) \n
			    set cue target of newCue to Cue \"$b\"\n
			    set q name of newCue to \"".$name2[1]."\" \n
                            set post wait of newCue to \"$newduration1\" \n
			    set duration of newCue to \"$fade_in_time\" \n
                            set do opacity of newCue to true \n
                            set opacity of newCue to \"100\" \n";
                          
                            // new code for webpages apple script
                             if($carrousel_item_details[$carr_item_id]['next_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$script_duration-$total_fade_time;
                              $url_name=$carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['name'];
                             // echo '<br> '.$carr_item_id.' pre:url_name='.$url_name.'<br>';
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }else if($carrousel_item_details[$carr_item_id]['previous_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['previous_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$script_duration-$total_fade_time;
                              $url_name='11000';
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }else{
                              $script_content.="set continue mode of newCue to auto_continue \n";// from old applescript
                             }
                            
            $script_content.="make type \"Animation\"
			    set newCue to last item of (selected as list)
			    set cue target of newCue to Cue \"$b\"\n
			    set q name of newCue to \"".$name2[1]."\" \n
			    set duration of newCue to \"$fade_out_time\" \n
			    set do opacity of newCue to true \n
			    set opacity of newCue to \"0\" \n
			    set stop target when done of newCue to \"yes\" \n
                            
                            set current cue list to first cue list whose q name is startCueList \n
			    make type \"Start\" \n
			    set newStartCue to last item of (selected as list) \n
			    set cue target of newStartCue to first cue list whose q name is mediaCueList \n
                            set post wait of newStartCue to \"$script_duration\" \n
			    set continue mode of newStartCue to auto_continue \n";
                            
          
             $b=$b+$cue_increment;
             
          } else  if($name1[1]=="mp4" || $name1[1]=="mov") {
            ob_start();
            passthru("$ffmpeg_path -i /Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name 2>&1");
            $duration = ob_get_contents();
            ob_end_clean();

            preg_match('/Duration: (.*?),/', $duration, $matches);
            $duration = $matches[1];
            $duration_array = explode(':', $duration);
            $newduration=$duration_array[0]*60+$duration_array[1];
	    $newduration1=$newduration-$fade_out_time;
            $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                             make type \"Video\"\n
                             set newCue to last item of (selected as list)\n
                             set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\" \n
                             set file target of newCue to newFileTarget\n
                             set q name of newCue to \"".$name2[1]."\" \n
                             set opacity of newCue to \"0\" \n
                             set continue mode of newCue to auto_continue \n 
                             
                             make type \"Animation\" \n
			    			 set newCue to last item of (selected as list) \n
			    			 set cue target of newCue to Cue \"$b\"\n
			    			 set q name of newCue to \"".$name2[1]."\" \n
                             set post wait of newCue to \"$newduration1\" \n
			    			 set duration of newCue to \"$fade_in_time\" \n
                             set do opacity of newCue to true \n
                             set opacity of newCue to \"100\" \n";
			   				                          
                            // new code for webpages apple script
                             if($carrousel_item_details[$carr_item_id]['next_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$newduration-$total_fade_time;
                              $url_name=$carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['name'];
                            
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }else if($carrousel_item_details[$carr_item_id]['previous_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['previous_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$newduration-$total_fade_time;
                              $url_name='11000';
                            
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }else{
                              $script_content.="set continue mode of newCue to auto_continue \n";// from old applescript
                             }
                             
            $script_content.="make type \"Animation\" \n
			     set newCue to last item of (selected as list) \n
			     set cue target of newCue to cue \"$b\" \n
                             set q name of newCue to \"".$name2[1]."\" \n
			     set duration of newCue to \"$fade_out_time\" \n
			     set do opacity of newCue to true \n
			     set opacity of newCue to \"0\" \n
			      set stop target when done of newCue to \"yes\" \n
                             
                             set current cue list to first cue list whose q name is startCueList \n
			     make type \"Start\" \n
			     set newStartCue to last item of (selected as list) \n
			     set cue target of newStartCue to first cue list whose q name is mediaCueList \n
                             set post wait of newStartCue to \"$newduration\" \n
			     set continue mode of newStartCue to auto_continue \n";			   

                             
                             
              $b=$b+$cue_increment;
            
          }else{//  new code for webpages apple script (URL'S ITEM)
         
                $script_duration_url_i=$result1['duration'];
                $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                make type \"Wait\" \n
			    set mySel to selected \n
			    set newCue to last item of mySel
			    set duration of newCue to \"$script_duration_url_i\" \n
			    set current cue list to first cue list whose q name is startCueList \n
			    make type \"Start\" \n
			    set newStartCue to last item of (selected as list) \n
			    set cue target of newStartCue to first cue list whose q name is mediaCueList \n
                            set post wait of newStartCue to \"$script_duration_url_i\" \n
			    set continue mode of newStartCue to auto_continue \n";
              //$clue_increment=($carrousel_item_details[$carr_item_id]['url_no']+1)*2;
              $b=$b+$cue_increment;   
            
          }
        
      }
        //echo "b_bottom=".$b_bottom;
        $script_content.="make type \"RESET\" \n
                          set newCue to last item of (selected as list) \n
                          set cue target of newCue to first cue list whose q name is mediaCueList \n
                          set continue mode of newCue to auto_continue \n";
        $script_content.="make type \"GOTO\" \n
                          set newStartCue to last item of (selected as list) \n
                          set cue target of newStartCue to cue \"$b_bottom\" \n
                          set continue mode of newStartCue to auto_continue \n
                          set playback position of first cue list whose q name is mediaCueList to cue \"1\" \n
                          set playback position of first cue list whose q name is startCueList to cue \"$b_bottom\" \n
                          start cue \"$b_bottom\" \n ";
         
        $script_content.="end tell\n
                            end tell";
      }

      $carrouel_name = stripslashes($carrouel_name);
      $carrouel_name=str_replace(" ","_",$carrouel_name);
      $carrouel_name=str_replace("'","__",$carrouel_name);
      $ourFileName = "$carrouel_name.scpt";
      
      $ourFileHandle = fopen("script/".$ourFileName, 'w') or die("can't open file");
      fwrite($ourFileHandle, $script_content);
      fclose($ourFileHandle);
      
      $ourFileHandle1 = fopen('script_reboot.scpt', 'w') or die("can't open file");
      fwrite($ourFileHandle1, $script_content);
      fclose($ourFileHandle1);
      /*line added by arti*/
      /*the following line inserts db logs*/
      
     // $db->writeDbLog($carr_id);
     // $db->writeLog($ourFileName);
      exec("osascript script/$ourFileName");

  } 
  else {
   
       $b=1;
	   if($num_rows_a > 0 ){
	 $script_query="select * from carrousel where c_id='$carr_id' order by record_listing_id asc";
		}else{
		 	$script_query = "select * from combined_carrousel_details order by record_listing_id asc";
		}
        $res1=$db->query($script_query);
        $count_carr=mysql_num_rows($res1);
        if($count_carr==1) {
          $result1=mysql_fetch_array($res1);
          $carr_name=$result1['name'];
          $name1=explode(".",$result1['name']);
          $name2=explode("/",$result1['name']);
          
          $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                            make type \"Video\" \n
                            set newCue to last item of (selected as list) \n
                            set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\" \n
                            set file target of newCue to newFileTarget \n
                            set q name of newCue to \"".$name2[1]."\" \n ";
            
           if($name1[1]!="jpg" && $name1[1]!="png" && $name1[1]!="gif") {
            $script_content.="set infinite loop of newCue to true \n";
           }                    
           $script_content.="start cue \"1\" \n
	                    end tell \n
	                   end tell ";
        } else {
        
        $b_bottom=4;
        while($result1=mysql_fetch_array($res1)) {
        $carr_item_id=$result1['id'];
        $carr_name=$result1['name'];
        $name1=explode(".",$result1['name']);
        $name2=explode("/",$result1['name']);
        
        $next_item_id=$carrousel_item_details[$carr_item_id]['next_id'];
        if(isset($carrousel_item_details[$next_item_id]['distance_from_url']) && $carrousel_item_details[$next_item_id]['distance_from_url']==2){
          $cue_increment=6;
        }else{
          $cue_increment=4;
        }        
        
         if($carrousel_item_details[$carr_item_id]['type']=='url' && $carrousel_item_details[$carr_item_id]['position']==2){
          $b_bottom=6;
         }
        
        
        
         if($name1[1]=="jpg" || $name1[1]=="png" || $name1[1]=="gif") {
          $script_duration=$result1['duration'];
          $newduration1=$script_duration-$fade_out_time;
          $newduration2=$script_duration+$fade_out_time;
          $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                            make type \"Video\"\n
                            set newCue to last item of (selected as list)\n
                            set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\"\n
                            set file target of newCue to newFileTarget\n
                            set q name of newCue to \"".$name2[1]."\"\n
                            set continue mode of newCue to auto_continue \n
                            set opacity of newCue to \"0\"
                            
                            make type \"Animation\" \n
			    set newCue to last item of (selected as list) \n
			    set cue target of newCue to Cue \"$b\"\n
			    set q name of newCue to \"".$name2[1]."\" \n
                            set post wait of newCue to \"$newduration1\" \n
			    set duration of newCue to \"$fade_in_time\" \n
                            set do opacity of newCue to true \n
                            set opacity of newCue to \"100\" \n";
                            
                            // new code for webpages apple script
                             if($carrousel_item_details[$carr_item_id]['next_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$script_duration-$total_fade_time;
                              $url_name=$carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['name'];
                            
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }else if($carrousel_item_details[$carr_item_id]['previous_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['previous_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$script_duration-$total_fade_time;
                              $url_name='11000';
                            
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }else{
                              $script_content.="set continue mode of newCue to auto_continue \n";// from old applescript
                             }
                            
            $script_content.="make type \"Animation\"
			    set newCue to last item of (selected as list)
			    set cue target of newCue to Cue \"$b\"\n
			    set q name of newCue to \"".$name2[1]."\" \n
			    set duration of newCue to \"$fade_out_time\" \n
			    set do opacity of newCue to true \n
			    set opacity of newCue to \"0\" \n
			    set stop target when done of newCue to \"yes\" \n
                            
                            set current cue list to first cue list whose q name is startCueList \n
			    make type \"Start\" \n
			    set newStartCue to last item of (selected as list) \n
			    set cue target of newStartCue to first cue list whose q name is mediaCueList \n
                            set post wait of newStartCue to \"$script_duration\" \n
			    set continue mode of newStartCue to auto_continue \n";
                            
                          
             $b=$b+$cue_increment;
             
          } else if($name1[1]=="mp4" || $name1[1]=="mov") {
            ob_start();
            passthru("$ffmpeg_path -i /Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name 2>&1");
            $duration = ob_get_contents();
            ob_end_clean();

            preg_match('/Duration: (.*?),/', $duration, $matches);
            $duration = $matches[1];
            $duration_array = split(':', $duration);
            $newduration=$duration_array[1]*60+$duration_array[2];
	    $newduration1=$newduration-$fade_out_time;
            $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                             make type \"Video\"\n
                             set newCue to last item of (selected as list)\n
                             set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\" \n
                             set file target of newCue to newFileTarget\n
                             set q name of newCue to \"".$name2[1]."\" \n
                             set opacity of newCue to \"0\" \n
                             set continue mode of newCue to auto_continue \n
                             
                             make type \"Animation\" \n
                             set newCue to last item of (selected as list) \n
                             set cue target of newCue to Cue \"$b\"\n
                             set q name of newCue to \"".$name2[1]."\" \n
                             set duration of newCue to \"$fade_in_time\" \n
                             set do opacity of newCue to true \n
                             set opacity of newCue to \"100\" \n
                             set post wait of newCue to \"$newduration1\" \n";
                             
                              // new code for webpages apple script
                             if($carrousel_item_details[$carr_item_id]['next_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$newduration-$total_fade_time;
                              $url_name=$carrousel_item_details[$carrousel_item_details[$carr_item_id]['next_id']]['name'];
                            
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }else if($carrousel_item_details[$carr_item_id]['previous_id']!=0 && $carrousel_item_details[$carrousel_item_details[$carr_item_id]['previous_id']]['type']=='url'){
                              $script_content.="set continue mode of newCue to auto_follow \n";// for webpages applescripts
                              
                              $script_duration_url=$newduration-$total_fade_time;
                              $url_name='11000';
                            
                              $script_content.="make type \"Start\" \n
                              set newCue to last item of (selected as list) \n
                              set cue target of newCue to cue \"".$url_name."\" \n
                              set continue mode of newCue to auto_follow \n
                              make type \"Wait\" \n
                              set mySel to selected \n
                              set newCue to last item of mySel \n
                              set duration of newCue to \"".$script_duration_url."\" \n
                              set continue mode of newCue to auto_continue \n";
                             }
                             else{
                              $script_content.="set continue mode of newCue to auto_continue \n";// from old applescript
                             }
                            
             $script_content.="
                             make type \"Animation\" \n
			     set newCue to last item of (selected as list) \n
			     set cue target of newCue to cue \"$b\" \n
                             set q name of newCue to \"".$name2[1]."\" \n
			     set duration of newCue to \"$fade_out_time\" \n
			     set do opacity of newCue to true \n
			     set opacity of newCue to \"0\" \n
			      set stop target when done of newCue to \"yes\" \n
                             
                             set current cue list to first cue list whose q name is startCueList \n
			     make type \"Start\" \n
			     set newStartCue to last item of (selected as list) \n
			     set cue target of newStartCue to first cue list whose q name is mediaCueList \n
			     set post wait of newStartCue to \"$newduration\" \n
                             set continue mode of newStartCue to auto_continue \n";
                            
              $b=$b+$cue_increment;
            
          }else{//  new code for webpages apple script (URL'S ITEM)
	        $script_duration_url_i=$result1['duration'];
                $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                make type \"Wait\" \n
			    set mySel to selected \n
			    set newCue to last item of mySel
			    set duration of newCue to \"$script_duration_url_i\" \n
			    set current cue list to first cue list whose q name is startCueList \n
			    make type \"Start\" \n
			    set newStartCue to last item of (selected as list) \n
			    set cue target of newStartCue to first cue list whose q name is mediaCueList \n
                            set post wait of newStartCue to \"$script_duration_url_i\" \n
			    set continue mode of newStartCue to auto_continue \n";
            // $clue_increment=($carrousel_item_details[$carr_item_id]['url_no']+1)*2;
             $b=$b+$cue_increment;  
           
          }
        }
      }
      
    if($ipaddress!="") {
		//echo "In full";
		//exit;
      $c=1;
      $script_content1="";
      if($num_rows_a > 0 ){
    $select_query = "select a.*, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id=$carr_id and b.id IN($ipaddress) group by b.id";
   	}else{
   	 	 $select_query="select a.*, b.* from combined_carrousel_details as a, theatre_eppc as b  where theatre.status=1";
	
	// exit;
      }
      $connc=$db->query($select_query);
      while($selectresult=mysql_fetch_array($connc)) {
		//  echo "<pre>"; print_r($selectresult); echo "</pre>";
       $carr_name= stripslashes($selectresult['name']);
       $carr_name=str_replace(" ","_",$carr_name);
       $carr_name=str_replace("'","__",$carr_name);
       $carr_name=$carr_name.".scpt";
       $newipaddress=$selectresult['theatre_ip'];
       $username=$selectresult['username'];
       $password=$selectresult['password'];
       $theatre_label=$selectresult['theatre_label'];
       
	   if($is_url==1){
       $script_content1.="tell application \"Finder\" of machine \"eppc://$username:$password@$newipaddress\" to open \"Macintosh HD:Applications:XAMPP:xamppfiles:htdocs:narrowcasting:clean_qlab_html_qlab2.app\" \n
              delay 4 \n
             repeat \n
 if application \"clean_qlab_html_qlab2\" of machine \"eppc://$username:$password@$newipaddress\" is not running then exit repeat \n
 delay 1 \n
end repeat \n";

   } else {
   	 $script_content1.="tell application \"Finder\" of machine \"eppc://$username:$password@$newipaddress\" to open \"Macintosh HD:Applications:XAMPP:xamppfiles:htdocs:narrowcasting:clean_qlab_qlab2.app\" \n
              delay 4 \n
             repeat \n
 if application \"clean_qlab_qlab2\" of machine \"eppc://$username:$password@$newipaddress\" is not running then exit repeat \n
 delay 1 \n
end repeat \n";
   }
       
       $script_content1.="tell application \"Qlab\" of machine \"eppc://$username:$password@$newipaddress\" \n
                          tell front workspace \n" ;
                          
     $script_content1.="make type \"Cue List\" \n";// for webpages applescripts
       $script_content1.="set q name of last cue list to \"Startcues\" \n
			  make type \"Cue List\" \n
			  set q name of last cue list to \"Media\" \n
			  set mediaCueList to \"Media\" \n
                          set startCueList to \"Startcues\" \n ";
                       
       $script_content1.=$script_content;
       if($count_carr!=1) {
        $script_content1.="make type \"RESET\" \n
			  set newCue to last item of (selected as list) \n
			  set cue target of newCue to first cue list whose q name is mediaCueList \n
			  set continue mode of newCue to auto_continue \n";
        $script_content1.="make type \"GOTO\" \n
			 set newStartCue to last item of (selected as list) \n
			 set cue target of newStartCue to cue \"$b_bottom\" \n
			 set continue mode of newStartCue to auto_continue \n
			 set playback position of first cue list whose q name is mediaCueList to cue \"1\" \n
			 set playback position of first cue list whose q name is startCueList to cue \"$b_bottom\" \n
			 start cue \"$b_bottom\" \n ";
       
        $script_content1.="end tell\n
                          end tell";
       }

       $carrouel_name = stripslashes($carrouel_name);
       $carrouel_name=str_replace(" ","_",$carrouel_name);
       $carrouel_name=str_replace("'","__",$carrouel_name);
       $ourFileName = "$carrouel_name$c.scpt";
       
       $ourFileHandle = fopen("script/".$ourFileName, 'w') or die("can't open file");
       fwrite($ourFileHandle, $script_content1);
       fclose($ourFileHandle);
       
      $ourFileHandle1 = fopen("/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/script_reboot_eppc/$theatre_label/script_reboot.scpt", 'w') or die("can't open file");
       fwrite($ourFileHandle1, $script_content1);
       fclose($ourFileHandle1);
	  
       //$db->writeLog($ourFileName);
      // $db->writeDbLog($carr_id);
	 
       exec("osascript script/$ourFileName");
       $c++;
       $script_content1="";
     }
        
    } else {
	    if($num_rows_a > 0 ){
		 $select_query="select a.*, b.* from carrousel_listing as a, theatre_eppc as b where a.id=$carr_id and a.ipaddress=b.id";
	 	}else{
	 	 	 $select_query="select a.*, b.* from combined_carrousel_details as a, theatre_eppc as b where  a.ipaddress=b.id";
	 	}
      $connc=$db->query($select_query);
      $selectresult=mysql_fetch_array($connc);
      $carr_name= stripslashes($selectresult['name']);
      $carr_name=str_replace(" ","_",$carr_name);
      $carr_name=str_replace("'","__",$carr_name);
      $carr_name=$carr_name.".scpt";
      $newipaddress=$selectresult['theatre_ip'];
      $username=$selectresult['username'];
      $password=$selectresult['password'];
      $theatre_label=$selectresult['theatre_label'];
      
        if($is_url==1){
               $script_content1.="tell application \"Finder\" of machine \"eppc://$username:$password@$newipaddress\" to open \"Macintosh HD:Applications:XAMPP:xamppfiles:htdocs:narrowcasting:clean_qlab_html_qlab2.app\" \n
			   delay 4 \n
			                 repeat \n
			     if application \"clean_qlab_html_qlab2\" of machine \"eppc://$username:$password@$newipaddress\" is not running then exit repeat \n
			     delay 1 \n
			    end repeat \n";
        } else{
         $script_content1.="tell application \"Finder\" of machine \"eppc://$username:$password@$newipaddress\" to open \"Macintosh HD:Applications:XAMPP:xamppfiles:htdocs:narrowcasting:clean_qlab_qlab2.app\" \n
	   delay 4 \n
	                 repeat \n
	     if application \"clean_qlab_qlab2\" of machine \"eppc://$username:$password@$newipaddress\" is not running then exit repeat \n
	     delay 1 \n
	    end repeat \n";
         }
           $script_content1.="delay 4 \n
              repeat \n
  if application \"clean_qlab_html_qlab2\" of machine \"eppc://$username:$password@$newipaddress\" is not running then exit repeat \n
  delay 1 \n
 end repeat \n";
      $script_content1.="tell application \"Qlab\" of machine \"eppc://$username:$password@$newipaddress\" \n
                        tell front workspace \n ";
                        $script_content1.="make type \"Cue List\" \n";// for webpages applescripts
      $script_content1.="set q name of last cue list to \"Startcues\" \n
			 make type \"Cue List\" \n
			 set q name of last cue list to \"Media\" \n
			 set mediaCueList to \"Media\" \n
                         set startCueList to \"Startcues\" \n ";
                                  
      $script_content1.=$script_content;
      if($count_carr!=1) {
      $script_content1.="make type \"RESET\" \n
			 set newCue to last item of (selected as list) \n
			 set cue target of newCue to first cue list whose q name is mediaCueList \n
			 set continue mode of newCue to auto_continue \n";
      $script_content1.="make type \"GOTO\" \n
			 set newStartCue to last item of (selected as list) \n
			 set cue target of newStartCue to cue \"$b_bottom\" \n
			 set continue mode of newStartCue to auto_continue \n
			 set playback position of first cue list whose q name is mediaCueList to cue \"1\" \n
			 set playback position of first cue list whose q name is startCueList to cue \"$b_bottom\" \n
			 start cue \"$b_bottom\" \n ";
       
      $script_content1.="end tell\n
                          
                         end tell";
      }
      
      $carrouel_name = stripslashes($carrouel_name);
      $carrouel_name=str_replace(" ","_",$carrouel_name);
      $carrouel_name=str_replace("'","__",$carrouel_name);
      $ourFileName = "$carrouel_name.scpt";
      
      $ourFileHandle = fopen("script/".$ourFileName, 'w') or die("can't open file");
      fwrite($ourFileHandle, $script_content1);
      fclose($ourFileHandle);
      
      $ourFileHandle1 = fopen("/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/script_reboot_eppc/$theatre_label/script_reboot.scpt", 'w') or die("can't open file");
      fwrite($ourFileHandle1, $script_content1);
      fclose($ourFileHandle1);
       
      //$db->writeLog($ourFileName);
    //  $db->writeDbLog($carr_id);
	 // echo $ourFileName;
	  //exit;
      exec("osascript script/$ourFileName");
    }
    $c="";
    $script_content1="";
  }
  }
?>
