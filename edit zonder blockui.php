<?php
session_start();
ini_set('max_execution_time', 3600);
error_reporting(0);

header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require('config/database.php');
require('config/master_database.php');

include('color_scheme_setting.php');
$db = new Database;
$master_db = new master_Database();
   $folder_query = "select * from narrowcasting_folder";
   $record_set = $db->query($folder_query);
   $resultset = mysql_fetch_array($record_set);
   $is_regular = $resultset['is_regular'];
   $is_client = $resultset['is_client'];
   $is_master = $resultset['is_master'];
$close_button_path="./img/$color_scheme/close_btn.png";
$edit_button_path= "./img/$color_scheme/edit02.png";
$video_button_path= "./img/$color_scheme/video.png";
$preview_button_path="./img/$color_scheme/preview_icon.png";
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 //header('Location:  index.php');
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}

## get master status
$master_query = "select is_master from narrowcasting_folder";
$master_set = $db->query($master_query);
$master_data = mysql_fetch_array($master_set);


//work for directory naming using db......
$actualDirectory=array(
    'overlays'     =>'overlays',		       
    'images'       =>'images',
    'mededelingen' =>'mededelingen',
    
    'delay_mededelingen' => 'delay_mededelingen',
    'promotie'     =>'promotie',		       
    'upload'       =>'upload',
    'videos'       =>'videos'	,
    'commercials'  =>'commercials'
   );

## get all directory name from db
$theatreDetailsSql  = "select * from theatre where status=1 limit 1";
$theatreDetails =$db->query($theatreDetailsSql);
$theatreDetails = mysql_fetch_array($theatreDetails);
$directoryMappings=array();
if(is_array($theatreDetails) && count($theatreDetails)){		 
  $directoryMappings[$actualDirectory['overlays']]     =$theatreDetails['overlays'];			 
  $directoryMappings[$actualDirectory['images']]       =$theatreDetails['images'];		     
  $directoryMappings[$actualDirectory['mededelingen']] =$theatreDetails['mededelingen'];
  
  $directoryMappings[$actualDirectory['delay_mededelingen']] =$theatreDetails['delay_mededelingen'];
  $directoryMappings[$actualDirectory['promotie']]     =$theatreDetails['promotie'];
  $directoryMappings[$actualDirectory['upload']]       =$theatreDetails['upload'];
  $directoryMappings[$actualDirectory['videos']]       =$theatreDetails['videos'];
  $directoryMappings[$actualDirectory['commercials']]  =$theatreDetails['commercials'];
}

 //------ code for commercial check with database -------------------------------------------
$comm_query="select * from theatre where status=1";
$comm_result=$db->query($comm_query);
$comm_res=mysql_fetch_array($comm_result);
$erang1=$comm_res['1erang'];
$avro=$comm_res['avro'];


 if($_GET['status']=="") {
  $status=0;
 } else {
  $status=$_GET['status'];
 }
if(isset($_GET['id']) and $_GET['id']>0)
{
  $session_id  =session_id();
  $carrouselid = $_GET['id'];
  
  $carr_id=$carrouselid;
   // update carrousel edit_status to 1 when user edit
   if ($is_master==1){
	$update_carrousel_query="update master_carrousel_listing set edit_status=1 where id=".$carrouselid;
	$master_db->query($update_carrousel_query);
} elseif ($_GET['is_client']==1){
   $update_carrousel_query="update client_carrousel_listing set edit_status=1 where id=".$carrouselid;
   $db->query($update_carrousel_query);
} elseif ($_GET['is_block']==1){
   $update_carrousel_query="update client_content_block_listing set edit_status=1 where id=".$carrouselid;
   $db->query($update_carrousel_query);
}else{
  $update_carrousel_query="update carrousel_listing set edit_status=1 where id=".$carrouselid;
  $db->query($update_carrousel_query);
   }
 
  
  $overlay = $_GET['key'];
  //----------------- delete tmp table for this session ----------------------------
  if(empty($overlay)){
   $delete_tmp="delete from temp_carrousel where status='0'";
   //$delete_tmp="truncate table temp_carrousel";
   $db->query($delete_tmp);
  }
  
  $duration_query = "select SUM(duration) as total from temp_carrousel where s_id='$session_id'";
  $duration =$db->query($duration_query);
  $duration_res =mysql_fetch_array($duration);
  if((isset($duration_res['total']) and $duration_res['total']<=0) || ($duration_res['total']==NULL)){
  
  if ($is_master==1){
  $selectquery="select * from master_carrousel where c_id='$carrouselid' ORDER BY record_listing_id ASC";
  $conn=$master_db->query($selectquery);
} elseif ($_GET['is_client']==1){
	$selectquery="select * from client_carrousel where c_id='$carrouselid' ORDER BY record_listing_id ASC";
	$conn=$db->query($selectquery);
} elseif ($_GET['is_block']==1){
	$selectquery="select * from client_content_block where c_id='$carrouselid' ORDER BY record_listing_id ASC";
	$conn=$db->query($selectquery);
} else {
	$selectquery="select * from carrousel where c_id='$carrouselid' ORDER BY record_listing_id ASC";
	$conn=$db->query($selectquery);
}
  
   while($result=mysql_fetch_array($conn, MYSQL_ASSOC)) {
      $path=$result['name'];
      $cid=$result['id'];
      $duration=$result['duration'];
		
      $countquery="select count(name) as cnt from temp_carrousel where s_id='$session_id'";
      $count=$db->query($countquery);
      $res =mysql_fetch_array($count);
      if($res){
	 $cnt = $res['cnt']+1;
      } else{
	$cnt = 1;
      }
     $Query1 = "insert into temp_carrousel (s_id,name,record_listing_id,duration,status) values('$session_id','$path','$cnt','$duration','1')";
     $conn1=$db->query($Query1);
   }
  }
}



//--------------------------- used for saving new carroussel ------------------------------- 
 $error_message="";
 $sid=session_id();
 $carrouselid=$_GET['id'];
 if($carrouselid=="") {
   $carrouselid=$_REQUEST['carr_id'];
 }
 
   //--------------------------- used for showing carroussel name before create ------------------------------- 
   $carrouel_name="";
   if($is_master==1){
   $selectname="select * from master_carrousel_listing where id='$carrouselid'";
   $conn1=$master_db->query($selectname);
   }elseif($_GET['is_client']==1){
    $selectname="select * from client_carrousel_listing where id='$carrouselid'";
	$conn1=$db->query($selectname);
}elseif($_GET['is_block']==1){
 $selectname="select * from client_content_block_listing where id='$carrouselid'";
 $conn1=$db->query($selectname);
}   else {
   	$selectname="select * from carrousel_listing where id='$carrouselid'";
	$conn1=$db->query($selectname);
   }
   
   $result1=mysql_fetch_array($conn1);
   $carrouel_name=stripcslashes($result1['name']);
   #find carosal name from temp table !! added by khushboo verma on 22nov,2012
   $selectnameTemp ="select * from temp_carrousel where s_id='$sid' and name_carrousel is not null limit 1";
   $conn1Temp=$db->query($selectnameTemp);
   $result1Temp=mysql_fetch_array($conn1Temp);   
   $carrouel_name_temp =stripcslashes($result1Temp['name_carrousel']);
   if(!empty($carrouel_name_temp))
   {
    $carrouel_name = $carrouel_name_temp;
   }
   
 
 //--------------------------- used for fetching video and img from upload folder ------------------------------- 
  $videoimage_listing="";
  if ($handle = opendir("./upload")) {
    $i=1;
     while (false !== ($entry = readdir($handle))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="thumb" and $entry!="totalthumb" and $entry!="preview" and $entry!=".DS_Store" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
         if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./upload/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"upload\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='./upload/$entry' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"upload\",\"$carrouselid\",\"$status\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         } else if($entry1[1]=="mov" or $entry1[1]=="mp4"){
           $itemname="<div class='block_img'><img src='./upload/thumb/".str_replace(" ","_",$entry1[0]).".jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"upload\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='javascript:void(0);' onclick='popitup(\"upload\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",2,\"upload\",\"$carrouselid\",\"$status\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         } else {
          
         }
         if($itemname!='')
        $videoimage_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";

      }
     }
     $search_block='<div  class="item_search_box_container">
                     <input  id="upload_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';
     $videoimage_listing=$search_block.$videoimage_listing;
     closedir($handle);
     unset($itemname);
   }
   
   
    // for url listing
        $final_name="";
	$getUrlQuery="select * from urls order by id limit 10";
        $url_data=$db->query($getUrlQuery);
        while($url_row =mysql_fetch_array($url_data)){
	  $row_url = preg_replace('/^http(s)?:\/\//','', $url_row['url']);
	  $row_image_name  = $url_row['image'];
	  $row_id    = $url_row['id'];
	  if($url_row['name']==""){
	    $final_name=substr($row_url,0,35);
	  }else{
	    $final_name=$url_row['name'];
	  }
	  $new_url=str_replace('http://localhost','..',$url_row["url"]);
	  $row_dynamic_cues_id    = $url_row['dynamic_cues_id'];
	  $itemname="<div class='block_img'><img src='./urls/thumb/$row_image_name?time=".time()."' width='135' height='135'></div><p>".$final_name."</p><a href='javascript:void(0);' onclick='additem(\"url\",\"$row_dynamic_cues_id\")'>
		    <div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' rel='single' >
		    <div class='btn2' onclick='popit_url(\"$new_url\")' >Preview</div></a>
		    <div class='clear'></div>";
	    //<div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$row_url\",1,\"url\",\"$carrouselid\",\"$status\")'><img src='./img/close_btn.png' alt=''></a></div>
	 $url_listing.="<div class='block' id='start-".substr($row_image_name,0,35)."'>$itemname</div>";
	}
   
   //--------------------------- used for fetching video and img from overlay folder ------------------------------- 
  $overlay_listing="";
  if ($handle1 = opendir("./overlay_video")) {
    $i=1;
     while (false !== ($entry = readdir($handle1))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="thumb" and $entry!="totalthumb" and $entry!="preview" and $entry!=".DS_Store" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
	$newname=explode("_",$entry1[0]);
	$data=htmlentities($entry);
         if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./overlay_video/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($newname[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"overlay_video\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='./overlay_video/$entry' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	    <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"overlays_image\",\"$data\",\"$carrouselid\")'>
	   <img src='$edit_button_path' alt='' width='20' height='20'></a></div>
	  
	  
	   <div class='close_btn_near_edit'>
	      <a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"overlay_video\",\"$carrouselid\",\"$status\",\"$is_client\")'>
	      <img src='$close_button_path' alt=''></a>
	   </div>
	   
	   
	   <div class='clear'></div>";
         } else if($entry1[1]=="mov" or $entry1[1]=="mp4"){
           $itemname="<div class='block_img'><img src='./overlay_video/thumb/".str_replace(" ","_",$entry1[0]).".jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($newname[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"overlay_video\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='javascript:void(0);' onclick='popitup(\"overlay_video\",\"$entry2\")'><div class='btn2'>Preview</div></a>
	   
	   <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"overlay_video\",\"$data\",\"$carrouselid\")'>
	   <img src='$edit_button_path' alt='' width='20' height='20'></a>
	   </div>
	  
	   <div class='close_btn_near_edit'>
	      <a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"overlay_video\",\"$carrouselid\",\"$status\")'>
	      <img src='$close_button_path' alt=''></a>
	   </div>
	   
	   <div class='clear'></div>";
         } else {
          
         }
         if($itemname!='')
        $overlay_listing.="<div class='block' id='start-".substr($newname[0],0,35)."'>$itemname</div>";

      }
     }
     $search_block='<div class="item_search_box_container">
                     <input  id="overlay_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';
     $overlay_listing=$search_block.$overlay_listing;
     closedir($handle1);
     unset($itemname);
   }
  //--------------------------- used for fetching images from images folder ------------------------------- 
  $images_listing="";
  if ($handle2 = opendir("./images")) {
    $i=1;
     while (false !== ($entry = readdir($handle2))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!=".DS_Store" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!="_Archived Items" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./images/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"images\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='./images/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"images\",\"$carrouselid\",\"$status\",\"$is_client\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         } else {
           $itemname="<div class='block_img'><img src='$video_button_path?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"images\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='#'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"images\",\"$carrouselid\",\"$status\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         }
          if($itemname!='')
        $images_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
     }
     $search_block='<div class="item_search_box_container">
                     <input  id="photo_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';
		     
     $images_listing=$search_block.$images_listing;
     closedir($handle2);
     unset($itemname);
   }
   
  //--------------------------- used for fetching video from video folder ------------------------------- 
  $videos_listing="";
  if ($handle3 = opendir("./videos")) {
    $i=1;
     while (false !== ($entry = readdir($handle3))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!=".DS_Store" and $entry!="thumb" and $entry!="totalthumb" and $entry!="preview" and $entry!="_Archived Items" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        $itemname="<div class='block_img'><img src='./videos/thumb/$entry1[0].jpg?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"videos\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='javascript:void(0);' onclick='popitup(\"videos\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",2,\"videos\",\"$carrouselid\",\"$status\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         if($itemname!='')
        $videos_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
     }
      $search_block='<div  class="item_search_box_container">
                     <input  id="video_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';
      $videos_listing=$search_block.$videos_listing;
     closedir($handle3);
     unset($itemname);
   }
   if($_GET[carrousel_id]!=''){
	  $carrousel_id=$_GET[carrousel_id];
	}
	if($_GET[id]!=''){
	  $carrousel_id=$_GET[id];
	}
 //--------------------------- used for fetching video from Mededelingen folder ------------------------------- 
  $medede_listing="";
  if ($handle4 = opendir("./mededelingen")) {
    $i=1;
     while (false !== ($entry = readdir($handle4))) {
      $entry =str_replace(" ?","__",$entry);
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!=".DS_Store" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
	$data=htmlentities($entry);
	
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
	  if(substr($entry1[0],0,7)=="Twitter") {
	   $twitter_name=explode("_",$entry1[0]);
	   $itemname="<div class='block_img'><img src='./mededelingen/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("-",'"',$twitter_name[0])."</p><a href='javascript:void(0);' onclick='additem(\"mededelingen\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='./mededelingen/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"mededelingen\",\"$carrouselid\",\"$status\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
	  } else {
           $itemname="<div class='block_img'><img src='./mededelingen/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"mededelingen\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='./mededelingen/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	   
	    <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"mededelingen\",\"$data\",\"$carrousel_id\")'>
	   <img src='$edit_button_path' alt='' width='20' height='20'></a></div>
	   
	   <div class='close_btn_near_edit'>
	   <a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"mededelingen\",\"$carrouselid\",\"$status\")'>
	   <img src='$close_button_path' alt=''></a></div>
	   
	   <div class='clear'></div>";
	  }
         } else {
           $itemname="<div class='block_img'><img src='./mededelingen/thumb/$entry1[0].jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"mededelingen\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='javascript:void(0);' onclick='popitup(\"mededelingen\",\"$entry2\")'><div class='btn2'>Preview</div></a>
	   
	    <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"mededelingen\",\"$data\",\"$carrousel_id\")'>
	   <img src='$edit_button_path' alt='' width='20' height='20'></a></div>
	   
	   <div class='close_btn_near_edit'>
	   <a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"mededelingen\",\"$carrouselid\",\"$status\")'>
	   <img src='$close_button_path' alt=''></a></div>
	   
	   <div class='clear'></div>";
         }
	  if($itemname!='')
        $medede_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname  </div>";
      }
     }
     $search_block='<div class="item_search_box_container">
                     <input  id="communications_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';       	
     $medede_listing=$search_block.$medede_listing;
     closedir($handle4);
     unset($itemname);
   }
   
   
   
   //------------------------------- used for fetching images from delay mededelingen folder -----------------------------//
 
 if($theatreDetails[is_delay]==1){
    $delay_medede_listing="";
  if ($handle4_delay = opendir("./delay_mededelingen")) {
    $i_delay =1;
     while (false !== ($entry_delay  = readdir($handle4_delay))) {
      $entry_delay  =str_replace(" ?","__",$entry_delay);
       if($entry_delay!="" and $entry_delay!="." and $entry_delay!=".." and $entry_delay!="Thumbs.db" and $entry_delay!="preview" and $entry_delay!=".DS_Store" and $entry_delay!="thumb" and $entry_delay!=".gitignore" and $entry!="cropped") {
        $entry1_delay=explode('.',$entry_delay);
        $entry2_delay=urlencode($entry_delay);
	$data_delay=htmlentities($entry_delay);
	$entry_delay = $entry_delay;
        if($entry1_delay[1]=="jpg" or $entry1_delay[1]=="gif" or $entry1_delay[1]=="png") {
	  $thumb_medede_path_delay='./delay_mededelingen/thumb/'.$entry_delay;
	  
	   if(!file_exists($thumb_medede_path_delay)){
	     $thumb_medede_path_delay='./delay_mededelingen/thumb/default.png';
	   }
	  if(substr($entry1_delay[0],0,7)=="Twitter") {
	   $twitter_name_delay=explode("_",$entry1_delay[0]);
	   
	   $itemname_delay="<div class='block_img'><img src='$thumb_medede_path_delay?time=".time()."' width='135' height='135'></div><p>".str_replace("-",'"',$twitter_name_delay[0])."</p><a href='javascript:void(0);' onclick='additem(\"delay_mededelingen\",\"$entry2_delay\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./delay_mededelingen/$entry_delay?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	    
	   <div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry_delay\",1,\"mededelingen\",\"$carrouselid_delay\",\"$status_delay\")'>
	   <img src='$close_button_path_delay' alt=''></a></div><div class='clear'></div>";
	  } else {
           $itemname_delay="<div class='block_img'><img src='$thumb_medede_path_delay?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1_delay[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"delay_mededelingen\",\"$entry2_delay\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./delay_mededelingen/$entry_delay?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	   
	   
	   
	   <div class='clear'></div>";
	  }
         } else {
	  $entry_image_a_delay=$entry1_delay[0].'.jpg';
           $itemname_delay="<div class='block_img'><img src='./delay_mededelingen/thumb/$entry_image_a_delay?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1_delay[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"delay_mededelingen\",\"$entry2_delay\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"delay_mededelingen\",\"$entry2_delay\")'><div class='btn2'>Preview</div></a>
	   
	   
	   <div class='clear'></div>";
         }
	 
        $medede_listing_delay.="<div class='block' id='start-".substr($entry1_delay[0],0,35)."'>$itemname_delay  </div>";
 
        

      }
     }
 
     $search_block_delay='<div class="item_search_box_container">
                     <input  id="communications_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';       	
     $medede_listing_delay=$search_block_delay.$medede_listing_delay;
     closedir($handle4_delay);
     unset($itemname_delay);
   }
 }
 //------------------------------- used for fetching images from delay mededelingen folder -----------------------------//
   
 //--------------------------- used for fetching video and image from commercials folder ------------------------------- 
   $comme_listing="";
  if ($handle5 = opendir("./commercials")) {
    $i=1;
     while (false !== ($entry = readdir($handle5))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!=".DS_Store" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./commercials/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"commercials\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='./commercials/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"commercials\",\"$carrouselid\",\"$status\",\"$is_client\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         } else {
           $itemname="<div class='block_img'><img src='./commercials/thumb/$entry1[0].jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"commercials\",\"$entry2\",\"$carrouselid\")'><div class='btn1'>Toevoegen</div></a><a href='javascript:void(0);' onclick='popitup(\"commercials\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"commercials\",\"$carrouselid\",\"$status\",\"$is_client\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         }
          if($itemname!='')
        $comme_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
     }
     $search_block='<div  class="item_search_box_container">
                     <input  id="commercial_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';      
     $comme_listing=$search_block.$comme_listing;
     closedir($handle5);
     unset($itemname);
   }
  
  //--------------------------- used for fetching video and image from promotie folder ------------------------------- 
  $promotie_listing="";
  if ($handle6 = opendir("./promotie")) {
    $i=1;
     while (false !== ($entry = readdir($handle6))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!=".DS_Store" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./promotie/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"promotie\",\"$entry2\",\"$carrouselid\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./promotie/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"promotie\",\"$carrouselid\",\"$status\",\"$is_client\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         } else {
           $itemname="<div class='block_img'><img src='./promotie/thumb/$entry1[0].jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"promotie\",\"$entry2\",\"$carrouselid\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"promotie\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"promotie\",\"$carrouselid\",\"$status\",\"$is_client\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
         }
        $promotie_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
     }
     $search_block='<div  class="item_search_box_container">
                     <input  id="promotie_search" type="text" placeholder="Zoeken" class="item_search_box"></div>'; 
     $promotie_listing=$search_block.$promotie_listing;
     closedir($handle6);
     unset($itemname);
   }
   
   
    // getting all dynamic url info..........
   $getUrlQuery="select * from urls order by id limit 10";
   $url_data=$db->query($getUrlQuery);
   $dynamic_url_info=array();
   while($url_row =mysql_fetch_array($url_data)){
	$dynamic_url_info[$url_row['dynamic_cues_id']]['dynamic_cues_id']=$url_row['dynamic_cues_id'];
	$dynamic_url_info[$url_row['dynamic_cues_id']]['image']=$url_row['image'];
	$dynamic_url_info[$url_row['dynamic_cues_id']]['url']=$url_row['url'];
	if($url_row['name']==""){
		 $dynamic_url_info[$url_row['dynamic_cues_id']]['name']=str_replace("http://","",$url_row['url']);
		}else{
		 $dynamic_url_info[$url_row['dynamic_cues_id']]['name']=$url_row['name'];
		}
   }
   
   
   
 //--------------------------- used for showing carroussels before create ------------------------------- 
  $carrousel_listing="";
  $session_id  =session_id();
   $selectquery="select * from temp_carrousel where s_id='$session_id' ORDER BY record_listing_id ASC";
   $conn=$db->query($selectquery);
   while($result=mysql_fetch_array($conn)) {
    
    $path=$result['name'];
    $cid=$result['id'];
    $tempid=$result['id'];
    $imagename=explode('/',$path);
     $image_name_timeline='';
    $image_name_timeline=$imagename[1];
    $url_ques_id=$result['name'];
    $imagename1=explode('.',$imagename[1]);
    if($path==$imagename[0] and $imagename[1]=="") {
       $row_type="url";
    }else{
      $row_type="";
   }
    
    if($imagename1[1]=="jpg" or $imagename1[1]=="gif" or $imagename1[1]=="png" or $row_type=='url') {
     $path=$result['name'];
     $real_path=$result['name'];
     $path1=explode("/",$result['name']);
     if($path1[0]=="images") {
       $path="images/thumb/".$imagename[1];
     }
     else if($path1[0]=="upload") {
       $path="upload/thumb/".$imagename[1];
     }
     else if($path1[0]=="overlay_video") {
       $path="overlay_video/thumb/".$imagename[1];
     }
     else if($path1[0]=="mededelingen") {
       $path="mededelingen/thumb/".$imagename[1];
     }
     else if($path1[0]=="promotie") {
       $path="promotie/thumb/".$imagename[1];
     } else if($path==$path1[0] and $path1[1]=="") {
       $path="urls/thumb/".$dynamic_url_info[$url_ques_id]['image'];
     }
     $duration="<input type='text' name='duration[]' size='1' value='".$result['duration']."' onblur='updatetime($cid,this.value,$carrouselid)'><span>s</span>";
     if($path1[0]=="overlay_video") {
      $overlay_link="";
    }else if($row_type=='url') {
       $overlay_link="";
      }
    
    else if($path1[0]=="mededelingen"){
      $checktwt=substr($path1[1],0,7);
      if($checktwt=="Twitter") {
	$overlay_link="";
      } else {
	if($_GET['is_client']==1){
	$overlay_link="<a href='javascript://' onclick='pop_permission()'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}else{
	  $overlay_link="<a href='createimage_overlay.php?path=$real_path&id=$tempid&cid=$carrouselid&status=$_GET[status]&is_block=$_GET[is_block]'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}
      }
    } else {
      if($_GET['is_client']==1){
	$overlay_link="<a href='javascript://' onclick='pop_permission()'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}else{
      $overlay_link="<a href='createimage_overlay.php?path=$real_path&id=$tempid&cid=$carrouselid&status=$_GET[status]&is_block=$_GET[is_block]'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}
    }
    
    if($row_type!='url'){
    $preview_btn="<a href='./$path1[0]/$image_name_timeline?time=".time()."' rel='single'  class='pirobox'><div class='btn_preview'><img src='$preview_button_path' width='20' height='20' /></div></a>";
    }else{
      $preview_btn='';
    }
    } else if($imagename1[1]=="mov" or $imagename1[1]=="mp4"){
     $path1=explode("/",$result['name']);
    if($path1[0]=="videos") {
      $path="videos/thumb/".$imagename1[0].".jpg";
      $real_path="videos/".$imagename[1];
    } else if($path1[0]=="mededelingen") {
      $path="mededelingen/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="mededelingen/".$imagename[1];
    } else if($path1[0]=="overlay_video") {
      $path="overlay_video/thumb/".$imagename1[0].".jpg";
      $real_path="overlay_video/".$imagename[1];
    } else if($path1[0]=="commercials") {
      $path="commercials/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="commercials/".$imagename[1];
    }else if($path1[0]=="promotie") {
      $path="promotie/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="promotie/".$imagename[1];
    }else {
      $path="upload/thumb/".$imagename1[0].".jpg";
      $real_path="upload/".$imagename[1];
    }
    $duration="<input type='hidden' name='duration[]' value='1'>";
    if($path1[0]=="overlay_video") {
      $overlay_link="";
    }else if($row_type=='url') {
       $overlay_link="";
      }
    else if($path1[0]=="mededelingen"){
      $checktwt=substr($path1[1],0,7);
      if($checktwt=="Twitter") {
	$overlay_link="";
      } else {
	if($_GET['is_client']==1){
	$overlay_link="<a href='javascript://' onclick='pop_permission()'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}else{
	$overlay_link="<a href='create_overlay.php?path=$real_path&id=$tempid&cid=$carrouselid&status=$_GET[status]&is_block=$_GET[is_block]'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}
      }
    } else {
      if($_GET['is_client']==1){
	$overlay_link="<a href='javascript://' onclick='pop_permission()'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}else{
      $overlay_link="<a href='create_overlay.php?path=$real_path&id=$tempid&cid=$carrouselid&status=$_GET[status]&is_block=$_GET[is_block]'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	}
    }
    $preview_btn="<a href='javascript:void(0);'   onclick='popitup(\"$path1[0]\",\"$path1[1]\")'><div class='btn_preview'><img src='$preview_button_path' width='20' height='20' /></div></a>"; 
  } else {
	
  }
  $imagename=explode("/",$path);
  
  
   if($row_type=='url'){
 
      $image_name=preg_replace('/^http(s)?:\/\//','', $dynamic_url_info[$url_ques_id]['name']);
    }
   else if($imagename[0]!="overlay_video") {
     if(substr($imagename1[0],0,7)=="Twitter") {
      $twitter_name=explode("_",$imagename1[0]);
      $image_name=str_replace("-",'"',$twitter_name[0]);
     } else {
      $image_name=stripslashes(str_replace("_"," ",substr($imagename1[0],0,35)));
     }
    }else {
     if(substr($imagename1[0],0,7)=="Twitter") {
      $twitter_name=explode("_",$imagename1[0]);
      $image_name=str_replace("-",'"',$twitter_name[0]);
     } else {
     $image_name=stripslashes(str_replace("_"," ",substr(substr($imagename1[0],0,-5),0,35)));
     }
   }
    if(strstr($path, 'thumb')==true){
    $path_thumb = $path;
   
    }else{
     $path_thumb = explode('/',$path);
    $path_thumb = $path_thumb[0].'/thumb/'.$path_thumb[1];
     
    }
    $ext=explode(".", $image_name_timeline);
      if($path1[0]=='mededelingen' || $path1[0]=='overlay_video'){
     if($ext[1]!='mov' && $path1[0]!='mededelingen'){
     $edit_btn="<div class='edit_btn_edit'><a href='javascript:void(0);' onclick='editImage(\"overlays_image\",\"$image_name_timeline\",\"$carr_id\")'><img src='$edit_button_path' alt='' width='20' height='20'></a>
	   </div>";
     }else{
      $edit_btn="<div class='edit_btn_edit'><a href='javascript:void(0);' onclick='editImage(\"$path1[0]\",\"$image_name_timeline\",\"$carr_id\")'><img src='$edit_button_path' alt='' width='20' height='20'></a>
	   </div>";
     }
    }else{
     $edit_btn='';
    }
    $commercial_add.='*'.$real_path;
    $carrousel_listing.="<li class='horiz_block' id='recordsArray_".stripslashes($cid)."'>
                        	<div class='horiz_img'>
				$edit_btn
                            	<img class='horiz_img_thumb' src='./".stripslashes($path_thumb)."?time=".time()."' width='135' height='135' alt='' />
                            	<div class='close_btn'><a href='javascript:void(0);' onclick='deleteCarrousel(\"$cid\",\"$carrouselid\")'><img src='$close_button_path' alt=''/></a></div>
				$preview_btn
                            </div>
                             <p>".$image_name."</p>
                             $overlay_link
                             <div class='horiz_input_field fl_r_changes'>
                             	
                                $duration
                              <input type='hidden' name='cname[]' value='$real_path'>
                              <input type='hidden' name='tempid[]' value='$tempid'>
			      <input type='hidden' name='htotalrow'  id='htotalrow' value='$num_rows'>			      
                                <div class='clear'></div>
                             </div>
                        </li>";
   
   }
   $carrousel_listing.="<input type='hidden' name='commercial_check' id='commercial_check' value='$commercial_add'>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />


<title>Pandora | Bewerk uw bestaande carrousel</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="./css_pirobox/style_1/style.css"/>
<link rel="stylesheet" href="./css/jquery-ui.css" type="text/css" />

<?php include('jsfiles.html')?>

<style type="text/css">

        #alle_carrousels,#nieuw_bestand,#nieuwe_twitterfeed,#uploaden_bestand,#iets_nieuws,#spreekuur,#tot_ziens{
		  <?php echo $css_color_rule;?>
	}

	#nieuw{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
	#alle_carrousels{font-size:14px;line-height:30px;}
	#maak_carrousel{font-size:14px;color: #b5b5b5;line-height:30px;}
	#nieuw_bestand{font-size:14px;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;;line-height:30px;}
	#iets_nieuws{font-size:14px;line-height:30px;}
	#spreekuur{font-size:14px;line-height:30px;}
	#uploaden_bestand{font-size:14px;line-height:30px;}
	#tot_ziens{font-size:14px;line-height:30px;}
	
	 ul.ui-autocomplete {z-index: 99999999 !important;}
	 .search_result_background {
		  position:fixed;
		  top:0; left:0;
		  width:100%;
		  height:100%;
		  z-index:1111;
		  background:#000000;
		  display:none;
		  cursor:pointer;
               }
        .ui-menu .ui-menu-item {
	       margin: 0;
	       padding: 0;
	       width: 100%;
	       support: IE10, see #8844;
	       list-style-image: url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7);
	       background: #f1f1f1;
	       margin-top: 3px;
             }
	     
	  
	
</style>

</head>

<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->
<script type="text/javascript">


$(document).ready(function() {


// fixing of placeholder issue IE<=9
var is_browser_browser=$.browser.msie;
if(is_browser_browser){
  var theaterLabel=$("input#comman_search").val();
  if(theaterLabel==""){
     $("input#comman_search").val("Zoeken");
  }
  $("input#comman_search").bind("focus",function(){
  if(is_browser_browser){
       theaterLabel=$("input#comman_search").val();
      if(theaterLabel=="Zoeken"){
	$("input#comman_search").val("")
      }
    }
  });
   
  $("input#comman_search").bind("focusout",function(){
    if(is_browser_browser){
       theaterLabel=$("input#comman_search").val();
      if(theaterLabel==""){
       $("input#comman_search").val("Zoeken");
      }
    }
  });
  
}
/////


    setTimeout(function(){
     getSelecteTabImageData();
     getAllTabImageData();
       $().piroBox_ext({
        piro_speed : 900,
        bg_alpha : 0.1,
        piro_scroll : true //pirobox always positioned at the center of the page
    });
     },100);
    // autocomplete jsondata setup at time of page load
    
    
   // for searchbox clear button
   $("span#global_search_clear").bind("click",function(){
     $("input#comman_search").val("");
     $('div#blocks_container_comman_search div.block').css('display','block');
     $('#comman_search').autocomplete('close');
     $(".search_result_background").css({'display':'none'});
     $("#search_result_content").css({'display':'none'});
     $("span#global_search_clear").animate({ opacity:0}, 400, function(){ $("span#global_search_clear").hide();});
   });
    
});




function popitup(path,id) {
    url='previewvideo.php?id='+id+'&path='+path+"&time="+new Date().getTime();
    newwindow=window.open(url,'Preview','height=500,width=640,left=190,top=150,screenX=200,screenY=100');
}
/************** START OF CODE FOR AUTOCOMPLETE ***********/

// getting all jsondata for media search at local
var currentSelectedTab="";
var currentWorkingSearchDiv="";
var currentSearchBox="";
// variable that store all media info in json format
var autoSuggestJsonData=
{
'showcommunications':[], // for mededelingen tab info
'showvideo':         [], // for video tab info
'showphoto':         [], // for photo tab info
'showupload':        [], // for upload tab info
'showcommercial':    [],
'showoverlay':       [],
'showpromotie':      [],
'showurlbottom':     [],
'all_data':          []
}

// it set selected tab json info in autoSuggestJsonData, initilize variables currentWorkingSearchDiv and currentSearchBox
function getSelecteTabImageData() {
  var selectedTabJsonLength=0;
  // at page load a tab is selected by default
  if(currentSelectedTab==""){
    currentSelectedTab= $.trim($('div.tabs ul li a.current').attr('id'));
  }
  currentWorkingSearchDiv='show'+currentSelectedTab;
  currentSearchBox=currentSelectedTab+'_search';
  try{
     selectedTabJsonLength=autoSuggestJsonData[currentWorkingSearchDiv].length;
  }
  catch(err){}
  // get all info from selected tab's "info div" if it is not set ( first time tab is clicked)
  if(selectedTabJsonLength<=0){
    $('div#show'+currentSelectedTab+' div.block').each(
      function(key)
      {
	 var data=trim($(this).attr('id'));
	 var label=data.replace(/_/g," ");
	 autoSuggestJsonData[currentWorkingSearchDiv].push({ label: label, value: data});
	 
      }
    );
  }
  //console.log(autoSuggestJsonData[currentWorkingSearchDiv]);
}



// it set selected tab json info in autoSuggestJsonData for all data
function getAllTabImageData() {
 $.each(autoSuggestJsonData, function(index, value) {
  // get all info from selected tab's "info div" if it is not set ( first time tab is clicked)
    $('div#'+index+' div.block').each(
      function(key)
      {
	 var data=trim($(this).attr('id'));
	 var label=data.replace(/_/g," ");
	 autoSuggestJsonData['all_data'].push({ label: label, value: data});
	 
      }
    );
   $('div#blocks_container_comman_search').append( $('div#'+index).html());
   $('div#blocks_container_comman_search div.item_search_box_container').remove();
   $('div#blocks_container_comman_search div.clear').remove();
 });
 $('div#blocks_container_comman_search').append('<div class="clear"></div>');
 $('div#blocks_container_comman_search div.block').css('display','b');
}


// autocomplete for comman search
$(function() {
  var pageHeight=$(document).height();
  var result_container_margin=($(document).width()-950)/2;
    $("#comman_search").autocomplete({
      source: function(request, response) {
            var data =autoSuggestJsonData['all_data'];
	    var data_filtered     = $.ui.autocomplete.filter(data,request.term);	    
	    
	    $('div#blocks_container_comman_search div.block').css('display','none');
	    $.each(data_filtered,function(key,currentRow){
	      var blockId=currentRow.value;
	      $('#blocks_container_comman_search div#'+blockId).css('display','block');
	    });
	    // handle case of showing all media when search_box is empty after back button clicks
	    setTimeout(function(){
	     if($('#comman_search').val()==""){
	        $('div#blocks_container_comman_search div.block').css('display','block');
	        $('#comman_search').autocomplete('close');
		$(".search_result_background").css({'display':'none'});
	        $("#search_result_content").css({'display':'none'});
		$("#search_result_content").css('scrollTop',0);
		$("span#global_search_clear").animate({ opacity:0 }, 200, function(){$("span#global_search_clear").hide();});
	      }
	   
	    },40)
	     //search_result_content
	    $(".search_result_background").css({'top':'519px','display':'block','height':'515px'});
	    $("#search_result_content").css({'top':'524px','display':'block','margin-left':result_container_margin+'px'});
	    $("span#global_search_clear").show();
	    $("span#global_search_clear").animate({ opacity:0.7}, 200, function(){});
            response(data_filtered);
	    
        },
      select:function(event,selecteItem){
	$('div#blocks_container_comman_search div.block').css('display','none');
	$('div#blocks_container_comman_search div#'+selecteItem.item.value).css('display','block');
      },
      close:function(){
	$('#comman_search').val($('#comman_search').val().replace(/_/g," "));
      },
      delay: 0,
      minLength: 0
     }); 
    
     $.ui.autocomplete.filter = function (array, term) {
       try{
	  term=$.trim(term.replace(/_/g," "));
	  var matcher = new RegExp("" + $.ui.autocomplete.escapeRegex(term), "i");
       }catch(err){}
       try{
	  return $.grep(array, function (value) {
	      //return matcher.test(value.label || value.value || value);
	      return matcher.test(value.label);
	  });
	}catch(err){}
    };
  });


// jquery autocomplete code for all tabs....

$(function() {
    $("#communications_search,#photo_search,#promotie_search,#overlay_search,#upload_search,#video_search,#commercial_search").autocomplete({
      source: function(request, response) {
            var data =autoSuggestJsonData[currentWorkingSearchDiv];
	    var filter_data=$.ui.autocomplete.filter(data,request.term);
	    $('div#'+currentWorkingSearchDiv+' div.block').css('display','none');
	    $.each(filter_data,function(key,currentRow){
	      var blockId=currentRow.value;
	      $('div#'+currentWorkingSearchDiv+' div#'+blockId).css('display','block');
	    });
	    // handle case of showing all media when search_box is empty after back button clicks
	    setTimeout(function(){
	     if($('#'+currentSearchBox).val()==""){
	        $('div#'+currentWorkingSearchDiv+' div.block').css('display','block');
	        $('#'+currentSearchBox).autocomplete('close');
	      }
	     
	    },20)
            response(filter_data);
        },
      select:function(event,selecteItem){
	$('div#'+currentWorkingSearchDiv+' div.block').css('display','none');
	$('div#'+currentWorkingSearchDiv+' div#'+selecteItem.item.value).css('display','block');
      },
      close:function(){
	 $('#'+currentSearchBox).val($('#'+currentSearchBox).val().replace(/_/g," "));
      },
      delay: 0,
      minLength: 0
     }); 
    
     $.ui.autocomplete.filter = function (array, term) {
       try{
	  term=$.trim(term.replace(/_/g," "));
	  var matcher = new RegExp("" + $.ui.autocomplete.escapeRegex(term), "i");
       }catch(err){}
       try{
	  return $.grep(array, function (value) {
	      //return matcher.test(value.label || value.value || value);
	      return matcher.test(value.label);
	  });
	}catch(err){}
    };
  });
/************** END OF CODE FOR AUTOCOMPLETE ***********/





var dragging = false;// flag for drag start	
$(document).ready(function(){ 		   
  bindSortableInTimeline();
 
});



function bindSortableInTimeline(){
   var is_client=<?php echo isset($_GET[is_client]) ? $_GET[is_client] : "0"; ?>;
  if(is_client==1){
 //   alert("You cant add an item to timeline");
    return false;
  }
  $("#contentLeft ul").sortable(
		{
		opacity: 0.6,cursor: 'move',revert: true,dropOnEmpty:true,
		start: function(event, ui) {
		  dragging=true;
		  scrollOnDrag();
		},
		stop: function(event, ui) {
		  dragging=false;
		  $('div.horizon_gallery2').unbind("mousemove");
		},
		update: function() {
		    var order = $(this).sortable("serialize") + '&action=updateRecordsListings';
		    $.post("updateDB.php", order, function(theResponse){
		     $("#result").html(theResponse);
		    }); 															 
		}								  
    });
  
   //$("#contentLeft ul").disableSelection();

   
  }





function scrollOnDrag() {
	    var windowHeight=$(window).height();
	    var containerHeight=$("div.horizon_gallery2").height();;
	    var containerTopPosition=$("div.horizon_gallery2").position().top;
	    var maxScrollableheight=containerHeight+containerTopPosition;
	    var divScrollHeight=$('div.horizon_gallery2')[0].scrollHeight;
	    var divTotalHeight=containerHeight+divScrollHeight;
	    var flagWindowUp=true;
	    var flagWindowDown=true;
	    var coordinateScaleRatio=divTotalHeight/containerHeight;
	    $("div.horizon_gallery2").mousemove(function(e) {
		  if(dragging) {
		   if($.browser.mozilla){
		     var windowScrollHeight=$("html,body").scrollTop();//+windowHeight;
		   }else{
		     var windowScrollHeight=$("body").scrollTop();//+windowHeight;
		   }
		   
	           var mousePostionY=e.pageY;
		   // for window page scroll movement
		   if(Math.abs(windowScrollHeight-mousePostionY)<50 && flagWindowUp==true){
		    flagWindowUp=false;
		     $('body,html').animate({
                        scrollTop:(containerTopPosition)
                       }, '500', 
                        function(){flagWindowUp=true;} // callback method 
                      );
		   }else if(Math.abs((windowScrollHeight+windowHeight)-mousePostionY)<50 && flagWindowDown==true){
		      flagWindowDown=false;
		      $('body,html').animate({
                        scrollTop:(maxScrollableheight-windowHeight)
                       }, '500', 
                        function(){flagWindowDown=true;} // callback method 
                      );
		      
		      
		   }
		    var divScrollTop=$("div.horizon_gallery2").scrollTop();//+windowHeight;
		    var scrollPositionDiff=(mousePostionY-$(this).offset().top)*coordinateScaleRatio;
		    if(scrollPositionDiff<0){
		      scrollPositionDiff=0;
		    }else if(scrollPositionDiff==undefined){
		       scrollPositionDiff=0;
		    }
		    
		    
		      // for container div scroll movement
		    if(scrollPositionDiff<(150*coordinateScaleRatio)){
		      $('div.horizon_gallery2').scrollTop(0);
		     }
		    else if(scrollPositionDiff>(150*coordinateScaleRatio)){
		      if(scrollPositionDiff> (divScrollHeight-containerHeight)){
		       scrollPositionDiff=(divScrollHeight-containerHeight);
		      }
		      else if(scrollPositionDiff<0){
		       scrollPositionDiff=0;
		      }
		      $('div.horizon_gallery2').scrollTop(scrollPositionDiff);
		    }
		    else{
		     $('div.horizon_gallery2').scrollTop(divScrollHeight-containerHeight);  
		    }
		    
		    
		  }
	
	    });
}


 function movedata(){
	
		
		$("#contentLeft ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&action=updateRecordsListings';
			$.post("updateDB.php", order, function(theResponse){
				$("#result").html(theResponse);
			}); 															 
		}								  
		});
 }

 function showhidediv(id) {
    // it has value of current selected tab id
    currentSelectedTab=id;
    // it will set all media data(id,name) in its json
    getSelecteTabImageData();
    
    
   if(id=='video') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("current");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
     <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("");
	 <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
	 <?php endif;?>
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="block";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="none";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
       <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="none";
	   <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="none"; 
	   <?php endif;?> 
      }catch(err){}
    
         
   } else if(id=='photo') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("current");
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
     <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("");
	 <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
	 <?php endif;?>
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="block";  
       document.getElementById("showcommunications").style.display="none";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
       <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="none";
	   <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="none";  
	   <?php endif;?>
      }catch(err){} 
     
   } else if(id=='communications') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("current");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
     <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("");
	 <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
	 <?php endif;?>
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="block";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
       <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="none";
	   <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="none";  
	   <?php endif;?>
      }catch(err){} 
     
   }else if(id=='delay_communications') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("current");
      <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("");
	 <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
	 <?php endif;?>
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="none";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="block";
       <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="none";
	   <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="none";  
	   <?php endif;?>
      }catch(err){} 
     
   }
   else if(id=='upload') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
      <?php endif;?>
     $("#upload").removeClass("current").addClass("current");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("");
	 <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
	 <?php endif;?>
     
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="none";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
        <?php endif;?>
       document.getElementById("showupload").style.display="block";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="none";
	   <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="none";  
	   <?php endif;?>
      }catch(err){}  
     
   } else if(id=='commercial') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");     
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
      <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("current");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("");
	 <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
     <?php endif;?>
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="none";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
        <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="block";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="none";
	   <?php if($theatreDetails[url]==1): ?>
      document.getElementById("showurlbottom").style.display="none";
	  <?php endif;?>
     }catch(err){}
     
   } else if(id=='overlay') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("");
      <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
     <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("current");
     $("#promotie").removeClass("current").addClass("");
	  <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
	 <?php endif;?>
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="none";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
       <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="block";
       document.getElementById("showpromotie").style.display="none";
	    <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="none";  
	   <?php endif;?>
      }catch(err){}  
     
   } else if(id=='promotie') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
     <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("current");
	  <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current");
   <?php endif;?>
     try{
		 <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="none";
        <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
       <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="block";
	    <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="none";  
	   <?php endif;?>
      }catch(err){}  
   }else if(id=='urlbottom') {
    <?php if($theatreDetails[url]==1): ?>
     $("#urlbottom").removeClass("current").addClass("current");
	 <?php endif;?>
	 <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
     $("#delaycommunications").removeClass("current").addClass("");
     <?php endif;?>
     $("#upload").removeClass("current").addClass("");
     $("#commercial").removeClass("current").addClass("");
     $("#overlay").removeClass("current").addClass("");
     $("#promotie").removeClass("current").addClass("");
     try{
		 <?php if($theatreDetails[url]==1): ?>
       document.getElementById("showurlbottom").style.display="block";  
	   <?php endif;?>
	   <?php if($theatreDetails[is_poster]==0): ?>
       document.getElementById("showvideo").style.display="none";
	   <?php endif;?>
       document.getElementById("showphoto").style.display="none";  
       document.getElementById("showcommunications").style.display="none";
       <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
       document.getElementById("showdelaycommunications").style.display="none";
       <?php endif;?>
       document.getElementById("showupload").style.display="none";
       document.getElementById("showcommercial").style.display="none";
       document.getElementById("showoverlay").style.display="none";
       document.getElementById("showpromotie").style.display="none";
       
      }catch(err){}  
     
   }
   else {
   
   }
 
 }

function pop_permission(){
 alert("U kunt geen overlay maken vanuit een master carrousel.");
}

 function additem(id1,id2,id3) {
  var is_client=<?php echo isset($_GET[is_client]) ? $_GET[is_client] : "0"; ?>;
  if(is_client==1){
    alert("U kunt een master carrousel niet bewerken.");
    return false;
  }
  var vscroll = (document.all ? document.scrollTop : window.pageYOffset);
  
  if(vscroll<=300){
   vscroll =vscroll;
  }else{
   vscroll= vscroll- 250;
  }
  
  document.getElementById("ajax_loader_image").style.marginTop= vscroll+'px';
  document.getElementById("ajax_loader").style.display="block";
  var value_name = document.getElementById("carrouel_name_updates").value;
    $.ajax({  
              type: "POST", url: 'edit_additem.php', data: "name1="+id1+"&name2="+id2+"&name3="+id3+"&status="+<?php echo isset($_GET['status']) ? $_GET['status'] : '';?>+"&is_block="+<?php echo isset($_GET['is_block']) ? $_GET['is_block'] : '0';?>, async: false,
              complete: function(data){
	          //alert(data.responseText);
                  $("#show").html(data.responseText);
		  document.getElementById("ajax_loader").style.display="none";
		  getTotalTime();
		   // removing and re assigning sortable to div( as start/stop callback of sortable was not working after ajax response)
		  $("#contentLeft ul").sortable('destroy');
		  $("#contentLeft ul").unbind();
		  bindSortableInTimeline();
		  $().piroBox_ext({
		  piro_speed : 900,
		  bg_alpha : 0.1,
		  piro_scroll : true //pirobox always positioned at the center of the page
	      });
		  
              }  
          });
    addname3(value_name,id3);

 }
 
 function deleteUpload(id,id1,dir,cid,status,is_client) {
  var is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "0"; ?>;
  if(is_client==1){
    alert("U kunt vanuit een master carrousel geen items verwijderen.");
    return false;
  }
   $ans = confirm('Weet u zeker dat u ' +id +' wilt verwijderen?');
    if($ans){
        switch(dir)
        {
            case "videos" : div = "showvideo"; break;
            case "images" : div = "showphoto"; break;
            case "mededelingen" : div = "showcommunications"; break;
            case "upload" : div = "showupload"; break;
            case "commercials" : div = "showcommercial"; break;
            case "overlay_video" : div = "showoverlay"; break;
        }
    /*$.ajax({  
              type: "POST", url: 'deleteupload.php', data: "delete="+id+"&id="+id1+"&dir="+dir,  
              complete: function(data){
		  
                  $("#"+div).html(data.responseText);
              }  
          }); */
    window.location = 'deleteupload.php?delete='+id+'&id='+id1+'&dir='+dir+'&cid='+cid+'&status='+status;
   }
 }
 
 function deleteUpload(id,id1,dir,cid,status) {
  var is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "0"; ?>;
  if(is_client==1){
    alert("U kunt vanuit een master carrousel geen items verwijderen.");
    return false;
  }
   $ans = confirm('Weet u zeker dat u ' +id +' wilt verwijderen?');
    if($ans){
        switch(dir)
        {
            case "videos" : div = "showvideo"; break;
            case "images" : div = "showphoto"; break;
            case "mededelingen" : div = "showcommunications"; break;
            case "upload" : div = "showupload"; break;
            case "commercials" : div = "showcommercial"; break;
            case "overlay_video" : div = "showoverlay"; break;
	    case "urlbottom" : div = "showurl"; break;
	    case "promotie" : div = "showpromotie"; break;
	    default: div = "showvideo"; break;
        }
     
       $.ajax({  
              type: "post", url: 'check_delete_item.php',data: "delete_item_name="+id+"&dir="+dir+'&edit=1',
	      async: false,
              complete: function(data){ 
                 var response=$.trim(data.responseText);
		 var del=true;
		 if(response>0){
		  del=confirm('Dit item wordt gebruikt in '+response+' carrousel(s), weet u zeker dat u door wilt gaan met verwijderen?');
		 }
		 if(del){
		       $.ajax({  
                         type: "POST", url: 'deleteupload.php', data: "delete="+id+"&id="+id1+"&dir="+dir+'&cid='+cid+'&status='+status,  
                         complete: function(data){ 
                          $("#"+div).html(data.responseText);
                         }  
                       });
		     
		 }
              },
	      error:function(error){
	       alert("Error in XML HTTP REQUEST");
	      }
	      
          });
     
   }
 }
 
 
 
 
 
function addname3(value,carr_id){
   var name = trim(value);
    if(name !=''){
     $.ajax({  
              type: "POST", url: 'edit_additem.php', data: "name_carrousel="+name+"&name3="+carr_id+"&status="+<?php echo isset($_GET['status']) ? $_GET['status'] : '';?>+"&is_block="+<?php echo isset($_GET['is_block']) ? $_GET['is_block'] : '0';?>,  
              complete: function(data){
                  $("#show").html(data.responseText);
		  $("#contentLeft ul").sortable('destroy');
		  $("#contentLeft ul").unbind();
		  bindSortableInTimeline();
		  $().piroBox_ext({
		  piro_speed : 900,
		  bg_alpha : 0.1,
		  piro_scroll : true //pirobox always positioned at the center of the page
	      });
              }  
          });  
     
     
    }
 }
 
 function updatetime(id,value,cid){
  
  if(id!='' && value!=''){
   $.ajax({  
              type: "POST", url: 'edit_additem.php', data: "update_id="+id+"&update_value="+value+"&name3="+cid,  
              complete: function(data){
                  $("#show").html(data.responseText);
		  getTotalTime();
		  $().piroBox_ext({
		  piro_speed : 900,
		  bg_alpha : 0.1,
		  piro_scroll : true //pirobox always positioned at the center of the page
	      });
              }  
          });  
   
  }
 
 }
 function deleteCarrousel(id,cid) {
  var is_client = <?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "0"; ?>;
  if(is_client==1){
    alert('U kunt een master carrousel niet bewerken.');
    return false;
  }
  
    $.ajax({  
              type: "POST", url: 'edit_additem.php', data: "delete="+id+"&name3="+cid,  
              complete: function(data){ 
                  $("#show").html(data.responseText);
		  getTotalTime();
		   // removing and re assigning sortable to div( as start/stop callback of sortable was not working after ajax response)
		  $("#contentLeft ul").sortable('destroy');
		  $("#contentLeft ul").unbind();
		  bindSortableInTimeline();
		  $().piroBox_ext({
		    piro_speed : 900,
		    bg_alpha : 0.1,
		    piro_scroll : true //pirobox always positioned at the center of the page
		});
              }  
          });  
 }
 
 function getTotalTime(){
  
   $.ajax({  
              type: "POST", url: 'calculate_time.php',  
              complete: function(data){
                  $("#carrouel_total_time").val(secondsToHms(data.responseText));
              }  
          });  
   
 
 }
 
function ltrim(str) { 
	for(var k = 0; k < str.length && isWhitespace(str.charAt(k)); k++);
	return str.substring(k, str.length);
}
function rtrim(str) {
	for(var j=str.length-1; j>=0 && isWhitespace(str.charAt(j)) ; j--) ;
	return str.substring(0,j+1);
}
function trim(str) {
	return ltrim(rtrim(str));
}
function isWhitespace(charToCheck) {
	var whitespaceChars = " \t\n\r\f";
	return (whitespaceChars.indexOf(charToCheck) != -1);
}

function secondsToHms(d) {
	d = Number(d);
	var h = Math.floor(d / 3600);
	var m = Math.floor(d % 3600 / 60);
	var s = Math.floor(d % 3600 % 60);
	return ((h > 0 ? h + ":" : "") + (m > 0 ? (h > 0 && m < 10 ? "0" : "") + m + ":" : "0:") + (s < 10 ? "0" : "") + s);
}

function autosave(id) {
  var is_block = <?php echo isset($_GET['is_block']) ? $_GET['is_block'] : '0'; ?>;
   var letters = /^[a-zA-Z0-9 ]+$/;  
   var cname=document.getElementById('carrouel_name_updates').value;
   var c_totaltime=document.getElementById('carrouel_total_time').value;
   var hidden_cid=document.getElementById('carr_id').value;
   var cra_status=document.getElementById('cra_status').value;
   var dbAcessError="Error in database access.";
   c_totaltime = c_totaltime.split(':');
   // to fix bugs of  ajax call fail on slow network conn
   if(c_totaltime[1]==undefined){
    c_totaltime[1]=00;
   }
   if((c_totaltime[0]==0 && c_totaltime[1]==00) && cname!="" && is_block!=1) {
    alert('U kunt geen lege carrousel opslaan. Verwijder de naam van de carrousel of voeg een item toe.');
   }else if(c_totaltime[0]==0 && c_totaltime[1]==00 && is_block!=1) {
    if(id==1) {
       window.location='welcome.php';
      } else if(id==2) {
       window.location='create_carrousel.php';
      } else if(id==3) {
       window.location='upload_videoimage.php';
      } else if(id==4) {
       window.location='twitter_overlay.php';
      } else if(id==5) {
       
	window.location='mededelingen_overlay.php';
      } else if(id==6) {
       window.location='logout.php';
      } else if(id==7) {
	   window.location='url.php';
      } else if(id==8) {
   window.location='delay_mededelingen_overlay.php';
      } else if(data.responseText==500) {
	//alert(data.responseText);
	alert(dbAcessError);
      } else{
        alert(data.responseText);
      }
      
   }else if(cname=="" && is_block!=1) {
     alert('U dient de carrousel een naam te geven.'); 
   } else if(cname!="" && cname.search(letters)==-1 && is_block!=1) {
     alert('U kunt alleen letters en cijfers in uw carrousel naam gebruiken (dus geen speciale karakters of interpunctie).'); 
   } else {
    /*count no of commercials add */
       
	 var erang1=<?php echo $erang1; ?>;
	 var avro=<?php echo $avro; ?>;
	 var valid_add = document.getElementById("commercial_check").value;
	 var check = valid_add.split('*');
	 var count_commercial = 0;
	 var count_commercial_1erang=0;
	 var count_commercial_avro=0;
	 for(var k=1;k<check.length;k=k+1)
	 {   try{
	       var category = check[k].split('/');
	     }catch(err){
	      category=Array("");
	     }
	     try{
	        var valid_file = category[1].split('_');
	     }catch(err){
	      valid_file=Array("");
	     }
	   
	     if(erang1==1 && avro==1) {
	       if(category[0]=='commercials' && valid_file[0]=='1eRang') {
		count_commercial_1erang++;
	       }
	       if(category[0]=='commercials' && valid_file[0]=='AVRO') {
		count_commercial_avro++;
	       }
	     } else if(erang1==1 && avro==0) {
	       if(category[0]=='commercials' && valid_file[0]=='1eRang')
	       count_commercial_1erang++;
	     } else if(erang1==0 && avro==1) {
	       if(category[0]=='commercials' && valid_file[0]=='AVRO')
	       count_commercial_avro++;
	     } else {
	      
	     }
	 }
	 count_commercial=count_commercial_1erang+count_commercial_avro;
	 /*end here*/
	 
	 var sec = trim(document.getElementById("carrouel_total_time").value);
	 sec = sec.split(':')
	 var min = Math.floor(sec[1] / 60);
	 var min = parseInt(sec[0]) + parseInt(min);
	
	 if(sec!=0 && min>=2){
	   var occur = Math.floor(min / 6);
	   occur = occur + 1;
	
	   if((occur > count_commercial_1erang) && (erang1==1 && avro==0))
	   {
	     var ans = confirm('U heeft '+count_commercial_1erang+' 1eRang station call(s) toegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' station call(s) te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
	     if(ans==false) {
	      return false;
	     } else {
	       $.ajax({  
	       type: "POST", url: 'autosave_edit.php', data: "id="+id+"&cname="+cname+"&carr_id="+hidden_cid+"&cra_status="+cra_status+"&is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "" ; ?>"+"&is_block=<?php echo $_GET['is_block']; ?>",  
	       complete: function(data){
		if(data.responseText==1) {
		 window.location='welcome.php';
		} else if(data.responseText==3) {
		 window.location='upload_videoimage.php';
		} else if(data.responseText==4) {
		 window.location='twitter_overlay.php';
		} else if(data.responseText==5) {
		 window.location='mededelingen_overlay.php';
		} else if(data.responseText==6) {
		 window.location='logout.php';
		} else if(data.responseText==7) {
		   window.location='url.php';
	  } else if(data.responseText==8) {
	  window.location='delay_mededelingen_overlay.php';
		} else if(data.responseText==500) {
		  alert(dbAcessError);
		} else{
		  alert(data.responseText);
		}
	       }  
	       });
	     }
	   } else if ((occur > count_commercial_avro) && (erang1==0 && avro==1)) {
	    var ans = confirm('U heeft '+count_commercial_avro+' AVRO spot(s) toegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' spot(s) te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
	    if(ans==false) {
	      return false;
	     } else {
	       $.ajax({  
	       type: "POST", url: 'autosave_edit.php', data: "id="+id+"&cname="+cname+"&carr_id="+hidden_cid+"&cra_status="+cra_status+"&is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "" ; ?>"+"&is_block=<?php echo $_GET['is_block']; ?>",
	       complete: function(data){
		if(data.responseText==1) {
		 window.location='welcome.php';
		} else if(data.responseText==3) {
		 window.location='upload_videoimage.php';
		} else if(data.responseText==4) {
		 window.location='twitter_overlay.php';
		} else if(data.responseText==5) {
		 window.location='mededelingen_overlay.php';
		} else if(data.responseText==6) {
		 window.location='logout.php';
		} else if(data.responseText==7) {
		   window.location='url.php';
	} else if(data.responseText==8) {
	 window.location='delay_mededelingen_overlay.php';
		} else if(data.responseText==500) {
		  alert(dbAcessError);
		} else{
		  alert(data.responseText);
		}
	       }   
	       });
	     }
	   } else if ((occur > count_commercial_1erang) && (erang1==1 && avro==1)) {
	    var ans = confirm('U heeft '+count_commercial_1erang+' 1eRang spot(s) en '+count_commercial_avro+' AVRO spot(s) toegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' spot(s) en '+ occur +' AVRO spot(s) te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
	    if(ans==false) {
	      return false;
	     } else {
	       $.ajax({  
	       type: "POST", url: 'autosave_edit.php', data: "id="+id+"&cname="+cname+"&carr_id="+hidden_cid+"&cra_status="+cra_status+"&is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "" ; ?>"+"&is_block=<?php echo $_GET['is_block']; ?>",
	       complete: function(data){
		if(data.responseText==1) {
		 window.location='welcome.php';
		} else if(data.responseText==3) {
		 window.location='upload_videoimage.php';
		} else if(data.responseText==4) {
		 window.location='twitter_overlay.php';
		} else if(data.responseText==5) {
		 window.location='mededelingen_overlay.php';
		} else if(data.responseText==6) {
		 window.location='logout.php';
		} else if(data.responseText==7) {
		   window.location='url.php';
	} else if(data.responseText==8) {
	   window.location='delay_mededelingen_overlay.php';
		} else if(data.responseText==500) {
		  alert(dbAcessError);
		} else{
		  alert(data.responseText);
		}
	       }  
	       });
	     }
	   } else if ((occur > count_commercial_avro) && (erang1==1 && avro==1)) {
	    var ans = confirm('U heeft '+count_commercial_1erang+' 1eRang spot(s) en '+count_commercial_avro+' AVRO spot(s) toegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' spot(s) en '+ occur +' AVRO spot(s) te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
	    if(ans==false) {
	      return false;
	     } else {
	       $.ajax({  
	       type: "POST", url: 'autosave_edit.php', data: "id="+id+"&cname="+cname+"&carr_id="+hidden_cid+"&cra_status="+cra_status+"&is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "" ; ?>"+"&is_block=<?php echo isset($_GET['is_block']) ? $_GET['is_block'] : '0'; ?>", 
	       complete: function(data){
		if(data.responseText==1) {
		 window.location='welcome.php';
		} else if(data.responseText==3) {
		 window.location='upload_videoimage.php';
		} else if(data.responseText==4) {
		 window.location='twitter_overlay.php';
		} else if(data.responseText==5) {
		if(current_page!=undefined)
		window.location='mededelingen_overlay.php';
		} else if(data.responseText==6) {
		 window.location='logout.php';
	} else if(data.responseText==7) {
	   window.location='url.php';
} else if(data.responseText==8) {
   window.location='delay_mededelingen_overlay.php';
		} else if(data.responseText==500) {
		  alert(dbAcessError);
		} else{
		  alert(data.responseText);
		}
	       }    
	       });
	     }
	   }
	   else {
	     $.ajax({  
	       type: "POST", url: 'autosave_edit.php', data: "id="+id+"&cname="+cname+"&carr_id="+hidden_cid+"&cra_status="+cra_status+"&is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "" ; ?>"+"&is_block=<?php echo isset($_GET['is_block']) ? $_GET['is_block'] : '0'; ?>",  
	       complete: function(data){
		if(data.responseText==1) {
		 window.location='welcome.php';
		} else if(data.responseText==3) {
		 window.location='upload_videoimage.php';
		} else if(data.responseText==4) {
		 window.location='twitter_overlay.php';
		} else if(data.responseText==5) {
		window.location='mededelingen_overlay.php';
		} else if(data.responseText==6) {
		 window.location='logout.php';
	} else if(data.responseText==7) {
	   window.location='url.php';
} else if(data.responseText==8) {
   window.location='delay_mededelingen_overlay.php';
		} else if(data.responseText==500) {
		  alert(dbAcessError);
		} else{
		  alert(data.responseText);
		}
	       }  
	       });
	   }
	   
	 }
	 else{
	  
	  $.ajax({  
	       type: "POST", url: 'autosave_edit.php', data: "id="+id+"&cname="+cname+"&carr_id="+hidden_cid+"&cra_status="+cra_status+"&is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "" ; ?>"+"&is_block=<?php echo isset($_GET['is_block']) ? $_GET['is_block'] : '0'; ?>",  
	       complete: function(data){
		if(data.responseText==1) {
		 window.location='welcome.php';
		} else if(data.responseText==3) {
		 window.location='upload_videoimage.php';
		} else if(data.responseText==4) {
		 window.location='twitter_overlay.php';
		} else if(data.responseText==5) {
		window.location='mededelingen_overlay.php';
		} else if(data.responseText==6) {
		 window.location='logout.php';
	} else if(data.responseText==7) {
	   window.location='url.php';
} else if(data.responseText==8) {
   window.location='delay_mededelingen_overlay.php';
		} else if(data.responseText==500) {
		  alert(dbAcessError);
		} else{
		  alert(data.responseText);
		}
	       }   
	       });
	}
    
   }
}

function editImage(imageFrom,imageName,carrouselId) {
  var is_client=<?php echo isset($_GET['is_client']) ? $_GET['is_client'] : "0"; ?>;
  if(is_client==1){
    alert("U kunt vanuit een master carrousel geen items bewerken.");
    return false;
  }
   $ans = confirm('Wilt u deze mededeling/overlay bewerken?');
    if($ans){
      
    $.ajax({  
              type: "POST", url: 'checkEditableStatus.php',data: "image_from="+imageFrom+"&image_name="+imageName,  
              complete: function(data){ 
               var responseData=data.responseText;
	       try
	       {
		 responseInJson=$.parseJSON(responseData);
                 // if any conditional error occurs at server(old file,db in conssitency,file-deltion etc)
		 if(responseInJson['error']!=undefined){
		    alert(responseInJson['error']);
		 }
		 else if(responseInJson['type']=="mededelingen" && responseInJson['id']!=""){
		    var mededelingen_id=responseInJson['id'];
		    var template_id=responseInJson['template_id'];
		    var created_file_name=responseInJson['created_file_name'];
		    if(template_id==""){
		      template_id=1;
		    }
		    window.location = 'mededelingen_overlay.php?carrousel_id='+carrouselId+'&request_for=template_edit&temp_mededelingen_id='+mededelingen_id+'&template_id='+template_id+'&edit_image_name='+created_file_name+"&come_from=med_edit&type=edit&file=edit&status=<?php echo $_GET['status']?>&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>";
		 }
		 else if(responseInJson['type']=="overlays_image" && responseInJson['id']!=""){
		    var created_file_name=responseInJson['created_file_name'];
		    var image_overlay_id=responseInJson['id'];
		    var selected_file_path=responseInJson['selected_file_path'];
		    var carrousel_id=responseInJson['carrousel_id'];
		    window.location = 'createimage_overlay.php?path='+selected_file_path+'&cid='+carrouselId+'&id=&edit_image_name='+created_file_name+"&method=edit&temp_overlay_id="+image_overlay_id+"&type=edit&status=<?php echo $_GET['status']?>&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>";
		  //path=images/image2.jpg&cid=&edit_image_name=image2_4923.png&id=2096&method=edit&temp_overlay_id=18
		 }
		 else if(responseInJson['type']=="overlays_video" && responseInJson['id']!=""){
		    var created_file_name=responseInJson['created_file_name'];
		    var video_overlay_id=responseInJson['id'];
		    var selected_file_path=responseInJson['selected_file_path'];
		    var carrousel_id=responseInJson['carrousel_id'];
		    window.location = 'create_overlay.php?path='+selected_file_path+'&cid='+carrouselId+'&id=&edit_image_name='+created_file_name+"&method=edit&temp_overlay_id="+video_overlay_id+"&type=edit&status=<?php echo $_GET['status']?>&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>";
		 }
		 // if some unexpected happen (case is not taken into considration)
		 else{
		    alert('something went wrong...');
		 }
		 
	       }// if data received from server is not json compatable
	       catch(err)
	       {
		alert('something went wrong...');
	       }
              }  
          });
   
   }
 }
 

 var newwindow;
function popit_url(url) {
    if(newwindow!=undefined){
      newwindow.close()
    }
     newwindow=window.open(url,'Preview','height=800,width=1000,left=190,top=150,screenX=200,screenY=100');
}
</script>
<body onload="getTotalTime()">
<!--------------- include header --------------->
   <?php include('header_carrousel.html'); ?>
    
    
        <!-- START MAIN CONTAINER -->
    <!-- START MAIN CONTAINER -->
   <div class="main_container">
   	<div class="content">
		<?php if ($_GET['is_block']==1):?>
        <span class="title">Client content block bewerken</span>
              <p>Hieronder kunt u uw client content block bewerken. De inhoud hiervan wordt aan de inhoud van de master carrousel toegevoegd.</p>
			  <p>Indien u een item wilt gebruiken in uw block klikt u op "Toevoegen". Hierna verschijnt het item in de tijdlijn. Items in de tijdlijn kunt u rangschikken door middel van 'drag and drop'. Voor afbeeldingen en URL's dient u een tijdsduur in te vullen.</p>
        <p>Indien u een informatiebalk over een foto of een video wilt plaatsen voegt u een item eerst toe aan de tijdlijn en klikt u daarna op "Tekst toev.". Aangemaakte informatiebalken worden geplaatst onder het tabblad "Overlays" en kunt u vanaf daar opnieuw plaatsen.</p>
 	   <p>Aangemaakte mededelingen en overlays kunt u bewerken door op het bewerk symbool in de linkerbovenhoek te klikken</p>
	<?php elseif ($_GET['is_client']==1): ?>
		<span class="title">Master carrousel bekijken</span>
              <p>Op deze pagina kunt u de inhoud van de master carrousel bekijken. Het is niet mogelijk om deze carrousel te bewerken.</p>
	<?php else: ?>
		 <span class="title">Carrousel bewerken</span>
                 <p>Hieronder kunt u een bestaande carrousel bewerken. Indien u een item wilt gebruiken in de carrousel klikt u op "Toevoegen". Hierna verschijnt het item in de tijdlijn. Items in de tijdlijn kunt u rangschikken door middel van 'drag and drop'. Voor afbeeldingen en URL's dient u een tijdsduur in te vullen.</p>
           <p>Indien u een informatiebalk over een foto of een video wilt plaatsen voegt u een item eerst toe aan de tijdlijn en klikt u daarna op "Tekst toev.". Aangemaakte informatiebalken worden geplaatst onder het tabblad "Overlays" en kunt u vanaf daar opnieuw in een carrousel plaatsen.</p>
    	   <p>Aangemaakte mededelingen en overlays kunt u bewerken door op het bewerk symbool in de linkerbovenhoek te klikken</p>
	 <?php endif ?>
             
    
	 <div  class="global_item_search_box_container">
	   <span class="deleteicon">
        <input  id="comman_search" type="text" placeholder="Zoeken" class="item_search_box" style="margin-top:20px;">
	 <span id="global_search_clear"></span>
	 </span>	  
	</div>
	
	</div>
       <!-- VERTICAL GALLERY STARTS -->
        <?php
	#add by Himani Agarwal
	#purpose : class current on selected div
	if($_GET['dir'] && $_GET['dir']!=''){
	 $folder = $_GET['dir'];
	}
	elseif ($theatreDetails[is_poster]==1){
	 $folder = 'images';
	}
	
	else {
		$folder = 'videos';
	}
	?>
        <div class="vertical_gallery">
	 <div id="ajax_loader" align="center" style="text-align:center;margin-left:440px; z-index:99999;position:absolute;display :none"><img id="ajax_loader_image" src="img/loader_transparant.gif"></div>
        	<div class="tabs">
            	<ul>
					<?php if($theatreDetails[is_poster]==0): ?>
		 <?php if($folder=='videos')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="video" onclick="showhidediv('video')"><?php echo $directoryMappings[$actualDirectory['videos']];?></a></li>
				  <?php endif;?>
		  <?php if($folder=='images')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="photo" onclick="showhidediv('photo')"><?php echo $directoryMappings[$actualDirectory['images']];?></a></li>
		  <?php if($folder=='mededelingen')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="communications" onclick="showhidediv('communications')"><?php echo $directoryMappings[$actualDirectory['mededelingen']];?></a></li>
		  <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
		  <?php if($folder=='delay_mededelingen')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="delaycommunications" onclick="showhidediv('delay_communications')"><?php echo $directoryMappings[$actualDirectory['delay_mededelingen']];?></a></li>
                  <?php endif; ?>
		  <?php if($folder=='promotie')  $class='current'; else $class='';?>
		  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="promotie" onclick="showhidediv('promotie')"><?php echo $directoryMappings[$actualDirectory['promotie']];?></a></li>
		  <?php if($folder=='upload')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="upload" onclick="showhidediv('upload')"><?php echo $directoryMappings[$actualDirectory['upload']];?></a></li>
		  <?php if($folder=='commercials')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="commercial" onclick="showhidediv('commercial')"><?php echo $directoryMappings[$actualDirectory['commercials']];?></a></li>
		   <?php if($theatreDetails[url]==1): ?>
		  <?php if($folder=='urlbottom')  $class='current'; else $class='';?>
		   <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="urlbottom" onclick="showhidediv('urlbottom')">URL's</a></li>
		<?php endif; ?>
		  <?php if($folder=='overlay_video')  $class='current'; else $class='';?>
		  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="overlay" onclick="showhidediv('overlay')"><?php echo $directoryMappings[$actualDirectory['overlays']];?></a></li>
                </ul>
            </div>
            <?php if($theatreDetails[is_poster]==0): ?>
	     <?php if($folder=='videos')  $display='block'; else $display='none';?>
            <div class="blocks_container" id="showvideo" style="display:<?php echo $display;?>;">
               <?php echo $videos_listing; ?>
            	<div class="clear"></div>
            </div>
			<?php endif;?>
             <?php if($folder=='images')  $display='block'; else $display='none';?>
            <div class="blocks_container" id="showphoto" style="display:<?php echo $display;?>;">
               <?php echo $images_listing; ?>
            	<div class="clear"></div>
            </div>
             <?php if($folder=='mededelingen')  $display='block'; else $display='none';?>
            <div class="blocks_container" id="showcommunications" style="display:<?php echo $display;?>;">
                <?php echo $medede_listing; ?>
            	<div class="clear"></div>
            </div>
	    
	    <?php if($theatreDetails[is_delay]==1 && $master_data['is_master']==0):?>
	      <?php if($folder=='delay_mededelingen')  $display='block'; else $display='none';?>
            <div class="blocks_container" id="showdelaycommunications" style="display:<?php echo $display;?>;">
                <?php echo $medede_listing_delay; ?>
            	<div class="clear"></div>
            </div>
	    <?php endif; ?>
             <?php if($folder=='upload')  $display='block'; else $display='none';?>
            <div class="blocks_container" id="showupload" style="display:<?php echo $display;?>;">
               <?php echo $videoimage_listing; ?>
            	<div class="clear"></div>
            </div>
	     <?php if($folder=='commercials')  $display='block'; else $display='none';?>
	    <div class="blocks_container" id="showcommercial" style="display:<?php echo $display;?>;">
               <?php echo $comme_listing; ?>
            	<div class="clear"></div>
            </div>
	     <?php if($folder=='overlay_video')  $display='block'; else $display='none';?>
	    <div class="blocks_container" id="showoverlay" style="display:<?php echo $display;?>;">
               <?php echo $overlay_listing; ?>
            	<div class="clear"></div>
            </div>
            <?php if($folder=='promotie')  $display='block'; else $display='none';?>
	    <div class="blocks_container" id="showpromotie" style="display:<?php echo $display;?>;">
               <?php echo $promotie_listing; ?>
            	<div class="clear"></div>
            </div>
			<?php if($theatreDetails[url]==1): ?>
	   <?php if($folder=='urlbottom')  $display='block'; else $display='none';?>
	    <div class="blocks_container" id="showurlbottom" style="display:<?php echo $display;?>;">
               <?php echo $url_listing; ?>
            	<div class="clear"></div>
            </div>
            <?php endif; ?>
        </div>
        <!-- INPUT FIELD START -->
       <form name="c_create" action="edit.php" method="POST" onsubmit='return formsubmit();'>
        <div style=" margin-top:15px;"><span style="color:red; font-family:Adelle Basic; font-size:17px;"><b><?php echo $error_message; ?></b></span></div>
        <div class="search_field">
            <p class="form_title">Naam carrousel</p>
			<?php if ($_GET['is_client']==1): ?>
			<input type="text" name="cr_name" value="<?php echo stripcslashes($carrouel_name); ?>" class="input_field" onblur="addname3(this.value);" id="carrouel_name_updates" disabled/>
		<?php else: ?>
            <input type="text" name="cr_name" value="<?php echo stripcslashes($carrouel_name); ?>" class="input_field" onblur="addname3(this.value);" id="carrouel_name_updates"/>
		<?php endif; ?>
	    <span class="form_title2">Lengte carrousel: <input type="text" name="cr_totaltime" value="0" id='carrouel_total_time' class="input_field3"/>&nbsp;</span>
        </div>
        
        <div class="horizon_gallery2">
        <div class="horizon_blocks_cont" id="show" style="width: 940px;"><div id='contentLeft'><ul>
          <?php echo $carrousel_listing; ?>

            </ul></div><div class="clear">&nbsp;</div>
         </div>
          <div class="clear">&nbsp;</div>
        </div>
        <div id="result"  style="display:none"></div>
   		<!-- START FOOTER -->
    	<div class="footer">
          <div class="footer_tab"><input type="hidden" name="carr_id" id="carr_id" value="<?php echo $carrouselid; ?>">
          <input type="hidden" name="cra_status" id="cra_status" value="<?php echo $status; ?>">
          <input type="hidden" name="hiddenname" value="<?php echo stripslashes($carrouel_name) ; ?>">
	 
          <a id="overzicht" href="javascript:void(0)" onclick="autosave('1')"><input type="button" name="submit" value="Naar het overzicht"></a></div>
	  
        </div>
     </form>
    	<!-- END FOOTER -->
    </div>



<div  class="search_result_background" 	style="display:none;height:500px; opacity:0.6;position:absolute;" >
</div>
<div id='search_result_content' class="main_container" style="display:none;top:0px;width: 946px; height:500px; position:absolute;z-index:1234;padding: 20px 0px 0px 0px;margin-left: 0px;overflow-y:auto;" >
 <div class="vertical_gallery" style="width: 935px;">
 <div class="blocks_container" id="blocks_container_comman_search" style="display: block;width:935px;">

          
	    
	      
  
       </div>
   </div>


</div>
</body>
</html>
