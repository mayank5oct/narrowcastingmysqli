<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    error_reporting(0);
    ini_set('max_execution_time', 12343600);
   
    include('config/database.php');
    $db = new Database;
    $id=$_REQUEST['cid'];
    $add_totalcarr="";
    $item_list=array();
    $site_url="/var/www/html/narrowcasting/";
    
  
    
    if(!file_exists($site_url."fade_temp")){
      mkdir($site_url."fade_temp", 0777);
    }else{
      chmod($site_url."fade_temp", 0777);
    }

    // check for urls in carrousels
     $url_in_carr_query="select count(*) from carrousel where name in (select dynamic_cues_id from urls) and carrousel.c_id='$id'";
     $res_url_in_carr=$db->query($url_in_carr_query);
     $res_url_in_carr=mysql_fetch_array($res_url_in_carr,2);
     if($res_url_in_carr[0]>0){
        echo 'url in carrousel';
        die;
     }

    $select_carr="select a.name as c_name, b.* from carrousel_listing as a, carrousel as b where b.c_id='$id' and a.id=b.c_id order by b.record_listing_id asc";
   
    $res_carr=$db->query($select_carr);
    $counter=0;
    while($result_carr=mysql_fetch_array($res_carr)) {
      $carr_name=str_replace(" ","_",$result_carr['c_name']);
      $carr_name=$carr_name.".mp4";
      $duration=$result_carr['duration'];
      
      if($result_carr['duration']==1) {
      //------- convert video in mpg format -------------------
        $videoname=explode(".",$result_carr['name']);
        $carr_videoname=$videoname[0]."_temp_$counter.mp4";
        $orig_videoname=$site_url.$result_carr['name'];
        $new_videoname=$site_url.$carr_videoname;
        $video_info=get_video_info($orig_videoname);
        // if current vcode is not h264
        if($video_info[1]==1 and $video_info[2]==0){
           $item_list[]=array($orig_videoname,$video_info[0],0);
        }
        else{
           exec("$ffmpeg_path -i $orig_videoname  -c:v libx264 -profile:v baseline -an $new_videoname");
           $item_list[]=array($new_videoname,$video_info[0],1);
        }
       
      } else {
      //---------- convert image in mp4 video ----------------
        $imagename=explode(".",$result_carr['name']);
        $carr_imagename=$imagename[0]."_temp_$counter.mp4";
        $orig_imagename=$site_url.$result_carr['name'];
        $new_imagename=$site_url.$carr_imagename;
        //exec("$ffmpeg_path -loop_input -f image2 -i $orig_imagename -vcodec mpeg2video -t $duration -an -pix_fmt yuv422p -sameq $new_imagename");
        exec("$ffmpeg_path -loop 1 -f image2 -i  $orig_imagename -vcodec libx264 -t $duration -pix_fmt yuv420p $new_imagename");
        $item_list[]=array($new_imagename,$duration,1);
      }
      //
      $counter++;
    }
   ## work for fade in and fade out
    $item_list_with_fade=array();
    $item_count=count($item_list);
    if(count($item_list)>0){
      foreach($item_list as $key=>$original_path_data){
        $new_file_path=$site_url."fade_temp/fade_".$key.".mp4";
        $no_of_frame=get_total_frame_no($original_path_data[0]);
        $duration=$original_path_data[1];
        $active_frames=ceil(($no_of_frame/$duration)*0.5);// for .5 second
        $fade_in_value="fade=in:1:$active_frames";
        $start_frame=(($no_of_frame+1)-$active_frames);
        $fade_out_value="fade=out:$start_frame:$active_frames";
        $fade_value=$fade_in_value.' , '.$fade_out_value.' , '."scale=1920:1080";
        exec("$ffmpeg_path -i $original_path_data[0] -vf '".$fade_value."' -strict -2 $new_file_path");
        
        /*
          // echo $fade_out_value.'<br>';
           if($key==0){
            exec("$ffmpeg_path -i $original_path_data[0] -vf '".$fade_out_value."' $new_file_path");
           }else if($key==($item_count-1)){
             exec("$ffmpeg_path -i $original_path_data[0] -vf '".$fade_in_value."' $new_file_path");
           }else{
             $fade_value=$fade_in_value.' , '.$fade_out_value;
             exec("$ffmpeg_path -i $original_path_data[0] -vf '".$fade_value."' $new_file_path");
           }
        */
        $item_list_with_fade[]=$new_file_path;
         // deleting temp files
        if(($original_path_data[2]==1) && file_exists($original_path_data[0])){
           unlink($original_path_data[0]);
        }
      }
     
    }

   // writing concataned media file in temp txt file
   $temp_file_path=$site_url."fade_temp/temp.txt";
   $file = fopen($temp_file_path,"w");
   fwrite($file,implode(PHP_EOL,array_map(function($value){return 'file '.$value;},$item_list_with_fade)));
   fclose($file);
   
   // delete old file with same name 
   if(file_exists($site_url."export_carr/$carr_name")){
      unlink($site_url."export_carr/$carr_name");
   }
 
   
  //--------- combine all videos and create one video , mpg format--------------
 // exec("$ffmpeg_path -i concat:$add_totalcarr -vcodec copy -acodec copy -qscale 0 -an -pix_fmt yuv422p $site_url/export_carr/$carr_name");
  //echo "$ffmpeg_path -i concat:$add_totalcarr -vcodec copy -acodec copy -qscale 0 -an -pix_fmt yuv422p $site_url/export_carr/$carr_name";
   exec("$ffmpeg_path -f concat -i $temp_file_path -c copy  $site_url"."export_carr/$carr_name");
   ## deleting temp files
   for($i=0;$i<count($item_list_with_fade); $i++) {
    unlink($item_list_with_fade[$i]);
   }
   echo"export completed.";
 
 
  ## common functions......... 
  function get_total_frame_no($mediaPath){
     exec("$ffmpeg_path -i $mediaPath -vcodec copy -acodec copy -f null /dev/null 2>&1 | grep 'frame='",$output);
     $frame_data=$output[0];
     $metadata1= preg_split('/frame=\s*/',$frame_data);
     $metadata2=preg_split('/\s+/',$metadata1[1]);
     return $metadata2[0];
  }
  
  function get_video_info($video_path){
    ob_start();
    passthru("$ffmpeg_path -i $video_path 2>&1");
  
    $duration = ob_get_contents();
    ob_end_clean();
    preg_match('/Duration: (.*?),/', $duration, $matches);
    preg_match('/Video: h264/', $duration, $matches_h264);
    preg_match('/Audio:/', $duration, $matches_audio);
    preg_match('/\s(?<width>\d+)[x](?<height>\d+)\s\[/', $duration, $ma); 
    
    $duration = $matches[1];
    $duration_array = split(':', $duration);
    $total_duration_insec=$duration_array[0]*3600+$duration_array[1]*60+$duration_array[2];
    return array($total_duration_insec,count($matches_h264),count($matches_audio));
  
  }
 

 
?>
