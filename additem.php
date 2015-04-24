<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
$id=session_id();
error_reporting(0);
include('config/database.php');
include('color_scheme_setting.php');
//echo "<pre>"; print_r($_POST); echo "</pre>";
//exit;
$close_button_path="./img/$color_scheme/close_btn.png";
$edit_button_path= "./img/$color_scheme/edit02.png";
$preview_button_path="./img/$color_scheme/preview_icon.png";
$calender_button_path="./img/$color_scheme/calendar_inactive.png";
$name1=$_REQUEST['name1'];
$name2=$_REQUEST['name2'];
$carr_id=$_REQUEST['carr_id'];

$delete_id=$_REQUEST['delete'];
$name_carrousel = trim($_REQUEST['name_carrousel']);
$update_id = trim($_REQUEST['update_id']);
$update_value = trim($_REQUEST['update_value']);

$delete_all_temp_caurrousel=trim($_REQUEST['delete_all_temp_caurrousel']);

$fullname=$name1."/".$name2;
$fullname=addslashes($fullname);

if($name1=='url'){
 $fullname=$name2;	
}
$db = new Database;


// delete all temp carraousels
 if($delete_all_temp_caurrousel!="") {
   $deleteQuery = "delete from temp_carrousel";
   $conn=$db->query($deleteQuery);
   echo 'success';
   die;
 }

//get duration value from active theatre
$duration_qry = "select duration from theatre where status=1";
$duration_rs = $db->query($duration_qry);
$duration_value = mysqli_fetch_array($duration_rs);

  // calculating total no of carrousel  (check for max no of 30 carrausel)
  $count_carrousel_query="select count(name) as cnt from  carrousel_listing";
  $count_carrousel=$db->query($count_carrousel_query);
  $result=mysqli_fetch_array($count_carrousel);
  $count_carrousel=$result['cnt'];
  $count_carrousel++;
  
   if($count_carrousel>30 ){
      echo "max carrousels count is reached";
      die;
   }
  
//---------------------- code used for ajax call to insert video or image in carrousal---------------
  $countquery="select count(name) as cnt from temp_carrousel where s_id='$id'";
  $count=$db->query($countquery);
  $res =mysqli_fetch_array($count);
  if($res){
	  $cnt = $res['cnt']+1;
  }else{
	  $cnt = 1;
  }
//echo $cnt.">>>>>>>>>";
 if($name1!="" and $name2!="") {
  if($name1=='url'){
   $for_url=true;	
  }
  $check_ext=explode(".",$name2);
  if($check_ext[1]=="png" or $check_ext[1]=="jpg" or $check_ext[1]=="gif" or $check_ext[1]=="jpeg" or $for_url==true) {
    $add_duration=$duration_value['duration'];    
  }else {
    $add_duration=1;	
  }
  // getting duration of items before each caurrasel url item
 
    $Query = "insert into temp_carrousel (s_id,name,record_listing_id,duration) values('$id','$fullname','$cnt','$add_duration')";
    $conn=$db->query($Query);
 
 }
 
 # Resolve issue for record listing id #
 if($delete_id!="") {
  $deleteQuery = "delete from temp_carrousel where id='$delete_id'";
  $conn=$db->query($deleteQuery);
  
    $select_tmp_carrousel="select * from temp_carrousel where s_id='$id' order by record_listing_id asc";
    $select_tmp_carrousel_rs=$db->query($select_tmp_carrousel);
    $select_tmp_carrousel_count = mysqli_num_rows($select_tmp_carrousel_rs);
    if($_SESSION['position_changed']!=1){
     $i=1;
    while($select_tmp_carrousel_result=mysqli_fetch_array($select_tmp_carrousel_rs)){
     $timetostring=$select_tmp_carrousel_result['enddate'];
  
     $update_tmp_carrousel_query ="update temp_carrousel set ";
     $update_tmp_carrousel_query.="record_listing_id=".$i." ";
     $update_tmp_carrousel_query.=" where id=".$select_tmp_carrousel_result[id]."";
     $i++;
     $update_tmp_carrousel_query_conn=$db->query($update_tmp_carrousel_query);
     $update_tmp_carrousel_query="";
    }
  }else{
     $record_listing=array();
    while($select_tmp_carrousel_result=mysqli_fetch_array($select_tmp_carrousel_rs)){
     $record_listing[$select_tmp_carrousel_result['id']]=$select_tmp_carrousel_result['record_listing_id'];
    }
    asort($record_listing);
    $j=1;
    $new_listing=array();
   foreach($record_listing as $key=>$val){
    if($j!=$val && $val > 1){
     $val=$val-1;
     $update_tmp_carrousel_query ="update temp_carrousel set ";
    $update_tmp_carrousel_query.="record_listing_id=".$val." ";
    $update_tmp_carrousel_query.=" where id=".$key."";
    $update_tmp_carrousel_query_conn=$db->query($update_tmp_carrousel_query);
    $update_tmp_carrousel_query="";
   }
   $j++;
 
   }
  }
  unset($_SESSION['position_changed']);
 }
 /*if($delete_id!="") {
  $deleteQuery = "delete from temp_carrousel where id='$delete_id'";
  $conn=$db->query($deleteQuery);
 }*/
 /* code for update carrousel name*/
 if( isset($name_carrousel) and $name_carrousel!=''){

   $carrouselquery="select count(name) as cnt from temp_carrousel where s_id='$id'";
   $carrousel=$db->query($carrouselquery);
   $carrouselresult =mysqli_fetch_array($carrousel);
   if($carrouselresult){
	if($carrouselresult['cnt']>0){
	$name_carrousel = addslashes($name_carrousel);
	$Query = "UPDATE temp_carrousel SET name_carrousel='$name_carrousel' WHERE s_id='$id'";
	$conn=$db->query($Query);	
	}
	
   }
   
	
 }
 /* code for update carrousel duration value*/
 if($update_value!='' and $update_id !=''){
	
   $countupdatequery="select count(name) as cnt from temp_carrousel where id='$update_id'";
   $countupdqate=$db->query($countupdatequery);
   $resupdate =mysqli_fetch_array($countupdqate);
   if($resupdate){
      $update = "UPDATE temp_carrousel SET duration='$update_value' WHERE id='$update_id'";
      $conn=$db->query($update);
	
   }
 }
 
 
   // getting all dynamic url info..........
   $getUrlQuery="select * from urls limit 10";
   $url_data=$db->query($getUrlQuery);
   $dynamic_url_info=array();
   while($url_row =mysqli_fetch_array($url_data)){
   
	$dynamic_url_info[$url_row['dynamic_cues_id']]['dynamic_cues_id']=$url_row['dynamic_cues_id'];
	$dynamic_url_info[$url_row['dynamic_cues_id']]['image']=$url_row['image'];
	$dynamic_url_info[$url_row['dynamic_cues_id']]['url']=$url_row['url'];
	if($url_row['name']==""){
	 $dynamic_url_info[$url_row['dynamic_cues_id']]['name']=str_replace("http://","",$url_row['url']);
	}else{
	 $dynamic_url_info[$url_row['dynamic_cues_id']]['name']=$url_row['name'];
	}
   }
 
 $carrousel_listing="";
 $selectquery="select * from temp_carrousel where s_id='$id' ORDER BY record_listing_id asc";
 $conn=$db->query($selectquery);
 $num_rows = mysqli_num_rows($conn);
 $commercial_add = "";
 $carrousel_listing.="<div id='contentLeft'><ul>";
 while($result=mysqli_fetch_array($conn)) {
  
  $cid=$result['id'];
  $path=$result['name'];
  $url_ques_id=$result['name'];
  $tempid=$result['id'];
  $timetostring=$result['enddate'];
  if($timetostring==0){
    $today=date('m-d-Y');
   $today=explode("-",$today);
   $timetostringa=mktime(0,0,0,$today[0],$today[1],$today[2]);
   $enddate=date('m-d-Y', $timetostringa);
  }else{
   $enddate=date('m-d-Y', $timetostring);
  }
  $imagename=explode('/',$path);
  $imagename1=explode('.',$imagename[1]);
  $image_name_timeline=$imagename[1];
  if($path==$imagename[0] and $imagename[1]=="") {
       $row_type="url";
       
   }else{
       $row_type="";
   }
  
  if($imagename1[1]=="jpg" or $imagename1[1]=="gif" or $imagename1[1]=="png" or $row_type=='url') {
   
    $path=$result['name'];
    $path1=explode("/",$result['name']);
    $real_path=$result['name'];
    $timelineneme=$imagename[1];
    if($path1[0]=="images") {
       $path="images/thumb/".$imagename[1];
       $timelineneme=$imagename[1];
     }
     if($path=="promotie") {
       $path="promotie/thumb/".$imagename[1];
       
     }
     if($path==$path1[0] and $path1[1]=="") {
       $path="urls/thumb/".$dynamic_url_info[$url_ques_id]['image'];
     }
    $duration_value=$result['duration'];
    if($carr_id) {
     $duration="<input type='text' name='duration[]'  value='$duration_value' onblur='updatetime($tempid,this.value, $carr_id)'><span>s</span>"; 
    }else {
    $duration="<input type='text' name='duration[]'  value='$duration_value' onblur='updatetime($tempid,this.value)'><span>s</span>";
    }
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
	//$overlay_link="<a href='createimage_overlay.php?path=$real_path&id=$tempid&cid=$carr_id&show_cal=1'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	$overlay_link="<a href='javascript://' onclick='create_image_overlay(\"$real_path\",\"$tempid\",\"$carr_id\",1)'><div class='green_btn_horizon'>Tekst toev.</div></a>";
      }
    } else {
      //$overlay_link="<a href='createimage_overlay.php?path=$real_path&id=$tempid&cid=$carr_id&show_cal=1'><div class='green_btn_horizon'>Tekst toev.</div></a>";
      $overlay_link="<a href='javascript://' onclick='create_image_overlay(\"$real_path\",\"$tempid\",\"$carr_id\",1)'><div class='green_btn_horizon'>Tekst toev.</div></a>";
    }
  
    if($row_type!='url'){
    $preview_btn="<a href='./$path1[0]/$image_name_timeline?time=".time()."' rel='single'  class='pirobox'><div class='btn_preview'><img src='$preview_button_path' width='20' height='20' /></div></a>";
    }else{
     $preview_btn='';
    }
  } else if($imagename1[1]=="mov" or $imagename1[1]=="mp4"){
  
    $path1=explode("/",$result['name']);
  
    if($path1[0]=="videos") {
      $path="videos/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="videos/".$imagename[1];

    } else if($path1[0]=="mededelingen") {
      $path="mededelingen/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="mededelingen/".$imagename[1];
      $timelineneme=$imagename[1];
    }else if($path1[0]=="delay_mededelingen") {
      $path="delay_mededelingen/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="delay_mededelingen/".$imagename[1];
     
    }else if($path1[0]=="overlay_video") {
      $path="overlay_video/thumb/".$imagename1[0].".jpg";
      $real_path="overlay_video/".$imagename[1];
      $timelineneme=$imagename[1];
    } else if($path1[0]=="commercials") {
      $path="commercials/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";

      $timelineneme=$imagename[1];
    }else if($path1[0]=="promotie") {
      $path="promotie/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="promotie/".$imagename[1];
    
    }else {
      $path="upload/thumb/".str_replace(" ","_",$imagename1[0]).".jpg";
      $real_path="upload/".$imagename[1];
      //$timelineneme=$imagename[1];
    }
    $duration="<input type='hidden' name='duration[]' value='1'>";
    if($path1[0]=="overlay_video") {
      $overlay_link="";
    } else if($path1[0]=="mededelingen"){
      $checktwt=substr($path1[1],0,7);
      if($checktwt=="Twitter") {
	$overlay_link="";
      } else {
	///$overlay_link="<a href='create_overlay.php?path=$real_path&id=$tempid&cid=$carr_id'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	$overlay_link="<a href='javascript://' onclick='create_video_overlay(\"$real_path\",\"$tempid\",\"$carr_id\",\"1\")'><div class='green_btn_horizon'>Tekst toev.</div></a>";
	
      }
    } else {
      //$overlay_link="<a href='create_overlay.php?path=$real_path&id=$tempid&cid=$carr_id'><div class='green_btn_horizon'>Tekst toev.</div></a>";
      $overlay_link="<a href='javascript://' onclick='create_video_overlay(\"$real_path\",\"$tempid\",\"$carr_id\",\"1\")'><div class='green_btn_horizon'>Tekst toev.</div></a>";
    }
   
   
    $preview_btn="<a href='javascript:void(0);'   onclick='popitup(\"$path1[0]\",\"$path1[1]\")'><div class='btn_preview'><img src='$preview_button_path' width='20' height='20' /></div></a>"; 
   
  } else {
	
  }//sssssssssss
  $imagename=explode("/",$path);
    if($row_type=='url'){
     $image_name=preg_replace('/^http(s)?:\/\//','', $dynamic_url_info[$url_ques_id]['name']);
    }
    else if($imagename[0]!="overlay_video") {
     if(substr($imagename1[0],0,7)=="Twitter") {
      $twitter_name=explode("_",$imagename1[0]);
      $image_name=str_replace("-",'"',$twitter_name[0]);
     } else {
      $image_name=stripslashes(str_replace("_"," ",substr($imagename1[0],0,40)));
     }
    }
    else {
     if(substr($imagename1[0],0,7)=="Twitter") {
      $twitter_name=explode("_",$imagename1[0]);
      $image_name=str_replace("-",'"',$twitter_name[0]);
     } else {
     $image_name=stripslashes(str_replace("_"," ",substr(substr($imagename1[0],0,-5),0,40)));
     }
   }
   
   
   
   if(strstr($path, 'thumb')==true){
    $path_thumb = $path;
    }else{
     $path_thumb = explode('/',$path);
    $path_thumb = $path_thumb[0].'/thumb/'.$path_thumb[1];
    }
    $commercial_add.='*'.$real_path;
    $ext=explode(".", $timelineneme);
    if($path1[0]=='mededelingen'){
     $timelineneme=$imagename[1];
    }
   
    
    if($path1[0]=='mededelingen' || $path1[0]=='overlay_video'){
     if($ext[1]!='mov' && $path1[0]!='mededelingen'){
     $edit_btn="<div class='edit_btn_edit'><a href='javascript:void(0);' onclick='editImage(\"overlays_image\",\"$timelineneme\",\"$carr_id\")'><img src='$edit_button_path' alt='' width='20' height='20'></a>
	   </div>";
     }else{
      $edit_btn="<div class='edit_btn_edit'><a href='javascript:void(0);' onclick='editImage(\"$path1[0]\",\"$timelineneme\",\"$carr_id\")'><img src='$edit_button_path' alt='' width='20' height='20'></a>
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
    
   /* if($ext[1]!='mov' && $path1[0]!='mededelingen'){
     $edit_btn="<div class='edit_btn_edit'><a href='javascript:void(0);' onclick='editImage(\"overlays_image\",\"$timelineneme\",\"$carr_id\")'><img src='$edit_button_path' alt='' width='15' height='15'></a>
	   </div>";
    }*/
    if($path1[0]=="mededelingen"){
      $overlay_link="";
    }
  $carrousel_listing.="<li class='horiz_block' id='recordsArray_".stripslashes($cid)."'>
                        	<div class='horiz_img'>
				$edit_btn
                            	<img class='horiz_img_thumb' src='./".stripslashes($path_thumb)."?time=".time()."' width='135' height='135' alt='' onmouseover='movedata()'/>
                            	<div class='close_btn'><a href='javascript:void(0);' onclick='deleteCarrousel(\"$cid\")'><img src='$close_button_path' alt=''/></a></div>
				$preview_btn
   		     <div class='calendar'>
   		     <a href='javascript:NewCal(\"demo_$cid\",\"mmddyyyy\", \"carrausel\")' >
              <img id='img_$cid' src='$calender_button_path' title='Pick end date' /></a>
       <input  type='hidden' name='sdate[]' id='demo_$cid' value='$enddate' class='datum_plannen' size='10' readonly'>
       <input  type='hidden' name='date[]' id='date_$cid' value='' class='datum_plannen' size='10' readonly'>
   
       <input  type='hidden' name='color_scheme[]' id='color_scheme_$cid' class='datum_plannen' size='10' value='$color_scheme'>
       </div>
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
 $carrousel_listing.="<input type='hidden' name='commercial_check' id='commercial_check' value='$commercial_add'></ul></div>";
 echo $carrousel_listing;
 exit; 
?>





