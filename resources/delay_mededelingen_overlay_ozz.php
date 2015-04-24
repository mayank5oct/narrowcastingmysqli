<?php
session_start();
error_reporting(0);
ini_set('max_execution_time', 3600);
header('<meta http-equiv="content-type" content="text/html; charset=utf-8">');
include('config/database.php');
include('config/get_theatre_label.php');
include('color_scheme_setting.php');
require_once('thumb-functions.php');
$_SESSION['refferer'] = 'delay_mededelingen_overlay.php';
$close_button_path="./img/$color_scheme/close_btn.png";
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}

header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
//echo "<pre>"; print_r($_POST); echo "</pre>";
/*if(isset($_POST['submit']) && $_POST['submit']=='template1'){
 echo "<pre>"; print_r($_POST); echo "</pre>";
exit;
$_POST['edit_image_name_1']=str_replace(" ","_",$_POST['edit_image_name_1']);
$templine1=urldecode($_GET['line1'])!=undefined?trim(urldecode($_GET['line1'])):"";
}*/


if(isset($_POST['submit']) && $_POST['submit']!='' && $_POST['is_delay']==0){

 $data_display ="none";
 //echo "<pre>"; print_r($_POST); echo "</pre>";
//exit;
} else{
 
 $data_display= "block";
 
}

$_POST['edit_image_name_1']=str_replace(" ","_",$_POST['edit_image_name_1']);
$_POST['edit_image_name_2']=str_replace(" ","_",$_POST['edit_image_name_2']);
$_POST['edit_image_name_3']=str_replace(" ","_",$_POST['edit_image_name_3']);
$_POST['edit_image_name_4']=str_replace(" ","_",$_POST['edit_image_name_4']);
$_POST['edit_image_name_5']=str_replace(" ","_",$_POST['edit_image_name_5']);


//---------------------------Code used for upload video and images -------------------------------
 $error_message="";
 $sid=session_id();
 $db = new Database;
 $template_id=$_REQUEST['template_id'];
 $templine1=urldecode($_GET['line1'])!=undefined?trim(urldecode($_GET['line1'])):"";
 $templine2=urldecode($_GET['line2'])!=undefined?trim(urldecode($_GET['line2'])):"";
 $templine3=urldecode($_GET['line3'])!=undefined?trim(urldecode($_GET['line3'])):"";
 $templine4=urldecode($_GET['line4'])!=undefined?trim(urldecode($_GET['line4'])):"";
 $templine5=urldecode($_GET['line5'])!=undefined?trim(urldecode($_GET['line5'])):"";
 $templine6=urldecode($_GET['line6'])!=undefined?trim(urldecode($_GET['line6'])):"";
 $templine7=urldecode($_GET['line7'])!=undefined?trim(urldecode($_GET['line7'])):"";
 $templine8=urldecode($_GET['line8'])!=undefined?trim(urldecode($_GET['line8'])):"";
 $theatre_id = $minutes_data['id'];    
 ## setting  writting  image on template
 $tempwrite_image_path = trim($_REQUEST['write_image_path'])!=undefined?trim($_REQUEST['write_image_path']):"";
 $tempwrite_image_path_array=explode("/",$tempwrite_image_path);
 if($tempwrite_image_path_array[0]==""){
  $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[1].'/thumb/'.$tempwrite_image_path_array[2];
 }
 else{
  $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[0].'/thumb/'.$tempwrite_image_path_array[1];
 }
 
 $request_for=trim($_REQUEST['request_for']);
 $carrousel_id=trim($_REQUEST['carrousel_id']);

 $temp_mededelingen_id=intval(trim($_REQUEST['temp_mededelingen_id']));
 $edit_image_name=trim(urldecode($_REQUEST['edit_image_name']))!=undefined?trim(urldecode($_REQUEST['edit_image_name'])):"";
 $come_from=trim(urldecode($_REQUEST['come_from']))!=undefined?trim(urldecode($_REQUEST['come_from'])):"";
 
 $showvideo="";// show created image overlay on the top
 //echo $theatre_tempid;
 
 
 //---------------- Code used for getting font from font folder -----------------------------------
 function font_listing($value=''){
 $font_listing="";
  if ($handle = opendir("./fonts")) {
    $i=1;
     $font_listing.="<option value=''>--selecteer lettertype--</option>";
     while (false !== ($entry = readdir($handle))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!=".DS_Store") {
	$font_style=explode(".",$entry);
	if($value==$entry){
	 $select ='selected';
	 }else{$select='';
	 }
        $font_listing.="<option value='$entry' $select>$font_style[0]</option>";
      }
     }
     closedir($handle);
   }
   return $font_listing;
 }
 
 
 function replacedot($imagename){
  $imagename_array=explode('.png',$imagename);
 
  $imagename=str_replace('.',"_",$imagename_array[0]);
  $imagename=$imagename.".png";

  return $imagename;
 }
 
 function text_center_options($value=''){
     $text_center_options="";//<option value=''>selecteer</option>";
     $text_centre_option_data=array('Ja'=>1,'Nee'=>0);
     foreach ($text_centre_option_data  as $label=>$optionValue) {
	if(($value===$optionValue)){
	  $select ='selected';
	 }
	 else{
	  $select='';
	 }
        $text_center_options.="<option value='$optionValue' $select>$label</option>";
      
     }
   return $text_center_options;
 }
 
 // form submit on on create caurrsal page
 if(isset($_POST['submit'])) {
  
  //echo "<pre>"; print_r($_POST); echo "</pre>";
  //exit;
  
   $temp_mededelingen_id=$_POST['temp_mededelingen_id'];
   
   $edit_image_name_number='edit_image_name_'.$_POST['number'];
   $edit_image_name = str_replace(" ","_",$_POST[$edit_image_name_number]);
  $edit_image_name = replacedot($_POST[$edit_image_name_number]);

    $number=$_POST['number'];
   
   
 
   
   
    
    
        $option1 = 'option_'.$number.'_1';
	$option2 = 'option_'.$number.'_2';
	$option3 = 'option_'.$number.'_3';
	$option4 = 'option_'.$number.'_4';
	$option5 = 'option_'.$number.'_5';
	$option6 = 'option_'.$number.'_6';
	$option7 = 'option_'.$number.'_7';
	$option8 = 'option_'.$number.'_8';
	$notpresent1 = 'notpresent_'.$number.'_1';
	$notpresent2 = 'notpresent_'.$number.'_2';
	$notpresent3 = 'notpresent_'.$number.'_3';
	$notpresent4 = 'notpresent_'.$number.'_4';
	$notpresent5 = 'notpresent_'.$number.'_5';
	$notpresent6 = 'notpresent_'.$number.'_6';
	$notpresent7 = 'notpresent_'.$number.'_7';
	$notpresent8 = 'notpresent_'.$number.'_8';
	$minutes1 = 'minutes_'.$number.'_1';
	$minutes2 = 'minutes_'.$number.'_2';
	$minutes3 = 'minutes_'.$number.'_3';
	$minutes4 = 'minutes_'.$number.'_4';
	$minutes5 = 'minutes_'.$number.'_5';
	$minutes6 = 'minutes_'.$number.'_6';
	$minutes7 = 'minutes_'.$number.'_7';
	$minutes8 = 'minutes_'.$number.'_8';
    
    
    
   if(isset($_POST[$option1]) && $_POST[$option1]=='emergency'){     
     $is_emergency1 = 1;
    }else{
     $is_emergency1 = 0;
    }
    
    if(isset($_POST[$option2]) && $_POST[$option2]=='emergency'){     
     $is_emergency2 = 1;
    }else{
     $is_emergency2 = 0;
    }
    
    if(isset($_POST[$option3]) && $_POST[$option3]=='emergency'){     
     $is_emergency3 = 1;
    }else{
     $is_emergency3 = 0;
    }
    
    if(isset($_POST[$option4]) && $_POST[$option4]=='emergency'){     
     $is_emergency4 = 1;
    }else{
     $is_emergency4 = 0;
    }
    
    if(isset($_POST[$option5]) && $_POST[$option5]=='emergency'){     
     $is_emergency5 = 1;
    }else{
     $is_emergency5 = 0;
    }
    
    if(isset($_POST[$option6]) && $_POST[$option6]=='emergency'){     
     $is_emergency6 = 1;
    }else{
     $is_emergency6 = 0;
    }
    
    if(isset($_POST[$option7]) && $_POST[$option7]=='emergency'){     
     $is_emergency7 = 1;
    }else{
     $is_emergency7 = 0;
    }
    
    if(isset($_POST[$option8]) && $_POST[$option8]=='emergency'){     
     $is_emergency8 = 1;
    }else{
     $is_emergency8 = 0;
    }
    
    if(isset($_POST[$option1]) && $_POST[$option1]=='notpresent'){     
     $not_present_1 = 1;
    }else{
     $not_present_1 = 0;
    }
    
    if(isset($_POST[$option2]) && $_POST[$option2]=='notpresent'){     
     $not_present_2 = 1;
    }else{
     $not_present_2 = 0;
    }
    
    if(isset($_POST[$option3]) && $_POST[$option3]=='notpresent'){     
     $not_present_3 = 1;
    }else{
     $not_present_3 = 0;
    }
    
    if(isset($_POST[$option4]) && $_POST[$option4]=='notpresent'){     
     $not_present_4 = 1;
    }else{
     $not_present_4 = 0;
    }
    
    if(isset($_POST[$option5]) && $_POST[$option5]=='notpresent'){     
     $not_present_5 = 1;
    }else{
     $not_present_5 = 0;
    }
    
    if(isset($_POST[$option6]) && $_POST[$option6]=='notpresent'){     
     $not_present_6 = 1;
    }else{
     $not_present_6 = 0;
    }
    
    if(isset($_POST[$option7]) && $_POST[$option7]=='notpresent'){     
     $not_present_7 = 1;
    }else{
     $not_present_7 = 0;
    }
    
    if(isset($_POST[$option8]) && $_POST[$option8]=='notpresent'){     
     $not_present_8 = 1;
    }else{
     $not_present_8 = 0;
    }
    
    if(isset($_POST[$option1]) && $_POST[$option1]=='minutes'){     
     $is_minutes1 = 1;
    }else{
     $is_minutes1 = 0;
    }
    
    if(isset($_POST[$option2]) && $_POST[$option2]=='minutes'){     
     $is_minutes2 = 1;
    }else{
     $is_minutes2 = 0;
    }
    
    if(isset($_POST[$option3]) && $_POST[$option3]=='minutes'){     
     $is_minutes3 = 1;
    }else{
     $is_minutes3 = 0;
    }
    
    if(isset($_POST[$option4]) && $_POST[$option4]=='minutes'){     
     $is_minutes4 = 1;
    }else{
     $is_minutes4 = 0;
    }
    
    if(isset($_POST[$option5]) && $_POST[$option5]=='minutes'){     
     $is_minutes5 = 1;
    }else{
     $is_minutes5 = 0;
    }
    
    if(isset($_POST[$option6]) && $_POST[$option6]=='minutes'){     
     $is_minutes6 = 1;
    }else{
     $is_minutes6 = 0;
    }
    
    if(isset($_POST[$option7]) && $_POST[$option7]=='minutes'){     
     $is_minutes7 = 1;
    }else{
     $is_minutes7 = 0;
    }
    
    if(isset($_POST[$option8]) && $_POST[$option8]=='minutes'){     
     $is_minutes8 = 1;
    }else{
     $is_minutes8 = 0;
    }
  
    
    
  
   
    
    $theatre_id =$_REQUEST['theatre_id'];
    $template_id =$_REQUEST['template_id']; 
    $getCordinates  = "select * from delay_mededelingen where id='$template_id'";
    $getCordinates =$db->query($getCordinates);
    $getCordinates = mysql_fetch_array($getCordinates);
   
    $template_name=$getCordinates['template_name'];
    
    
     $name_t_val	 = str_replace(" ","_",$getCordinates['name_t']);
   
    $name_t_val	 = str_replace(".","_",$getCordinates['name_t']);
    
    $template_nameArray = explode('.',$template_name);
    
    //$replace_word = array('"');
    
     $newimage='/var/www/html/narrowcasting/mededelingen_template/'.$template_name;
     $newpath=$template_nameArray[0];
   
    
    if($template_nameArray[1]=="jpeg" or $template_nameArray[1]=="jpg") {
	  $overlay_image= imagecreatefromjpeg($newimage);
	} else if($template_nameArray[1]=="gif") {
	  $overlay_image= imagecreatefromgif($newimage);
	} else if($template_nameArray[1]=="png") {
	  $overlay_image= imagecreatefrompng($newimage);
	} else {
	   
	}
   

    $width=imagesx($overlay_image);
    $height=imagesy($overlay_image);
     
    $image_p = imagecreatetruecolor($width, $height);
       
    imagesavealpha($image_p , true);
    $trans_colour = imagecolorallocatealpha($image_p , 0, 0, 0, 127);
    imagefill($image_p , 0, 0, $trans_colour);
    
    
   
    $imagetransparent='/var/www/html/narrowcasting/mededelingen_template/'.$template_name;;
   
   
    $im = imagecreatefrompng($imagetransparent);
   
    $width_1=imagesx($im);  
    $height_1=imagesy($im);
    
    
    $font_size1 = $getCordinates['font_size1'];
    $font_size2 = $getCordinates['font_size2'];
    $font_size3 = $getCordinates['font_size3'];
    $font_size4 = $getCordinates['font_size4'];
    $font_size5 = $getCordinates['font_size5'];
    $font_size6 = $getCordinates['font_size6'];
    $font_size7 = $getCordinates['font_size7'];
    $font_size8 = $getCordinates['font_size8']; 
	
    $x1 = $getCordinates['x1'];
    $x2 = $getCordinates['x2'];
    $x3 = $getCordinates['x3'];
    $x4 = $getCordinates['x4'];
    $x5 = $getCordinates['x5'];
    $x6 = $getCordinates['x6'];
    $x7 = $getCordinates['x7'];
    $x8 = $getCordinates['x8'];
    
    $x2_1 = $getCordinates['x2_1'];
    $x2_2 = $getCordinates['x2_2'];
    $x2_3 = $getCordinates['x2_3'];
    $x2_4 = $getCordinates['x2_4'];
    $x2_5 = $getCordinates['x2_5'];
    $x2_6 = $getCordinates['x2_6'];
    $x2_7 = $getCordinates['x2_7'];
    $x2_8 = $getCordinates['x2_8'];
	
	
    $y1 = $getCordinates['y1'];
    $y2 = $getCordinates['y2'];
    $y3 = $getCordinates['y3'];
    $y4 = $getCordinates['y4'];
    $y5 = $getCordinates['y5'];
    $y6 = $getCordinates['y6'];
    $y7 = $getCordinates['y7'];
    $y8 = $getCordinates['y8'];
	
	
	 $status1 = $getCordinates['status1'];
	 $status2 = $getCordinates['status2'];
	 $status3 = $getCordinates['status3'];
	 $status4 = $getCordinates['status4'];
	 $status5 = $getCordinates['status5'];
	 $status6 = $getCordinates['status6'];
	 $status7 = $getCordinates['status7'];
	 $status8 = $getCordinates['status8'];
	
	 
	$line_height1 = $getCordinates['line_height1'];
	$line_height2 = $getCordinates['line_height2'];
	$line_height3 = $getCordinates['line_height3'];
	$line_height4 = $getCordinates['line_height4'];
	$line_height5 = $getCordinates['line_height5'];
	$line_height6 = $getCordinates['line_height6'];
	$line_height7 = $getCordinates['line_height7'];
	$line_height8 = $getCordinates['line_height8'];
	## setting all text center variables
	
	$text_center1 = intval(trim($getCordinates['text_center1']));
	$text_center2 = intval(trim($getCordinates['text_center2']));
	$text_center3 = intval(trim($getCordinates['text_center3']));
	$text_center4 = intval(trim($getCordinates['text_center4']));
	$text_center5 = intval(trim($getCordinates['text_center5']));
	$text_center6 = intval(trim($getCordinates['text_center6']));
	$text_center7 = intval(trim($getCordinates['text_center7']));
	$text_center8 = intval(trim($getCordinates['text_center8']));
	
        $temp_mededelingen_id=intval(trim($_REQUEST['temp_mededelingen_id']));
	$edit_image_name_1=trim(urldecode(str_replace(" ","_",$_REQUEST['edit_image_name_1'])))!=undefined?trim(urldecode(str_replace(" ","_",$_REQUEST['edit_image_name_1']))):"";
        $twtcheck=$_REQUEST['twt'];
	
	$fonttype1=$getCordinates['font1'];
	$fonttype2=$getCordinates['font2'];
	$fonttype3=$getCordinates['font3'];
	$fonttype4=$getCordinates['font4'];
	$fonttype5=$getCordinates['font5'];
	$fonttype6=$getCordinates['font6'];
	$fonttype7=$getCordinates['font7'];
	$fonttype8=$getCordinates['font8'];
	
	$font1 = '/var/www/html/narrowcasting/fonts/'.$fonttype1;
	$font2 = '/var/www/html/narrowcasting/fonts/'.$fonttype2;
	$font3 = '/var/www/html/narrowcasting/fonts/'.$fonttype3;
	$font4 = '/var/www/html/narrowcasting/fonts/'.$fonttype4;
	$font5 = '/var/www/html/narrowcasting/fonts/'.$fonttype5;
	$font6 = '/var/www/html/narrowcasting/fonts/'.$fonttype6;
	$font7 = '/var/www/html/narrowcasting/fonts/'.$fonttype7;
	$font8 = '/var/www/html/narrowcasting/fonts/'.$fonttype8;
	
	$color1="#".$getCordinates['color1'];
	$color2="#".$getCordinates['color2'];
	$color3="#".$getCordinates['color3'];
	$color4="#".$getCordinates['color4'];
	$color5="#".$getCordinates['color5'];
	$color6="#".$getCordinates['color6'];
	$color7="#".$getCordinates['color7'];
	$color8="#".$getCordinates['color8'];
	
	$colorcode1=sscanf($color1, '#%2x%2x%2x');
	$colorcode2=sscanf($color2, '#%2x%2x%2x');
	$colorcode3=sscanf($color3, '#%2x%2x%2x');
	$colorcode4=sscanf($color4, '#%2x%2x%2x');
	$colorcode5=sscanf($color5, '#%2x%2x%2x');
	$colorcode6=sscanf($color6, '#%2x%2x%2x');
	$colorcode7=sscanf($color7, '#%2x%2x%2x');
	$colorcode8=sscanf($color8, '#%2x%2x%2x');
		 
	$color_1 =imagecolorallocate($im, $colorcode1[0], $colorcode1[1], $colorcode1[2]);
	
	
	$color_2= imagecolorallocate($im, $colorcode2[0], $colorcode2[1],$colorcode2[2]);
	$color_3= imagecolorallocate($im, $colorcode3[0], $colorcode3[1],$colorcode3[2]);
	$color_4= imagecolorallocate($im, $colorcode4[0], $colorcode4[1],$colorcode4[2]);
	$color_5= imagecolorallocate($im, $colorcode5[0], $colorcode5[1],$colorcode5[2]);
	$color_6= imagecolorallocate($im, $colorcode6[0], $colorcode6[1],$colorcode6[2]);
	$color_7= imagecolorallocate($im, $colorcode7[0], $colorcode7[1],$colorcode7[2]);
	$color_8= imagecolorallocate($im, $colorcode8[0], $colorcode8[1],$colorcode8[2]);
    
    
        $text1=str_replace($replace_word,"",trim($getCordinates['line1']));
	$text2=str_replace($replace_word,"",trim($getCordinates['line2']));
        $text3=str_replace($replace_word,"",trim($getCordinates['line3']));
	$text4=str_replace($replace_word,"",trim($getCordinates['line4']));
	$text5=str_replace($replace_word,"",trim($getCordinates['line5']));
	$text6=str_replace($replace_word,"",trim($getCordinates['line6']));
	$text7=str_replace($replace_word,"",trim($getCordinates['line7']));
	$text8=str_replace($replace_word,"",trim($getCordinates['line8']));
	
	
	$delay_text = "";
	$number=$_POST['number'];
	$minutes=$_POST['minutes'];
	$delay_text = "delay_text_".$number;
	$non_delay_text = "non_delay_text_".$number;
	$emergency_text = "emergency_".$number;
	
	
	 $delay_text = "";
	 ///////// First Template Begin ////////
	if($_POST[$option1]!='notpresent'){
	 if($_POST[$option1]=='emergency'){
	 
	 $delay_text_1 = $getCordinates['emergency_1'];
<<<<<<< HEAD:resources/delay_mededelingen_overlay_zonnestraal.php
	 
	 }elseif($_POST[$option1]=='notpresent'){
	 $delay_text_1 = $getCordinates['not_present_1'];
	 
	 }else{
	  if($_POST[$minutes1] != '' && $_POST[$minutes1] !=  '0'){
	   $color_1 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_1 = $getCordinates['delay_text_1'] ." ". $_POST[$minutes1] ." min.";
	 }else{
	 
=======
	 
	 }elseif($_POST[$option1]=='notpresent'){
	 $delay_text_1 = $getCordinates['not_present_1'];
	 
	 }else{
	  if($_POST[$minutes1] != '' && $_POST[$minutes1] !=  '0'){
	   $color_1 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_1 = $getCordinates['delay_text_1'] ." ". $_POST[$minutes1] ." min.";
	 }else{
	 
>>>>>>> FETCH_HEAD:resources/delay_mededelingen_overlay_ozz.php
	  $delay_text_1 = $getCordinates['non_delay_text_1'];
	 }
	  
	 }
	 $label_1=$getCordinates['label1'];
	}
        /*if(isset($_POST[$option1]) && !empty($_POST[$option1])){
	 if($_POST[$option1]=='notpresent'){	
	 $delay_text_1 = $getCordinates['not_present_1'];
	 
	 }else{
	  if($_POST[$minutes1] != '' && $_POST[$minutes1] !=  '0'){
	  $delay_text_1 = $getCordinates['delay_text_1'] ." ". $_POST[$minutes1] ." min.";
	 }else{
	  $delay_text_1 = $getCordinates['non_delay_text_1'];
	 }
	  
	 }
	}*/
	
	if($_POST[$option2]!='notpresent'){
	 if($_POST[$option2]=='emergency'){	
	 $delay_text_2 = $getCordinates['emergency_2'];
	 
	 }elseif($_POST[$option2]=='notpresent'){
	  $delay_text_2 = $getCordinates['not_present_2'];
	 }else{
	  if($_POST[$minutes2] != '' && $_POST[$minutes2] !=  '0'){
	   $color_2 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_2 = $getCordinates['delay_text_2'] ." ". $_POST[$minutes2] ." min.";
	 }else{
	  
	  $delay_text_2 = $getCordinates['non_delay_text_2'];
	 }
	  
	 }
	 $label_2=$getCordinates['label2'];
	}
	/*if(isset($_POST[$option2]) && !empty($_POST[$option2])){
	 if($_POST[$option2]=='notpresent'){
	 $delay_text_2 = $getCordinates['not_present_2'];
	 
	 }else{
	  if($_POST[$minutes2] != '' && $_POST[$minutes2] !=  '0'){
	  $delay_text_2 = $getCordinates['delay_text_2'] ." ". $_POST[$minutes2] ." min.";
	 }else{
	  $delay_text_2 = $getCordinates['non_delay_text_2'];
	 }
	  
	 }
	}*/
	
	if($_POST[$option3]!='notpresent'){
	 if($_POST[$option3]=='emergency'){	
	 
	 $delay_text_3 = $getCordinates['emergency_3'];
	 }elseif($_POST[$option3]=='notpresent'){
	  $delay_text_3 = $getCordinates['not_present_3'];
	 }else{
	  if($_POST[$minutes3] != '' && $_POST[$minutes3] !=  '0'){
	   $color_3 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_3 = $getCordinates['delay_text_3'] ." ". $_POST[$minutes3] ." min.";
	 }else{
	  
	  $delay_text_3 = $getCordinates['non_delay_text_3'];
	 }
	  
	 }
	  $label_3=$getCordinates['label3'];
	}
	/*if(isset($_POST[$option3]) && !empty($_POST[$option3])){
	 if($_POST[$option3]=='notpresent'){
	 $delay_text_3 = $getCordinates['not_present_3'];
	 
	 }else{
	  if($_POST[$minutes3] != '' && $_POST[$minutes3] !=  '0'){
	  $delay_text_3 = $getCordinates['delay_text_3'] ." ". $_POST[$minutes3] ." min.";
	 }else{
	  $delay_text_3 = $getCordinates['non_delay_text_3'];
	 }
	  
	 }
	}*/
	if($_POST[$option4]!='notpresent'){
	 if($_POST[$option4]=='emergency'){	
	 
	 $delay_text_4 = $getCordinates['emergency_4'];
	 }elseif($_POST[$option4]=='notpresent'){
	  $delay_text_4 = $getCordinates['not_present_4'];
	 }else{
	  if($_POST[$minutes4] != '' && $_POST[$minutes4] !=  '0'){
	   $color_4 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_4 = $getCordinates['delay_text_4'] ." ". $_POST[$minutes4] ." min.";
	 }else{
	  
	  $delay_text_4 = $getCordinates['non_delay_text_4'];
	 }
	  
	 }
	  $label_4=$getCordinates['label4'];
	}
	/*if(isset($_POST[$option4]) && !empty($_POST[$option4])){
	 if($_POST[$option4]=='notpresent'){
	 $delay_text_4 = $getCordinates['not_present_4'];
	 
	 }else{
	  if($_POST[$minutes4] != '' && $_POST[$minutes4] !=  '0'){
	  $delay_text_4 = $getCordinates['delay_text_4'] ." ". $_POST[$minutes4] ." min.";
	 }else{
	  $delay_text_4 = $getCordinates['non_delay_text_4'];
	 }
	  
	 }
	}*/
	
	if($_POST[$option5]!='notpresent'){
	 if($_POST[$option5]=='emergency'){	
	 $delay_text_5 = $getCordinates['emergency_5'];
	 
	 }elseif($_POST[$option5]=='notpresent'){
	  $delay_text_5 = $getCordinates['not_present_5'];
	 }else{
	  if($_POST[$minutes5] != '' && $_POST[$minutes5] !=  '0'){
	   $color_5 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_5 = $getCordinates['delay_text_5'] ." ". $_POST[$minutes5] ." min.";
	 }else{
	  
	  $delay_text_5 = $getCordinates['non_delay_text_5'];
	 }
	  
	 }
	  $label_5=$getCordinates['label5'];
	}
	/*if(isset($_POST[$option5]) && !empty($_POST[$option5])){
	 if($_POST[$notpresent5]=='notpresent'){
	 $delay_text_5 = $getCordinates['not_present_5'];
	
	 }else{
	   if($_POST[$minutes5] != '' && $_POST[$minutes5] !=  '0'){
	  $delay_text_5 = $getCordinates['delay_text_5'] ." ". $_POST[$minutes5] ." min.";
	 }else{
	  $delay_text_5 = $getCordinates['non_delay_text_5'];
	 }
	  
	 }
	}*/
	
	if($_POST[$option6]!='notpresent'){
	 if($_POST[$option6]=='emergency'){	
	 $delay_text_6 = $getCordinates['emergency_6'];
<<<<<<< HEAD:resources/delay_mededelingen_overlay_zonnestraal.php
	 
	 }elseif($_POST[$option6]=='notpresent'){
	  $delay_text_6 = $getCordinates['not_present_6'];
	  }else{
	  if($_POST[$minutes6] != '' && $_POST[$minutes6] !=  '0'){
	   $color_6 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_6 = $getCordinates['delay_text_6'] ." ". $_POST[$minutes6] ." min.";
	 }else{
	  
	  $delay_text_6 = $getCordinates['non_delay_text_6'];
	 }
	  
	 }
	  $label_6=$getCordinates['label6'];
=======
	 
	 }elseif($_POST[$option6]=='notpresent'){
	  $delay_text_6 = $getCordinates['not_present_6'];
	  }else{
	  if($_POST[$minutes6] != '' && $_POST[$minutes6] !=  '0'){
	   $color_6 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_6 = $getCordinates['delay_text_6'] ." ". $_POST[$minutes6] ." min.";
	 }else{
	  
	  $delay_text_6 = $getCordinates['non_delay_text_6'];
	 }
	  
	 }
	  $label_6=$getCordinates['label6'];
	}
	
	if($_POST[$option7]!='notpresent'){
	 if($_POST[$option7]=='emergency'){	
	 $delay_text_7 = $getCordinates['emergency_7'];
	 
	 }elseif($_POST[$option7]=='notpresent'){
	  $delay_text_7 = $getCordinates['not_present_7'];
	  }else{
	  if($_POST[$minutes7] != '' && $_POST[$minutes7] !=  '0'){
	   $color_7 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_7 = $getCordinates['delay_text_7'] ." ". $_POST[$minutes7] ." min.";
	 }else{
	  
	  $delay_text_7 = $getCordinates['non_delay_text_7'];
	 }
	  
	 }
	  $label_7=$getCordinates['label7'];
	}
	
	
	if($_POST[$option8]!='notpresent'){
	 if($_POST[$option8]=='emergency'){	
	 $delay_text_8 = $getCordinates['emergency_8'];
	 
	 }elseif($_POST[$option8]=='notpresent'){
	  $delay_text_8 = $getCordinates['not_present_8'];
	  }else{
	  if($_POST[$minutes8] != '' && $_POST[$minutes8] !=  '0'){
	   $color_8 =imagecolorallocate($im, 148, 54, 55);
	  $delay_text_8 = $getCordinates['delay_text_8'] ." ". $_POST[$minutes8] ." min.";
	 }else{
	  
	  $delay_text_8 = $getCordinates['non_delay_text_8'];
	 }
	  
	 }
	  $label_8=$getCordinates['label8'];
>>>>>>> FETCH_HEAD:resources/delay_mededelingen_overlay_ozz.php
	}
	/*if(isset($_POST[$option6]) && !empty($_POST[$option6])){
	 if($_POST[$option6]=='notpresent'){
	  $delay_text_6 = $getCordinates['not_present_6'];
	 
	 }else{
	  if($_POST[$minutes6] != '' && $_POST[$minutes6] !=  '0'){
	  $delay_text_6 = $getCordinates['delay_text_6'] ." ". $_POST[$minutes6] ." min.";
	 }else{
	  $delay_text_6 = $getCordinates['non_delay_text_6'];
	 }
	 
	 }
	}*/
	
	
	//////// First Template End //////
	/*if($_POST['emergency_'.$number]!='on'){	
	 if($minutes_2 != '' && $minutes_2 != '0'){
	  $delay_text_2 = $getCordinates['delay_text_2'] ." ". $_POST['minutes_2_'.$number] ." min.";
	 }else{
	  $delay_text_2 = $getCordinates['non_delay_text_2'];
	 }
	 }else{
	 $delay_text_2 = $getCordinates['emergency_2'];
	}
	
	
	if($_POST['emergency_'.$number]!='on'){	
	if($minutes_3 != '' && $minutes_3 != '0'){
	 $delay_text_3 = $getCordinates['delay_text_3'] ." ". $_POST['minutes_3_'.$number] ." min.";
	}else{
	 $delay_text_3 = $getCordinates['non_delay_text_3'];
	}
	 }else{
	 $delay_text_3 = $getCordinates['emergency_3'];
	}
	
	
	if($_POST['emergency_'.$number]!='on'){	
	if($minutes_4 != '' && $minutes_4 != '0'){
	 $delay_text_4 = $getCordinates['delay_text_4'] ." ". $_POST['minutes_4_'.$number] ." min.";
	}else{
	 $delay_text_4 = $getCordinates['non_delay_text_4'];
	}
	 }else{
	 $delay_text_4 = $getCordinates['emergency_4'];
	}
	
	
	
	if($_POST['emergency_'.$number]!='on'){	
	if($minutes_5 != '' && $minutes_5 != '0'){
	 $delay_text_5 = $getCordinates['delay_text_5'] ." ". $_POST['minutes_5_'.$number] ." min.";
	}else{
	 $delay_text_5 = $getCordinates['non_delay_text_5'];
	}
	 }else{
	 $delay_text_5 = $getCordinates['emergency_5'];
	}
	
	if($_POST['emergency_'.$number]!='on'){	
	if($minutes_6 != '' && $minutes_6 != '0'){
	 $delay_text_6 = $getCordinates['delay_text_6'] ." ". $_POST['minutes_6_'.$number] ." min.";
	}else{
	 $delay_text_6 = $getCordinates['non_delay_text_6'];
	}
	
	}else{
	 $delay_text_6 = $getCordinates['emergency_6'];
	}*/
	
        $ImageWidth=$width_1;
        $ImageHeight=$height_1;
        $LINE_MARGIN=array($line_height1,$line_height2,$line_height3,$line_height4,$line_height5,$line_height6,$line_height7,$line_height8);
	$previousField=0;
	
        $CurrentYposition=0;
	$count_notpresent = 0;
	if($_POST[$option1]=='notpresent'){
	 $count_notpresent = $count_notpresent + 1;
	}
	if($_POST[$option2]=='notpresent'){
	 $count_notpresent = $count_notpresent + 1;
	}
	if($_POST[$option3]=='notpresent'){
	 $count_notpresent = $count_notpresent + 1;
	}
	if($_POST[$option4]=='notpresent'){
	 $count_notpresent = $count_notpresent + 1;
	}
	if($_POST[$option5]=='notpresent'){
	 $count_notpresent = $count_notpresent + 1;
	}
	if($_POST[$option6]=='notpresent'){
	 $count_notpresent = $count_notpresent + 1;
	}
	
	
	
	
	$count_total_status=0;
	if($status1==1){
	 $count_total_status = $count_total_status + 1;
	}
	if($status2==1){
	 $count_total_status = $count_total_status + 1;
	}
	if($status3==1){
	 $count_total_status = $count_total_status + 1;
	}
	if($status4==1){
	 $count_total_status = $count_total_status + 1;
	}
	if($status5==1){
	 $count_total_status = $count_total_status + 1;
	}
	if($status6==1){
	 $count_total_status = $count_total_status + 1;
	}
	//echo "Total Status count: ".$count_total_status." Not present count: ".$count_notpresent;
	$position = $count_total_status - $count_notpresent;
	
	
	 //echo "Current Number : ".$current_number."Difference : ".$difference."<br>";
	
   
    
     $position_array=array();
     if($_POST[$option1]=='notpresent'){
	   $position_array[1]=0;
     }else{
       $position_array[1]=1;
     }
     if($_POST[$option2]=='notpresent'){
	    $position_array[2]=0;
     }else{
      $position_array[2]=1;
     }
     if($_POST[$option3]=='notpresent'){
	   $position_array[3]=0;
     }else{
	   $position_array[3]=1;
     }
     if($_POST[$option4]=='notpresent'){
	  $position_array[4]=0;
     }else{
	   $position_array[4]=1;
     }
     if($_POST[$option5]=='notpresent'){
	   $position_array[5]=0;
     }else{
	  $position_array[5]=1;
     }
     if($_POST[$option6]=='notpresent'){
	    $position_array[6]=0;
     }else{
	  $position_array[6]=1;
     }
<<<<<<< HEAD:resources/delay_mededelingen_overlay_zonnestraal.php
     
     $y_coordinate_array=array("1"=>$getCordinates['y1'],"2"=>$getCordinates['y2'],"3"=>$getCordinates['y3'],"4"=>$getCordinates['y4'],"5"=>$getCordinates['y5'],"6"=>$getCordinates['y6']);
=======
     if($_POST[$option7]=='notpresent'){
	    $position_array[7]=0;
     }else{
	  $position_array[7]=1;
     }
     if($_POST[$option8]=='notpresent'){
	    $position_array[8]=0;
     }else{
	  $position_array[8]=1;
     }
     
     $y_coordinate_array=array("1"=>$getCordinates['y1'],"2"=>$getCordinates['y2'],"3"=>$getCordinates['y3'],"4"=>$getCordinates['y4'],"5"=>$getCordinates['y5'],"6"=>$getCordinates['y6'],"7" => $getCordinates['y7'], "8" => $getCordinates['y8']);
>>>>>>> FETCH_HEAD:resources/delay_mededelingen_overlay_ozz.php
     $new_array=array();
     $i=1;
     foreach($position_array as $key=>$val){
      if($position_array[$key]==1){
       $new_array[$key] = $y_coordinate_array[$key];
      }
     }
      
      foreach($new_array as $key=>$val){
       $new_array[$key] = $y_coordinate_array[$i];
	$i++;
      }
	
	  if($status1==1){
	   if($_POST[$option1]!='notpresent'){
	   if($x2_1!=0){
	   
	    $width_temp=abs($x2_1-$x1);
	   }
	   else{
	   
	    $width_temp=abs($ImageWidth-$x1);
	   }
	   //$y1=get_y_cordinate($count_notpresent, 1,$y1,$y2,$y3,$y4,$y5,$y6);
	   $inputStringArray=splitStringByWidthAndSpecialChar($font_size1,$font1, $delay_text_1,$width_temp,$x1,$x2_1,$text_center1);
	   $inputStringArray_label_1=splitStringByWidthAndSpecialChar($font_size1,$font1, $label_1,$width_temp,$x1,$x2_1,$text_center1);
	    if($_POST[$minutes1] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size1,$font1, $getCordinates['non_delay_text_1'],$width_temp,$x1,$x2_1,$text_center1);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_1['height'] = $non_delay_height['height'];
	  }
	  
	   if($text_center1==0){
	     writeStringOnImage($inputStringArray,$x1,$x2_1,$new_array[1],$im,$color_1,$font1,$font_size1,0);
	   }else{
	     writeStringOnImageCentre($inputStringArray,$x1,$x2_1,$new_array[1],$im,$color_1,$font1,$font_size1,0);
	   }
	   
	   $previousField=0;
	
	  }
	  $color_1 =imagecolorallocate($im, 0, 0, 0);
	  writeLabelOnImage($label_1,$x1-740,$x2_1,$new_array[1],$im,$color_1,$font1,$font_size1,0);
	  
	}
	
       if($status2==1){
	
	if($_POST[$option2]!='notpresent'){
	  if($x2_2!=0){
	   $width_temp=abs($x2_2-$x2);
	  }
	  else{
	   $width_temp=$ImageWidth-$x2;
	  }
	  
	 // $y2=get_y_cordinate($count_notpresent, 2,$y1,$y2,$y3,$y4,$y5,$y6);
	  
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size2,$font2, $delay_text_2,$width_temp,$x2,$x2_2,$text_center2);
	  $inputStringArray_label_2=splitStringByWidthAndSpecialChar($font_size2,$font2, $label_2,$width_temp,$x2,$x2_2,$text_center2);
	   if($_POST[$minutes2] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size2,$font2, $getCordinates['non_delay_text_2'],$width_temp,$x2,$x2_2,$text_center2);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_2['height'] = $non_delay_height['height'];
	  }
	 // echo "<pre>"; print_r($inputStringArray); echo "</pre>";
	 // echo "text_center : ".$text_center2;
	  
	  if($text_center2==0){
	    writeStringOnImage($inputStringArray,$x2,$x2_2,$new_array[2],$im,$color_2,$font2,$font_size2,1);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x2,$x2_2,$new_array[2],$im,$color_2,$font2,$font_size2,1);
	  }
	  
	  $previousField=1;
       }
       $color_2 =imagecolorallocate($im, 0, 0, 0);
       writeLabelOnImage($label_2,$x2-740,$x2_2,$new_array[2],$im,$color_2,$font2,$font_size2,0);
       
       }
	if($status3==1){
	 if($_POST[$option3]!='notpresent'){
	  if($x2_3!=0){
	   $width_temp=abs($x2_3-$x3);
	  }
	  else{
	   $width_temp=$ImageWidth-$x3;
	  }
	  
	   //$y3=get_y_cordinate($count_notpresent, 3,$y1,$y2,$y3,$y4,$y5,$y6);
	 
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size3,$font3, $delay_text_3,$width_temp,$x3,$x2_3,$text_center3);
	  $inputStringArray_label_3=splitStringByWidthAndSpecialChar($font_size3,$font3, $label_3,$width_temp,$x3-150,$x2_3,$text_center3);
	 // echo "<pre>"; print_r($inputStringArray_label_3); echo "</pre>";
	  if($_POST[$minutes3] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size3,$font3, $getCordinates['non_delay_text_3'],$width_temp,$x3,$x2_3,$text_center3);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_3['height'] = $non_delay_height['height'];
	  }
	  
	  if($text_center3==0){
	    writeStringOnImage($inputStringArray,$x3,$x2_3,$new_array[3],$im,$color_3,$font3,$font_size3,2);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x3,$x2_3,$new_array[3],$im,$color_3,$font3,$font_size3,2);
	  }
	   
	  $previousField=2;
	}
	$color_3 =imagecolorallocate($im, 0, 0, 0);
	 writeLabelOnImage($label_3,$x3-740,$x2_3,$new_array[3],$im,$color_3,$font3,$font_size3,0);
	}
	if($status4==1){
	 if($_POST[$option4]!='notpresent'){
	  if($x2_4!=0){
	   $width_temp=abs($x2_4-$x4);
	  }
	  else{
	   $width_temp=$ImageWidth-$x4;
	  }
	  
	   //$y4=get_y_cordinate($count_notpresent, 4,$y1,$y2,$y3,$y4,$y5,$y6);
	  
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size4,$font4, $delay_text_4,$width_temp,$x4,$x2_4,$text_center4);
	  $inputStringArray_label_4=splitStringByWidthAndSpecialChar($font_size4,$font4, $label_4,$width_temp,$x4-400,$x2_4,$text_center1);
	  if($_POST[$minutes4] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size4,$font4, $getCordinates['non_delay_text_4'],$width_temp,$x4,$x2_4,$text_center4);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_4['height'] = $non_delay_height['height'];
	  }
	  
	  if($text_center4==0){
	    writeStringOnImage($inputStringArray,$x4,$x2_4,$new_array[4],$im,$color_4,$font4,$font_size4,3);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x4,$x2_4,$new_array[4],$im,$color_4,$font4,$font_size4,3);
	  }
	  
	  $previousField=3;
	}
	$color_4 =imagecolorallocate($im, 0, 0, 0);
	writeLabelOnImage($label_4,$x4-740,$x2_4,$new_array[4],$im,$color_4,$font4,$font_size4,0);
	}
	if($_POST[$option5]!='notpresent'){
	if($status5==1){
	  if($x2_5!=0){
	   $width_temp=abs($x2_5-$x5);
	  }
	  else{
	   $width_temp=$ImageWidth-$x5;
	  }
	 
	  // $y5=get_y_cordinate($count_notpresent, 5,$y1,$y2,$y3,$y4,$y5,$y6);
	 
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size5,$font5, $delay_text_5,$width_temp,$x5,$x2_5,$text_center5);
	  $inputStringArray_label_5=splitStringByWidthAndSpecialChar($font_size5,$font5, $label_5,$width_temp,$x5-400,$x2_5,$text_center5);
	  if($_POST[$minutes5] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size5,$font5, $getCordinates['non_delay_text_5'],$width_temp,$x5,$x2_5,$text_center5);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_5['height'] = $non_delay_height['height'];
	  }
	  
	  
	  if($text_center5==0){
	    writeStringOnImage($inputStringArray,$x5,$x2_5,$new_array[5],$im,$color_5,$font5,$font_size5,4);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x5,$x2_5,$new_array[5],$im,$color_5,$font5,$font_size5,4);
	  }
	 
	  $previousField=4;
	}
	$color_5 =imagecolorallocate($im, 0, 0, 0);
	writeLabelOnImage($label_5,$x5-740,$x2_5,$new_array[5],$im,$color_5,$font5,$font_size5,0);
	
	}
	
	if($status6==1){
	 if($_POST[$option6]!='notpresent'){
	 if($x2_6!=0){
	   $width_temp=abs($x2_6-$x6);
	  }
	  else{
	   $width_temp=$ImageWidth-$x6;
	  }
	   
	   //$y6=get_y_cordinate($count_notpresent, 6,$y1,$y2,$y3,$y4,$y5,$y6);
	  
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size6,$font6, $delay_text_6,$width_temp,$x6,$x2_6,$text_center6);
	  $inputStringArray_label_6=splitStringByWidthAndSpecialChar($font_size6,$font6, $label_6,$width_temp,$x6-400,$x2_6,$text_center6);
	  if($_POST[$minutes6] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size6,$font6, $getCordinates['non_delay_text_6'],$width_temp,$x6,$x2_6,$text_center6);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_6['height'] = $non_delay_height['height'];
	  }
	  
	  
	  
	  if($text_center6==0){
	    writeStringOnImage($inputStringArray,$x6,$x2_6,$new_array[6],$im,$color_6,$font6,$font_size6,5);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x6,$x2_6,$new_array[6],$im,$color_6,$font6,$font_size6,5);
	  }
	  
	  $previousField=5;
	}
<<<<<<< HEAD:resources/delay_mededelingen_overlay_zonnestraal.php
	$color_6 =imagecolorallocate($im, 0, 0, 0);
	writeLabelOnImage($label_6,$x6-740,$x2_6,$new_array[6],$im,$color_6,$font6,$font_size6,0);
	}
=======
	 $color_6 =imagecolorallocate($im, 0, 0, 0);
	writeLabelOnImage($label_6,$x6-740,$x2_6,$new_array[6],$im,$color_6,$font6,$font_size6,0);
	}
	
	if($status7==1){
	 if($_POST[$option7]!='notpresent'){
	 if($x2_7!=0){
	   $width_temp=abs($x2_7-$x7);
	  }
	  else{
	   $width_temp=$ImageWidth-$x7;
	  }
	   
	   //$y7=get_y_cordinate($count_notpresent, 7,$y1,$y2,$y3,$y4,$y6,$y7);
	  
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size7,$font7, $delay_text_7,$width_temp,$x7,$x2_7,$text_center7);
	  $inputStringArray_label_7=splitStringByWidthAndSpecialChar($font_size7,$font7, $label_7,$width_temp,$x7-400,$x2_7,$text_center7);
	  if($_POST[$minutes7] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size7,$font7, $getCordinates['non_delay_text_7'],$width_temp,$x7,$x2_7,$text_center7);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_7['height'] = $non_delay_height['height'];
	  }
	  
	  
	  
	  if($text_center7==0){
	    writeStringOnImage($inputStringArray,$x7,$x2_7,$new_array[7],$im,$color_7,$font7,$font_size7,6);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x7,$x2_7,$new_array[7],$im,$color_7,$font7,$font_size7,6);
	  }
	  
	  $previousField=6;
	}
	 $color_7 =imagecolorallocate($im, 0, 0, 0);
	writeLabelOnImage($label_7,$x7-740,$x2_7,$new_array[7],$im,$color_7,$font7,$font_size7,0);
	}
	
	if($status8==1){
	 if($_POST[$option8]!='notpresent'){
	 if($x2_8!=0){
	   $width_temp=abs($x2_8-$x8);
	  }
	  else{
	   $width_temp=$ImageWidth-$x8;
	  }
	   
	   //$y8=get_y_cordinate($count_notpresent, 8,$y1,$y2,$y3,$y4,$y7,$y8);
	  
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size8,$font8, $delay_text_8,$width_temp,$x8,$x2_8,$text_center8);
	  $inputStringArray_label_8=splitStringByWidthAndSpecialChar($font_size8,$font8, $label_8,$width_temp,$x8-400,$x2_8,$text_center8);
	  if($_POST[$minutes8] > 0){
	   $non_delay_height = splitStringByWidthAndSpecialChar($font_size8,$font8, $getCordinates['non_delay_text_8'],$width_temp,$x8,$x2_8,$text_center8);
	   $inputStringArray['height']=$non_delay_height['height'];
	   $inputStringArray_label_8['height'] = $non_delay_height['height'];
	  }
	  
	  
	  
	  if($text_center8==0){
	    writeStringOnImage($inputStringArray,$x8,$x2_8,$new_array[8],$im,$color_8,$font8,$font_size8,7);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x8,$x2_8,$new_array[8],$im,$color_8,$font8,$font_size8,7);
	  }
	  
	  $previousField=7;
	}
	 $color_8 =imagecolorallocate($im, 0, 0, 0);
	writeLabelOnImage($label_8,$x8-740,$x2_8,$new_array[8],$im,$color_8,$font8,$font_size8,0);
	}
>>>>>>> FETCH_HEAD:resources/delay_mededelingen_overlay_ozz.php

	
       
	
     imagecopyresampled($image_p, $im, 0, 0, 0, 0, $width, $height, $width_1, $height_1);
     imagepng($image_p,'/var/www/html/narrowcasting/tmp/mededeling2.png');
    
     if($edit_image_name!=""){
      
      $pathfor_image=str_replace(" ","_",$edit_image_name);
     
     }else{
      $pathfor_image=getMeddilingenImageName().".png";
     }
     
     $dest = '/var/www/html/narrowcasting/tmp/mededeling2.png';
   
     $source_gd_image= imagecreatefrompng($newimage);
     $source_width=imagesx( $source_gd_image );
     $source_height=imagesy( $source_gd_image );
     
     $overlay_gd_image = imagecreatefrompng($dest);
     $overlay_width = imagesx( $overlay_gd_image );
     $overlay_height = imagesy( $overlay_gd_image );    
     imagecopyresampled($source_gd_image, $overlay_gd_image, 0, 0, 0, 0, $source_width, $source_height, $overlay_width, $overlay_height);
     
    
   if($temp_mededelingen_id > 0){
     imagepng($source_gd_image,'/var/www/html/narrowcasting/delay_mededelingen/'.$edit_image_name);
	 
	 if ($landscape_portrait==0) {
	    createsingleThumb("./delay_mededelingen/$edit_image_name","./delay_mededelingen/thumb/$edit_image_name",135,135,'crop_right');
			}
		else {
			createsingleThumb("./delay_mededelingen/$edit_image_name","./delay_mededelingen/thumb/$edit_image_name",135,135,'crop_top');
		}

   }else{
    
    $name_t_val=str_replace(" ","_",$name_t_val);
    $thumb=$name_t_val.$pathfor_image;
    $thumb=str_replace(" ","_",$thumb);
     imagepng($source_gd_image,'/var/www/html/narrowcasting/delay_mededelingen/'.$name_t_val.$pathfor_image);
	 
	 if ($landscape_portrait==0) {
	     createsingleThumb("./delay_mededelingen/$thumb","./delay_mededelingen/thumb/$thumb",135,135,'crop_right');
	 }
		    else {
			 createsingleThumb("./delay_mededelingen/$thumb","./delay_mededelingen/thumb/$thumb",135,135,'crop_top');
	}

   }
    
    /* code for edit */
   $tempsid       = session_id();
   $template_id   = $_REQUEST['template_id'];
  
   $templine1     = addslashes($getCordinates['line1']);
   $templine2     = addslashes($getCordinates['line2']);
   $templine3     = addslashes($getCordinates['line3']);
   $templine4     = addslashes($getCordinates['line4']);
   $templine5     = addslashes($getCordinates['line5']);
   $templine6     = addslashes($getCordinates['line6']);
   $templine7     = addslashes($getCordinates['line7']);
   $templine8     = addslashes($getCordinates['line8']);
   
   $tempcolor1    = $getCordinates['color1'];
   $tempcolor2    = $getCordinates['color2'];
   $tempcolor3    = $getCordinates['color3'];
   $tempcolor4    = $getCordinates['color4'];
   $tempcolor5    = $getCordinates['color5'];
   $tempcolor6    = $getCordinates['color6'];
   $tempcolor7    = $getCordinates['color7'];
   $tempcolor8    = $getCordinates['color8'];
   
   $tempfont1     = $getCordinates['font1'];
   $tempfont2     = $getCordinates['font2'];
   $tempfont3     = $getCordinates['font3'];
   $tempfont4     = $getCordinates['font4'];
   $tempfont5     = $getCordinates['font5'];
   $tempfont6     = $getCordinates['font6'];
   $tempfont7     = $getCordinates['font7'];
   $tempfont8     = $getCordinates['font8'];
   
   
   ## for creation  of mededelingen image
   
   
   $minutes_1=$_POST[$minutes1];
   $minutes_2=$_POST[$minutes2];
   $minutes_3=$_POST[$minutes3];
   $minutes_4=$_POST[$minutes4];
   $minutes_5=$_POST[$minutes5];
   $minutes_6=$_POST[$minutes6];
   $minutes_7=$_POST[$minutes7];
   $minutes_8=$_POST[$minutes8];
   
   
   if(empty($temp_mededelingen_id)){
    $temp_name=$name_t_val.$pathfor_image;
    $temp_name=str_replace(" ","_",$temp_name);
   $Querytemp =   "insert into temp_delay_mededelingen
                                        (
					 template_id,line1,color1,font1,line2,color2,font2,line3,color3,font3,line4,color4,font4,line5,
                                         color5,font5,line6,color6,font6,line7,color7,font7,line8,color8,font8,temp_image_name,write_image_path,text_center1,text_center2,text_center3,
					 text_center4,text_center5,text_center6,text_center7,text_center8,minutes_1,minutes_2,minutes_3,minutes_4,minutes_5,minutes_6,minutes_7,minutes_8,is_emergency1,is_emergency2,is_emergency3,is_emergency4,is_emergency5,is_emergency6,is_emergency7,is_emergency8,not_present_1,not_present_2,not_present_3,not_present_4,not_present_5,not_present_6,not_present_7,not_present_8,is_minutes1,is_minutes2,is_minutes3,is_minutes4,is_minutes5,is_minutes6,is_minutes7,is_minutes8
					)
					values('$template_id','$templine1','$tempcolor1','$tempfont1','$templine2','$tempcolor2','$tempfont2'
					,'$templine3','$tempcolor3','$tempfont3','$templine4','$tempcolor4','$tempfont4','$templine5','$tempcolor5',
					'$tempfont5','$templine6','$tempcolor6','$tempfont6','$templine7','$tempcolor7','$tempfont7','$templine8','$tempcolor8','$tempfont8','$temp_name','$write_image_path',
					$text_center1,$text_center2,$text_center3,$text_center4,$text_center5,$text_center6,$text_center7,$text_center8,'".$minutes_1."','".$minutes_2."','".$minutes_3."','".$minutes_4."','".$minutes_5."','".$minutes_6."','".$minutes_7."','".$minutes_8."',$is_emergency1,$is_emergency2,$is_emergency3,$is_emergency4,$is_emergency5,$is_emergency6,$is_emergency7,$is_emergency8,$not_present_1,$not_present_2,$not_present_3,$not_present_4,$not_present_5,$not_present_6,$not_present_7,$not_present_8,$is_minutes1,$is_minutes2,$is_minutes3,$is_minutes4,$is_minutes5,$is_minutes6,$is_minutes7,$is_minutes8 )";
						  
						  
	
    $conn=$db->query($Querytemp);
    $temp_mededelingen_id=mysql_insert_id($db->Link_ID_PREV);
    $check_emergency_query="select tmpmed.is_emergency1, tmpmed.is_emergency2, tmpmed.is_emergency3, tmpmed.is_emergency4, tmpmed.is_emergency5, tmpmed.is_emergency6,tmpmed.is_emergency7,tmpmed.is_emergency8,tmpmed.not_present_1,tmpmed.not_present_2,tmpmed.not_present_3,tmpmed.not_present_4,tmpmed.not_present_5,tmpmed.not_present_6,tmpmed.not_present_7,tmpmed.not_present_8 from temp_delay_mededelingen as tmpmed  where tmpmed.id=$temp_mededelingen_id limit 1";
    $check_emergency_query_rs=$db->query($check_emergency_query);
    $check_emergency_query_result=mysql_fetch_array($check_emergency_query_rs);
    $is_emergency_checked1=$check_emergency_query_result['is_emergency1'];
    $is_emergency_checked2=$check_emergency_query_result['is_emergency2'];
    $is_emergency_checked3=$check_emergency_query_result['is_emergency3'];
    $is_emergency_checked4=$check_emergency_query_result['is_emergency4'];
    $is_emergency_checked5=$check_emergency_query_result['is_emergency5'];
    $is_emergency_checked6=$check_emergency_query_result['is_emergency6'];
    $is_emergency_checked7=$check_emergency_query_result['is_emergency7'];
    $is_emergency_checked8=$check_emergency_query_result['is_emergency8'];
    
    
    $is_not_present_1=$check_emergency_query_result['not_present_1'];
    $is_not_present_2=$check_emergency_query_result['not_present_2'];
    $is_not_present_3=$check_emergency_query_result['not_present_3'];
    $is_not_present_4=$check_emergency_query_result['not_present_4'];
    $is_not_present_5=$check_emergency_query_result['not_present_5'];
    $is_not_present_6=$check_emergency_query_result['not_present_6'];
    $is_not_present_7=$check_emergency_query_result['not_present_7'];
    $is_not_present_8=$check_emergency_query_result['not_present_8'];
    
    
    
   }else if($temp_mededelingen_id>0){ ## for editing of mededelingen image
    //echo "<pre>"; print_r($_POST); echo "</pre>";
    ## check previously created  mededelingen record exist in table
    $tempdeletequery  = "select tmpmed.id from temp_delay_mededelingen as tmpmed  where tmpmed.id=$temp_mededelingen_id limit 1";
    $tempeditvalues =$db->query($tempdeletequery);
    $tempresult_delete = mysql_fetch_array($tempeditvalues);
    
    
    
  $tempquerya  = "select * from temp_delay_mededelingen where id='$temp_mededelingen_id'";
  $tempeditvaluesa =$db->query($tempquerya);
  $tempresulta = mysql_fetch_array($tempeditvaluesa);
  
  if(!isset($_POST[$minutes1])){
   $minutes_1=$tempresulta['minutes_1'];
  }
  if(!isset($_POST[$minutes2])){
   $minutes_2=$tempresulta['minutes_2'];
  }
  if(!isset($_POST[$minutes3])){
   $minutes_3=$tempresulta['minutes_3'];
  }
  if(!isset($_POST[$minutes4])){
   $minutes_4=$tempresulta['minutes_4'];
  }
  if(!isset($_POST[$minutes5])){
   $minutes_5=$tempresulta['minutes_5'];
  }
  if(!isset($_POST[$minutes6])){
   $minutes_6=$tempresulta['minutes_6'];
  }
  
  if(!isset($_POST[$minutes7])){
   $minutes_7=$tempresulta['minutes_7'];
  }
  
  if(!isset($_POST[$minutes8])){
   $minutes_8=$tempresulta['minutes_8'];
  }
  
    if($tempresult_delete){
     $temp_name=$edit_image_name;
     $temp_name=str_replace(" ","_",$temp_name);
     
     
     
     
     $updateQueryTemp =  "update temp_delay_mededelingen set
                           template_id='$template_id',line1='$templine1',color1='$tempcolor1',font1='$tempfont1',line2='$templine2',
		           color2='$tempcolor2',font2='$tempfont2',line3='$templine3',color3='$tempcolor3',font3='$tempfont3',line4='$templine4',
		           color4='$tempcolor4',font4='$tempfont4',line5='$templine5',color5='$tempcolor5',font5='$tempfont5',line6='$templine6',
		           color6='$tempcolor6',font6='$tempfont6',line7='$templine7',
		           color7='$tempcolor7',font7='$tempfont7',line8='$templine8',
		           color8='$tempcolor8',font8='$tempfont8',text_center1=$text_center1,text_center2=$text_center2,text_center3=$text_center3
			   ,text_center4=$text_center4,text_center5=$text_center5,text_center6=$text_center6,text_center6=$text_center6,text_center7=$text_center7,text_center8=$text_center8,minutes_1='".$minutes_1."',minutes_2='".$minutes_2."'
			   ,minutes_3='".$minutes_3."',minutes_4='".$minutes_4."',minutes_5='".$minutes_5."',minutes_6='".$minutes_6."',minutes_7='".$minutes_7."',minutes_8='".$minutes_8."',
			   temp_image_name='$temp_name',write_image_path='$write_image_path',is_emergency1='$is_emergency1',is_emergency2='$is_emergency2',is_emergency3='$is_emergency3',is_emergency4='$is_emergency4',is_emergency5='$is_emergency5',is_emergency6='$is_emergency6',is_emergency7='$is_emergency7',is_emergency8='$is_emergency8', not_present_1='$not_present_1', not_present_2='$not_present_2', not_present_3='$not_present_3', not_present_4='$not_present_4', not_present_5='$not_present_5', not_present_6='$not_present_6', not_present_7='$not_present_7',not_present_8='$not_present_8',is_minutes1='$is_minutes1', is_minutes2='$is_minutes2', is_minutes3='$is_minutes3', is_minutes4='$is_minutes4', is_minutes5='$is_minutes5', is_minutes6='$is_minutes6' , is_minutes7='$is_minutes7', is_minutes8='$is_minutes8' where id=$temp_mededelingen_id";
		   
		
     $conn=$db->query($updateQueryTemp);
     $check_emergency_query="select tmpmed.is_emergency1, tmpmed.is_emergency2, tmpmed.is_emergency3, tmpmed.is_emergency4, tmpmed.is_emergency5, tmpmed.is_emergency6, tmpmed.is_emergency7,tmpmed.is_emergency8,tmpmed.not_present_1,tmpmed.not_present_2,tmpmed.not_present_3,tmpmed.not_present_4,tmpmed.not_present_5,tmpmed.not_present_6, tmpmed.not_present_7, tmpmed.not_present_8  from temp_delay_mededelingen as tmpmed  where tmpmed.id=$temp_mededelingen_id limit 1";
    $check_emergency_query_rs=$db->query($check_emergency_query);
    $check_emergency_query_result=mysql_fetch_array($check_emergency_query_rs);
    $is_emergency_checked1=$check_emergency_query_result['is_emergency1'];
    $is_emergency_checked2=$check_emergency_query_result['is_emergency2'];
    $is_emergency_checked3=$check_emergency_query_result['is_emergency3'];
    $is_emergency_checked4=$check_emergency_query_result['is_emergency4'];
    $is_emergency_checked5=$check_emergency_query_result['is_emergency5'];
    $is_emergency_checked6=$check_emergency_query_result['is_emergency6'];
    $is_emergency_checked7=$check_emergency_query_result['is_emergency7'];
    $is_emergency_checked8=$check_emergency_query_result['is_emergency8'];
  
    
    $is_not_present_1=$check_emergency_query_result['not_present_1'];
    $is_not_present_2=$check_emergency_query_result['not_present_2'];
    $is_not_present_3=$check_emergency_query_result['not_present_3'];
    $is_not_present_4=$check_emergency_query_result['not_present_4'];
    $is_not_present_5=$check_emergency_query_result['not_present_5'];
    $is_not_present_6=$check_emergency_query_result['not_present_6'];
    $is_not_present_7=$check_emergency_query_result['not_present_7'];
    $is_not_present_8=$check_emergency_query_result['not_present_8'];
    }else{
      $main_image_path='/var/www/html/narrowcasting/mededelingen/'.$pathfor_image;
      $thumb_image_path='/var/www/html/narrowcasting/mededelingen/thumb/'.$pathfor_image;
      unlink($main_image_path);
      unlink($thumb_image_path);
    }

   }
   
    /*end */
  
   if($carrousel_id=="" && $come_from!="") {
     $save_link="<div class='video_tab_new'><a href='hard_redirect.php'>Opslaan</a></div>";
   }else if($carrousel_id!="" && $come_from!=""){
     $save_link="<div class='video_tab_new'><a href='hard_redirect.php?id=$carrousel_id&status=1'>Opslaan</a></div>";
   }else{
     $save_link="<div class='video_tab_new'><a href='hard_redirect.php?page=mededelingen'>Opslaan</a></div>";
   }
     
    if(trim($_REQUEST['edit_image_name'])==""){
     $verwijderen="<div class='video_tab_new'><a href='delete_mededeoverlay.php?path=$pathfor_image&temp_mededelingen_id=$temp_mededelingen_id'>Verwijderen</a></div>"; 
   }else{
     $verwijderen="";
   } 
     
     
    $showvideo="<div class='video_section' id='width_932'>
        	 <div class='video_img'><img src='./mededelingen/$pathfor_image?time=".time()."' width='264' height='149'></div>
            	  <div class='video_tabs'>
		  <div class='video_tab_new'><a href='./mededelingen/$pathfor_image?time=".time()."' rel='single'  class='pirobox' style='color:white;'>Preview</a></div>
		  <div class='video_tab_new'><a href='mededelingen_overlay.php?carrousel_id=$carrousel_id&come_from=$come_from&request_for=template_edit&template_id=$template_id&temp_mededelingen_id=$temp_mededelingen_id&edit_image_name=$pathfor_image'>Bewerken</a></div>
		  $verwijderen
		  $save_link
                </div>
             </div>";
	     
    }
    
    
 
 
 function getMeddilingenImageName(){
  global $text1,$text2,$text3,$text4,$text5,$text6,$pathfor_image;
  $random_no=rand(1111,9999);
  if($text1!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text1);
  }else if($text2!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text2);
  }else if($text3!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text3);
  }else if($text4!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text4);
  }else if($text5!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text5);
  }else if($text6!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text6);
  }else{
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$pathfor_image);
  }
  $newMedImageName=preg_replace('/\_+/', '_',strtolower(substr($newMedImageName,0,20).'_'.$random_no));
  $newMedImageName = str_replace(".","_",$newMedImageName);
   return $newMedImageName;
 }
 
 
 function getMeddilingenImageName2(){
  global $text1,$text2,$text3,$text4,$text5,$text6,$pathfor_image_2;
  $random_no=rand(1111,9999);
  if($text1!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text1);
  }else if($text2!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text2);
  }else if($text3!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text3);
  }else if($text4!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text4);
  }else if($text5!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text5);
  }else if($text6!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text6);
  }else{
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$pathfor_image_2);
  }
  $newMedImageName=preg_replace('/\_+/', '_',strtolower(substr($newMedImageName,0,20).'_'.$random_no));
   return $newMedImageName;
 }
 
 function getMeddilingenImageName3(){
  global $text1,$text2,$text3,$text4,$text5,$text6,$pathfor_image_3;
  $random_no=rand(1111,9999);
  if($text1!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text1);
  }else if($text2!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text2);
  }else if($text3!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text3);
  }else if($text4!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text4);
  }else if($text5!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text5);
  }else if($text6!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text6);
  }else{
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$pathfor_image_3);
  }
  $newMedImageName=preg_replace('/\_+/', '_',strtolower(substr($newMedImageName,0,20).'_'.$random_no));
   return $newMedImageName;
 }
 
 
 
 
 
 function getMeddilingenImageName4(){
  global $text1,$text2,$text3,$text4,$text5,$text6,$pathfor_image_4;
  $random_no=rand(1111,9999);
  if($text1!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text1);
  }else if($text2!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text2);
  }else if($text3!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text3);
  }else if($text4!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text4);
  }else if($text5!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text5);
  }else if($text6!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text6);
  }else{
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$pathfor_image_4);
  }
  $newMedImageName=preg_replace('/\_+/', '_',strtolower(substr($newMedImageName,0,20).'_'.$random_no));
   return $newMedImageName;
 }
 
 function getMeddilingenImageName5(){
  global $text1,$text2,$text3,$text4,$text5,$text6,$pathfor_image_5;
  $random_no=rand(1111,9999);
  if($text1!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text1);
  }else if($text2!=""){
   $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text2);
  }else if($text3!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text3);
  }else if($text4!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text4);
  }else if($text5!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text5);
  }else if($text6!=""){
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text6);
  }else{
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$pathfor_image_5);
  }
  $newMedImageName=preg_replace('/\_+/', '_',strtolower(substr($newMedImageName,0,20).'_'.$random_no));
   return $newMedImageName;
 }
 
 
  // calculate image write position on template image based on set parameters from db(horizontal_center,vertical_center)
 
 
 
  function calculate_image_write_position($write_image_st_pos_x,$write_image_st_pos_y,$img_center_horizontal,$img_center_vertical){
       global $ImageWidth,$ImageHeight,$temp_write_image_thumb_actual_width,$temp_write_image_thumb_actual_height;
       ## set inetail x and y position
       $final_x_position=$write_image_st_pos_x;
       $final_y_position=$write_image_st_pos_y;
       
       $available_horizonatl_space =$ImageWidth-$write_image_st_pos_x;
       $available_vertical_space   =$ImageHeight-$write_image_st_pos_y;
       // make sure that $available_horizonatl_space and $available_horizonatl_space is positive
       $available_horizonatl_space =$available_horizonatl_space<0?0:$available_horizonatl_space;
       $available_vertical_space   =$available_vertical_space<0?0:$available_vertical_space;
       
       ## when center image horizontally is selected
       if($img_center_horizontal==1){
	 ## space on both side of write image
	 $extra_horizonatal_space=$available_horizonatl_space-$temp_write_image_thumb_actual_width;
	 ## actual shift( half of space on both side of write image)
	 $extra_horizonatal_space=$extra_horizonatal_space/2;
	 $extra_horizonatal_space=$extra_horizonatal_space<0?0:$extra_horizonatal_space;
	 $final_x_position=$final_x_position+$extra_horizonatal_space;
       }
         ## when center image vertically is selected
        if($img_center_vertical==1){
	 ## space on both side of write image
	 $extra_vertical_space=$available_vertical_space-$temp_write_image_thumb_actual_height;
	 ## actual shift( half of space on both side of write image)
	 $extra_vertical_space=$extra_vertical_space/2;
	 $extra_vertical_space=$extra_vertical_space<0?0:$extra_vertical_space;
	 
	 $final_y_position=$final_y_position+$extra_vertical_space;
       }
      
      return array($final_x_position,$final_y_position);
   }
   
    //------- function for breaking string by available width and special char $----------------------------
    function splitStringByWidthAndSpecialChar($fontSize,$fontFace, $inputString, $width,$xStart,$xEnd,$center){
       global $ImageWidth;
      $inputStringArray = explode('$', $inputString);
      $finalInputString=array('height'=>0,'multilineString'=>array());
      for($l=0;$l<=(count($inputStringArray)-1);$l++)
      {
        if($center==1){
	  $result=breakStringByCentreWidth($fontSize,$fontFace, $inputStringArray[$l], $xEnd,$xStart);
	  $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  $finalInputString['height']=$result['height'];
	}else{
	  $result=breakStringByWidth($fontSize,$fontFace, $inputStringArray[$l], $width,$xStart,$ImageWidth);
	  if(count($result['multilineString'])>0){
	   $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  }else{
	    $finalInputString['multilineString'][]="";
	  }
	  $finalInputString['height']=$result['height'];
	}
      }
      return $finalInputString;
    }
    
    function splitStringByWidthAndSpecialChar2($fontSize,$fontFace, $inputString, $width,$xStart,$xEnd,$center){
       global $ImageWidth_2;
       
      $inputStringArray = explode('$', $inputString);
      $finalInputString=array('height'=>0,'multilineString'=>array());
      for($l=0;$l<=(count($inputStringArray)-1);$l++)
      {
        if($center==1){
	 
	  $result=breakStringByCentreWidth($fontSize,$fontFace, $inputStringArray[$l], $xEnd,$xStart);
	  $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  $finalInputString['height']=$result['height'];
	}else{
	 
	  $result=breakStringByWidth($fontSize,$fontFace, $inputStringArray[$l], $width,$xStart, $ImageWidth_2);
	  if(count($result['multilineString'])>0){
	   $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  }else{
	    $finalInputString['multilineString'][]="";
	  }
	  $finalInputString['height']=$result['height'];
	}
      }
      return $finalInputString;
    }
 
 
 function splitStringByWidthAndSpecialChar3($fontSize,$fontFace, $inputString, $width,$xStart,$xEnd,$center){
       global $ImageWidth_3;
      $inputStringArray = explode('$', $inputString);
      $finalInputString=array('height'=>0,'multilineString'=>array());
      for($l=0;$l<=(count($inputStringArray)-1);$l++)
      {
        if($center==1){
	  $result=breakStringByCentreWidth($fontSize,$fontFace, $inputStringArray[$l], $xEnd,$xStart);
	  $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  $finalInputString['height']=$result['height'];
	}else{
	  $result=breakStringByWidth($fontSize,$fontFace, $inputStringArray[$l], $width,$xStart, $ImageWidth_3);
	  if(count($result['multilineString'])>0){
	   $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  }else{
	    $finalInputString['multilineString'][]="";
	  }
	  $finalInputString['height']=$result['height'];
	}
      }
     
      return $finalInputString;
    }
    
    function splitStringByWidthAndSpecialChar4($fontSize,$fontFace, $inputString, $width,$xStart,$xEnd,$center){
       global $ImageWidth_4;
      $inputStringArray = explode('$', $inputString);
      $finalInputString=array('height'=>0,'multilineString'=>array());
      for($l=0;$l<=(count($inputStringArray)-1);$l++)
      {
        if($center==1){
	  $result=breakStringByCentreWidth($fontSize,$fontFace, $inputStringArray[$l], $xEnd,$xStart);
	  $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  $finalInputString['height']=$result['height'];
	}else{
	  $result=breakStringByWidth($fontSize,$fontFace, $inputStringArray[$l], $width,$xStart, $ImageWidth_4);
	  if(count($result['multilineString'])>0){
	   $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  }else{
	    $finalInputString['multilineString'][]="";
	  }
	  $finalInputString['height']=$result['height'];
	}
      }
     
      return $finalInputString;
     
    }
    
    function splitStringByWidthAndSpecialChar5($fontSize,$fontFace, $inputString, $width,$xStart,$xEnd,$center){
       global $ImageWidth_5;
      $inputStringArray = explode('$', $inputString);
      $finalInputString=array('height'=>0,'multilineString'=>array());
      for($l=0;$l<=(count($inputStringArray)-1);$l++)
      {
        if($center==1){
	  $result=breakStringByCentreWidth($fontSize,$fontFace, $inputStringArray[$l], $xEnd,$xStart);
	  $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  $finalInputString['height']=$result['height'];
	}else{
	  $result=breakStringByWidth($fontSize,$fontFace, $inputStringArray[$l], $width,$xStart, $ImageWidth_5);
	  if(count($result['multilineString'])>0){
	   $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  }else{
	    $finalInputString['multilineString'][]="";
	  }
	  $finalInputString['height']=$result['height'];
	}
      }
     
      return $finalInputString;
     
    }
 
          function writeStringOnImage($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	   global $LINE_MARGIN,$CurrentYposition,$previousField;
	   
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition;
	   }else if($CurrentYposition > $yStart && intval($yStart)!=0){
	   }
	  
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);	     
	     $yStart+=($LINE_MARGIN[$currentField]+$inputStringArray['height']);	   
	   }
	   
	   
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   
	    $yStart-=($LINE_MARGIN[$currentField]);
	   }
	   
	   $CurrentYposition=$yStart;
	  
	 }
	 
	   function writeLabelOnImage($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
                      imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $inputStringArray);
	   
	  
	 }
	 
	 
	 function writeStringOnImage2($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	   global $LINE_MARGIN_2,$CurrentYposition_2,$previousField_2;
	   
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition_2;
	   }else if($CurrentYposition_2 > $yStart && intval($yStart)!=0){	     
	   }
	   
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN_2[$currentField]+$inputStringArray['height']);
	     
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN_2[$currentField]);
	   }
	   $CurrentYposition_2=$yStart;
	  
	 }
	 
	 function writeStringOnImage3($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	   global $LINE_MARGIN_3,$CurrentYposition_3,$previousField_3;
	   
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition_3;
	   }else if($CurrentYposition_3 > $yStart && intval($yStart)!=0){
	   }
	   
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN_3[$currentField]+$inputStringArray['height']);
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN_3[$currentField]);
	   }
	   $CurrentYposition_3=$yStart;
	  
	 }
	 
	 function writeStringOnImage4($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
       //echo "<pre>"; print_r($inputStringArray); echo "</pre>";
	   global $LINE_MARGIN_4,$CurrentYposition_4,$previousField_4;
	   
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition_4;
	   }else if($CurrentYposition_4 > $yStart && intval($yStart)!=0){
	     
	   }
	   
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN_4[$currentField]+$inputStringArray['height']);
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN_4[$currentField]);
	   }
	   $CurrentYposition_4=$yStart;
	  
	 }
	 
	 
	 
	 
	 
	 function writeStringOnImagea($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	   global $LINE_MARGIN,$CurrentYposition,$previousField;
	   $CurrentYposition = $yStart;
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition;
	   }else if($CurrentYposition > $yStart && intval($yStart)!=0){
	     
	   }
	   
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN[$currentField]+$inputStringArray['height']);
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN[$currentField]);
	   }
	   $CurrentYposition=$yStart;
	  
	  
	 }
	 
	 function writeStringOnImagea2($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	   global $LINE_MARGIN_2,$CurrentYposition_2,$previousField_2;
	   $CurrentYposition_2 = $yStart;
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition_2;
	   }else if($CurrentYposition_2 > $yStart && intval($yStart)!=0){
	    
	   }
	   
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN_2[$currentField]+$inputStringArray['height']);
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN_2[$currentField]);
	   }
	   $CurrentYposition_2=$yStart;
	  
	  
	 }
	 
	 function writeStringOnImagea3($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	   global $LINE_MARGIN_3,$CurrentYposition_3,$previousField_3;
	   $CurrentYposition_3 = $yStart;
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition_3;
	   }else if($CurrentYposition_3 > $yStart && intval($yStart)!=0){
	     
	   }
	   
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN_3[$currentField]+$inputStringArray['height']);
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN_3[$currentField]);
	   }
	   $CurrentYposition_3=$yStart;
	  
	  
	 }
	 
	 function writeStringOnImagea4($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
	  
	   global $LINE_MARGIN_4,$CurrentYposition_4,$previousField_4;
	   $CurrentYposition = $yStart;
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition_4;
	   }else if($CurrentYposition_4 > $yStart && intval($yStart)!=0){
	     
	   }
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN_4[$currentField]+$inputStringArray['height']);
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN_4[$currentField]);
	   }
	   $CurrentYposition_4=$yStart;
	  
	 }
	 
	 function writeStringOnImagea5($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
	  
	   global $LINE_MARGIN_5,$CurrentYposition_5,$previousField_5;
	   $CurrentYposition = $yStart;
	   if(intval($yStart)==0){
	     $yStart=$CurrentYposition_5;
	   }else if($CurrentYposition_5 > $yStart && intval($yStart)!=0){
	     
	   }
	   foreach ($inputStringArray['multilineString'] as $word ){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $word);
	     $yStart+=($LINE_MARGIN_5[$currentField]+$inputStringArray['height']);
	   }
	   // removing added space after last line
	   if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	    $yStart-=($LINE_MARGIN_5[$currentField]);
	   }
	   $CurrentYposition_5=$yStart;
	  
	 }
	 
	 
	 
	  
       function breakStringByWidth($fontSize,$fontFace, $inputString, $width,$xStart,$ImageWidth){
	 
	  // if total line wdith is greater than image width, remove extra width
	  if(abs($xStart+$width)>$ImageWidth){
	  $width =abs($ImageWidth-$xStart);
	  }
	  $ret = "";
	  $inputString=preg_replace('/\s+/', ' ',$inputString);
	  $inputStringArr = explode(' ', trim($inputString));
	  $multilineString=array();
	  $counter=0;
	  foreach ($inputStringArr as $key => $word){
            
	      $teststring = $ret.' '.$word;
	      $dimention = getStringBoxDimention($fontSize,$fontFace, $teststring);
	      
	      $currentWidth = $dimention['width'];
	      $currentHeight = $dimention['height'];
	      if ($currentWidth > $width ){
	         if($key==0){
		    $multilineString[$counter]=$word;
		    $ret="";
		 }else if($ret==""){
		   $multilineString[$counter]=$word;
		   $ret="";
		 }else{
		   $multilineString[$counter]=trim($ret);
		   $ret=$word;
		 }
		
		 $counter++;
   
	      } else {
	       $ret=$ret.' '.$word;
	      }
	  }
	  if(trim($ret)!="")
	  $multilineString[$counter]=trim($ret);
	  $finalResult=array();
	  $finalResult['height']=$currentHeight;
	  $finalResult['multilineString']=$multilineString;	  
	  return $finalResult;
      }
      
      
      
       function writeStringOnImageCentre($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	  global $LINE_MARGIN,$CurrentYposition,$BLOCK_MARGIN,$previousField,$ImageWidth;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition+$BLOCK_MARGIN[$previousField];
	  }
	  if($xEnd>$ImageWidth){
	    $xEnd=$ImageWidth;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
          
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	    $wordWidth=$wordDetails['width'];
	    if($wordWidth>=$MaxWriteWidth){
	     
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN[$currentField]+$inputStringArray['height']);
	  }
	  
	  
	  
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN[$currentField]);
	  }
	  $CurrentYposition=0;
	  
	 }
	 
	 function writeStringOnImageCentre2($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	  global $LINE_MARGIN_2,$CurrentYposition_2,$previousField_2,$ImageWidth_2;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition_2;
	  }
	  if($xEnd>$ImageWidth_2){
	    $xEnd=$ImageWidth_2;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
          
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	    $wordWidth=$wordDetails['width'];
	    if($wordWidth>=$MaxWriteWidth){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN_2[$currentField]+$inputStringArray['height']);
	  }
	   
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN_2[$currentField]);
	  }
	  $CurrentYposition_2=0;
	  
	 }
	  
	  function writeStringOnImageCentrea2($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	  global $LINE_MARGIN_2,$CurrentYposition_2,$previousField_2,$ImageWidth_2;
	  $CurrentYposition_2=$yStart;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition_2;
	  }
	  if($xEnd>$ImageWidth_2){
	    $xEnd=$ImageWidth_2;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
          
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	    $wordWidth=$wordDetails['width'];
	    if($wordWidth>=$MaxWriteWidth){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN_2[$currentField]+$inputStringArray['height']);
	  }
	  
	  
	  
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN_2[$currentField]);
	  }
	  $CurrentYposition_2=0;
	  
	 }
	 
	 function writeStringOnImageCentre3($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	  global $LINE_MARGIN_3,$CurrentYposition_3,$previousField_3,$ImageWidth_3;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition_3;
	  }
	  if($xEnd>$ImageWidth_3){
	    $xEnd=$ImageWidth_3;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
          
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	    $wordWidth=$wordDetails['width'];
	    if($wordWidth>=$MaxWriteWidth){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN_3[$currentField]+$inputStringArray['height']);
	  }
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN_3[$currentField]);
	  }
	  $CurrentYposition_3=0;
	  
	 }
	 
	 function writeStringOnImageCentrea3($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	  global $LINE_MARGIN_3,$CurrentYposition_3,$previousField_3,$ImageWidth_3;
	  $CurrentYposition_3 = $yStart;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition_3;
	  }
	  if($xEnd>$ImageWidth_3){
	    $xEnd=$ImageWidth_3;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
          
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	    $wordWidth=$wordDetails['width'];
	    if($wordWidth>=$MaxWriteWidth){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN_3[$currentField]+$inputStringArray['height']);
	  }
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN_3[$currentField]);
	  }
	  $CurrentYposition_3=0;
	  
	 }
	 
	 function writeStringOnImageCentre4($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	  global $LINE_MARGIN_4,$CurrentYposition_4,$previousField_4,$ImageWidth_4;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition_4;
	  }
	  if($xEnd>$ImageWidth_4){
	    $xEnd=$ImageWidth_4;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
          
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	    $wordWidth=$wordDetails['width'];
	    if($wordWidth>=$MaxWriteWidth){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN_4[$currentField]+$inputStringArray['height']);
	  }
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN_4[$currentField]);
	  }
	  $CurrentYposition_4=0;
	  
	 }
	 
	 function writeStringOnImageCentrea4($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
	  global $LINE_MARGIN_4,$CurrentYposition_4,$previousField_4,$ImageWidth_4;
	  $CurrentYposition_4 = $yStart;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition_4;
	  }
	  if($xEnd>$ImageWidth_4){
	    $xEnd=$ImageWidth_4;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
          
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	    $wordWidth=$wordDetails['width'];
	    if($wordWidth>=$MaxWriteWidth){
	     imagettftext($image, $fontSize, 0, $xStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN_4[$currentField]+$inputStringArray['height']);
	  }
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN_4[$currentField]);
	  }
	  $CurrentYposition_4=0;
	  
	 }
	 
	
      
      
      
       function breakStringByCentreWidth($fontSize,$fontFace, $inputString, $xEnd,$xStart, $ImageWidth){
	  global $ImageWidth;
	  //remove multiple white space to single whitle space
	  $inputString=preg_replace('/\s+/', ' ',$inputString);
	  // if total line wdith is greater than image width, remove extra width
	  if(abs($xEnd)>$ImageWidth){
	    $xEnd =abs($ImageWidth);
	  }
	  $width=abs(($xEnd-$xStart));
	  $ret = "";
	  $inputStringArr = explode(' ', trim($inputString));
	  $multilineString=array();
	  $counter=0;
	  $previousCurrentWidth=0;
	  $currentWidth=0;
	  $currentHeight=0;
	  foreach ($inputStringArr as $key => $word){
              $previousCurrentWidth=$currentWidth;
	      $teststring = $ret.' '.$word;
	      $dimention = getStringBoxDimention($fontSize,$fontFace, $teststring);
	      $currentWidth = $dimention['width'];
	      $currentHeight = $dimention['height'];
	      if ($currentWidth > $width ){
	         if($key==0){
		    $multilineString[$counter]=array('text'=>$word,'width'=>$currentWidth);
		    $ret="";
		 }else if($ret==""){
		    $multilineString[$counter]=array('text'=>$word,'width'=>$previousCurrentWidth);
		    $ret="";
		 }else{
		   $multilineString[$counter]=array('text'=>trim($ret),'width'=>$previousCurrentWidth);
		   $ret=$word;
		 }
		
		 $counter++;
   
	      } else {
	       $ret=$ret.' '.$word;
	      }
	  }
	  if(trim($ret)!=""){
	    $dimention=getStringBoxDimention($fontSize,$fontFace, $ret);
	    $multilineString[$counter]=array('text'=>trim($ret),'width'=>$dimention['width']);
	  }else{
	    $multilineString[$counter]=array('text'=>"",'width'=>0);
	  }
	  $finalResult=array();
	  $finalResult['height']=$currentHeight;
	  $finalResult['multilineString']=$multilineString;
	  return $finalResult;
      }
      
      function getStringBoxDimention($fontSize,$fontFace,$inputString){
	  $dimention=array();  
	  $testbox = imagettfbbox($fontSize, 0, $fontFace, trim($inputString));
	  $dimention['width'] = abs($testbox[0]) + abs($testbox[4]);
	  $dimention['height'] = abs($testbox[1]) + abs($testbox[5]);
	  //echo  $dimention['width'] .'<br>';
	  return $dimention;
      }	
 
?>
<?php
// for edit when come back from edit carrousal link
 if(isset($_GET['template_id']) and $_GET['request_for']=='template_edit'){
  $tempsid    = session_id();
  $tempquery  = "select * from temp_delay_mededelingen where id='$temp_mededelingen_id'";
  $tempeditvalues =$db->query($tempquery);
  $tempresult = mysql_fetch_array($tempeditvalues);
  //echo '<pre>'.$temp_mededelingen_id; print_r($tempresult);
  if($tempresult){
   
   $template_id      = $tempresult['template_id'];
  
   
   $templine1    = stripslashes($tempresult['line1']);
   $templine2    = stripslashes($tempresult['line2']);
   $templine3    = stripslashes($tempresult['line3']);
   $templine4    = stripslashes($tempresult['line4']);
   $templine5    = stripslashes($tempresult['line5']);
   $templine6    = stripslashes($tempresult['line6']);
   
   $tempcolor1    = $tempresult['color1'];
   $tempcolor2    = $tempresult['color2'];
   $tempcolor3    = $tempresult['color3'];
   $tempcolor4    = $tempresult['color4'];
   $tempcolor5    = $tempresult['color5'];
   $tempcolor6    = $tempresult['color6'];
   
   $tempfont1    = $tempresult['font1'];
   $tempfont2    = $tempresult['font2'];
   $tempfont3    = $tempresult['font3'];
   $tempfont4    = $tempresult['font4'];
   $tempfont5    = $tempresult['font5'];
   $tempfont6    = $tempresult['font6'];
   
   
   $text_center1 = intval($tempresult['text_center1']);
   $text_center2 = intval($tempresult['text_center2']);
   $text_center3 = intval($tempresult['text_center3']);
   $text_center4 = intval($tempresult['text_center4']);
   $text_center5 = intval($tempresult['text_center5']);
   $text_center6 = intval($tempresult['text_center6']);
   $tempwrite_image_path    = $tempresult['write_image_path'];
   $tempwrite_image_path_array=explode("/",$tempwrite_image_path);
   if($tempwrite_image_path_array[0]==""){
    $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[1].'/thumb/'.$tempwrite_image_path_array[2];
   }
   else{
    $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[0].'/thumb/'.$tempwrite_image_path_array[1];
   } 
   
   // getting static data from theater templateb table
   $tempquery1  = "select * from delay_mededelingen where id=' $template_id'";
   $tempeditvalues1 =$db->query($tempquery1);
   $tempresult1 = mysql_fetch_array($tempeditvalues1);
   $selected_template_label     = $tempresult1['name_t'];
   $label1        = $tempresult1['label1'];
   $label2        = $tempresult1['label2'];
   $label3        = $tempresult1['label3'];
   $label4        = $tempresult1['label4'];
   $label5        = $tempresult1['label5'];
   $label6        = $tempresult1['label6'];
   
   $limit1        = $tempresult1['limit1'];
   $limit2        = $tempresult1['limit2'];
   $limit3        = $tempresult1['limit3'];
   $limit4        = $tempresult1['limit4'];
   $limit5        = $tempresult1['limit5'];
   $limit6        = $tempresult1['limit6'];
   
   $status1    	  = $tempresult1['status1']; 
   $status2       = $tempresult1['status2'];
   $status3       = $tempresult1['status3'];
   $status4       = $tempresult1['status4'];
   $status5       = $tempresult1['status5'];
   $status6       = $tempresult1['status6'];
   
   $field_type1   = $tempresult1['field_type1']; 
   $field_type2   = $tempresult1['field_type2'];
   $field_type3   = $tempresult1['field_type3'];
   $field_type4   = $tempresult1['field_type4'];
   $field_type5   = $tempresult1['field_type5'];
   $field_type6   = $tempresult1['field_type6'];
   
   ## setting all variables for writting  image on template
   $write_image_status      = $tempresult1['write_image_status'];
   $write_image_st_pos_x    = $tempresult1['write_image_st_pos_x'];
   $write_image_st_pos_y    = $tempresult1['write_image_st_pos_y'];
   $write_image_width       = $tempresult1['write_image_width'];
   $write_image_height      = $tempresult1['write_image_height'];
   
   
    // $temp_no=rand(1111,9999);
    
     $pathfor_image=$newpath.$name_t_val."_".$temp_no.".png";
     $create_image_path='/var/www/html/narrowcasting/mededelingen/'.$pathfor_image;
     $create_image_thumb_path='/var/www/html/narrowcasting/mededelingen/thumb/'.$pathfor_image;
     //unlink($deleteimage);
  }
 
 }
 // end of code for edit when come back from edit carrousal link
 
 
 $tempfont6 = (isset($tempfont6)) ? $tempfont6 : '';
 $tempfont5 = (isset($tempfont5)) ? $tempfont5 : '';
 $tempfont4 = (isset($tempfont4)) ? $tempfont4 : '';
 $tempfont3 = (isset($tempfont3)) ? $tempfont3 : '';
 $tempfont2 = (isset($tempfont2)) ? $tempfont2 : '';
 $tempfont1 = (isset($tempfont1)) ? $tempfont1 : '';
 
 
?>

<?php
#code for setting font   when theater_templateId is not null readio button is clicked
    #add by Himani Agarwal
  if(trim($template_id)!='' && $_GET['request_for']=='template_switch'){ 
  
  $tempquery  = "select * from delay_mededelingen where id='$template_id'";
  $tempeditvalues =$db->query($tempquery);
  $tempresult = mysql_fetch_array($tempeditvalues);
  
  if($tempresult){
   
   $template_name               = $tempresult['template_name'];
   $selected_template_label     = $tempresult['name_t'];
   
   $tempcolor1    = $tempresult['color1'];
   $tempcolor2    = $tempresult['color2'];
   $tempcolor3    = $tempresult['color3'];
   $tempcolor4    = $tempresult['color4'];
   $tempcolor5    = $tempresult['color5'];
   $tempcolor6    = $tempresult['color6'];
   $tempcolor7    = $tempresult['color7'];
   $tempcolor8    = $tempresult['color8'];
   
   $tempfont1    = $tempresult['font1'];
   $tempfont2    = $tempresult['font2'];
   $tempfont3    = $tempresult['font3'];
   $tempfont4    = $tempresult['font4'];
   $tempfont5    = $tempresult['font5'];
   $tempfont6    = $tempresult['font6'];
   $tempfont7    = $tempresult['font7'];
   $tempfont8    = $tempresult['font8'];
   
   
   $label1    = $tempresult['label1'];
   $label2    = $tempresult['label2'];
   $label3    = $tempresult['label3'];
   $label4    = $tempresult['label4'];
   $label5    = $tempresult['label5'];
   $label6    = $tempresult['label6'];
   $label7    = $tempresult['label7'];
   $label8    = $tempresult['label8'];
   
   $limit1    = $tempresult['limit1']; 
   $limit2    = $tempresult['limit2'];
   $limit3    = $tempresult['limit3'];
   $limit4    = $tempresult['limit4'];
   $limit5    = $tempresult['limit5'];
   $limit6    = $tempresult['limit6'];
   $limit7    = $tempresult['limit7'];
   $limit8    = $tempresult['limit8'];
   
   
   
   $status1    = $tempresult['status1']; 
   $status2    = $tempresult['status2'];
   $status3    = $tempresult['status3'];
   $status4    = $tempresult['status4'];
   $status5    = $tempresult['status5'];
   $status6    = $tempresult['status6'];
   $status7    = $tempresult['status7'];
   $status8    = $tempresult['status8'];
   
   $field_type1    = $tempresult['field_type1']; 
   $field_type2    = $tempresult['field_type2'];
   $field_type3    = $tempresult['field_type3'];
   $field_type4    = $tempresult['field_type4'];
   $field_type5    = $tempresult['field_type5'];
   $field_type6    = $tempresult['field_type6'];
   $field_type7    = $tempresult['field_type7'];
   $field_type8    = $tempresult['field_type8'];
   
   ## setting all variables for writting  image on template
   $write_image_status      = $tempresult['write_image_status'];
   $write_image_st_pos_x    = $tempresult['write_image_st_pos_x'];
   $write_image_st_pos_y    = $tempresult['write_image_st_pos_y'];
   $write_image_width       = $tempresult['write_image_width'];
   $write_image_height      = $tempresult['write_image_height'];
   
     ## setting all variables for writting  text on center of 2 points
   $text_center1 = intval(trim($tempresult['text_center1']));
   $text_center2 = intval(trim($tempresult['text_center2']));
   $text_center3 = intval(trim($tempresult['text_center3']));
   $text_center4 = intval(trim($tempresult['text_center4']));
   $text_center5 = intval(trim($tempresult['text_center5']));
   $text_center6 = intval(trim($tempresult['text_center6']));
   $text_center7 = intval(trim($tempresult['text_center7']));
   $text_center8 = intval(trim($tempresult['text_center8']));
   
  }}
  
  
  
 
 
  $tempquery  = "select * from delay_mededelingen";
  $tempeditvalues =$db->query($tempquery);
  $tempresult = mysql_fetch_array($tempeditvalues);
  
  if($tempresult){
   
   $template_name               = $tempresult['template_name'];
   $selected_template_label     = $tempresult['name_t'];
   
   $tempcolor1    = $tempresult['color1'];
   $tempcolor2    = $tempresult['color2'];
   $tempcolor3    = $tempresult['color3'];
   $tempcolor4    = $tempresult['color4'];
   $tempcolor5    = $tempresult['color5'];
   $tempcolor6    = $tempresult['color6'];
   $tempcolor7    = $tempresult['color7'];
   $tempcolor8    = $tempresult['color8'];
   
   $tempfont1    = $tempresult['font1'];
   $tempfont2    = $tempresult['font2'];
   $tempfont3    = $tempresult['font3'];
   $tempfont4    = $tempresult['font4'];
   $tempfont5    = $tempresult['font5'];
   $tempfont6    = $tempresult['font6'];
   $tempfont7    = $tempresult['font7'];
   $tempfont8    = $tempresult['font8'];
   
   
   $label1    = $tempresult['label1'];
   $label2    = $tempresult['label2'];
   $label3    = $tempresult['label3'];
   $label4    = $tempresult['label4'];
   $label5    = $tempresult['label5'];
   $label6    = $tempresult['label6'];
   $label7    = $tempresult['label7'];
   $label8    = $tempresult['label8'];
   
   $limit1    = $tempresult['limit1']; 
   $limit2    = $tempresult['limit2'];
   $limit3    = $tempresult['limit3'];
   $limit4    = $tempresult['limit4'];
   $limit5    = $tempresult['limit5'];
   $limit6    = $tempresult['limit6'];
   $limit7    = $tempresult['limit7'];
   $limit8    = $tempresult['limit8'];
   
   $status1    = $tempresult['status1']; 
   $status2    = $tempresult['status2'];
   $status3    = $tempresult['status3'];
   $status4    = $tempresult['status4'];
   $status5    = $tempresult['status5'];
   $status6    = $tempresult['status6'];
   $status7    = $tempresult['status7'];
   $status8    = $tempresult['status8'];
   
   $field_type1    = $tempresult['field_type1']; 
   $field_type2    = $tempresult['field_type2'];
   $field_type3    = $tempresult['field_type3'];
   $field_type4    = $tempresult['field_type4'];
   $field_type5    = $tempresult['field_type5'];
   $field_type6    = $tempresult['field_type6'];
   $field_type7    = $tempresult['field_type7'];
   $field_type8    = $tempresult['field_type8'];
   
   ## setting all variables for writting  image on template
   $write_image_status      = $tempresult['write_image_status'];
   $write_image_st_pos_x    = $tempresult['write_image_st_pos_x'];
   $write_image_st_pos_y    = $tempresult['write_image_st_pos_y'];
   $write_image_width       = $tempresult['write_image_width'];
   $write_image_height      = $tempresult['write_image_height'];
   
     ## setting all variables for writting  text on center of 2 points
   $text_center1 = intval(trim($tempresult['text_center1']));
   $text_center2 = intval(trim($tempresult['text_center2']));
   $text_center3 = intval(trim($tempresult['text_center3']));
   $text_center4 = intval(trim($tempresult['text_center4']));
   $text_center5 = intval(trim($tempresult['text_center5']));
   $text_center6 = intval(trim($tempresult['text_center6']));
   $text_center7 = intval(trim($tempresult['text_center7']));
   $text_center8 = intval(trim($tempresult['text_center8']));
   
  }


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Wachttijden spreekuur</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />

<?php include('jsfiles.html')?>
<!--::: it depends on which style you choose :::-->
<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->
<script type="text/javascript">
 
function checkvalidation(input){
  var maxlengthExceed=false;
  $('#line1,#line2,#line3,#line4,#line5,#line6').each(function(){
	var valueLength=$.trim($(this).val()).length;
	var maxLength=$(this).attr('maxlength');
	var isVisible=$(this).parent('div').css('display');
	if(isVisible=='block'){
	  if(valueLength>maxLength){
	   maxlengthExceed=true;
	   return;
	  }
	}
    });
 
  
  if(maxlengthExceed){
   alert('U heeft het maximaal aantal toegestane karakters van n of meer velden overschreden.');
  }
   return !maxlengthExceed;
 }
 
 
 
 function showcontent(theatre_id,template_id,is_minute, is_delay, printtext) { 
    document.getElementById('showcreate').style.display="block";
    var line1 = $('#line1').val();
    var line2 = $('#line2').val();//document.getElementById('line2').value; 
    var line3 = $('#line3').val();//document.getElementById('line3').value;
    var line4 = $('#line4').val();//document.getElementById('line4').value;
    var line5 = $('#line5').val();//document.getElementById('line5').value; 
    var line6 = $('#line6').val();//document.getElementById('line6').value;
    var selected_image_path=$('#selected_image_path').val();
    var edit_image_name = $('#edit_image_name').val();
    var temp_mededelingen_id=$('#temp_mededelingen_id').val();
    var carrousel_id=$('#carrousel_id').val();
    var come_from=$('#come_from').val();
    //var template_id = document.getElementById('template_id').value;
    window.location = 'mededelingen_overlay.php?carrousel_id='+carrousel_id+'&request_for=template_switch&template_id='+template_id+'&line1='+line1+'&line2='+line2+'&line3='+line3+'&line4='+line4+'&line5='+line5+'&line6='+line6+
    '&write_image_path='+selected_image_path+'&temp_mededelingen_id='+temp_mededelingen_id+"&edit_image_name="+edit_image_name+'&come_from='+come_from+'&is_minute='+is_minute+'&is_delay='+is_delay+'&printtext='+printtext;
 }
 
function popitup(path,id) {
    var url='previewvideo.php?id='+id+'&path='+path;
    var newwindow=window.open(url,'Preview','height=500,width=640,left=190,top=150,screenX=200,screenY=100');
}

function changefont(id1,id2) {
  var fonttype=id1.split(".");
  document.getElementById("font"+id2).innerHTML='<span style="font-family:'+fonttype[0]+'">Voorbeeld</span>';
}




 
 function trim(s)
  {
	  return rtrim(ltrim(s));
  }

  function ltrim(s)
  {
	  var l=0;
	  while(l < s.length && s[l] == ' ')
	  {	l++; }
	  return s.substring(l, s.length);
  }

  function rtrim(s)
  {
	  var r=s.length -1;
	  while(r > 0 && s[r] == ' ')
	  {	r-=1;	}
	  return s.substring(0, r+1);
  }
  
  // for directory listing functionality
var current_directory="";
     
     $(document).ready(function() {
      
       $('img.dir_icon,img.file_icon').live('click',function(){
	 var seletedItemClass=$(this).attr("class");
	 var name=trim($(this).attr("data"));
	 if(seletedItemClass=='dir_icon'){
	   urlGoForward(name);
	   getDirectoryList();
	   // urlGoForward();
	 }else if(seletedItemClass=='file_icon'){
	  
	  var confirmAction=confirm("Wilt u deze afbeelding selecteren?");
	  if(confirmAction){
	   $('#selected_image_path').val(current_directory+'/'+name);
	   $('img#selected_image_thumb').attr('src',$(this).attr("src"));
	   $('img#selected_image_thumb').css('display','block');
	   $('div#selected_image_block').css('display','block');
	   $('.piro_close').trigger('click');
	  }
	 }
       });
       
       
        $('#go_back_button').live('click',function(){
	 var seletedItemClass=$(this).attr("class");
	 var name=$(this).attr("data");
	// alert("hi")
	 if(current_directory!="" && current_directory!="/" ){
	    urlGoBack();
	    getDirectoryList();
	   if(current_directory==""){
	    
	   }
	 }
	  
       });
	
	 $('img#selected_image_thumb_remove').live('click',function(){
	  var confirmAction=confirm("Wilt u de selectie verwijderen?");
	  if(confirmAction){
	   $('#selected_image_path').val("");
	   $('img#selected_image_thumb').attr('src',"");
	   $('div#selected_image_block').css('display','none');
	   
	  }
	  
       });
	
  
       

     });
    
     
     function chnagePiroboxHeight(){
          var windowHeight=$(window).height();
          var piroboxHeight=$('div.div_reg div.horizon_gallery2').height();
	  var liCount=$('div.div_reg div.horizon_gallery2 ul#directory_listing_table li').length;
	  if(piroboxHeight>=(windowHeight-100)){
	   piroboxHeight=windowHeight-100;
	  }else if(liCount<=5){
	   piroboxHeight=200+100;
	  }
	  else if(liCount>5){
	   piroboxHeight=windowHeight-100;
	  }
	  var heightDiff=(windowHeight-piroboxHeight)/2;
	 if($.browser.mozilla){
	   var windowScrollHeight=$("html,body").scrollTop();//+windowHeight;
	 }else{
	   var windowScrollHeight=$("body").scrollTop();//+windowHeight;
	 }
          $('div.resize').animate({ height:(piroboxHeight+'px') },'500',function(){} );
          $('div.div_reg').animate({ height:(piroboxHeight+'px') },'500',function(){} );
	  $('div.div_reg div.horizon_gallery2').animate({ height:((piroboxHeight-100)+'px') },'500',function(){} );
	  $('table.piro_html').animate({ height:(piroboxHeight+20+'px') },'500',function(){} );
	  $('table.piro_html').animate({ top:(windowScrollHeight+heightDiff+'px') },'500',function(){} );
     }
     function urlGoBack(){
       var lastIndexOfSlash= current_directory.lastIndexOf('/');
       if(lastIndexOfSlash!=undefined){
	current_directory=current_directory.substring(0,lastIndexOfSlash);
       }
       if(current_directory==""){
	$('div.div_reg  #go_back_button').css('display','none');
	$('div#directory_container  #go_back_button').css('display','none');
	
	
       }
     }
     
     function urlGoForward(directory_name){
      var lastIndexOfSlash= current_directory.lastIndexOf('/');
      lastSegement=current_directory.substring(lastIndexOfSlash+1);
      if(lastSegement!=directory_name){
        current_directory+='/'+directory_name;
	$('div.div_reg  #go_back_button').css('display','block');
	$('div#directory_container  #go_back_button').css('display','block');
      }
      
     }
     
      
     
   function getDirectoryList() {
    var piroboxHeight=$('table.piro_html').height();
    $('div.div_reg  #loading_div').css('display','block');
    $('div.div_reg  #loading_div').css('height',piroboxHeight+'px');
    $.ajax({  
              type: "GET", cache: false,url: 'get_directory_list.php?directory_name='+current_directory,
              complete: function(data){
                setTimeout(function(){
		   try
		    {
		     var content_length=$("div.div_reg").html().length;
		    }
		    catch(err){}
		   if(content_length>0 && content_length!=undefined){
		     $("ul#directory_listing_table").html(data.responseText);
		     chnagePiroboxHeight();
		     $('div.div_reg  #loading_div').css('display','none');
		    }else{
		       $('div.div_reg  #loading_div').css('display','none');
		       urlGoBack();
		    }
		   },500);
		  
              } ,
	      error: function() {
	           
	            $('div.div_reg  #loading_div').css('display','none');
		     urlGoBack();
                    alert("Er is iets fout gegaan...");
		    
                }      
          });
  
 }
  
  
 function autosave(id) {
 if(id==1) {
     window.location='welcome.php';
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
    } 
    else if(id==2){
     window.location='create_carrousel.php';
    } else {
     
    }
}

function validate_delay_form(){
 
 if($("#minutes_1_1").length && $("#minutes_1_1").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 if($("#minutes_2_1").length && $("#minutes_2_1").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 if($("#minutes_3_1").length && $("#minutes_3_1").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_4_1").length && $("#minutes_4_1").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_5_1").length && $("#minutes_5_1").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_6_1").length && $("#minutes_6_1").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_1_2").length && $("#minutes_1_2").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_2_2").length && $("#minutes_2_2").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 if($("#minutes_3_2").length && $("#minutes_3_2").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_4_2").length && $("#minutes_4_2").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_5_2").length && $("#minutes_5_2").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_6_2").length && $("#minutes_6_2").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_1_3").length && $("#minutes_1_3").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_2_3").length && $("#minutes_2_3").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_3_3").length && $("#minutes_3_3").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_4_3").length && $("#minutes_4_3").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_5_3").length && $("#minutes_5_3").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_6_3").length && $("#minutes_6_3").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_1_4").length && $("#minutes_1_4").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_2_4").length && $("#minutes_2_4").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_3_4").length && $("#minutes_3_4").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_4_4").length && $("#minutes_4_4").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_5_4").length && $("#minutes_5_4").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 
 if($("#minutes_6_4").length && $("#minutes_6_4").val()==""){
  alert("U dient alle velden in te vullen");
  return false;
 }
 showBlockUI();
}

 function showBlockUI(){
  $.blockUI({ 
            message: $('#domMessage'),
            css: { opacity: .9,
            padding: '20',
            textAlign: 'center',
            } 
          }); 
}

function openitup(){
 var template_id=$("#template_id").val();
var url = "delaytxt.php?template_id="+template_id;
 window.open(url,'Crop','height=500,width=640,left=190,top=150,screenX=200,screenY=100,scrollbars=yes');
}
 function disableText(id) {
    document.getElementById(id).disabled = true;
}
function enableText(id) {
    document.getElementById(id).disabled = false;
}
</script>

<style type="text/css">
	
	#alle_carrousels,#maak_carrousel,#nieuwe_twitterfeed,#uploaden_bestand,#nieuw_bestand,#iets_nieuws,#tot_ziens{
		  <?php echo $css_color_rule;?>; 
	}
	
	
	
	#alle_carrousels{font-size:14px;line-height:30px;}
	#maak_carrousel{font-size:14px;line-height:30px;}
	
	#uploaden_bestand{font-size:14px;line-height:30px;}
	#nieuw_bestand{font-size:14px;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
	#iets_nieuws{font-size:14px;line-height:30px;}
	#tot_ziens{font-size:14px;line-height:30px;}
	#spreekuur{font-size:14px;color: #b5b5b5;line-height:30px;}
	#delay_mededelingen{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
	.nav{float:right;}
	.main_container{margin-top:100px;}
	.single_row textarea {color: #272727;font-size: 15px;border: 1px solid #b5b5b5;}
	.loading {  background: url("./img/<?php echo $color_scheme;?>/loader.gif") no-repeat scroll center transparent;
	  display: block; height: 223px;opacity: 0.6; opacity: 0.6;  position: absolute;top: 0px; left:0px;width: 94%;z-index: 99;
	  
	  background-color: rgba(69, 71, 71, 0.5);
	  }
	 
	 .delete_image_icon{
	  cursor: pointer; height: 19px; margin:-102px -5px 0 153px; width: 19px;
	 }
</style>

</head>
<?php
if(isset($temptname) and $temptname!='' and isset($_GET['method'])){
?>

<body onload="showcontent(<?php echo $temptname;?>)">
<?php }else{?>
 <body >
 <?php }?>

   <?php if($_SESSION['usertype']=='delay'): ?>
   <div id="wachttijden_popup" style="margin-top:0px;">
   
   <?php elseif(trim($_REQUEST['come_from'])==''): ?>
      <?php include('header.html'); ?>
	  <div class="main_container" style="margin-top:0px;">
	  <?php else: ?>

    <?php include('header_overlay.html'); ?>
	<div class="main_container" style="margin-top:0px;">
	<?php endif; ?>
   
   
   
    
        	
     <?php if(trim($_REQUEST['come_from'])==''){?>
   <div class="content" id="wachttijden_tekst">
    	 <span class="title">Wachttijden spreekuur</span>
   
    	 
    	 <p>Voor elk spreekuur kunt u hier de vertraging doorgeven. Vul een 0 in indien u op schema loopt. Indien een arts met spoed is weggeroepen kunt u dit aanvinken. Er wordt dan gemeld dat de wachttijd onbekend is. Als u op "Opslaan" drukt worden de wijzigingen doorgevoerd en is dit terug te zien op de schermen, indien u een wachttijd boodschap in de lopende carrousel heeft geplaatst.</p><br />
	
   <?php }?>
     <!--request_for=template_edit -->
     <?php if($carrousel_id=="" && $come_from!="" && trim($_GET['thumb_page'])=="") { ?>
      <div class="video_tab_new"><a href='create_carrousel.php'>Terug</a></div>
     <?php } else if($carrousel_id!="" && $come_from!="" && trim($_GET['thumb_page'])==""){ ?>
      <div class="video_tab_new"><a href="edit.php?id=<?php echo $carrousel_id;?>&status=1">Terug</a></div>

     <?php } ?>
     
        
	<?php
	
	 $action = "delay_mededelingen_overlay.php?thumb_page=1&template_id=".$template_id;
	
	?>
	<!--<form name="c_create" action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" onsubmit="return validate_delay_form();">-->
	<div style="margin-top:-10px; margin-left:-7px;" class="vertical_gallery">
        	<div class="minutes">
		<table cellspacing="7" cellpadding="5" id="wachttijden_tabel">
	
	<?php
	$checked="";
      // echo "Number : ".$_POST['number'];
      // echo "Checked : ".$_POST['emergency_'.$i];
      $qry_folder="select * from narrowcasting_folder";
      $get_folder=$db->query($qry_folder);
      $get_folder_result=mysql_fetch_array($get_folder);
      
      if($get_folder_result['is_client']==1){
      
<<<<<<< HEAD:resources/delay_mededelingen_overlay_zonnestraal.php
	$qy= "select a.* , b.* from delay_mededelingen as a, clients as b where a.client_id=b.id and b.is_active=1";
      }else{
=======
	$qy= "select a.*, b.id as clientid, b.is_active from delay_mededelingen as a, clients as b where a.client_id=b.id and b.is_active=1 and a.theatre_id in (select id from theatre where status = '1') order by a.id asc";
     }else{
>>>>>>> FETCH_HEAD:resources/delay_mededelingen_overlay_ozz.php
	
	 $qy="select * from delay_mededelingen where theatre_id in (select id from theatre where status = '1') order by id asc";
      }
	 
	 $get_qy_templates=$db->query($qy);
	 $i=1;
	
	 while($template_qy_result =mysql_fetch_array($get_qy_templates)){
	  ?>
	 <form name="template<?php echo $i; ?>" id="template<?php echo $i; ?>" action="<?php echo $action; ?>"  method="post">
	 <?php 
	 $template_name  = $template_qy_result['template_name'];
	 $template_id    = $template_qy_result['id']; 
	 $status1	 = $template_qy_result['status1'];
	 $status2	 = $template_qy_result['status2'];
	 $status3	 = $template_qy_result['status3'];
	 $status4	 = $template_qy_result['status4'];
	 $status5	 = $template_qy_result['status5'];
	 $status6	 = $template_qy_result['status6'];
	 $status7	 = $template_qy_result['status7'];
	 $status8	 = $template_qy_result['status8'];
	 $name_t	 = $template_qy_result['name_t'];
	 $label1	 = $template_qy_result['label1'];
	 $label2	 = $template_qy_result['label2'];
	 $label3	 = $template_qy_result['label3'];
	 $label4	 = $template_qy_result['label4'];
	 $label5	 = $template_qy_result['label5'];
	 $label6	 = $template_qy_result['label6'];
	 $label7	 = $template_qy_result['label7'];
	 $label8	 = $template_qy_result['label8'];
	 
	 $last_inser_value  = "select * from temp_delay_mededelingen where template_id = $template_id limit 1";
	 $last_inser_value_row_set =$db->query($last_inser_value);
	 $last_inser_value_row_data = mysql_fetch_array($last_inser_value_row_set);
	 $is_emergency_checked1=$last_inser_value_row_data['is_emergency1'];
	 $is_emergency_checked2=$last_inser_value_row_data['is_emergency2'];
	 $is_emergency_checked3=$last_inser_value_row_data['is_emergency3'];
	 $is_emergency_checked4=$last_inser_value_row_data['is_emergency4'];
	 $is_emergency_checked5=$last_inser_value_row_data['is_emergency5'];
	 $is_emergency_checked6=$last_inser_value_row_data['is_emergency6'];
	 $is_emergency_checked7=$last_inser_value_row_data['is_emergency7'];
	 $is_emergency_checked8=$last_inser_value_row_data['is_emergency8'];
	
	 $is_not_present_1=$last_inser_value_row_data['not_present_1'];
	 $is_not_present_2=$last_inser_value_row_data['not_present_2'];
	 $is_not_present_3=$last_inser_value_row_data['not_present_3'];
	 $is_not_present_4=$last_inser_value_row_data['not_present_4'];
	 $is_not_present_5=$last_inser_value_row_data['not_present_5'];
	 $is_not_present_6=$last_inser_value_row_data['not_present_6'];
	 $is_not_present_7=$last_inser_value_row_data['not_present_7'];
	 $is_not_present_8=$last_inser_value_row_data['not_present_8'];
	 
	 $is_minutes_1=$last_inser_value_row_data['is_minutes1'];
	 $is_minutes_2=$last_inser_value_row_data['is_minutes2'];
	 $is_minutes_3=$last_inser_value_row_data['is_minutes3'];
	 $is_minutes_4=$last_inser_value_row_data['is_minutes4'];
	 $is_minutes_5=$last_inser_value_row_data['is_minutes5'];
	 $is_minutes_6=$last_inser_value_row_data['is_minutes6'];
	 $is_minutes_7=$last_inser_value_row_data['is_minutes7'];
	 $is_minutes_8=$last_inser_value_row_data['is_minutes8'];
	 
	 if($is_minutes_1==1 ){
	  $minutes_checked1='checked="checked"';
	 }else{
	  $minutes_checked1="";
	 }
	 
	 if($is_minutes_2==1){
	  $minutes_checked2='checked="checked"';
	 }else{
	  $minutes_checked2="";
	 }
	 
	 if($is_minutes_3==1){
	  $minutes_checked3='checked="checked"';
	 }else{
	  $minutes_checked3="";
	 }
	 
	 if($is_minutes_4==1){
	  $minutes_checked4='checked="checked"';
	 }else{
	  $minutes_checked4="";
	 }
	 
	 if($is_minutes_5==1){
	  $minutes_checked5='checked="checked"';
	 }else{
	  $minutes_checked5="";
	 }
	 
	 if($is_minutes_6==1){
	  $minutes_checked6='checked="checked"';
	 }else{
	  $minutes_checked6="";
	 }
	 
	 if($is_minutes_7==1){
	  $minutes_checked7='checked="checked"';
	 }else{
	  $minutes_checked7="";
	 }
	 
	 if($is_minutes_8==1){
	  $minutes_checked8='checked="checked"';
	 }else{
	  $minutes_checked8="";
	 }
	 
	 if($is_emergency_checked1==1){
	  $checked1='checked="checked"';
	 }else{
	  $checked1="";
	 }
	 if($is_emergency_checked2==1){
	  $checked2='checked="checked"';
	 }else{
	  $checked2="";
	 }
	 if($is_emergency_checked3==1){
	  $checked3='checked="checked"';
	 }else{
	  $checked3="";
	 }
	 
	 if($is_emergency_checked4==1){
	  $checked4='checked="checked"';
	 }else{
	  $checked4="";
	 }
	 
	 if($is_emergency_checked5==1){
	  $checked5='checked="checked"';
	 }else{
	  $checked5="";
	 }
	 
	 if($is_emergency_checked6==1){
	  $checked6='checked="checked"';
	 }else{
	  $checked6="";
	 }
	 
	 if($is_emergency_checked7==1){
	  $checked7='checked="checked"';
	 }else{
	  $checked7="";
	 }
	 
	 if($is_emergency_checked8==1){
	  $checked8='checked="checked"';
	 }else{
	  $checked8="";
	 }
	 
	 
	 if($is_not_present_1==1){
	  $not_present_checked1='checked="checked"';
	 }else{
	  $not_present_checked1="";
	 }
	 
	 if($is_not_present_2==1){
	  $not_present_checked2='checked="checked"';
	 }else{
	  $not_present_checked2="";
	 }
	 
	 if($is_not_present_3==1){
	  $not_present_checked3='checked="checked"';
	 }else{
	  $not_present_checked3="";
	 }
	 
	 if($is_not_present_4==1){
	  $not_present_checked4='checked="checked"';
	 }else{
	  $not_present_checked4="";
	 }
	 
	 if($is_not_present_5==1){
	  $not_present_checked5='checked="checked"';
	 }else{
	  $not_present_checked5="";
	 }
	 
	 if($is_not_present_6==1){
	  $not_present_checked6='checked="checked"';
	 }else{
	  $not_present_checked6="";
	 }
	 
	 if($is_not_present_7==1){
	  $not_present_checked7='checked="checked"';
	 }else{
	  $not_present_checked7="";
	 }
	 
	 if($is_not_present_8==1){
	  $not_present_checked8='checked="checked"';
	 }else{
	  $not_present_checked8="";
	 }
	
	 
	  $default_minute_val1=$last_inser_value_row_data['minutes_1'];
	  $default_minute_val2=$last_inser_value_row_data['minutes_2'];
	  $default_minute_val3=$last_inser_value_row_data['minutes_3'];
	  $default_minute_val4=$last_inser_value_row_data['minutes_4'];
	  $default_minute_val5=$last_inser_value_row_data['minutes_5'];
	  $default_minute_val6=$last_inser_value_row_data['minutes_6'];
	  $default_minute_val7=$last_inser_value_row_data['minutes_7'];
	  $default_minute_val8=$last_inser_value_row_data['minutes_8'];
	 
	 
	?>
	
        
<?php if($status1==1):?>

 <tr>
	 <td id="titel_wachttijden"><?php echo $name_t; ?></td>
 <tr />
	 <tr>
     <td><label for="line1"><?php echo $label1;?> :</label></td><td><input type="radio" class="radio"<?php echo $minutes_checked1; ?> name="option_<?php echo $i?>_1" id="minutes1" value="" onclick="enableText('minutes_<?php echo $i?>_1')" <?php if($not_present_checked1=="" && $checked1==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked1!="" || $checked1!=""){echo 'disabled="disabled"'; } ?> type="text" name="minutes_<?php echo $i?>_1" id="minutes_<?php echo $i?>_1" value="<?php echo $default_minute_val1; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"  class="radio" <?php echo $checked1; ?> name="option_<?php echo $i?>_1" id="emergency<?php echo $i; ?>" value="emergency" onclick="disableText('minutes_<?php echo $i?>_1')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked1; ?> name="option_<?php echo $i?>_1" id="notpresent1" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_1')"/>Afwezig&nbsp;&nbsp;&nbsp;&nbsp;
     
    
    
     </td>
     </tr>
 
		 <?php endif; ?>
	 <?php if($status2==1):?>
	 

	 <tr>
     <td><label for="line1"><?php echo $label2;?> :</label></td><td><input type="radio" class="radio" <?php echo $minutes_checked2; ?> name="option_<?php echo $i?>_2" id="minutes2" value="" onclick="enableText('minutes_<?php echo $i?>_2')" <?php if($not_present_checked2=="" && $checked2==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked2!="" || $checked2!=""){echo 'disabled="disabled"'; } ?>type="text" name="minutes_<?php echo $i?>_2" id="minutes_<?php echo $i?>_2" value="<?php echo $default_minute_val2; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"  class="radio" <?php echo $checked2; ?> name="option_<?php echo $i; ?>_2" id="emergency2" value="emergency" onclick="disableText('minutes_<?php echo $i?>_2')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked2; ?> name="option_<?php echo $i?>_2" id="notpresent2" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_2')"/>Afwezig&nbsp;&nbsp;&nbsp;&nbsp;
     
     
    
     </td>
     </tr>
 
		 <?php endif; ?>
	 <?php if($status3==1):?>
  <tr>
	
     <td><label for="line1"><?php echo $label3;?> :</label></td><td><input type="radio" class="radio" <?php echo $minutes_checked3; ?> name="option_<?php echo $i?>_3" id="minutes3" value="" onclick="enableText('minutes_<?php echo $i?>_3')" <?php if($not_present_checked3=="" && $checked3==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked3!="" || $checked3!=""){echo 'disabled="disabled"'; } ?>type="text" name="minutes_<?php echo $i?>_3" id="minutes_<?php echo $i?>_3" value="<?php echo $default_minute_val3; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio" class="radio" <?php echo $checked3; ?> name="option_<?php echo $i?>_3" id="emergency3" value="emergency" onclick="disableText('minutes_<?php echo $i?>_3')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked3; ?> name="option_<?php echo $i?>_3" id="notpresent3" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_3')"/>Afwezig&nbsp;&nbsp;&nbsp;&nbsp;
     
     
    
     </td>
     </tr>
		 <?php endif; ?>
	 <?php if($status4==1):?>
  <tr>
	
     <td><label for="line1"><?php echo $label4;?> :</label></td><td><input type="radio" class="radio" <?php echo $minutes_checked4; ?> name="option_<?php echo $i?>_4" id="minutes4" value="" onclick="enableText('minutes_<?php echo $i?>_4')" <?php if($not_present_checked4=="" && $checked4==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked4!="" || $checked4!=""){echo 'disabled="disabled"'; } ?>type="text" name="minutes_<?php echo $i?>_4" id="minutes_<?php echo $i?>_4" value="<?php echo $default_minute_val4; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"  class="radio" <?php echo $checked4; ?> name="option_<?php echo $i?>_4" id="emergency4" value="emergency" onclick="disableText('minutes_<?php echo $i?>_4')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked4; ?> name="option_<?php echo $i?>_4" id="notpresent4" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_4')"/>Afwezig&nbsp;&nbsp;&nbsp;&nbsp;
     
     
    
     </td>
     </tr>
		 <?php endif; ?>
	 <?php if($status5==1):?>
  <tr>
	
     <td><label for="line1"><?php echo $label5;?> :</label></td><td><input type="radio" class="radio" <?php echo $minutes_checked5; ?> name="option_<?php echo $i?>_5" id="minutes5" value="" onclick="enableText('minutes_<?php echo $i?>_5')" <?php if($not_present_checked5=="" && $checked5==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked5!="" || $checked5!=""){echo 'disabled="disabled"'; } ?>type="text" name="minutes_<?php echo $i?>_5" id="minutes_<?php echo $i?>_5" value="<?php echo $default_minute_val5; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"  class="radio" <?php echo $checked5; ?> name="option_<?php echo $i?>_5" id="emergency5" value="emergency" onclick="disableText('minutes_<?php echo $i?>_5')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked5; ?> name="option_<?php echo $i?>_5" id="notpresent5" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_5')"/>Afwezig&nbsp;&nbsp;&nbsp;
     
     
    
     </td>
     </tr>
		 <?php endif; ?>
	 <?php if($status6==1):?>
  <tr>
	
     <td><label for="line1"><?php echo $label6;?> :</label></td><td><input type="radio" class="radio" <?php echo $minutes_checked6; ?> name="option_<?php echo $i?>_6" id="minutes6" value="" onclick="enableText('minutes_<?php echo $i?>_6')" <?php if($not_present_checked6=="" && $checked6==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked6!="" || $checked6!=""){echo 'disabled="disabled"'; } ?>type="text" name="minutes_<?php echo $i?>_6" id="minutes_<?php echo $i?>_6" value="<?php echo $default_minute_val6; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"  class="radio" <?php echo $checked6; ?> name="option_<?php echo $i?>_6" id="emergency6" value="emergency" onclick="disableText('minutes_<?php echo $i?>_6')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked6; ?> name="option_<?php echo $i?>_6" id="notpresent6" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_6')"/>Afwezig&nbsp;&nbsp;&nbsp;
     
     
    
     </td>
     </tr>
<?php endif; ?>
<?php if($status7==1):?>
  <tr>
	
     <td><label for="line1"><?php echo $label7;?> :</label></td><td><input type="radio" class="radio" <?php echo $minutes_checked7; ?> name="option_<?php echo $i?>_7" id="minutes7" value="" onclick="enableText('minutes_<?php echo $i?>_7')" <?php if($not_present_checked7=="" && $checked7==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked7!="" || $checked7!=""){echo 'disabled="disabled"'; } ?>type="text" name="minutes_<?php echo $i?>_7" id="minutes_<?php echo $i?>_7" value="<?php echo $default_minute_val7; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"  class="radio" <?php echo $checked7; ?> name="option_<?php echo $i?>_7" id="emergency7" value="emergency" onclick="disableText('minutes_<?php echo $i?>_7')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked7; ?> name="option_<?php echo $i?>_7" id="notpresent7" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_7')"/>Afwezig&nbsp;&nbsp;&nbsp;
     
     
    
     </td>
     </tr>
<?php endif; ?>

 <?php if($status8==1):?>
  <tr>
	
     <td><label for="line1"><?php echo $label8;?> :</label></td><td><input type="radio" class="radio" <?php echo $minutes_checked8; ?> name="option_<?php echo $i?>_8" id="minutes8" value="" onclick="enableText('minutes_<?php echo $i?>_8')" <?php if($not_present_checked8=="" && $checked8==""){?>checked="checked"<?php } ?>/><input <?php if($not_present_checked8!="" || $checked8!=""){echo 'disabled="disabled"'; } ?>type="text" name="minutes_<?php echo $i?>_8" id="minutes_<?php echo $i?>_8" value="<?php echo $default_minute_val8; ?>"/> minuten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"  class="radio" <?php echo $checked8; ?> name="option_<?php echo $i?>_8" id="emergency8" value="emergency" onclick="disableText('minutes_<?php echo $i?>_8')"/>Spoed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="radio" <?php echo $not_present_checked8; ?> name="option_<?php echo $i?>_8" id="notpresent8" value="notpresent" onclick="disableText('minutes_<?php echo $i?>_8')"/>Afwezig&nbsp;&nbsp;&nbsp;
     
     
    
     </td>
     </tr>
<?php endif; ?>

<tr><td>
<input type="hidden" name="number" id="number" value="<?php echo $i; ?>" />
<input type="hidden" name="template_name" value="<?php echo $template_name; ?>" id="template_name">
<input type="hidden" name="template_id" value="<?php echo $template_id; ?>" id="template_id">	
<input type="hidden" name="temp_mededelingen_id" value="<?php echo $last_inser_value_row_data['id'] ?>" id="temp_mededelingen_id">
<!--<input type="hidden" name="theatre_id>" value="<?php // echo $theatre_id; ?>" id="theatre_id">-->
<input type="hidden" name="edit_image_name_<?php echo $i?>" value="<?php echo $last_inser_value_row_data['temp_image_name']; ?>" id="edit_image_name">
<input type="hidden" name="carrousel_id" value="<?php echo $carrousel_id; ?>" id="carrousel_id">
<input type="hidden" name="come_from" value="<?php echo $come_from; ?>" id="come_from">
	   <div class="footer_tab" id="wachttijden_opslaan"><input type="submit" name="submit" value="Opslaan" /></div>
	   </td>
</tr>
</form>	
       <?php $i++; } ?>
        <!-- IMG BLOCKS END -->
			
		 
	</table>
	     
	</div>
        <div class="overlay_fields">
	    
	 <?php $flag=1; if($flag=='1') {?>
	      <div class="overlay_wrap" id="with_agedot" style="display:none;">
	      
	       <div class="single_row"  style="display:<?php if(intval($write_image_status)==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
	       <span style="width:150px;float:left;"><label for="line1">Kies een afbeelding:</label></span>
                    <div class="video_tab_new" style="margin-left:150px; margin-top:-3px;">
		        <a href="#directory_container" rel="inline-940-350" class="pirobox"  id="abc">Bladeren</a>
		     </div>
                     <input type="hidden" name="write_image_path" value="<?php echo $tempwrite_image_path?>" id="selected_image_path" />
		     <?php
		     $show_image='none';
		      if(trim($tempwrite_image_path)!=""){
		      $show_image="block";
		      }?>
		    <div id="selected_image_block" style="margin:-28px 0px 83px 150px;display:<?php echo $show_image;?>;" >
		      <div class="block_img">
			  <img style="display:<?php echo $show_image;?>;" src="<?php echo '.'.$tempwrite_image_path?>" id="selected_image_thumb" width="164" height="93">
		      </div>
		  
	           <div class="delete_image_icon">
		    <a href="javascript:void(0);">
	               <img alt="" src='<?php echo $close_button_path; ?>' id="selected_image_thumb_remove">
		    </a>
		   </div>
		   <div class="clear"></div>
		    </div>
                     <!-- <div >
		       <img src="./img/close_btn.png" alt="" style="margin-left: 145px;z-index: 118;">
		       
	           </div>
		   
		   -->
		   
		    <br><br>
                </div>
	        <div class="single_row"  style="display:<?php if(intval($status1)==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line1"><?php echo $label1;?>:</label></span>
		     
	             <?php if(intval($field_type1)==1){?>
                     <input type="text" name="line1" id="line1" style="width:270px;" maxlength="<?php echo $limit1;?>" value="<?php if(isset($templine1)){echo $templine1;}?>"  <?php if(strlen($templine1)>$limit1) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" style="width:270px; vertical-align:middle;" name="line1" id="line1" cols="33" maxlength="<?php echo $limit1;?>" <?php if(strlen($templine1)>$limit1) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine1)){echo trim($templine1);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color1" class="color" size="6" readonly="" value="<?php if(isset($tempcolor1)){echo $tempcolor1;}?>" >
                     <label for="line1">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font1" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont1); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Centreren:</label>
		     <select name="text_center1"  style="width:68px;">
                      <?php echo text_center_options($text_center1);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen(trim($templine1))>$limit1) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
	        <div class="single_row"  style="display:<?php if($status2==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line2"><?php echo $label2;?>:</label></span>
		     
	             <?php if(intval($field_type2)==1){?>
                     <input type="text" name="line2" id="line2" style="width:270px;" maxlength="<?php echo $limit2;?>" value="<?php if(isset($templine2)){echo $templine2;}?>"  <?php if(strlen($templine2)>$limit2) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line2" id="line2" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit2;?>" <?php if(strlen($templine2)>$limit2) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine2)){echo trim($templine2);}?></textarea>
		     
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color2" class="color" size="6" readonly="" value="<?php if(isset($tempcolor2)){echo $tempcolor2;}?>" >
                     <label for="line2">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font2" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont2); ?>
                     </select>&nbsp;&nbsp;
		    <label for="line1">Centreren:</label>
		     <select name="text_center2"  style="width:68px;">
                      <?php echo text_center_options($text_center2);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine2)>$limit2) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		<div class="single_row"  style="display:<?php if($status3==1) { echo 'block';  }else {echo 'none';} ?>">
                     <span style="width:145px;float:left;"><label for="line3"><?php echo $label3;?>:</label></span>
	             <?php if(intval($field_type3)==1){?>
                     <input type="text" name="line3" id="line3" style="width:270px;" maxlength="<?php echo $limit3;?>" value="<?php if(isset($templine3)){echo $templine3;}?>"  <?php if(strlen($templine3)>$limit3) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		      <textarea name="line3" id="line3" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit3;?>" <?php if(strlen($templine3)>$limit3) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine3)){echo trim($templine3);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color3" class="color" size="6" readonly="" value="<?php if(isset($tempcolor3)){echo $tempcolor3;}?>" >
                     <label for="line1">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font3" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont3); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Centreren:</label>
		     <select name="text_center3"  style="width:68px;">
                       <?php echo text_center_options($text_center3);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine3)>$limit3) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
		<div class="single_row"  style="display:<?php if($status4==1) { echo 'block';  }else {echo 'none';} ?>">
                     <span style="width:145px;float:left;"><label for="line4"><?php echo $label4;?>:</label></span>
		     
	             <?php if(intval($field_type4)==1){?>
                     <input type="text" name="line4" id="line4" style="width:270px;" maxlength="<?php echo $limit4;?>" value="<?php if(isset($templine4)){echo $templine4;}?>"  <?php if(strlen($templine4)>$limit4) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line4" id="line4" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit4;?>" <?php if(strlen($templine4)>$limit4) {?> class="timeline_length_error" <?php }?>><?php if(isset($templine4)){echo trim($templine4);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color4" class="color" size="6" readonly="" value="<?php if(isset($tempcolor4)){echo $tempcolor4;}?>" >
                     <label for="line4">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font4" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont4); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Centreren:</label>
		     <select name="text_center4" style="width:68px;">
		       <?php echo text_center_options($text_center4);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine4)>$limit4) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
		<div class="single_row"  style="display:<?php if($status5==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line5"><?php echo $label5;?>:</label></span>
		     
	             <?php if(intval($field_type5)==1){?>
                     <input type="text" name="line5" id="line5" style="width:270px;" maxlength="<?php echo $limit5;?>" value="<?php if(isset($templine5) ){echo $templine5;}?>"  <?php if(strlen($templine5)>$limit5) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		    <textarea rows="4" name="line5" id="line5" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit5;?>" <?php if(strlen($templine5)>$limit5) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine5)){echo trim($templine5);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color5" class="color" size="6" readonly="" value="<?php if(isset($tempcolor5)){echo $tempcolor5;}?>" >
                     <label for="line5">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font5" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont5); ?>
                     </select>&nbsp;&nbsp;
		    <label for="line1">Centreren:</label>
		     <select name="text_center5"  style="width:68px;">
                       <?php echo text_center_options($text_center5);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine5)>$limit5) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		<div class="single_row"  style="display:<?php if($status6==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line6"><?php echo $label6;?>:</label></span>
		     
	             <?php if(intval($field_type6)==1){?>
                     <input type="text" name="line6" id="line6" style="width:270px;" maxlength="<?php echo $limit6;?>" value="<?php if(isset($templine6) ){echo $templine6;}?>"  <?php if(strlen($templine6)>$limit6) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line6" id="line6" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit6;?>" <?php if(strlen($templine6)>$limit6) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine6)){echo trim($templine6);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color6" class="color" size="6" readonly="" value="<?php if(isset($tempcolor6)){echo $tempcolor6;}?>" >
                     <label for="line6">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font6" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont6); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Centreren:</label>
		     <select name="text_center6"  style="width:68px;">
                      <?php echo text_center_options($text_center6);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine6)>$limit6) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    
                </div>
		
		
		
		
		<div class="single_row"  style="display:<?php if($status7==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">                     <span style="width:145px;float:left;"><label for="line7"><?php echo $label6;?>:</label></span>
		     
	             <?php if(intval($field_type7)==1){?>
                     <input type="text" name="line7" id="line7" style="width:270px;" maxlength="<?php echo $limit7;?>" value="<?php if(isset($templine7) ){echo $templine7;}?>"  <?php if(strlen($templine7)>$limit7) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line7" id="line7" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit7;?>" <?php if(strlen($templine7)>$limit7) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine7)){echo trim($templine7);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color7" class="color" size="6" readonly="" value="<?php if(isset($tempcolor7)){echo $tempcolor7;}?>" >
                     <label for="line7">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font6" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont7); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Centreren:</label>
		     <select name="text_center7"  style="width:68px;">
                      <?php echo text_center_options($text_center7);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine7)>$limit7) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    
                </div>
		
		
		<div class="single_row"  style="display:<?php if($status8==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">                     <span style="width:145px;float:left;"><label for="line7"><?php echo $label8;?>:</label></span>
		     
	             <?php if(intval($field_type8)==1){?>
                     <input type="text" name="line8" id="line8" style="width:270px;" maxlength="<?php echo $limit8;?>" value="<?php if(isset($templine8) ){echo $templine8;}?>"  <?php if(strlen($templine8)>$limit8) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line8" id="line8" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit8;?>" <?php if(strlen($templine8)>$limit8) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine8)){echo trim($templine8);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color8" class="color" size="6" readonly="" value="<?php if(isset($tempcolor8)){echo $tempcolor8;}?>" >
                     <label for="line7">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font6" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont8); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Centreren:</label>
		     <select name="text_center7"  style="width:68px;">
                      <?php echo text_center_options($text_center8);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine8)>$limit8) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    
                </div>
		
	     
               <div class="clear"></div>
						
	    </div>
	    <?php }?>
					
					
    	     
        <!-- input fields end -->
        
   		<!-- START FOOTER -->
			<?php if($_SESSION['usertype']=='delay'):?>
			<a id="uitloggen" style="margin-left:6px; position:relative;top:-40px;" href="javascript:void(0)" onclick="autosave('6')">Uitloggen</a> 
				<?php endif; ?>
			
			
    	<?php if($flag=='1') $display_button='block'; else $display_button='none';?>
	
	 <div style="margin-top:-40px; margin-left:7px;" class="footer" style="display:<?php echo $display_button;?>;" id="showcreate">	
	     
	   <!--<div class="footer_tab" id="wachttijden_opslaan"><input type="submit" name="submit" value="Wijzigingen opslaan"></div>-->
	 </div>
	 
     
  
<div  id="directory_container" style="display:none;">
<div class="main_container" style="margin-top:0px;width: 930px;" >

  <!--<span style="font-size:17px; font-weight:bold;">Select Image From Server</span><br><br>-->
    <br>
     <div class="login_fields">
     <input type='button' value='Terug' class="login_submit" id='go_back_button' style="display:none;"/>
     </div>
   
    <div class="horizon_gallery2" style="width: 930px;">
    <div class="horizon_blocks_cont" id="show" style="width: 930px;margin-left:2px;">
	 <div id="contentLeft">
	   <ul class="ui-sortable" id="directory_listing_table" style="width: 930px;">
           <?php include('get_directory_list.php');?>
           </ul>
        </div>
  </div>
  <div class="clear">&nbsp;</div>
  </div>
  <div id="loading_div" class="loading" style="height: 370px; width: 940px; display: none;"></div>  
</div>

</div>
 <div id="domMessage" style="display:none;">

<img style="margin:20px 0px 10px 0px;" src="img/<?php echo $color_scheme;?>/loader.gif" />
    
    <h5 style="margin:3px 10px 10px 10px;font-size:15px;">Uw wijzigingen worden doorgevoerd</h5>
    
</div> 
     </div> 
