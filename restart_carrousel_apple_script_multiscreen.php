 <?php
 
 $carr_list_query="select * from carrousel_listing where status=0";
  $carr_list_rs = $db->query($carr_list_query);
  
  while($record_data=mysql_fetch_array($carr_list_rs, MYSQL_ASSOC)){
  $carr_id=$record_data['id'];
  
  $script_content="";
  
  $multiscreen_query="select multiscreen from carrousel_listing where id='$carr_id'";
  $multiscreen_query_rs=$db->query($multiscreen_query);
  $result_select=mysql_fetch_array($multiscreen_query_rs);
  $multiscreen=$result_select['multiscreen'];

  $ffmpeg_path = $db->ffmpeg_path;

 
 
 
 //start normal code from start_carrousel_apple_script_multiscreen
   //-------- get carrousel name -------------------------------------------------
  $select_query="select name from carrousel_listing where id='$carr_id'";
  $sel_res=$db->query($select_query);
  $result_select=mysql_fetch_array($sel_res);
  $script_content="";
 // print_r($result_select);echo "HIIIIIIII";
  $carrouel_name=$result_select['name'];
 // if($ipaddress=="") {
     if($multiscreen!="") {
       $script_query="select * from carrousel where c_id='$carr_id' order by record_listing_id asc";
       $res1=$db->query($script_query);
       $count_carr=mysql_num_rows($res1);
       if($count_carr==1) {
        $result1=mysql_fetch_array($res1);
        $carr_name=$result1['name'];
          $name1=explode(".",$result1['name']);
          $name2=explode("/",$result1['name']);
          
          if($name1[1]=="jpg" || $name1[1]=="png" || $name1[1]=="gif") {
          $script_duration=$result1['duration'];
          $newduration1=$script_duration-00.30;
          $newduration2=$script_duration+00.30;
          $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                            make type \"Video\"\n
                            set newCue to last item of (selected as list)\n
                            set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\"\n
                            set file target of newCue to newFileTarget\n
                            set q name of newCue to \"".$name2[1]."\" \n
                            set continue mode of newCue to auto_continue \n
                            
                            make type \"Pause\" \n
			    set newCue to last item of (selected as list) \n
			    set cue target of newCue to Cue \"1\"\n
			    set q name of newCue to \"".$name2[1]."\" \n ";
             $b=$b+4;
             
          } else {
            ob_start();
            passthru("$ffmpeg_path -i /Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name 2>&1");
            $duration = ob_get_contents();
            ob_end_clean();

            preg_match('/Duration: (.*?),/', $duration, $matches);
            $duration = $matches[1];
            $duration_array = split(':', $duration);
            $newduration=$duration_array[1]*60+$duration_array[2];
	    $newduration1=$newduration-00.30;
            $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                             make type \"Video\"\n
                             set newCue to last item of (selected as list)\n
                             set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\" \n
                             set file target of newCue to newFileTarget\n
                             set q name of newCue to \"".$name2[1]."\" \n
                             set continue mode of newCue to auto_continue \n
                            
                             make type \"Pause\" \n
			     set newCue to last item of (selected as list) \n
			     set cue target of newCue to Cue \"1\"\n
			     set q name of newCue to \"".$name2[1]."\" \n ";
              $b=$b+4;
            
          }
          
       } else {
        
        //--------- get last cue value for first cue ---------------------------
        $lastcue_query="select * from carrousel where c_id='$carr_id' order by record_listing_id desc limit 0,1";
        $last_res=$db->query($lastcue_query);
        $last_result=mysql_fetch_array($last_res);
        $lastcarr_name=$last_result['name'];
        $last_name=explode("/",$lastcarr_name['name']);
        $lastext=explode(".",$last_name[1]);
        if($lastext[1]=="jpg" || $lastext[1]=="png" || $lastext[1]=="gif") {
          $last_duration=$result1['duration'];
        } else {
          ob_start();
          passthru("$ffmpeg_path -i /Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$lastcarr_name 2>&1");
          $lastduration = ob_get_contents();
          ob_end_clean();

          preg_match('/Duration: (.*?),/', $lastduration, $matches);
          $lastduration = $matches[1];
          $lastduration_array = split(':', $lastduration);
          $last_duration=$lastduration_array[1]*60+$alstduration_array[2];  
        }
        
        $x=1;
        $b=1;
        
        while($result1=mysql_fetch_array($res1)) {
          $carr_name=$result1['name'];
          $name1=explode(".",$result1['name']);
          $name2=explode("/",$result1['name']);
          
          if($x==1) {
            $stop_name=$last_name[1];
          } else {
            $stop_name=$b-4;
          }
          
          if($name1[1]=="jpg" || $name1[1]=="png" || $name1[1]=="gif") {
          $script_duration=$result1['duration'];
          $newduration1=$script_duration-00.30;
          $newduration2=$script_duration+00.30;
          if($x==$count_carr) {
            $last_duration=$result1['duration'];
          }
          if($x!=$count_carr) {
           $script_content.="set current cue list to first cue list whose q name is mediaCueList \n ";
          } else {
           $script_content.="";
          }
          $script_content.="make type \"Video\"\n
                            set newCue to last item of (selected as list)\n
                            set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\"\n
                            set file target of newCue to newFileTarget\n
                            set q name of newCue to \"".$name2[1]."\" \n
                            set continue mode of newCue to auto_continue \n
                            
                            make type \"Pause\" \n
			    set newCue to last item of (selected as list) \n
			    set cue target of newCue to Cue \"$b\"\n
			    set q name of newCue to \"".$name2[1]."\" \n
			    set continue mode of newCue to auto_continue \n ";
          if($x!=$count_carr) {                 
           $script_content.="make type \"Stop\" \n ";
           if($x==1) {
             $script_content.="set firstStopCue to last item of (selected as list) \n";
           } else {
             $script_content.="set newCue to last item of (selected as list) \n";
           }
	
           if($x==1) {
            //$script_content.="set q name of firstStopCue to \"".$name2[1]."\" \n";
           } else {
            //$script_content.="set q name of newCue to \"".$name2[1]."\" \n";
            $script_content.="set cue target of newCue to cue \"$stop_name\" \n";
           }
	     $script_content.="set current cue list to first cue list whose q name is startCueList \n
                            
			    make type \"Start\" \n
			    set newStartCue to last item of (selected as list) \n
			    set cue target of newStartCue to first cue list whose q name is mediaCueList \n
			    set post wait of newStartCue to \"$script_duration\" \n
			    set continue mode of newStartCue to auto_continue \n";
          } else {
            $script_content.="make type \"Stop\" \n
		            set newCue to last item of (selected as list) \n
		            set cue target of newCue to cue \"$stop_name\" \n
		            set q name of newCue to \"".$name2[1]."\" \n
		            set continue mode of newCue to auto_continue \n
                            set cue target of firstStopCue to cue \"$b\" \n";
                            
          }
             $b=$b+4;
             
          } else {
            ob_start();
            passthru("$ffmpeg_path -i /Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name 2>&1");
            $duration = ob_get_contents();
            ob_end_clean();

            preg_match('/Duration: (.*?),/', $duration, $matches);
            $duration = $matches[1];
            $duration_array = split(':', $duration);
            $newduration=$duration_array[1]*60+$duration_array[2];
	    $newduration1=$newduration-00.30;
            if($x==$count_carr) {
             $last_duration=$newduration;
            }
            if($x!=$count_carr) {
             $script_content.="set current cue list to first cue list whose q name is mediaCueList \n ";
            } else {
             $script_content.="";
            }
            $script_content.="make type \"Video\"\n
                              set newCue to last item of (selected as list)\n
                              set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\"\n
                              set file target of newCue to newFileTarget\n
                              set q name of newCue to \"".$name2[1]."\" \n
                              set continue mode of newCue to auto_continue \n
                            
                              make type \"Pause\" \n
			      set newCue to last item of (selected as list) \n
			      set cue target of newCue to Cue \"$b\"\n
			      set q name of newCue to \"".$name2[1]."\" \n
			      set continue mode of newCue to auto_continue \n ";
                             
            if($x!=$count_carr) {
	     $script_content.="make type \"Stop\" \n ";
                if($x==1) {
                  $script_content.="set firstStopCue to last item of (selected as list) \n";
                } else {
                  $script_content.="set newCue to last item of (selected as list) \n";
                }
		
                if($x==1) {
                 //$script_content.="set q name of firstStopCue to \"".$name2[1]."\" \n";
                } else {
                 //$script_content.="set q name of newCue to \"".$name2[1]."\" \n";
                 $script_content.="set cue target of newCue to cue \"$stop_name\" \n";
                }
                 $script_content.="set current cue list to first cue list whose q name is startCueList
                             
                              make type \"Start\" \n
			      set newStartCue to last item of (selected as list) \n
			      set cue target of newStartCue to first cue list whose q name is mediaCueList \n
			      set post wait of newStartCue to \"$newduration\" \n
			      set continue mode of newStartCue to auto_continue \n";
            } else {
             $script_content.="make type \"Stop\" \n
		            set newCue to last item of (selected as list) \n
		            set cue target of newCue to cue \"$stop_name\" \n
		            set q name of newCue to \"".$name2[1]."\" \n
		            set continue mode of newCue to auto_continue \n
                            set cue target of firstStopCue to cue \"$b\" \n";
            
            }
              $b=$b+4;
            }
            $x++;
          }
          
        }    

      $c=1;
      $script_content1="";
      $select_query="select a.name, b.* from carrousel_listing as a, theatre_multiscreen as b where a.id=$carr_id and b.id IN ($multiscreen)";
      $connc=$db->query($select_query);
      while($selectresult=mysql_fetch_array($connc)) {
       $carr_name= stripslashes($selectresult['name']);
       $carr_name=str_replace(" ","_",$carr_name);
       $carr_name=str_replace("'","__",$carr_name);
       $carr_name=$carr_name.".scpt";
       $screen_id=$selectresult['id'];
       $workspace_path=$selectresult['workspace_path'];
       $workspace_name=$selectresult['workspace_name'];
       
       //--------- write code for script reboot file--------------------------
       $reboot_content1="";
       $reboot_content2="";
       $reboot_content1.="set Reboot$screen_id to POSIX file \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/script_reboot_multi/Reboot$screen_id.scpt\" as alias \n";
       $reboot_content2.="run script Reboot$screen_id \n";
       
       $script_content1.=" tell application id \"com.figure53.qlab.2\" to activate \n
                            tell application id \"com.figure53.qlab.2\" \n"; 
       $script_content1.="open \"$workspace_path\" \n";
       $script_content1.="tell workspace \"$workspace_name\" \n";
       $script_content1.="make type \"Cue List\" \n
		           repeat until (count cue list) = 1 \n
			    delete first cue list \n
		           end repeat \n
                           
                           set q name of last cue list to \"Startcues\" \n
		           make type \"Cue List\" \n
		           set q name of last cue list to \"Media\" \n
		           set mediaCueList to \"Media\" \n
		           set startCueList to \"Startcues\" \n ";
                           
                       
       $script_content1.=$script_content;
       
       if($count_carr==1) {
        $script_content1.="start cue \"1\" \n
                           end tell \n
                           end tell";
       } else {
       
       $script_content1.="make type \"RESET\" \n
		          set newCue to last item of (selected as list) \n
		          set cue target of newCue to first cue list whose q name is mediaCueList \n
		          set continue mode of newCue to auto_continue \n
                          set post wait of newCue to \"$last_duration\" \n
                          
                          make type \"GOTO\" \n
                          set newStartCue to last item of (selected as list) \n
                          set cue target of newStartCue to cue \"4\" \n
                          set continue mode of newStartCue to auto_continue \n
                          set playback position of first cue list whose q name is mediaCueList to cue \"1\" \n
                          set playback position of first cue list whose q name is startCueList to cue \"4\" \n
                          start cue \"4\" \n
                          end tell \n
                          end tell";
       }
       $carrouel_name = stripslashes($carrouel_name);
       $carrouel_name=str_replace(" ","_",$carrouel_name);
       $carrouel_name=str_replace("'","__",$carrouel_name);
       $ourFileName = "$carrouel_name$c.scpt";
       
       //echo"<pre>";
       //echo $script_content1;
       //echo"</pre>";
       //exit;
       
       $ourFileHandle = fopen("script/".$ourFileName, 'w') or die("can't open file1");
       fwrite($ourFileHandle, $script_content1);
       fclose($ourFileHandle);
       
       $reboot_file="script_reboot_multi/Reboot".$screen_id.".scpt";
       $ourFileHandle1 = fopen("$reboot_file", 'w') or die("can't open file2");
       fwrite($ourFileHandle1, $script_content1);
       fclose($ourFileHandle1);
      
       //$db->writeLog($ourFileName);
       $db->writeDbLog($carr_id);
       exec("osascript script/$ourFileName");
       $c++;
       $script_content1="";
       }
       
       //----------- code for script reboot-------------------------
       //$script_reboot.="delay 15 \n";
       //$script_reboot.=$reboot_content1;
       //$script_reboot.=$reboot_content2;
      // 
       //$ourFileHandle1 = fopen("script_reboot.scpt", 'w') or die("can't open file");
       //fwrite($ourFileHandle1, $script_reboot);
       //fclose($ourFileHandle1);
      // 
      }   
       else {
    
    //------------- code for creating script command without ipaddress----------------------------------------------
        $script_content.="tell application \"Finder\" to open \"Macintosh HD:Applications:XAMPP:xamppfiles:htdocs:narrowcasting:clean_qlab_2.app\" \n
                           delay 2 \n
                          repeat \n
				 if application \"clean_qlab_2\" is not running then exit repeat \n
				 delay 1 \n
			 end repeat \n";
        
        $script_content.="tell application id \"com.figure53.qlab.2\" \n
                         tell front workspace\n";
        
        $script_content.="set q name of last cue list to \"Startcues\" \n
			  make type \"Cue List\" \n
			  set q name of last cue list to \"Media\" \n
                          set mediaCueList to \"Media\" \n
                          set startCueList to \"Startcues\" \n";
        $b=1;
        $script_query="select * from carrousel where c_id='$carr_id' order by record_listing_id asc";
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
        
        while($result1=mysql_fetch_array($res1)) {
        $carr_name=$result1['name'];
        $name1=explode(".",$result1['name']);
        $name2=explode("/",$result1['name']);
        
         if($name1[1]=="jpg" || $name1[1]=="png" || $name1[1]=="gif") {
          $script_duration=$result1['duration'];
          $newduration1=$script_duration-00.30;
          $newduration2=$script_duration+00.30;
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
			    set duration of newCue to \"00.70\" \n
                            set do opacity of newCue to true \n
                            set opacity of newCue to \"100\" \n
			    set continue mode of newCue to auto_continue \n
                            
                            make type \"Animation\"
			    set newCue to last item of (selected as list)
			    set cue target of newCue to Cue \"$b\"\n
			    set q name of newCue to \"".$name2[1]."\" \n
			    set duration of newCue to \"00.30\" \n
			    set do opacity of newCue to true \n
			    set opacity of newCue to \"0\" \n
			    set stop target when done of newCue to \"yes\" \n
                            
                            set current cue list to first cue list whose q name is startCueList \n
			    make type \"Start\" \n
			    set newStartCue to last item of (selected as list) \n
			    set cue target of newStartCue to first cue list whose q name is mediaCueList \n
			    set post wait of newStartCue to \"$script_duration\" \n
			    set continue mode of newStartCue to auto_continue \n";
             $b=$b+4;
             
          } else {
            ob_start();
            passthru("$ffmpeg_path -i /Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name 2>&1");
            $duration = ob_get_contents();
            ob_end_clean();

            preg_match('/Duration: (.*?),/', $duration, $matches);
            $duration = $matches[1];
            $duration_array = split(':', $duration);
            $newduration=$duration_array[1]*60+$duration_array[2];
	    $newduration1=$newduration-00.30;
            $script_content.="set current cue list to first cue list whose q name is mediaCueList \n
                             make type \"Video\"\n
                             set newCue to last item of (selected as list)\n
                             set newFileTarget to \"/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$carr_name\" \n
                             set file target of newCue to newFileTarget\n
                             set q name of newCue to \"".$name2[1]."\" \n
                             set opacity of newCue to \"0\"
                             set continue mode of newCue to auto_continue \n
                            
                             make type \"Animation\" \n
                             set newCue to last item of (selected as list) \n
                             set cue target of newCue to Cue \"$b\"\n
                             set q name of newCue to \"".$name2[1]."\" \n
                             set duration of newCue to \"00.70\" \n
                             set do opacity of newCue to true \n
                             set opacity of newCue to \"100\" \n
                             set continue mode of newCue to auto_continue \n
                             set post wait of newCue to \"$newduration1\" \n
                             
                             make type \"Animation\" \n
			     set newCue to last item of (selected as list) \n
			     set cue target of newCue to cue \"$b\" \n
                             set q name of newCue to \"".$name2[1]."\" \n
			     set duration of newCue to \"00.30\" \n
			     set do opacity of newCue to true \n
			     set opacity of newCue to \"0\" \n
			      set stop target when done of newCue to \"yes\" \n
                             
                             set current cue list to first cue list whose q name is startCueList \n
			     make type \"Start\" \n
			     set newStartCue to last item of (selected as list) \n
			     set cue target of newStartCue to first cue list whose q name is mediaCueList \n
			     set post wait of newStartCue to \"$newduration\" \n
			     set continue mode of newStartCue to auto_continue \n";
              $b=$b+4;
            
          }
        
      }
     
        $script_content.="make type \"RESET\" \n
                          set newCue to last item of (selected as list) \n
                          set cue target of newCue to first cue list whose q name is mediaCueList \n
                          set continue mode of newCue to auto_continue \n";
        $script_content.="make type \"GOTO\" \n
                          set newStartCue to last item of (selected as list) \n
                          set cue target of newStartCue to cue \"4\" \n
                          set continue mode of newStartCue to auto_continue \n
                          set playback position of first cue list whose q name is mediaCueList to cue \"1\" \n
                          set playback position of first cue list whose q name is startCueList to cue \"4\" \n
                          start cue \"4\" \n ";
         
        $script_content.="end tell \n
                            end tell";
      }

      $carrouel_name = stripslashes($carrouel_name);
      $carrouel_name=str_replace(" ","_",$carrouel_name);
      $carrouel_name=str_replace("'","__",$carrouel_name);
      $ourFileName = "$carrouel_name.scpt";
      
      $ourFileHandle = fopen("script/".$ourFileName, 'w') or die("can't open file3");
      fwrite($ourFileHandle, $script_content);
      fclose($ourFileHandle);
      
      $ourFileHandle1 = fopen('script_reboot.scpt', 'w') or die("can't open file4");
      fwrite($ourFileHandle1, $script_content);
      fclose($ourFileHandle1);
      
      //$db->writeLog($ourFileName);
      $db->writeDbLog($carr_id);
      exec("osascript script/$ourFileName");

  }
// } 
 
 
 }
 
 
 
 
  
  
  ?>
