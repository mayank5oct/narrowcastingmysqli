<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(1);
ini_set('max_execution_time', 3600);
ini_set("memory_limit","12M");


include('config/database.php');
include('color_scheme_setting.php');

$close_button_path="./img/$color_scheme/close_btn.png";
$edit_button_path= "./img/$color_scheme/edit02.png";
$video_button_path= "./img/$color_scheme/video.png";
$preview_button_path="./img/$color_scheme/preview_icon.png";
//$calender_button_path="./img/$color_scheme/cal.gif";
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 //header('Location:  index.php');
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}
 
 $db = new Database;
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
$theatreDetails = mysqli_fetch_array($theatreDetails);
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

## get master status
$master_query = "select is_master from narrowcasting_folder";
$master_set = $db->query($master_query);
$master_data = mysqli_fetch_array($master_set);

//echo "<pre>";
//print_r($directoryMappings);



//--------------------------- used for saving new carroussel ------------------------------- 
 $error_message="";
 $sid=session_id();
 $carrouel_name="";
 $overlaystatus=$_GET['status'];
 if($_POST['submit']=="" && $overlaystatus!=2) {
    //----------------- delete tmp table for this session ----------------------------
   $delete_tmp="delete from temp_carrousel where status='1'";
   $db->query($delete_tmp);
 }
 
 //------ code for commercial check with database -------------------------------------------
$comm_query="select * from theatre where status=1";
$comm_result=$db->query($comm_query);
$comm_res=mysqli_fetch_array($comm_result);
$erang1=$comm_res['1erang'];
$avro=$comm_res['avro'];
 
 
 
 //--------------------------- used for fetching video and img from upload folder -------------------------------
 function listdir_by_date($path, $is_sort){
    $dir = opendir($path);
    $list = array();
    while($file = readdir($dir)){
	if ($file != '.' and $file != '..'){
	    // add the filename, to be sure not to
	    // overwrite a array key
	    $ctime = filemtime($path . $file) . ',' . $file;
	    
	    $list[$ctime] = $file;
	}
    }
    closedir($dir);
   // echo "<pre>"; print_r($list); echo "</pre>";
    if($is_sort == 1){
      krsort($list);
    }else{
        sort($list);
    }
    

    return $list;
  }
  $videoimage_listing="";
  
  if ($handle = listdir_by_date("/var/www/html/narrowcasting/upload/", $db->sort_by_date)) {
//echo "<pre>"; print_r($handle); echo "</pre>";
    $i=1;
    // while (false !== ($entry = readdir($handle))) {
    foreach($handle as $key=>$entry){
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".DS_Store" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
         if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./upload/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"upload\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./upload/$entry?time=".time()."' rel='single'  class='pirobox'>
	   
	   <div class='btn2'>Preview</div></a>
	   <div class='close_btn_new'><a href='javascript:void(0);' >
	   <img src='$close_button_path' alt='' onclick='deleteUpload(\"$entry\",1,\"upload\",\"$carrouselid\",\"$status\")' class='upload_delete'>
	   </a></div>
	   
	
	   <div class='checkbox_class'>
	   <span style='display:none;' class='upload_check_box'>
	      <input type='checkbox' class='upload_check_box' id='overlay_$l' name='overlay_$l' value='$entry'/>
	      </span>
	   </div>
	
	<div class='clear'></div>";
	   
	   
	   
	   
	   
         } else if($entry1[1]=="mov" or $entry1[1]=="mp4"){
           $itemname="<div class='block_img'><img src='./upload/thumb/".str_replace(" ","_",$entry1[0]).".jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"upload\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"upload\",\"$entry2\")'>
	   <div class='btn2'>Preview</div></a>
	   <div class='close_btn_new'>
	   <a href='javascript:void(0);' ><img src='$close_button_path' alt='' onclick='deleteUpload(\"$entry\",2,\"upload\",\"$carrouselid\",\"$status\")' class='upload_delete'></a></div>
	  
	   <div class='checkbox_class'>
	   <span style='display:none;' class='upload_check_box'>
	      <input type='checkbox' class='upload_check_box' id='overlay_$l' name='overlay_$l' value='$entry'/>
	      </span>
	   </div>
	   <div class='clear'></div>";
         } else {
          
         }
        $videoimage_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";

      }
      $i++;
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
        while($url_row =mysqli_fetch_array($url_data)){
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
 
 //--------------------------- used for fetching video and img from upload folder ------------------------------- 
 // $videoimage_listing="";
//  if ($handle = opendir("./upload")) {
 //   $i=1;
 //    while (false !== ($entry = readdir($handle))) {
 //      if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".DS_Store" and $entry!=".gitignore") {
  //      $entry1=explode('.',$entry);
 //       $entry2=urlencode($entry);
 //        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
 //          $itemname="<div class='block_img'><img src='./upload/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"upload\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./upload/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",1,\"upload\",\"$carrouselid\",\"$status\")'>
//	   <img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
   //      } else if($entry1[1]=="mov" or $entry1[1]=="mp4"){
 //          $itemname="<div class='block_img'><img src='./upload/thumb/".str_replace(" ","_",$entry1[0]).".jpg?time=".time()."' width='64' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"upload\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"upload\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' onclick='deleteUpload(\"$entry\",2,\"upload\",\"$carrouselid\",\"$status\")'><img src='$close_button_path' alt=''></a></div><div class='clear'></div>";
    //     } else {
   //       
     //    }
    //    $videoimage_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
	//
   //   }
   //  }
   //  $search_block='<div  class="item_search_box_container">
    //                 <input  id="upload_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';
  //   $videoimage_listing=$search_block.$videoimage_listing;
   //  closedir($handle);
  //   unset($itemname);
 //  }
   
  //--------------------------- used for fetching video and img from overlay folder -------------------------------
  

  $overlay_listing="";
  //echo "sort_by_date Value : ".$db->sort_by_date;
  //exit;
  
  
  if ($handle1 = listdir_by_date("/var/www/html/narrowcasting/overlay_video/", $db->sort_by_date)) {
   // echo "<pre>"; print_r($handle1); echo "</pre>";
   // exit;
    $l=1;
      foreach($handle1 as $key=>$entry){
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".DS_Store" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
	$newname=explode("_",$entry1[0]);
	$data=htmlentities($entry);
	$entry=$entry;
         if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
    
           $itemname="<div class='block_img'><img src='./overlay_video/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($newname[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"overlay_video\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./overlay_video/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	    <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"overlays_image\",\"$data\",\"0\")'>
	   <img src='$edit_button_path' alt='' width='20' height='20'></a></div>
    
	   <div class='close_btn_near_edit'>
	      <a href='javascript:void(0);' >
	      
	      <img class='overlay_video_delete' onclick='deleteUpload(\"$entry\",1,\"overlay_video\",\"$carrouselid\",\"$status\")' src='$close_button_path' alt=''></a>
	   </div>
	  <div class='checkbox_class'>
	   <span style='display:none;' class='overlay_check_box'>
	      <input type='checkbox' class='overlay_check_box' id='overlay_$l' name='overlay_$l' value='$entry'/>
	      </span>
	  </div>    
	   <div class='clear'></div>";
         
	 } else if($entry1[1]=="mov" or $entry1[1]=="mp4"){
	  $entry_name_overlay=$entry1[0].'.jpg';
	    
           $itemname="<div class='block_img'><img src='./overlay_video/thumb/".str_replace(" ","_",$entry_name_overlay)."?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($newname[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"overlay_video\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);'
	   onclick='popitup(\"overlay_video\",\"$entry2\")'><div class='btn2'>Preview</div></a>
	  
	  <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"overlay_video\",\"$data\",\"0\")'>
	   <img src='$edit_button_path' alt='' width='20' height='20'></a></div>
	   
	   <div class='close_btn_near_edit'>
	      <a href='javascript:void(0);' >
	      <img class='overlay_video_delete' onclick='deleteUpload(\"$entry\",1,\"overlay_video\",\"$carrouselid\",\"$status\")' src='$close_button_path' alt=''>
	     
	      </a>
	   </div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='overlay_check_box'>
	      <input type='checkbox' class='overlay_check_box' id='overlay_$l' name='overlay_$l' value='$entry'/>
	      </span>
	  </div>    
	   <div class='clear'></div>";
         } else {
          
         }
        $overlay_listing.="<div class='block' id='start-".substr($newname[0],0,35)."'>$itemname</div>";
	

      }
      $l++;
     }
     $search_block='<div class="item_search_box_container">
                    <input  id="overlay_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';
     $overlay_listing=$search_block.$overlay_listing;
     closedir($handle1);
     unset($itemname);
   }
   
  //--------------------------- used for fetching images from images folder ------------------------------- 
  $images_listing="";
  if ($handle2 = listdir_by_date("/var/www/html/narrowcasting/images/", $db->sort_by_date)) {
    $i=1;
     foreach($handle2 as $key=>$entry){
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="preview" and $entry!=".DS_Store"  and $entry!="thumb" and $entry!="totalthumb" and $entry!="_Archived Items" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./images/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"images\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./images/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' ><img class='image_delete' onclick='deleteUpload(\"$entry\",1,\"images\",\"$carrouselid\",\"$status\")' src='$close_button_path' alt=''></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='image_check_box'><input type='checkbox' class='image_check_box' id='image_$i' name='image_$i' value='$entry2'/></span>
	   </div>
	   <div class='clear'></div>";
         } else {
           $itemname="<div class='block_img'><img src='$video_button_path' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"images\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='#'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' ><img class='image_delete' onclick='deleteUpload(\"$entry\",1,\"images\",\"$carrouselid\",\"$status\")' src='$close_button_path' alt=''></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='image_check_box'><input type='checkbox' class='image_check_box' id='image_$i' name='image_$i' value='$entry2'/></span>
	   </div>
	   <div class='clear'></div>";
         }
        $images_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
      $i++;
     }
     $search_block='<div class="item_search_box_container">
                     <input  id="photo_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';
		     
     $images_listing=$search_block.$images_listing;
     closedir($handle2);
     unset($itemname);
   }
   
  //--------------------------- used for fetching video from video folder ------------------------------- 
  $videos_listing="";
  if ($handle3 = listdir_by_date("/var/www/html/narrowcasting/videos/", $db->sort_by_date)) {
    $j=1;
     foreach($handle3 as $key => $entry){
       if($entry!="" and $entry!="." and $entry!=".." and $entry!=".DS_Store" and $entry!="preview" and $entry!="thumb" and $entry!="totalthumb" and $entry!="_Archived Items" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        $itemname="<div class='block_img'><img src='./videos/thumb/$entry1[0].jpg?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"videos\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"videos\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' ><img onclick='deleteUpload(\"$entry\",2,\"videos\",\"$carrouselid\",\"$status\")' class='video_delete' src='$close_button_path' alt=''></a></div>
	<div class='checkbox_class'>
	<span style='display:none;' class='video_check_box'><input class='video_check_box' type='checkbox' id='video_$j' name='video_$j' value='$entry2'/></span>
	</div>
	<div class='clear'></div>";
        $videos_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
      $j++;
     }
      $search_block='<div  class="item_search_box_container">
                     <input  id="video_search" type="text" placeholder="Search" class="item_search_box"></div>';
      $videos_listing=$search_block.$videos_listing;
     closedir($handle3);
     unset($itemname);
   }
   
 //--------------------------- used for fetching video from Mededelingen folder ------------------------------- 
  $medede_listing="";
  if ($handle4 = listdir_by_date("/var/www/html/narrowcasting/mededelingen/", $db->sort_by_date)) {
    $k=1;
     
     foreach($handle4 as $key => $entry){
      $entry =str_replace(" ?","__",$entry);
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="preview" and $entry!=".DS_Store" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
	$data=htmlentities($entry);
	$entry = $entry;
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
	  if(substr($entry1[0],0,7)=="Twitter") {
	   $twitter_name=explode("_",$entry1[0]);
	   $itemname="<div class='block_img'><img src='./mededelingen/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("-",'"',$twitter_name[0])."</p><a href='javascript:void(0);' onclick='additem(\"mededelingen\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./mededelingen/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	    
	   <div class='close_btn_new'><a href='javascript:void(0);' >
	   <img class='mededelingen_delete' onclick='deleteUpload(\"$entry\",1,\"mededelingen\",\"$carrouselid\",\"$status\")' src='$close_button_path' alt=''></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='mededelingen_check_box'><input type='checkbox' class='mededelingen_check_box' id='mededelingen_$k' name='mededelingen_$k' value='$entry2'/></span>
	   </div>
	   <div class='clear'></div>";
	  } else {
           $itemname="<div class='block_img'><img src='./mededelingen/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"mededelingen\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./mededelingen/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	   <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"mededelingen\",\"$data\",\"0\")'>
	   <img src='$edit_button_path' alt='' width='20' height='20'></a></div>
	   
	   <div class='close_btn_near_edit'>
	   <a href='javascript:void(0);' >
	   <img class='mededelingen_delete' onclick='deleteUpload(\"$entry\",1,\"mededelingen\",\"$carrouselid\",\"$status\")' src='$close_button_path' alt=''></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='mededelingen_check_box'><input type='checkbox' class='mededelingen_check_box' id='mededelingen_$k' name='mededelingen_$k' value='$entry'/></span>
	   </div>
	   <div class='clear'></div>";
	  }
         } else {
	  $entry_image_a=$entry1[0].'.jpg';
           $itemname="<div class='block_img'><img src='./mededelingen/thumb/$entry_image_a?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"mededelingen\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"mededelingen\",\"$entry2\")'><div class='btn2'>Preview</div></a>
	   
	   <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"mededelingen\",\"$data\",\"0\")'>
	   <img src='$edit_button_path' alt=''  width='20' height='20'></a></div>
	   
	   <div class='close_btn_near_edit'>
	   <a href='javascript:void(0);' ><img class='mededelingen_delete' onclick='deleteUpload(\"$entry\",1,\"mededelingen\",\"$carrouselid\",\"$status\")' src='$close_button_path' alt=''></a></div>
	   <div class='checkbox_class'><span style='display:none;' class='mededelingen_check_box'><input type='checkbox' class='mededelingen_check_box' id='mededelingen_$k' name='mededelingen_$k' value='$entry2'/></span></div>
	   <div class='clear'></div>";
         }
	 
        $medede_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname  </div>";
 
        

      }
      $k++;
     }
 
     $search_block='<div class="item_search_box_container">
                     <input  id="communications_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';       	
     $medede_listing=$search_block.$medede_listing;
     closedir($handle4);
     unset($itemname);
   }
   
 //--------------------------- used for fetching video and image from commercials folder -------------------------------
 
 //------------------------------- used for fetching images from delay mededelingen folder -----------------------------//
 
 if($theatreDetails[is_delay]==1){
    $delay_medede_listing="";
  if ($handle4_delay = listdir_by_date("/var/www/html/narrowcasting/delay_mededelingen/", $db->sort_by_date)) {
    $i_delay =1;
    $l=1;
     foreach($handle4_delay as $key=>$entry_delay){
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
	   
	   $itemname_delay="<div class='block_img'><img src='$thumb_medede_path_delay?time=".time()."' width='135' height='135'></div><p>".str_replace("-",'"',$twitter_name_delay[0])."</p><a href='javascript:void(0);' onclick='additem(\"delay_mededelingen\",\"$entry2_delay\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./delay_mededelingen/$entry_delay?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>";
	  } else {
           $itemname_delay="<div class='block_img'><img src='$thumb_medede_path_delay?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1_delay[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"delay_mededelingen\",\"$entry2_delay\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./delay_mededelingen/$entry_delay?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a>
	   
	   <div class='clear'></div>";
	  }
         } else {
	  $entry_image_a_delay=$entry1_delay[0].'.jpg';
           $itemname_delay="<div class='block_img'><img src='./delay_mededelingen/thumb/$entry_image_a_delay?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1_delay[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"delay_mededelingen\",\"$entry2_delay\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"mededelingen\",\"$entry2_delay\")'><div class='btn2'>Preview</div></a>
	   
	   <div class='edit_btn_new'>
	    <a href='javascript:void(0);' onclick='editImage(\"mededelingen\",\"$data_delay\")'>
	   <img src='$edit_button_path_delay' alt=''  width='20' height='20'></a></div>
	   
	   <div class='close_btn_near_edit'>
	   <a href='javascript:void(0);' onclick='deleteUpload(\"$entry_delay\",1,\"mededelingen\",\"$carrouselid_delay\",\"$status_delay\")'><img src='$close_button_path_delay' alt=''></div>
	   <div class='clear'></div>";
         }
	 
        $medede_listing_delay.="<div class='block' id='start-".substr($entry1_delay[0],0,35)."'>$itemname_delay  </div>";
 
        

      }
      $l++;
     }
 
     $search_block='<div class="item_search_box_container">
                     <input  id="delaycommunications_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';       	
     $medede_listing_delay=$search_block_delay.$medede_listing_delay;
     closedir($handle4_delay);
     unset($itemname_delay);
   }
 }
 //------------------------------- used for fetching images from commercial folder -----------------------------//
  $comme_listing="";
  if ($handle5 = listdir_by_date("/var/www/html/narrowcasting/commercials/", $db->sort_by_date)) {
    $m=1;
     foreach($handle5 as $key => $entry){
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="preview" and $entry!=".DS_Store" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./commercials/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"commercials\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./commercials/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' ><img src='$close_button_path' onclick='deleteUpload(\"$entry\",1,\"commercials\",\"$carrouselid\",\"$status\")' alt='' class='commercial_delete'></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='commercials_check_box'><input type='checkbox' class='commercials_check_box' id='commercials_$m' name='commercials_$m' value='$entry2'/></span>
	   </div>
	   <div class='clear'></div>";
         } else {
           $itemname="<div class='block_img'><img src='./commercials/thumb/$entry1[0].jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"commercials\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"commercials\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' ><img src='$close_button_path' onclick='deleteUpload(\"$entry\",1,\"commercials\",\"$carrouselid\",\"$status\")' alt='' class='commercial_delete'></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='commercials_check_box'><input type='checkbox' class='commercials_check_box' id='commercials_$m' name='commercials_$m' value='$entry2'/></span>
	   </div>
	   <div class='clear'></div>";
         }
        $comme_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
      $m++;
     }
     $search_block='<div  class="item_search_box_container">
                     <input  id="commercial_search" type="text" placeholder="Zoeken" class="item_search_box"></div>';      
     $comme_listing=$search_block.$comme_listing;
     closedir($handle5);
     unset($itemname);
   }
 
 //--------------------------- used for fetching video and image from promotie folder ------------------------------- 
  $promotie_listing="";
  if ($handle6 = listdir_by_date("/var/www/html/narrowcasting/promotie/", $db->sort_by_date)) {
    $n=1;
     foreach($handle6 as $key => $entry){
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="preview" and $entry!=".DS_Store" and $entry!="thumb" and $entry!="totalthumb" and $entry!=".gitignore" and $entry!="cropped") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
        if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<div class='block_img'><img src='./promotie/thumb/$entry?time=".time()."' width='135' height='135'></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"promotie\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='./promotie/$entry?time=".time()."' rel='single'  class='pirobox'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' ><img class='promotie_delete' src='$close_button_path' alt='' onclick='deleteUpload(\"$entry\",1,\"promotie\",\"$carrouselid\",\"$status\")'></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='promotie_check_box'><input type='checkbox' class='promotie_check_box' id='promotie_$n' name='promotie_$n' value='$entry2'/></span>
	   </div>
	   <div class='clear'></div>";
         } else {
           $itemname="<div class='block_img'><img src='./promotie/thumb/$entry1[0].jpg?time=".time()."' width='135' height='135' ></div><p>".str_replace("_"," ",substr($entry1[0],0,35))."</p><a href='javascript:void(0);' onclick='additem(\"promotie\",\"$entry2\")'><div class='btn1 k'>Toevoegen</div></a><div class='btn1 kl' style='display:none;color:#a9a9a9;'>Toevoegen</div><a href='javascript:void(0);' onclick='popitup(\"promotie\",\"$entry2\")'><div class='btn2'>Preview</div></a><div class='close_btn_new'><a href='javascript:void(0);' ><img class='promotie_delete' src='$close_button_path' alt='' onclick='deleteUpload(\"$entry\",1,\"promotie\",\"$carrouselid\",\"$status\")'></a></div>
	   <div class='checkbox_class'>
	   <span style='display:none;' class='promotie_check_box'><input type='checkbox' class='promotie_check_box' id='promotie_$n' name='promotie_$n' value='$entry2'/></span>
	   </div>
	   <div class='clear'></div>";
         }
        $promotie_listing.="<div class='block' id='start-".substr($entry1[0],0,35)."'>$itemname</div>";
      }
      $n++;
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
   while($url_row =mysqli_fetch_array($url_data)){
	$dynamic_url_info[$url_row['dynamic_cues_id']]['dynamic_cues_id']=$url_row['dynamic_cues_id'];
	$dynamic_url_info[$url_row['dynamic_cues_id']]['image']=$url_row['image'];
	$dynamic_url_info[$url_row['dynamic_cues_id']]['url']=$url_row['url'];
   }
   
   
 //--------------------------- used for showing temp carroussels before create ------------------------------- 
  $carrousel_listing="";
   $selectquery="select * from temp_carrousel where s_id='$sid' ORDER BY record_listing_id ASC";
   $conn=$db->query($selectquery);
   $num_rows = mysqli_num_rows($conn);
   while($result=mysqli_fetch_array($conn)) {
    //echo "<pre>"; print_r($result); echo "</pre>";
    $path=$result['name'];
    $carrouel_name=$result['name_carrousel'];
    $timetostring = $result['enddate'];
    if($timetostring==0){
     $enddate=date('m-d-Y');
    }else{
     $enddate=date('m-d-Y', $result['enddate']);
    }
    $url_ques_id=$result['name'];
    $cid=$result['id'];
    if($tempid==''){
     $tempids= $result['id'];
    }else{
     $tempids = $tempid;
    }
    
  
    $imagename=explode('/',$path);
    $imagename1=explode('.',$imagename[1]);
    $image_name_timeline='';
    $image_name_timeline=$imagename[1];
    
    
   
    if($path==$imagename[0] and $imagename[1]=="") {
       $row_type="url";
     }
   
  
    if($imagename1[1]=="jpg" or $imagename1[1]=="gif" or $imagename1[1]=="png" or $row_type=='url') {
      
      $path1=explode("/",$result['name']);
      $real_path=$result['name'];
       //echo "<pre>"; print_r($path1); echo "</pre>";
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
     }
     else if($path==$path1[0] and $path1[1]=="") {
       $path="urls/thumb/".$dynamic_url_info[$url_ques_id]['image'];
     }
     else {
      
     }
       $duration="<input type='text' name='duration[]' size='1' value='".$result['duration']."'onblur='updatetime($cid,this.value)'><span>s</span>";
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
	//$overlay_link="<a href='createimage_overlay.php?path=$real_path&id=$tempids&show_cal=1'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	$overlay_link="<a href='javascript://' onclick='create_image_overlay(\"$real_path\",\"$tempids\",1);'><div class='green_btn_horizon'>Tekst toev.</div></a>";
      }
    } else {
      //$overlay_link="<a href='createimage_overlay.php?path=$real_path&id=$tempids&show_cal=1'><div class='green_btn_horizon'>Tekst toev.</div></a>";
      $overlay_link="<a href='javascript://' onclick='create_image_overlay(\"$real_path\",\"$tempids\",1);'><div class='green_btn_horizon'>Tekst toev.</div></a>";
    }
   
    if($row_type!='url'){
    $preview_btn="<a href='./$path1[0]/$image_name_timeline?time=".time()."' rel='single'  class='pirobox'><div class='btn_preview'><img src='$preview_button_path' width='20' height='20' /></div></a>";
    }else{
      $preview_btn='';
    }
    } else if($imagename1[1]=="mov" or $imagename1[1]=="mp4"){
     
     $path1=explode("/",$result['name']);
    /// echo "<pre>"; print_r($path1); echo "</pre>";
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
    } else if($path1[0]=="mededelingen"){
      $checktwt=substr($path1[1],0,7);
      if($checktwt=="Twitter") {
	$overlay_link="";
      } else {
	//$overlay_link="<a href='create_overlay.php?path=$real_path&id=$tempids&show_cal=$_GET[show_cal]'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	$overlay_link="<a href='javascript://' onclick='create_video_overlay(\"$real_path\",\"$tempids\",1);'><div class='green_btn_horizon'>Tekst toev.</div></a>";
      }
    } else {
     // $overlay_link="<a href='create_overlay.php?path=$real_path&id=$tempids&show_cal=$_GET[show_cal]'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	 $overlay_link="<a href='javascript://' onclick='create_video_overlay(\"$real_path\",\"$tempids\",1);'><div class='green_btn_horizon'>Tekst toev.</div></a>";
    }
    $preview_btn="<a href='javascript:void(0);'   onclick='popitup(\"overlay_video\",\"$path1[1]\")'><div class='btn_preview'><img src='$preview_button_path' width='20' height='20' /></div></a>"; 
  } else {
	
  }
  $imagename=explode("/",$path);
 
   if($row_type=='url'){
	$image_name=preg_replace('/^http(s)?:\/\//','', $dynamic_url_info[$url_ques_id]['url']);
    }
   else if($imagename[0]!="overlay_video") {
     if(substr($imagename1[0],0,7)=="Twitter") {
      $twitter_name=explode("_",$imagename1[0]);
      $image_name=str_replace("-",'"',$twitter_name[0]);
     } else {
      $image_name=stripslashes(str_replace("_"," ",substr($imagename1[0],0,35)));
     }
    }
    else {
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
     $edit_btn="<div class='edit_btn_edit'><a href='javascript:void(0);' onclick='editImage(\"overlays_image\",\"$image_name_timeline\",\"$cid\")'><img src='$edit_button_path' alt='' width='20' height='20'></a>
	   </div>";
     }else{
      $edit_btn="<div class='edit_btn_edit'><a href='javascript:void(0);' onclick='editImage(\"$path1[0]\",\"$image_name_timeline\",\"$cid\")'><img src='$edit_button_path' alt='' width='20' height='20'></a>
	   </div>";
     }
    }else{
     $edit_btn='';
    }
    if($timetostring==0){
       $calender_button_path="./img/$color_scheme/calendar_inactive.png";
      
    }else{     
      $calender_button_path="./img/$color_scheme/calendar.png";
    }
    if($path1[0]=="mededelingen"){
      
      $overlay_link="";
    }
    $carrousel_listing.="<li class='horiz_block' id='recordsArray_".stripslashes($cid)."'>
                        	<div class='horiz_img'>
				$edit_btn
                            	<img class='horiz_img_thumb' src='./".stripslashes($path)."?time=".time()."' width='135' height='135' alt='' />
                            	<div class='close_btn'><a href='javascript:void(0);' onclick='deleteCarrousel(\"$cid\")'><img src='$close_button_path' alt=''/></a></div>
				$preview_btn
		     <div class='calendar'>
		     <a href='javascript:NewCal(\"demo_$cid\",\"mmddyyyy\",\"carrausel\");' >
           <img id='img_$cid' src='$calender_button_path' alt='Pick end date' title='Pick end date' /></a>
    <input  type='hidden' name='sdate[]' id='demo_$cid' class='datum_plannen' size='10' value=$enddate>
    <input  type='hidden' name='date[]' id='date_$cid' class='datum_plannen' size='10' value='$enddate'>
   
    <input  type='hidden' name='color_scheme[]' id='color_scheme_$cid' class='datum_plannen' size='10' value='$color_scheme'>
    
    </div>
                            </div><p>".$image_name."</p>
                            $overlay_link
                             <div class='horiz_input_field fl_r_changes'>
                             	$duration 
                              <input type='hidden' name='cname[]' value='$real_path'>
                              <input type='hidden' name='htotalrow'  id='htotalrow' value='$num_rows'>
			      <input type='hidden' name='commercial_check'  id='commercial_check' value='$real_path'>
                                <div class='clear'></div>
                             </div>
			    
                        </li>";
   }
   $selectquery2="select name_carrousel from temp_carrousel where s_id='$sid' and name_carrousel IS NOT NULL GROUP BY s_id";
   $conn2=$db->query($selectquery2);
   $result2=mysqli_fetch_array($conn2) ;
   $carrouel_name=stripcslashes($result2['name_carrousel']);

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

<title>Pandora | Maak een nieuwe carrousel</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="./css_pirobox/style_1/style.css"/>
<link rel="stylesheet" href="./css/jquery-ui.css" type="text/css" />

<!--::: it depends on which style you choose :::-->

<?php include('jsfiles.html')?>
<script type="text/javascript" src="./js/datetimepicker.js"></script>

	<style type="text/css">
	
		#alle_carrousels,#nieuw_bestand,#nieuwe_twitterfeed,#uploaden_bestand,#iets_nieuws,#spreekuur,#tot_ziens{
		  <?php echo $css_color_rule;?>
	        }
		#nieuw{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
		#alle_carrousels{font-size:14px;line-height:30px;}
		#maak_carrousel{font-size:14px;color: #b5b5b5;line-height:30px;}
		#nieuw_bestand{font-size:14px;line-height:30px;}
		#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
		#uploaden_bestand{font-size:14px;line-height:30px;}
		#iets_nieuws{font-size:14px;line-height:30px;}
		#spreekuur{font-size:14px;line-height:30px;}
		#tot_ziens{font-size:14px;line-height:30px;}
	     
	       
	        ul.ui-autocomplete {z-index: 99999999 !important;}
	       .search_result_background {position:fixed;top:0; left:0; width:100%; height:100%; z-index:1111;
		   background:#000000;display:none;cursor:pointer;
		} 
	       .ui-menu .ui-menu-item {margin: 0;padding: 0;width: 100%;support: IE10, see #8844;background: #f1f1f1;margin-top: 3px;
	        list-style-image: url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7);
               }
	      ul.ui-autocomplete{display:none !important;}
    
	</style>
  <script>
    function create_image_overlay(path, id, show_cal){
      var enddate = $('#date_'+id).val();
      window.location = 'createimage_overlay.php?path='+path+'&id='+id+'&show_cal=1&enddate='+enddate;
    }
    function create_video_overlay(path, id, show_cal){
      var enddate = $('#date_'+id).val();
      window.location = 'create_overlay.php?path='+path+'&id='+id+'&show_cal=1&enddate='+enddate;
    }
  </script>

</head>
<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->
<script type="text/javascript">


$(document).ready(function(){
  
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



getAllTabImageData();
  // for searchbox clear button
   $("span#global_search_clear").bind("click",function(){
     $("input#comman_search").val("");
     $('div#blocks_container_comman_search div.block').css('display','block');
     $('#comman_search').autocomplete('close');
     $(".search_result_background").css({'display':'none'});
     $("#search_result_content").css({'display':'none'});
     $("div#blocks_container_comman_search").css('scrollTop',0);
     $("span#global_search_clear").animate({ opacity:0}, 400, function(){ $("span#global_search_clear").hide();});
   });






});

function popitup(path,id) {
    var url='previewvideo.php?id='+id+'&path='+path+'&time='+new Date().getTime();
    var newwindow=window.open(url,'Preview','height=500,width=640,left=190,top=150,screenX=200,screenY=100');
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
'showdelaycommunications':        [], // for delay mededelingen
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
 $('div#blocks_container_comman_search div.block').css('display','block');
}



// jquery autocomplete code for all tabs....

$(function() {
    $("#communications_search,#photo_search,#promotie_search,#overlay_search,#upload_search,#video_search,#commercial_search,#delaycommunications_search").autocomplete({
      source: function(request, response) {
      var data =autoSuggestJsonData[currentWorkingSearchDiv];
      var filter_data=$.ui.autocomplete.filter(data,request.term);
	    
	    $('div#'+currentWorkingSearchDiv+' div.block').css('display','none');
	    $.each(filter_data,function(key,currentRow){
	      var blockId=currentRow.value;
	      if(blockId!="")
	      $('div#'+currentWorkingSearchDiv+' div#'+blockId).css('display','block');
	    });
	   // alert("********");
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


// autocomplete for comman search
$(function() {
  var pageHeight=$(document).height();
  var result_container_margin=($(document).width()-950)/2;
  //alert($(document).width()-951);
    $("#comman_search").autocomplete({
      source: function(request, response) {
            var data =autoSuggestJsonData['all_data'];
	    var data_filtered     = $.ui.autocomplete.filter(data,request.term);	    
	      
	    $('div#blocks_container_comman_search div.block').css('display','none');
	    $.each(data_filtered,function(key,currentRow){
	      var blockId=currentRow.value;
	      // console.log(blockId);
	      $('#blocks_container_comman_search div#'+blockId).css('display','block');
	    });
	      $('#blocks_container_comman_search div#41').css('display','block');
	  // a= $('div#blocks_container_comman_search #999999_one_of_androi_1530').html();
	   // alert(s)
	    // handle case of showing all media when search_box is empty after back button clicks
	    setTimeout(function(){
	     if($('#comman_search').val()==""){
	        $('div#blocks_container_comman_search div.block').css('display','block');
	        $('#comman_search').autocomplete('close');
		$(".search_result_background").css({'display':'none'});
	        $("#search_result_content").css({'display':'none'});
	        $("div#search_result_content div.vertical_gallery").css('scrollTop',0);
		$("span#global_search_clear").animate({ opacity:0 }, 200, function(){$("span#global_search_clear").hide();});
	      }
	   
	    },40)
	    //search_result_content
	    $(".search_result_background").css({'top':'570px','display':'block','height':'515px'});
	    $("#search_result_content").css({'top':'577px','display':'block','margin-left':result_container_margin+'px'});
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



/************** END OF CODE FOR AUTOCOMPLETE ***********/

$(document).ready(function() {
    $().piroBox_ext({
        piro_speed : 900,
        bg_alpha : 0.1,
        piro_scroll : true //pirobox always positioned at the center of the page
    });
    // autocomplete jsondata setup at time of page load
    getSelecteTabImageData();
});

var dragging = false;// flag for drag start	
$(document).ready(function(){ 		   
  bindSortableInTimeline();
});



function bindSortableInTimeline(){
  $("#contentLeft ul").sortable(
		{
		opacity: 0.6,cursor: 'move',revert: true,dropOnEmpty:true,
		start: function(event, ui) {
		  //console.log('start>>>>>>');
		  dragging=true;
		  scrollOnDrag();
		},
		stop: function(event, ui) {
		  dragging=false;
		  $('div.horizon_gallery2').unbind("mousemove");
		  //console.log('stop>>>>>>');
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
	//var vhscroll = (document.all ? document.scrollTop : window.pageYOffset);
	//vhscroll= vhscroll-300;
		//window.scrollBy(0,200); 
		$("#contentLeft ul").sortable({ opacity: 0.6, cursor: 'move', revert: true,update: function() {
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
    var batch_delete_folder = $('#batch_delete_folder').val();
    if(batch_delete_folder!=''){
      alert('In de batch verwijder mode kunt u niet wisselen van map. U dient eerst de geselecteerde items te verwijderen. ');
      return false;
    }
    
   if(id=='video') {
	   <?php if($theatreDetails[is_poster]==0): ?>
     $("#video").removeClass("current").addClass("current");
	 <?php endif;?>
     $("#photo").removeClass("current").addClass("");
     $("#communications").removeClass("current").addClass("");
     <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
     <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
     <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
    <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
     <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
     <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
      <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
     <?php if($theatreDetails[is_delay]==1):?>
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
        <?php if($theatreDetails[is_delay]==1):?>
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
     <?php if($theatreDetails[is_delay]==1):?>
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
       <?php if($theatreDetails[is_delay]==1):?>
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
 function additem(id1,id2) {
  //var hscroll = (document.all ? document.scrollLeft : window.pageXOffset);
  var vscroll = (document.all ? document.scrollTop : window.pageYOffset);
 /* var error_msgs= [
		    "- De afbeelding/video voor de URL moet minsten 10 seconden duren.",
		    "- U heeft het maximaal aantal items bereikt. ",
		    "- U kunt de URL niet vooraal in de carrousel plaatsen en 2 URL's kunnen niet achter elkaar geplaatst worden."
		  ];
 */
  if(vscroll<=300){
   vscroll =vscroll;
  }else{
   vscroll= vscroll- 250;
  }
  //document.getElementById("ajax_loader_image").style.marginTop= vscroll+'px';
  //$("#ajax_loader_image").css("margin-bottom",vscroll+'px');
  document.getElementById("ajax_loader").style.display="block";
  var value_name = document.getElementById("carrouel_name_updates").value;
  $(".k").hide();
  $(".kl").show();
  var caurraselCountExceedMsg="U heeft het maximale aantal van 30 carrousels bereikt. U dient eerst een carrousel te verwijderen voordat u een nieuwe kunt aanmaken.";
    $.ajax({  
              type: "POST", url: 'additem.php', data: "name1="+id1+"&name2="+id2, async: false,  
              complete: function(data){
		 var responseData=$.trim(data.responseText);
		 if(responseData!="max carrousels count is reached"){
		    $("#show").html(data.responseText);
		    document.getElementById("ajax_loader").style.display="none";
		    $(".kl").hide();
		    $(".k").show();
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
		 }else{
		  $(".kl").hide();
		  $(".k").show();
		  alert(caurraselCountExceedMsg);
		 }
                
	      
              }  
          });
    addname(value_name);
  
 }
 
 function deleteCarrousel(id) {
    $.ajax({  
              type: "POST", url: 'additem.php', data: "delete="+id,  
              complete: function(data){ 
                  try{ $("#show").html(data.responseText);
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
		 }catch(err){}
		
              },
	    error:function(){
	    
	    }
          });  
 }

function deleteUpload(id,id1,dir,cid,status) { 
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
              type: "post", url: 'check_delete_item.php',data: "delete_item_name="+id+"&dir="+dir+'&edit=0',
	      async: false,
              complete: function(data){ 
                 var response=$.trim(data.responseText);
		 var del=true;
		 if(response>0){
		  del=confirm('Dit item wordt gebruikt in '+response+' carrousel(s), weet u zeker dat u door wilt gaan met verwijderen?');
		 }
		 if(del){
		       $.ajax({  
                         type: "POST", url: 'deleteupload.php', data: "delete="+id+"&id="+id1+"&dir="+dir,  
                         complete: function(data){ 
                          $("#"+div).html(data.responseText);
                         }  
                       });
		 }
              },
	      error:function(error){
	       
	      }
	      
          });
     
   }
 }
 
  function editImage(imageFrom,imageName,itemid) {
   
   
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
		    window.location = 'mededelingen_overlay.php?carrousel_id=&request_for=template_edit&temp_mededelingen_id='+mededelingen_id+'&template_id='+template_id+'&edit_image_name='+created_file_name+"&come_from=med_edit&type=edit&file=create";
		 }
		 
		 else if(responseInJson['type']=="delay_mededelingen" && responseInJson['id']!=""){
		    var mededelingen_id=responseInJson['id'];
		    var template_id=responseInJson['template_id'];
		    var created_file_name=responseInJson['created_file_name'];
		    if(template_id==""){
		      template_id=1;
		    }
		    window.location = 'delay_mededelingen_overlay.php?carrousel_id=&request_for=template_edit&temp_mededelingen_id='+mededelingen_id+'&template_id='+template_id+'&edit_image_name='+created_file_name+"&come_from=med_edit";
		 }
		 else if(responseInJson['type']=="overlays_image" && responseInJson['id']!=""){
		    var created_file_name=responseInJson['created_file_name'];
		    var image_overlay_id=responseInJson['id'];
		    var selected_file_path=responseInJson['selected_file_path'];
		    var carrousel_id=responseInJson['carrousel_id'];
		   
		     if(itemid==0){
		       
		      var enddate='';
		      var show_cal='';
		     }else{
		      var enddate=$('#demo_'+itemid).val();
		       var show_cal=1;
		      
		     }
		    window.location = 'createimage_overlay.php?path='+selected_file_path+'&cid='+carrousel_id+'&id='+itemid+'&tempid='+itemid+'&edit_image_name='+created_file_name+"&method=edit&temp_overlay_id="+image_overlay_id+"&type=edit&show_cal="+show_cal+"&enddate="+enddate;
		  //path=images/image2.jpg&cid=&edit_image_name=image2_4923.png&id=2096&method=edit&temp_overlay_id=18
		 }
		 else if(responseInJson['type']=="overlays_video" && responseInJson['id']!=""){
		    var created_file_name=responseInJson['created_file_name'];
		    var video_overlay_id=responseInJson['id'];
		    var selected_file_path=responseInJson['selected_file_path'];
		    var carrousel_id=responseInJson['carrousel_id'];
		    
		    if(itemid==0){
		        var enddate='';
		      var show_cal='';
		     }else{
		       var enddate=$('#demo_'+itemid).val();
		       var show_cal=1;
		    
		     }
		    window.location = 'create_overlay.php?path='+selected_file_path+'&cid='+carrousel_id+'&id='+itemid+'&tempid='+itemid+'&edit_image_name='+created_file_name+"&method=edit&temp_overlay_id="+video_overlay_id+"&type=edit&show_cal="+show_cal+"&enddate="+enddate;
		 }
		 // if some unexpected happen (case is not taken into considration)
		 else{
		    alert('U kunt deze overlay/mededeling niet bewerken.');
		 }
		 
	       }// if data received from server is not json compatable
	       catch(err)
	       {
		alert('U kunt deze overlay/mededeling niet bewerken.');
	       }
              }  
          });
   
   }
 }
 function addname(value){
   var caurraselCountExceedMsg="You have reached to the limit of 30  carrousels, so please delete some of carrousel to add a new.";
   var name = trim(value);
    if(name !=''){
     
     $.ajax({  
              type: "POST", url: 'additem.php', data: "name_carrousel="+value,  
              complete: function(data){
		
		 var responseData=$.trim(data.responseText);
		 if(responseData!="max carrousels count is reached"){
		    $("#show").html(data.responseText);
		    // removing and re assigning sortable to div( as start/stop callback of sortable was not working after ajax response)
		    $("#contentLeft ul").sortable('destroy');
		    $("#contentLeft ul").unbind();
		    bindSortableInTimeline();
		 }else{
		    $('input#carrouel_name_updates').val("");
		    alert(caurraselCountExceedMsg);
		 }
              }  
          });  
     
     
    }
 }
 
 function updatetime(id,value){
    if(id!='' && value!=''){
     $.ajax({  
		type: "POST", url: 'additem.php', data: "update_id="+id+"&update_value="+value,  
		complete: function(data){
		    $("#show").html(data.responseText);
		    getTotalTime();
		}  
	    });  
    }
  

 }
  
 function getTotalTime(){
  
   $.ajax({  
              type: "POST", url: 'calculate_time.php',  
              complete: function(data){
	        var timer = secondsToHms(data.responseText);
                  $("#carrouel_total_time").val(timer);
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


 function formsubmit(){
 /*count no of commercials add */
 var erang1=<?php echo $erang1; ?>;
 var avro=<?php echo $avro; ?>;
 var valid_add = document.getElementById("commercial_check").value;
 var check = valid_add.split('*');
 var count_commercial = 0;
 var count_commercial_1erang=0;
 var count_commercial_avro=0;
 for(var k=1;k<check.length;k=k+1)
 {

     try{
	  var category = check[k].split('/');
        }
     catch(err){ category=Array(""); }
     try{
	var valid_file = category[1].split('_');
	
       }catch(err){ valid_file=Array(""); }
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
    if(ans==false)
    return false;
   } else if ((occur > count_commercial_avro) && (erang1==0 && avro==1)) {
    var ans = confirm('U heeft '+count_commercial_avro+' AVRO station call(s) toegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' station call(s) te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
    if(ans==false)
    return false;
   } else if ((occur > count_commercial_1erang) && (erang1==1 && avro==1)) {
    var ans = confirm('U heeft '+count_commercial_1erang+' 1eRang station call(s) en '+count_commercial_avro+' AVRO spotstoegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' station call(s) en '+ occur +' AVRO spot te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
    if(ans==false)
    return false;
   } else if ((occur > count_commercial_avro) && (erang1==1 && avro==1)) {
    var ans = confirm('U heeft '+count_commercial_1erang+' 1eRang station call(s) en '+count_commercial_avro+' AVRO spotstoegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' station call(s) en '+ occur +' AVRO spot te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
    if(ans==false)
    return false;
   }
   else {
     return true;
   }
   
 }
 else{
  return true;
 }
 /*if(min >6){
  var occur = Math.floor(min / 6);
  if(occur > count_commercial)
  {
    var ans = confirm('You have added '+count_commercial+' Commercial adds, there must be ' + occur + ' Commercial adds, Do you want to continue ?');
    if(ans==false)
    return false;
  }
 }else{
  return true;
 }*/
 //return false;
}

function autosave(id) {
   var dbAcessError="Error in database access.";
   var letters = /^[a-zA-Z0-9 ]+$/;  
   var cname=document.getElementById('carrouel_name_updates').value;
   var c_totaltime=document.getElementById('carrouel_total_time').value;
   c_totaltime = c_totaltime.split(':');
   // to fix bugs of  ajax call fail on slow network conn
   if(c_totaltime[1]==undefined){
    c_totaltime[1]=00;
   }
   
   if((c_totaltime[0]==0 && c_totaltime[1]==00) && cname!="") {
    alert('U kunt geen lege carrousel opslaan. Verwijder de naam van de carrousel of voeg een item toe.');
   }
   else if(c_totaltime[0]==0 && c_totaltime[1]==00 ) {
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
      }else if(id==7) {
       window.location='url.php';
   }else if(id==8) {
    window.location='delay_mededelingen_overlay.php';
      }else if(data.responseText==500) {
       alert(dbAcessError);
      }else{
       alert(data.responseText);
      }
   }else if(cname=="") {
    
     //  alert('U dient de carrousel een naam te geven.');
       var ans = confirm('Weet u zeker dat u deze pagina wilt verlaten? Uw gegevens worden niet opgeslagen. Indien u uw gegevens wilt opslaan klikt u op Cancel en geeft u de carrousel een naam.');
       if(ans){
       $.ajax({  
              type: "POST", url: 'additem.php', data: "delete_all_temp_caurrousel=1",  
              complete: function(data){ 
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
      }else if(id==7) {
       window.location='url.php';
   }else if(id==8) {
    window.location='delay_mededelingen_overlay.php';
		} else if(data.responseText==500) {
		  alert(dbAcessError);
		}else{
		  alert(data.responseText);
		}
              },
	    error:function(){
	    
	    }
          });
       }else{
	
       }
     
   } else if(cname!="" && cname.search(letters)==-1) {
     alert('U kunt alleen letters en cijfers in uw carrousel naam gebruiken (dus geen speciale karakters of interpunctie).'); 
   }
   else {
    /*count no of commercials add */
       
	 var erang1=<?php echo $erang1; ?>;
	 var avro=<?php echo $avro; ?>;
	 var valid_add = document.getElementById("commercial_check").value;
	 var check = valid_add.split('*');
	 var count_commercial = 0;
	 var count_commercial_1erang=0;
	 var count_commercial_avro=0;
	 for(var k=1;k<check.length;k=k+1)
	 {
	  try{
	     var category = check[k].split('/');
          }
          catch(err){ category=Array(""); }
         try{
	    var valid_file = category[1].split('_');
           }catch(err){ valid_file=Array(""); }
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
	       type: "POST", url: 'autosave.php', data: "id="+id+"&cname="+cname,  
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
		}else{
		   alert(data.responseText);
		}
	       }  
	       });
	     }
	   } else if ((occur > count_commercial_avro) && (erang1==0 && avro==1)) {
	    var ans = confirm('U heeft '+count_commercial_avro+' AVRO station call(s) toegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' station call(s) te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
	    if(ans==false) {
	      return false;
	     } else {
	       $.ajax({  
	       type: "POST", url: 'autosave.php', data: "id="+id+"&cname="+cname, 
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
                }else if(data.responseText==500) {
		 alert(dbAcessError);
		}else{
		  alert(data.responseText);
		}
	       }   
	       });
	     }
	   } else if ((occur > count_commercial_1erang) && (erang1==1 && avro==1)) {
	    var ans = confirm('U heeft '+count_commercial_1erang+' 1eRang station call(s) en '+count_commercial_avro+' AVRO spotstoegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' station call(s) en '+ occur +' AVRO spot te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
	    if(ans==false) {
	      return false;
	     } else {
	       $.ajax({  
	       type: "POST", url: 'autosave.php', data: "id="+id+"&cname="+cname, 
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
                }else if(data.responseText==500) {
		  alert(dbAcessError);
		}else{
		  alert(data.responseText);
		}
	       }  
	       });
	     }
	   } else if ((occur > count_commercial_avro) && (erang1==1 && avro==1)) {
	    var ans = confirm('U heeft '+count_commercial_1erang+' 1eRang station call(s) en '+count_commercial_avro+' AVRO spotstoegevoegd. Voor een carrousel van deze lengte dient u ' + occur + ' station call(s) en '+ occur +' AVRO spot te plaatsen. Klik op "Annuleren" om terug te gaan en deze toe te voegen of klik op "OK" om door te gaan.');
	    if(ans==false) {
	      return false;
	     } else {
	       $.ajax({  
	       type: "POST", url: 'autosave.php', data: "id="+id+"&cname="+cname, 
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
                }else if(data.responseText==500) {
		 alert(dbAcessError);
		}else{
		   alert(data.responseText);
		}
	       }    
	       });
	     }
	   }
	   else {
	     $.ajax({  
	       type: "POST", url: 'autosave.php', data: "id="+id+"&cname="+cname,   
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
                }else if(data.responseText==500) {
		 alert(dbAcessError);
		}else{
		   alert(data.responseText);
		}
	       }  
	       });
	   }
	   
	 }
	 else{
	  
	  $.ajax({  
	       type: "POST", url: 'autosave.php', data: "id="+id+"&cname="+cname,   
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
                }else if(data.responseText==500) {
		 alert(dbAcessError);
		}else{
		   alert(data.responseText);
		}
	       }   
	       });
	}
    
   }
}

function secondsToHms(d) {
	d = Number(d);
	var h = Math.floor(d / 3600);
	var m = Math.floor(d % 3600 / 60);
	var s = Math.floor(d % 3600 % 60);
	return ((h > 0 ? h + ":" : "") + (m > 0 ? (h > 0 && m < 10 ? "0" : "") + m + ":" : "0:") + (s < 10 ? "0" : "") + s);
}


var newwindow;
function popit_url(url) {
    if(newwindow!=undefined){
      newwindow.close()
    }
     newwindow=window.open(url,'Preview','height=800,width=1000,left=190,top=150,screenX=200,screenY=100');
}

function openitup(){
 
var url = "delay_mededelingen_overlay.php";
 window.open(url,'Crop','height=500,width=640,left=190,top=150,screenX=200,screenY=100,scrollbars=yes');
}
function show_batch_delete(){
	$('.checkbox_class').css({ top: '-7px', });
  $('#batch_delete_button').show();
  if($('#showvideo').css('display')=='block'){
    $('.video_check_box').show();
    $('.video_delete').hide();
    $('#batch_delete_folder').val('videos');
  }
  if($('#showphoto').css('display')=='block'){
     $('.image_check_box').show();
     $('.image_delete').hide();
     $('#batch_delete_folder').val('images');
  }
  if($('#showcommunications').css('display')=='block'){
    $('.mededelingen_check_box').show();
     $('.mededelingen_delete').hide();
     $('#batch_delete_folder').val('mededelingen');
  }
  if($('#showdelaycommunications').css('display')=='block'){
    $('.delaymededelingen_check_box').show();
    $('.delaymededelingen_delete').hide();
    $('#batch_delete_folder').val('delaymededelingen');
  }
  if($('#showupload').css('display')=='block'){
    $('.upload_check_box').show();
    $('.upload_delete').hide();
    $('#batch_delete_folder').val('upload');
  }
  if($('#showupload').css('display')=='block'){
    $('.upload_check_box').show();
    $('#batch_delete_folder').val('upload');
  }  
  if($('#showcommercial').css('display')=='block'){
  $('.commercials_check_box').show();
    $('.commercial_delete').hide();
    $('#batch_delete_folder').val('commercials');
  }
  if($('#showpromotie').css('display')=='block'){
    $('.promotie_check_box').show();
    $('.promotie_delete').hide();
    $('#batch_delete_folder').val('promotie');
  }
 
  if($('#showoverlay').css('display')=='block'){
	 
    $('.overlay_check_box').show();
    
    $('.overlay_video_delete').hide();
    
    $('#batch_delete_folder').val('overlay_video');
  }
  
  
  /*if(trim($('#showoverlay').css('display'))=='block'){
	  alert("here")
    $('.overlay_check_box').show();
    
    $('.overlay_video_delete').hide();
    
    $('#batch_delete_folder').val('overlay_video');
  }*/
  if($('#showurlbottom').css('display')=='block'){
    $('.url_check_box').show();
    $('#batch_delete_folder').val('url');
  }
  
  
}

function submit_batch_delete_form(){
  $('#batch_delete').submit();
}
 

</script>
<body onload="getTotalTime()">
<!--------------- include header --------------->
   <?php include('header_carrousel.html'); ?>
    
    
        <!-- START MAIN CONTAINER -->
    <div class="main_container">
    	<div class="content">
    	<div style=" margin-top:15px;"><span style="color:red; font-family:Adelle Basic; font-size:20px;"><b><?php echo $error_message; ?></b></span></div>
                 <span class="title">Nieuwe carrousel</span>
             <p>Hieronder kunt u een nieuwe carrousel samenstellen. Indien u een item wilt gebruiken in de carrousel klikt u op "Toevoegen". Hierna verschijnt het item in de tijdlijn. Items in de tijdlijn kunt u rangschikken door middel van 'drag and drop'. Voor afbeeldingen en URL's dient u een tijdsduur in te vullen.</p>
       <p>Indien u een informatiebalk over een item wilt plaatsen voegt u een item eerst toe aan de tijdlijn en klikt u daarna op "Tekst toev.". Aangemaakte informatiebalken worden geplaatst onder het tabblad "Overlays" en kunt u vanaf daar opnieuw in een carrousel plaatsen.</p>
	   <p>Aangemaakte mededelingen en overlays kunt u bewerken door op het bewerk symbool in de linkerbovenhoek te klikken. Items kunt u verwijderen door op het prulllenbak symbool te klikken. U kunt ook de <a href="javascript://" onclick="show_batch_delete()">batch verwijder mode</a> activeren om meerdere items tegelijkertijd te verwijderen.</p>
	
        <div>
	<div id="batch_delete_button" style="display:none;">
	  <div class="batch_delete_tab">
	<input type="button" name="submit" value="Verwijder geselecteerde items" onclick="submit_batch_delete_form()">
	  </div>
	</div></div>
        <div  class="global_item_search_box_container">
	   <span class="deleteicon">
               <input  id="comman_search" type="text" placeholder="Zoeken" class="item_search_box" style="margin-top:20px;">
	      <span id="global_search_clear" ></span>
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
            <div class="tabs">
            	<ul>
		  <?php if($folder=='videos')  $class='current'; else $class='';?>
		   <?php if($theatreDetails[is_poster]==0):?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="video" onclick="showhidediv('video')"><?php echo $directoryMappings[$actualDirectory['videos']];?></a></li>
				  <?php endif;?>
		  <?php if($folder=='images')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="photo" onclick="showhidediv('photo')"><?php echo $directoryMappings[$actualDirectory['images']];?></a></li>
		  <?php if($folder=='mededelingen')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="communications" onclick="showhidediv('communications')"><?php echo $directoryMappings[$actualDirectory['mededelingen']];?></a></li>
		  <?php if($theatreDetails[is_delay]==1):?>
		  <?php if($folder=='delay_mededelingen')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="delaycommunications" onclick="showhidediv('delay_communications')"><?php echo $directoryMappings[$actualDirectory['delay_mededelingen']];?></a></li>
		  <?php endif;?>
                 <?php if($folder=='promotie')  $class='current'; else $class='';?>
		  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="promotie" onclick="showhidediv('promotie')"><?php echo $directoryMappings[$actualDirectory['promotie']];?></a></li>
		  <?php if($folder=='upload')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="upload" onclick="showhidediv('upload')"><?php echo $directoryMappings[$actualDirectory['upload']];?></a></li>
		  <?php if($folder=='commercials')  $class='current'; else $class='';?>
                  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="commercial" onclick="showhidediv('commercial')"><?php echo $directoryMappings[$actualDirectory['commercials']];?></a></li>
				  <?php if($theatreDetails[url]==1): ?>
				  <?php if($folder=='urlbottom')  $class='current'; else $class='';?>
				   <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="urlbottom" onclick="showhidediv('urlbottom')">URL's</a></li>
		    <?php endif;?>
		  <?php if($folder=='overlay_video')  $class='current'; else $class='';?>
		  <li><a href="javascript:void(0);" class="<?php echo $class;?>" id="overlay" onclick="showhidediv('overlay')"><?php echo $directoryMappings[$actualDirectory['overlays']];?></a></li>
		 
		 
                </ul>
            </div>
            <div id="ajax_loader" align="center" style="text-align:center;margin-left:440px; z-index:99999;position:absolute;display :none"><img id="ajax_loader_image" src="img/loader_transparant.gif"></div>
	    <form name="batch_delete" id="batch_delete" action="batch_delete.php" method="POST">	
			<?php if($theatreDetails[is_poster]==0):?>
            <?php if($folder=='videos') $display='block'; else $display='none';?>
			
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
	    <?php if($theatreDetails[is_delay]==1):?>
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
			 <?php endif;?>
          
        
	 <input type="hidden" name="batch_delete_folder" id="batch_delete_folder" value="" />
	 <!--<input type="submit" name="submit" value="Batch Delete">--></div>
	</form>
	    <!--</div>-->
        <!-- INPUT FIELD START -->
      <form name="c_create" action="create_carrousel.php" method="POST" onsubmit='return formsubmit();'>
        <div style=" margin-top:15px;"><span style="color:red; font-family:Adelle Basic; font-size:17px;"><b><?php echo $error_message; ?></b></span></div>
        <div class="search_field">
            	<p class="form_title">Naam carrousel</p>
                <input type="text" name="cr_name" value="<?php echo $carrouel_name; ?>" class="input_field" onblur="addname(this.value)" id="carrouel_name_updates"/>
		<span class="form_title2">Lengte carrousel: <input type="text" name="cr_totaltime" value="0" id='carrouel_total_time' class="input_field3" readonly="readonly"/>&nbsp;</span>
        </div>
        <div class="horizon_gallery2" >
        <div class="horizon_blocks_cont" id="show" style="width: 940px;" ><div id='contentLeft' ><ul>
          <?php  echo $carrousel_listing; ?>
	  
           </ul></div> <div class="clear">&nbsp;</div>
		 </div>
          <div class="clear">&nbsp;</div>
        </div>
		<div id="result"  style="display:none"></div>
   		<!-- START FOOTER -->
    	<div class="footer">
        	<div class="footer_tab">
		 
		  <a id="overzicht" href="javascript:void(0)" onclick="autosave('1')"><input type="button" name="submit" value="Naar het overzicht"></a></div>
        </div>
	<input  type='hidden' name='color_scheme' id='color_scheme_a' value="<?php echo $color_scheme; ?>"/>
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





