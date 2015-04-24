<?php
session_start();
error_reporting(0);
ini_set('max_execution_time', 10000);
include('config/database.php');
include('config/get_theatre_label.php');
include('color_scheme_setting.php');
require_once('thumb-functions.php');
$close_button_path="./img/$color_scheme/close_btn.png";
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}

header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if(isset($_POST['submit']) and $_POST['submit']!=''){
//echo "<pre>"; print_r($_POST); echo "</pre>";
//exit;
 $data_display ="none";
 
} else{
 
 $data_display= "block";
 
}


$actualDirectory=array(
    'commercials'  =>'commercials',		       
    'images'        =>'images',
    'mededelingen' =>'mededelingen',
    'promotie'     =>'promotie',		       
    'upload'       =>'upload',
    'videos'       =>'videos'	   
   );

    ## get all directory name from db
    $theatreDetailsSql  = "select * from theatre where status=1 limit 1";
    $theatreDetails =$db->query($theatreDetailsSql);
    $theatreDetails = mysql_fetch_array($theatreDetails);
    $directoryMappings=array();
    if(is_array($theatreDetails) && count($theatreDetails)){	
       $directoryMappings[$actualDirectory['upload']]       =$theatreDetails['upload'];	 
       $directoryMappings[$actualDirectory['commercials']]  =$theatreDetails['commercials'];			 
       $directoryMappings[$actualDirectory['images']]       =$theatreDetails['images'];		     
       $directoryMappings[$actualDirectory['mededelingen']] =$theatreDetails['mededelingen'];			 
       $directoryMappings[$actualDirectory['promotie']]     =$theatreDetails['promotie'];
       $directoryMappings[$actualDirectory['videos']]       =$theatreDetails['videos'];
     }
   //echo "<pre>";print_r($directoryMappings);


//---------------------------Code used for upload video and images -------------------------------




 
 $error_message="";
 $sid=session_id();
 $db = new Database;
 $template_id=$_GET['template_id'];
 $templine1=urldecode($_GET['line1'])!=undefined?trim(urldecode($_GET['line1'])):"";
 $templine2=urldecode($_GET['line2'])!=undefined?trim(urldecode($_GET['line2'])):"";
 $templine3=urldecode($_GET['line3'])!=undefined?trim(urldecode($_GET['line3'])):"";
 $templine4=urldecode($_GET['line4'])!=undefined?trim(urldecode($_GET['line4'])):"";
 $templine5=urldecode($_GET['line5'])!=undefined?trim(urldecode($_GET['line5'])):"";
 $templine6=urldecode($_GET['line6'])!=undefined?trim(urldecode($_GET['line6'])):"";
 $templine7=urldecode($_GET['line7'])!=undefined?trim(urldecode($_GET['line7'])):"";
 $templine8=urldecode($_GET['line8'])!=undefined?trim(urldecode($_GET['line8'])):"";
 
 ## setting  writting  image on template
 $tempwrite_image_path = trim($_REQUEST['write_image_path'])!=undefined?trim($_REQUEST['write_image_path']):"";
 
 $tempwrite_image_path_2 = trim($_REQUEST['write_image_path_2'])!=undefined?trim($_REQUEST['write_image_path_2']):"";
 
 $tempwrite_image_path_a=$tempwrite_image_path;
 $tempwrite_image_path_array=explode("/",$tempwrite_image_path);
 if($tempwrite_image_path_array[0]==""){
  $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[1].'/thumb/'.$tempwrite_image_path_array[2];
 }
 else{
  $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[0].'/thumb/'.$tempwrite_image_path_array[1];
 }
 //print_r($tempwrite_image_path_thumbs);
  //print_r($tempwrite_image_path);
 $request_for=trim($_GET['request_for']);
 $carrousel_id=trim($_REQUEST['carrousel_id']);
 //print_r($_GET);
 $temp_mededelingen_id=intval(trim($_GET['temp_mededelingen_id']));
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
 
 function text_center_options($value=''){
     $text_center_options="";//<option value=''>selecteer</option>";
     $text_centre_option_data=array('Centreren'=>1,'Links'=>0, 'Rechts' => 2);
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
 //echo '<pre>';print_r($_POST);die;
    $theatre_id =$_REQUEST['theatre_id'];
    $template_id =$_REQUEST['template_id']; 
    $getCordinates  = "select * from mededelingen where id='$template_id'";
    $getCordinates =$db->query($getCordinates);
    $getCordinates = mysql_fetch_array($getCordinates);
    $template_name=$getCordinates['template_name'];
    
    
    $template_nameArray = explode('.',$template_name);
    $newimage='/var/www/html/narrowcasting/mededelingen_template/'.$template_name;
    $newpath = $template_nameArray[0]; //template_name without extension
   
    $replace_word = array('');
    if($template==1) {
     $newimage='/var/www/html/narrowcasting/mededelingen_template/huishoudelijke_mededeling.png';
     $newpath='huishoudelijke_mededeling';
    } else {
     
    }
    
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
  
  	if($width==1280 && $height==720){
    $imagetransparent='/var/www/html/narrowcasting/tmp/transparant1280.png';
   	}
	
	elseif($width==1080 && $height==1920){
  	$imagetransparent='/var/www/html/narrowcasting/tmp/transparant1080_1920.png';
    }
	
	else{
      $imagetransparent='/var/www/html/narrowcasting/tmp/transparant1920.png';
    }
    //$imagetransparent='/var/www/html/narrowcasting/tmp/test3.png';
    $im = imagecreatefrompng($imagetransparent);
    
   $transparent_image = imagecreatefrompng($imagetransparent);
    
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
	
	$dynamic_margin_bottom1 = $getCordinates['dynamic_margin_bottom1'];
	$dynamic_margin_bottom2 = $getCordinates['dynamic_margin_bottom2'];
	$dynamic_margin_bottom3 = $getCordinates['dynamic_margin_bottom3'];
	$dynamic_margin_bottom4 = $getCordinates['dynamic_margin_bottom4'];
	$dynamic_margin_bottom5 = $getCordinates['dynamic_margin_bottom5'];
	$dynamic_margin_bottom6 = $getCordinates['dynamic_margin_bottom6'];
	$dynamic_margin_bottom7 = $getCordinates['dynamic_margin_bottom7'];
	
	## setting all variables for writting  image on template
	$write_image_status      = $getCordinates['write_image_status'];
	$write_image_status_2      = $getCordinates['write_image_status_2'];
	$write_image_st_pos_x    = $getCordinates['write_image_st_pos_x'];
	$write_image_st_pos_y    = $getCordinates['write_image_st_pos_y'];
	
	$write_image_st_pos_x_2    = $getCordinates['write_image_st_pos_x_2'];
	$write_image_st_pos_y_2   = $getCordinates['write_image_st_pos_y_2'];
	
	$write_image_width       = $getCordinates['write_image_width'];
	$write_image_height      = $getCordinates['write_image_height'];
	
	$write_image_width_2       = $getCordinates['write_image_width_2'];
	$write_image_height_2      = $getCordinates['write_image_height_2'];
	
	$write_image_path        = trim($_REQUEST['write_image_path']);
	$write_image_path_2        = trim($_REQUEST['write_image_path_2']);
	
	$write_image_resize_option = $getCordinates['write_image_resize_option'];
	
	$write_image_resize_option_2 = $getCordinates['write_image_resize_option_2'];
	
	$img_center_horizontal   = $getCordinates['img_center_horizontal'];
	$img_center_vertical     = $getCordinates['img_center_vertical'];
	
	$image_place = $getCordinates['image_place'];
	
	
	## setting all text center variables
	
	//intval(trim(
	$text_center1 = intval(trim($_REQUEST['text_center1']));
	$text_center2 = intval(trim($_REQUEST['text_center2']));
	$text_center3 = intval(trim($_REQUEST['text_center3']));
	$text_center4 = intval(trim($_REQUEST['text_center4']));
	$text_center5 = intval(trim($_REQUEST['text_center5']));
	$text_center6 = intval(trim($_REQUEST['text_center6']));
	$text_center7 = intval(trim($_REQUEST['text_center7']));
	$text_center8 = intval(trim($_REQUEST['text_center8']));
	
        $temp_mededelingen_id=intval(trim($_REQUEST['temp_mededelingen_id']));
	$edit_image_name=trim(urldecode($_REQUEST['edit_image_name']))!=undefined?trim(urldecode($_REQUEST['edit_image_name'])):"";
        $twtcheck=$_REQUEST['twt'];
	
	// echo '<br>line_height1 '.$line_height1;
  //  echo '<br>line_height2 '.$line_height2;
    //echo '<br>line_height3 '.$line_height3;
    //die;
	
	$fonttype1=$_REQUEST['font1'];
	$fonttype2=$_REQUEST['font2'];
	$fonttype3=$_REQUEST['font3'];
	$fonttype4=$_REQUEST['font4'];
	$fonttype5=$_REQUEST['font5'];
	$fonttype6=$_REQUEST['font6'];
	$fonttype7=$_REQUEST['font7'];
	$fonttype8=$_REQUEST['font8'];
	
	$font1= '/var/www/html/narrowcasting/fonts/'.$fonttype1;
	$font2 = '/var/www/html/narrowcasting/fonts/'.$fonttype2;
	$font3 = '/var/www/html/narrowcasting/fonts/'.$fonttype3;
	$font4 = '/var/www/html/narrowcasting/fonts/'.$fonttype4;
	$font5 = '/var/www/html/narrowcasting/fonts/'.$fonttype5;
	$font6 = '/var/www/html/narrowcasting/fonts/'.$fonttype6;
	$font7 = '/var/www/html/narrowcasting/fonts/'.$fonttype7;
	$font8 = '/var/www/html/narrowcasting/fonts/'.$fonttype8;
	
	$color1="#".$_REQUEST['color1'];
	$color2="#".$_REQUEST['color2'];
	$color3="#".$_REQUEST['color3'];
	$color4="#".$_REQUEST['color4'];
	$color5="#".$_REQUEST['color5'];
	$color6="#".$_REQUEST['color6'];
	$color7="#".$_REQUEST['color7'];
	$color8="#".$_REQUEST['color8'];
	
	$colorcode1=sscanf($color1, '#%2x%2x%2x');
	$colorcode2=sscanf($color2, '#%2x%2x%2x');
	$colorcode3=sscanf($color3, '#%2x%2x%2x');
	$colorcode4=sscanf($color4, '#%2x%2x%2x');
	$colorcode5=sscanf($color5, '#%2x%2x%2x');
	$colorcode6=sscanf($color6, '#%2x%2x%2x');
	$colorcode7=sscanf($color7, '#%2x%2x%2x');
	$colorcode8=sscanf($color8, '#%2x%2x%2x');
	//print_r($colorcode1);;
	//print_r($colorcode6);die;
	 
	$color_1 =imagecolorallocate($im, $colorcode1[0], $colorcode1[1], $colorcode1[2]);
	$color_2= imagecolorallocate($im, $colorcode2[0], $colorcode2[1],$colorcode2[2]);
	$color_3= imagecolorallocate($im, $colorcode3[0], $colorcode3[1],$colorcode3[2]);
	$color_4= imagecolorallocate($im, $colorcode4[0], $colorcode4[1],$colorcode4[2]);
	$color_5= imagecolorallocate($im, $colorcode5[0], $colorcode5[1],$colorcode5[2]);
	$color_6= imagecolorallocate($im, $colorcode6[0], $colorcode6[1],$colorcode6[2]);
	$color_7= imagecolorallocate($im, $colorcode7[0], $colorcode7[1],$colorcode7[2]);
	$color_8= imagecolorallocate($im, $colorcode8[0], $colorcode8[1],$colorcode8[2]);
    
	$text1=str_replace($replace_word,"",trim($_REQUEST['line1']));
	$text2=str_replace($replace_word,"",trim($_REQUEST['line2']));
	$text3=str_replace($replace_word,"",trim($_REQUEST['line3']));
	$text4=str_replace($replace_word,"",trim($_REQUEST['line4']));
	$text5=str_replace($replace_word,"",trim($_REQUEST['line5']));
	$text6=str_replace($replace_word,"",trim($_REQUEST['line6']));
	$text7=str_replace($replace_word,"",trim($_REQUEST['line7']));
	$text8=str_replace($replace_word,"",trim($_REQUEST['line8']));
	// echo $im;
	//echo '<pre>'; print_r($_REQUEST);die;
        $ImageWidth=$width_1;
        $ImageHeight=$height_1;
	 
        $LINE_MARGIN=array($line_height1,$line_height2,$line_height3,$line_height4,$line_height5,$line_height6,$line_height7,$line_height8);
        $BLOCK_MARGIN=array(0=>$dynamic_margin_bottom1,1=>$dynamic_margin_bottom2,2=>$dynamic_margin_bottom3,3=>$dynamic_margin_bottom4,4=>$dynamic_margin_bottom5,5=>$dynamic_margin_bottom6,6=>$dynamic_margin_bottom7);//array(0=>50,1=>50,2=>50,3=>50,4=>50);//array(0=>50,1=>100,2=>300,3=>50,4=>100);
	 
	$previousField=0;
	
        $CurrentYposition=0;
        if(intval($status1)==1 && $text1!=""){
	  if($x2_1!=0){
	   $width_temp=abs($x2_1-$x1);
	  }
	  else{
	   $width_temp=abs($ImageWidth-$x1);
	  }
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size1,$font1, $text1,$width_temp,$x1,$x2_1,$text_center1);
	  
	  if($text_center1==0){
	    writeStringOnImage($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0);
	  }elseif($text_center1==1){
	    writeStringOnImageCentre($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0);
	  }elseif($text_center1==2){
	   writeStringOnImageRight($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0);
	  }else{
	   writeStringOnImage($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0);
	  }
	  $previousField=0;
	}else if(intval($status1)==1){
	  if($y1>0){
	   $CurrentYposition=($y1+$BLOCK_MARGIN[0]);
	  }
	  //echo $CurrentYposition;die;
	}
        if(intval($status2)==1 && $text2!=""){
	  if($x2_2!=0){
	   $width_temp=abs($x2_2-$x2);
	  }
	  else{
	   $width_temp=$ImageWidth-$x2;
	  }
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size2,$font2, $text2,$width_temp,$x2,$x2_2,$text_center2);
	  if($text_center2==0){
	    writeStringOnImage($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1);
	  }elseif($text_center2==1){
	    writeStringOnImageCentre($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1);
	  }elseif($text_center2==2){
	   writeStringOnImageRight($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1);
	  }else{
	   writeStringOnImage($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1);
	  }
	  $previousField=1;
	}else if(intval($status2)==1){
	  if($y2>0){
	   $CurrentYposition=($y2+$BLOCK_MARGIN[1]);
	  }
	}
	
	if(intval($status3)==1 && $text3!=""){
	  if($x2_3!=0){
	   $width_temp=abs($x2_3-$x3);
	  }
	  else{
	   $width_temp=$ImageWidth-$x3;
	  }
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size3,$font3, $text3,$width_temp,$x3,$x2_3,$text_center3);
	  if($text_center3==0){
	    writeStringOnImage($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2);
	  }elseif($text_center3==1){
	    writeStringOnImageCentre($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2);
	  }elseif($text_center3==2){
	   writeStringOnImageRight($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2);
	  }else{
	   writeStringOnImage($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2);
	  }
	  $previousField=2;
	}else if(intval($status3)==1){
	  if($y3>0){
	   $CurrentYposition=($y3+$BLOCK_MARGIN[2]);
	  }
	  
	}
	
	if(intval($status4)==1 && $text4!=""){
	  if($x2_4!=0){
	   $width_temp=abs($x2_4-$x4);
	  }
	  else{
	   $width_temp=$ImageWidth-$x4;
	  }
 
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size4,$font4, $text4,$width_temp,$x4,$x2_4,$text_center4);
	  if($text_center4==0){
	    writeStringOnImage($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3);
	  }elseif($text_center4==1){
	    writeStringOnImageCentre($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3);
	  }elseif($text_center4==2){
	    writeStringOnImageRight($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3);
	  }else{
	    writeStringOnImage($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3);
	  }
	  $previousField=3;
	}else if(intval($status4)==1){
	  if($y4>0){
	   $CurrentYposition=($y4+$BLOCK_MARGIN[3]);
	  }
	}
	
	
	
	if(intval($status5)==1 && $text5!=""){
	  if($x2_5!=0){
	   $width_temp=abs($x2_5-$x5);
	  }
	  else{
	   $width_temp=$ImageWidth-$x5;
	  }
	   
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size5,$font5, $text5,$width_temp,$x5,$x2_5,$text_center5);
	  if($text_center5==0){
	    writeStringOnImage($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4);
	  }elseif($text_center5==1){
	    writeStringOnImageCentre($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4);
	  }elseif($text_center5==2){
	    writeStringOnImageRight($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4);
	  }else{
	    writeStringOnImage($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4);
	  }
	  
	  $previousField=4;
	}else if(intval($status5)==1){
	  if($y5>0){
	   $CurrentYposition=($y5+$BLOCK_MARGIN[4]);
	  }
	}
	
	if(intval($status6)==1 && $text6!=""){
	 if($x2_6!=0){
	   $width_temp=abs($x2_6-$x6);
	  }
	  else{
	   $width_temp=$ImageWidth-$x6;
	  }
	    
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size6,$font6, $text6,$width_temp,$x6,$x2_6,$text_center6);
	  if($text_center6==0){
	    writeStringOnImage($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5);
	  }elseif($text_center6==1){
	    writeStringOnImageCentre($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5);
	  }elseif($text_center6==2){
	    writeStringOnImageRight($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5);
	  }else{
	    writeStringOnImage($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5);	   
	  }
	  
	}
	
	if(intval($status7)==1 && $text7!=""){
	 if($x2_7!=0){
	   $width_temp=abs($x2_7-$x7);
	  }
	  else{
	   $width_temp=$ImageWidth-$x7;
	  }
	    
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size7,$font7, $text7,$width_temp,$x7,$x2_7,$text_center7);
	  if($text_center7==0){
	    writeStringOnImage($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6);
	  }elseif($text_center7==1){
	    writeStringOnImageCentre($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6);
	  }elseif($text_center7==2){
	    writeStringOnImageRight($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6);
	  }else{
	    writeStringOnImage($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6);	   
	  }
	  
	}
	
	if(intval($status8)==1 && $text8!=""){
	 if($x2_8!=0){
	   $width_temp=abs($x2_8-$x8);
	  }
	  else{
	   $width_temp=$ImageWidth-$x8;
	  }
	    
	  $inputStringArray=splitStringByWidthAndSpecialChar($font_size8,$font8, $text8,$width_temp,$x8,$x2_8,$text_center8);
	  if($text_center8==0){
	    writeStringOnImage($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7);
	  }elseif($text_center8==1){
	    writeStringOnImageCentre($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7);
	  }elseif($text_center8==2){
	    writeStringOnImageRight($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7);
	  }else{
	    writeStringOnImage($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7);	   
	  }
	  
	}
	
     imagecopyresampled($image_p, $im, 0, 0, 0, 0, $width, $height, $width_1, $height_1);
     imagepng($image_p,'/var/www/html/narrowcasting/tmp/mededeling2.png');
     //exit;
     if($edit_image_name!=""){
      $pathfor_image=$edit_image_name;
     }else{
      $pathfor_image=getMeddilingenImageName().".png";
     }
     $dest = '/var/www/html/narrowcasting/tmp/mededeling2.png';
    
     $source_gd_image= imagecreatefrompng($newimage);
     $source_width=imagesx( $source_gd_image );
     $source_height=imagesy( $source_gd_image );
     // start of work for writtinge image on template image at given position with specfied width and height
   
     if($write_image_status==1 && $write_image_path!=''){
     
	
	//$write_image_path="/images/image2.jpg";//$overlay_image['write_image_path'];
	$write_image_extension=$base = substr($write_image_path, strrpos($write_image_path,'.' )+1,strlen($write_image_path)); //explode(".",$write_image_path);
	
	//exit;
	 if($write_image_extension=="jpeg" or $write_image_extension=="jpg") {
	     $temp_write_image_thumb_path='/var/www/html/narrowcasting/tmp/mededeling_write_image.jpg';
	     $img_a = @imagecreatefromjpeg($temp_write_image_thumb_path);
	   } else if($write_image_extension=="gif") {
	      $temp_write_image_thumb_path='/var/www/html/narrowcasting/tmp/mededeling_write_image.gif';
	      $img_a = @imagecreatefromgif($temp_write_image_thumb_path);
	   } else if($write_image_extension=="png") {
	      $temp_write_image_thumb_path='/var/www/html/narrowcasting/tmp/mededeling_write_image.png';
	      $img_a = @imagecreatefrompng($temp_write_image_thumb_path);
	   }
	   $this_width=imagesx($img_a);
	   $this_height=imagesy($img_a);
	   
        //print_r($write_image_path);echo $temp_write_image_thumb_path;die;
	## setting desired  width and height of specified in db
	$temp_write_image_thumb_width=$write_image_width;
	$temp_write_image_thumb_height=$write_image_height;
	
	require_once("resize-class.php");
	$resizeObj = new resize('/var/www/html/narrowcasting'.$write_image_path);
	//Resize image (options: exact, portrait, landscape, auto, crop)
	$resizeObj -> resizeImage($temp_write_image_thumb_width, $temp_write_image_thumb_height, $write_image_resize_option);
	
	 // *** 3) Save image
	$resizeObj -> saveImage($temp_write_image_thumb_path, 100);
	//echo $temp_write_image_thumb_path;
	//exit;
	## getting image refrence of newly thub of write_image
	//echo $temp_write_image_thumb_path;
	
	$write_image_thumb_extension =$base = substr($temp_write_image_thumb_path, strrpos($temp_write_image_thumb_path,'.' )+1,strlen($temp_write_image_thumb_path)); //explode(".",$write_image_path);
      
       if($write_image_thumb_extension=="jpeg" or $write_image_thumb_extension=="jpg") {
	    $write_image_thumb_overlay= imagecreatefromjpeg('/var/www/html/narrowcasting/tmp/mededeling_write_image.jpg');
	} else if($write_image_thumb_extension=="gif") {
	    $write_image_thumb_overlay= imagecreatefromgif('/var/www/html/narrowcasting/tmp/mededeling_write_image.gif');
	} else if($write_image_thumb_extension=="png") {
	    $write_image_thumb_overlay= imagecreatefrompng('/var/www/html/narrowcasting/tmp/mededeling_write_image.png');
	     
	} 
	## final width and height of write image after creating thumb with aminting aspect ratio
	$temp_write_image_thumb_actual_width=imagesx($write_image_thumb_overlay);
	$temp_write_image_thumb_actual_height=imagesy($write_image_thumb_overlay);
	## writing on template image...........
	
	$image_position=calculate_image_write_position($write_image_st_pos_x,$write_image_st_pos_y,$img_center_horizontal,$img_center_vertical);
	$write_image_st_pos_x=$image_position[0];
	$write_image_st_pos_y=$image_position[1];
	//print_r($a);
	
     
	//imagepng($source_gd_image,'/var/www/html/narrowcasting/mededelingen/abc_source.png');
	//imagepng($write_image_thumb_overlay,'/var/www/html/narrowcasting/mededelingen/abc_write_image_thumb_overlay.png');
	//echo $temp_write_image_thumb_actual_width;
	//echo $temp_write_image_thumb_actual_height;
	//exit;
    $overlay_gd_image = imagecreatefrompng($dest);
     $image_p_width = imagesx( $transparent_image );
     $image_p_height = imagesy( $transparent_image );
     
     $overlay_width = imagesx( $overlay_gd_image );
     $overlay_height = imagesy( $overlay_gd_image );
     
    if($image_place==0){
     
	imagecopyresampled($source_gd_image, $write_image_thumb_overlay,$write_image_st_pos_x ,$write_image_st_pos_y, 0, 0, $temp_write_image_thumb_actual_width, $temp_write_image_thumb_actual_height, $temp_write_image_thumb_actual_width ,$temp_write_image_thumb_actual_height);
	imagecopyresampled($source_gd_image, $overlay_gd_image,  0, 0, 0, 0,$overlay_width, $overlay_height, $source_width, $source_height);
	
	imagepng($source_gd_image,'/var/www/html/narrowcasting/mededelingen/'.$pathfor_image);
	
    }else{
     imageSaveAlpha($overlay_gd_image, true);
    ImageAlphaBlending($overlay_gd_image, false);
    $transparentColor = imagecolorallocatealpha($overlay_gd_image, 200, 200, 200, 127);
    imagefill($overlay_gd_image, 0, 0, $transparentColor);
    
         //imagepng($source_gd_image,'/var/www/html/narrowcasting/mededelingen/source_gd_image.png');
	 //imagepng($overlay_gd_image,'/var/www/html/narrowcasting/mededelingen/overlay_gd_image.png');
	 
    imagecopyresampled($source_gd_image,$overlay_gd_image, 0, 0, 0, 0,$source_width,$source_height,$overlay_width, $overlay_height);
   //imagepng($source_gd_image,'/var/www/html/narrowcasting/mededelingen/source_gd_image.png');
   
    imageSaveAlpha($source_gd_image, true);
    ImageAlphaBlending($source_gd_image, false);
    $transparentColor = imagecolorallocatealpha($source_gd_image, 200, 200, 200, 127);
    imagefill($source_gd_image, 0, 0, $transparentColor);
     
    
    imagecopyresampled($transparent_image, $write_image_thumb_overlay,  $write_image_st_pos_x, $write_image_st_pos_y, 0, 0, $temp_write_image_thumb_actual_width, $temp_write_image_thumb_actual_height, $temp_write_image_thumb_actual_width ,$temp_write_image_thumb_actual_height);
    imageSaveAlpha($transparent_image, true);
    ImageAlphaBlending($transparent_image, false);
    $transparentColor = imagecolorallocatealpha($transparent_image, 200, 200, 200, 127);
    imagefill($transparent_image, 0, 0, $transparentColor);
    imagepng($transparent_image,'/var/www/html/narrowcasting/tmp/write_image_thumb_overlay.png');
    if($write_image_status_2==0){
     
      imageSaveAlpha($source_gd_image, true);
    ImageAlphaBlending($source_gd_image, false);
    $transparentColor = imagecolorallocatealpha($source_gd_image, 200, 200, 200, 127);
    imagefill($source_gd_image, 0, 0, $transparentColor);
    
      $source_gd_image_final = imagecreatefrompng('/var/www/html/narrowcasting/tmp/write_image_thumb_overlay.png');
      $source_gd_image_final_width = imagesx($source_gd_image_final);
      $source_gd_image_final_height = imagesy($source_gd_image_final);
     imagecopyresampled($source_gd_image_final,$source_gd_image, 0, 0, 0, 0,$source_gd_image_final_width,  $source_gd_image_final_height, $source_width, $source_height);
     
     
     imagepng($source_gd_image_final,'/var/www/html/narrowcasting/mededelingen/'.$pathfor_image);
    }else{;
     imagepng($write_image_thumb_overlay,'/var/www/html/narrowcasting/mededelingen/'.$pathfor_image);
    }
       
    }
	
     }
     
     if($write_image_status==0 && $write_image_status_2==0){
    
     $overlay_gd_image = imagecreatefrompng($dest);
     $overlay_width = imagesx( $overlay_gd_image );
     $overlay_height = imagesy( $overlay_gd_image );
      imagecopyresampled($source_gd_image, $overlay_gd_image,  0, 0, 0, 0,$overlay_width, $overlay_height, $source_width, $source_height);
	
	imagepng($source_gd_image,'/var/www/html/narrowcasting/mededelingen/'.$pathfor_image);
     }
     
     //// Begin Code for second Image////////
     
     $newimage_2_2 = '/var/www/html/narrowcasting/tmp/write_image_thumb_overlay.png';
    // die('here');
     $source_gd_image_2= imagecreatefrompng($newimage_2_2);
     
     $source_width_2=imagesx( $source_gd_image_2 );
     $source_height_2=imagesy( $source_gd_image_2 );
     
     $newimage_2_2_2='/var/www/html/narrowcasting/mededelingen/'.$pathfor_image;
     
     $source_gd_image_2_2= imagecreatefrompng($newimage_2_2_2);
     
     $source_width_2_2=imagesx( $source_gd_image_2_2 );
     $source_height_2_2=imagesy( $source_gd_image_2_2 );
     
     
     if($write_image_status_2==1 && $write_image_path_2!=""){
     
	$write_image_extension_2=$base_2 = substr($write_image_path_2, strrpos($write_image_path_2,'.' )+1,strlen($write_image_path_2));
	
	if($write_image_extension_2=="jpeg" or $write_image_extension_2=="jpg") {
	     $temp_write_image_thumb_path_2='/var/www/html/narrowcasting/tmp/mededeling_write_image.jpg';
	     $img_a = @imagecreatefromjpeg($temp_write_image_thumb_path);
	   } else if($write_image_extension_2=="gif") {
	      $temp_write_image_thumb_path_2='/var/www/html/narrowcasting/tmp/mededeling_write_image.gif';
	      $img_a_2 = @imagecreatefromgif($temp_write_image_thumb_path_2);
	   } else if($write_image_extension_2=="png") {
	    $temp_write_image_thumb_path_2='/var/www/html/narrowcasting/tmp/mededeling_write_image.png';
          
	    $img_a_2 = @imagecreatefrompng();	   
	   }
	   $this_width_2=imagesx($img_a_2);
	   $this_height_2=imagesy($img_a_2);
	   
        //print_r($write_image_path);echo $temp_write_image_thumb_path;die;
	## setting desired  width and height of specified in db
	$temp_write_image_thumb_width_2=$write_image_width_2;
	$temp_write_image_thumb_height_2=$write_image_height_2;
	//echo '/var/www/html/narrowcasting'.$write_image_path_2;
	//exit;
	require_once("resize-class.php");
	$resizeObj_2 = new resize('/var/www/html/narrowcasting'.$write_image_path_2);
       
	
	$resizeObj_2 -> resizeImage($temp_write_image_thumb_width_2, $temp_write_image_thumb_height_2, $write_image_resize_option);
	
	 // *** 3) Save image
	$resizeObj_2 -> saveImage($temp_write_image_thumb_path_2, 100);
	//exit;
	## getting image refrence of newly thub of write_image
	
	
	$write_image_thumb_extension_2=$base_2 = substr($temp_write_image_thumb_path_2, strrpos($temp_write_image_thumb_path_2,'.' )+1,strlen($temp_write_image_thumb_path_2)); //explode(".",$write_image_path);
       
      
        
       
     
	if($image_place==0){
	 
	  if($write_image_thumb_extension_2=="jpeg" or $write_image_thumb_extension_2=="jpg") {
	    $write_image_thumb_overlay_2_2= imagecreatefromjpeg('/var/www/html/narrowcasting/tmp/mededeling_write_image.jpg');
	} else if($write_image_thumb_extension_2_2=="gif") {
	    $write_image_thumb_overlay_2_2= imagecreatefromgif('/var/www/html/narrowcasting/tmp/mededeling_write_image.gif');
	} else if($write_image_thumb_extension_2=="png") {
	    $write_image_thumb_overlay_2_2= imagecreatefrompng('/var/www/html/narrowcasting/tmp/mededeling_write_image.png');
	     
	}
	//echo $write_image_thumb_overlay_2;
	//imagepng($write_image_thumb_overlay_2,'/var/www/html/narrowcasting/mededelingen/abc_a.png');
	//exit;
	## final width and height of write image after creating thumb with aminting aspect ratio
	$temp_write_image_thumb_actual_width_2=imagesx($write_image_thumb_overlay_2_2);
	$temp_write_image_thumb_actual_height_2=imagesy($write_image_thumb_overlay_2_2);
	## writing on template image...........
	
	$image_position_2=calculate_image_write_position($write_image_st_pos_x_2,$write_image_st_pos_y_2,$img_center_horizontal_2,$img_center_vertical_2);
	
       
        $write_image_st_pos_x_2=$image_position_2[0];
	$write_image_st_pos_y_2=$image_position_2[1];
	
	imagecopyresampled($source_gd_image_2_2,$write_image_thumb_overlay_2_2, $write_image_st_pos_x_2, $write_image_st_pos_y_2,0,0, $temp_write_image_thumb_actual_width_2, $temp_write_image_thumb_actual_height_2, $temp_write_image_thumb_actual_width_2 ,$temp_write_image_thumb_actual_height_2);
	
	imagepng($source_gd_image_2_2,'/var/www/html/narrowcasting/mededelingen/'.$pathfor_image);
	}else{
	 
	  if($write_image_thumb_extension_2=="jpeg" or $write_image_thumb_extension_2=="jpg") {
	    $write_image_thumb_overlay_2= imagecreatefromjpeg('/var/www/html/narrowcasting/tmp/mededeling_write_image.jpg');
	} else if($write_image_thumb_extension_2=="gif") {
	    $write_image_thumb_overlay_2= imagecreatefromgif('/var/www/html/narrowcasting/tmp/mededeling_write_image.gif');
	} else if($write_image_thumb_extension_2=="png") {
	    $write_image_thumb_overlay_2= imagecreatefrompng('/var/www/html/narrowcasting/tmp/mededeling_write_image.png');
	     
	}
	//echo $write_image_thumb_overlay_2;
	//imagepng($write_image_thumb_overlay_2,'/var/www/html/narrowcasting/mededelingen/abc_a.png');
	//exit;
	## final width and height of write image after creating thumb with aminting aspect ratio
	$temp_write_image_thumb_actual_width_2=imagesx($write_image_thumb_overlay_2);
	$temp_write_image_thumb_actual_height_2=imagesy($write_image_thumb_overlay_2);
	## writing on template image...........
	
	$image_position_2=calculate_image_write_position($write_image_st_pos_x_2,$write_image_st_pos_y_2,$img_center_horizontal_2,$img_center_vertical_2);
	
       
        $write_image_st_pos_x_2=$image_position_2[0];
	$write_image_st_pos_y_2=$image_position_2[1];
         
	 /*imagepng($write_image_thumb_overlay_2,'/var/www/html/narrowcasting/mededelingen/write_image_thumb_overlay_2_2.png');
	 
	 imagepng($source_gd_image_2,'/var/www/html/narrowcasting/mededelingen/source_gd_image_2_2.png');
	 
	 imagepng($source_gd_image,'/var/www/html/narrowcasting/mededelingen/source_gd_image.png');*/
	 
	 
	 
         imagecopyresampled($source_gd_image_2,$write_image_thumb_overlay_2,$write_image_st_pos_x_2,$write_image_st_pos_y_2,0 , 0, $temp_write_image_thumb_actual_width_2, $temp_write_image_thumb_actual_height_2, $temp_write_image_thumb_actual_width_2,$temp_write_image_thumb_actual_height_2);
	
	 imagepng($source_gd_image_2,'/var/www/html/narrowcasting/tmp/write_image_thumb_overlay_2.png');
 
	 imagepng($write_image_thumb_overlay_2,'/var/www/html/narrowcasting/mededelingen/'.$pathfor_image);
	 
	 $source_gd_image_final = imagecreatefrompng('/var/www/html/narrowcasting/tmp/write_image_thumb_overlay_2.png');
	 $source_gd_image_final_width = imagesx($source_gd_image_final);
	 $source_gd_image_final_height = imagesy($source_gd_image_final);
	
	 
	//// imagepng($overlay_gd_image,'/var/www/html/narrowcasting/mededelingen/overlay_gd_image.png');
	 //imagecopyresampled($source_gd_image_final,$overlay_gd_image, 0, 0, 0, 0,$source_gd_image_final_width,  $source_gd_image_final_height, $overlay_width, $overlay_height);
	 imagecopyresampled($source_gd_image_final,$source_gd_image, 0, 0, 0, 0,$source_gd_image_final_width,  $source_gd_image_final_height, $source_width, $source_height);
	 imagepng($source_gd_image_final,'/var/www/html/narrowcasting/mededelingen/'.$pathfor_image);
	 unlink('/var/www/html/narrowcasting/tmp/write_image_thumb_overlay.png');
	unlink('/var/www/html/narrowcasting/tmp/write_image_thumb_overlay_2.png');
	}
	
       
     }
     //// End Code for second Image/////////
     
     
	 
	 if ($landscape_portrait==0) {
     createsingleThumb("./mededelingen/$pathfor_image","./mededelingen/thumb/$pathfor_image",135,135,'crop_right');
 		}
		else {
			createsingleThumb("./mededelingen/$pathfor_image","./mededelingen/thumb/$pathfor_image",135,135,'crop_top');
		}
    /* code for edit */
   $tempsid       = session_id();
   $template_id   	  = $_REQUEST['template_id'];
  
   $templine1     = addslashes($_REQUEST['line1']);
   $templine2     = addslashes($_REQUEST['line2']);
   $templine3     = addslashes($_REQUEST['line3']);
   $templine4     = addslashes($_REQUEST['line4']);
   $templine5     = addslashes($_REQUEST['line5']);
   $templine6     = addslashes($_REQUEST['line6']);
   $templine7     = addslashes($_REQUEST['line7']);
   $templine8     = addslashes($_REQUEST['line8']);
   
   $tempcolor1    = $_REQUEST['color1'];
   $tempcolor2    = $_REQUEST['color2'];
   $tempcolor3    = $_REQUEST['color3'];
   $tempcolor4    = $_REQUEST['color4'];
   $tempcolor5    = $_REQUEST['color5'];
   $tempcolor6    = $_REQUEST['color6'];
   $tempcolor7    = $_REQUEST['color7'];
   $tempcolor8    = $_REQUEST['color8'];
   
   $tempfont1     = $_REQUEST['font1'];
   $tempfont2     = $_REQUEST['font2'];
   $tempfont3     = $_REQUEST['font3'];
   $tempfont4     = $_REQUEST['font4'];
   $tempfont5     = $_REQUEST['font5'];
   $tempfont6     = $_REQUEST['font6'];
   $tempfont7     = $_REQUEST['font7'];
   $tempfont8     = $_REQUEST['font8'];
   
   /// new update codew for temp overl;ay
   
 /* $tempquery  = "select tmpmed.tempid,med.template_name from temp_mededelingen as tmpmed join mededelingen med  on med.id=tmpmed.template_id where tmpmed.sid='$tempsid' limit 1";
  $tempeditvalues =$db->query($tempquery);
  $tempresult3 = mysql_fetch_array($tempeditvalues);
  if($tempresult3){
    $templateName     = $tempresult3['template_name'];
    $tempRandomNumber    = $tempresult3['tempid'];
    $template_namedelArray = explode('.',$templateName);
    $imageName=$template_namedelArray[0].'_'.$tempRandomNumber.'.'.$template_namedelArray[1];
    $main_image_path='/var/www/html/narrowcasting/mededelingen/'.$imageName;
    $thumb_image_path='/var/www/html/narrowcasting/mededelingen/thumb/'.$imageName;
    unlink($main_image_path);
    unlink($thumb_image_path);
  }
 */

   ## for creation  of mededelingen image
   if($temp_mededelingen_id==0){
    $Querytemp =   "insert into temp_mededelingen
                                        (
					 template_id,line1,color1,font1,line2,color2,font2,line3,color3,font3,line4,color4,font4,line5,
                                         color5,font5,line6,color6,font6,line7,color7,font7,line8,color8,font8,temp_image_name,temp_image_name_2,write_image_path,write_image_path_2,text_center1,text_center2,text_center3,
					 text_center4,text_center5,text_center6,text_center7,text_center8, sip,sip_2, crop_type, crop_type_2
					)
					values('$template_id','$templine1','$tempcolor1','$tempfont1','$templine2','$tempcolor2','$tempfont2'
					,'$templine3','$tempcolor3','$tempfont3','$templine4','$tempcolor4','$tempfont4','$templine5','$tempcolor5',
					'$tempfont5','$templine6','$tempcolor6','$tempfont6','$templine7','$tempcolor7','$tempfont7','$templine8','$tempcolor8','$tempfont8','$pathfor_image','$pathfor_image','$write_image_path','$write_image_path_2',
					$text_center1,$text_center2,$text_center3,$text_center4,$text_center5,$text_center6,$text_center7,$text_center8,'$_POST[sip]','$_POST[sip_2]','$_POST[crop_type]','$_POST[crop_type_2]')";
				  
		  
    $conn=$db->query($Querytemp);
    $temp_mededelingen_id=mysql_insert_id($db->Link_ID_PREV);
   }else if($temp_mededelingen_id>0 && is_int($temp_mededelingen_id)){ ## for editing of mededelingen image
    
    ## check previously created  mededelingen record exist in table
    $tempdeletequery  = "select tmpmed.id from temp_mededelingen as tmpmed  where tmpmed.id=$temp_mededelingen_id limit 1";
    $tempeditvalues =$db->query($tempdeletequery);
    $tempresult_delete = mysql_fetch_array($tempeditvalues);
    if($tempresult_delete){
       $updateQueryTemp =  "update temp_mededelingen set
                           template_id='$template_id',line1='$templine1',color1='$tempcolor1',font1='$tempfont1',line2='$templine2',
		           color2='$tempcolor2',font2='$tempfont2',line3='$templine3',color3='$tempcolor3',font3='$tempfont3',line4='$templine4',
		           color4='$tempcolor4',font4='$tempfont4',line5='$templine5',color5='$tempcolor5',font5='$tempfont5',line6='$templine6',
		           color6='$tempcolor6',font6='$tempfont6',line7='$templine7',color7='$tempcolor7',font7='$tempfont7',line8='$templine8',color8='$tempcolor8',font8='$tempfont8',text_center1=$text_center1,text_center2=$text_center2,text_center3=$text_center3
			   ,text_center4=$text_center4,text_center5=$text_center5,text_center6=$text_center6,text_center7=$text_center7,text_center8=$text_center8,
			   temp_image_name='$pathfor_image',temp_image_name_2='$pathfor_image',write_image_path='$write_image_path',write_image_path_2='$write_image_path_2', sip='$_POST[sip]',sip_2='$_POST[sip_2]',crop_type='$_POST[crop_type]', crop_type_2='$_POST[crop_type_2]'  where id=$temp_mededelingen_id";
       $conn=$db->query($updateQueryTemp);
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
     $save_link="<div class='video_tab_new'><a href='hard_redirect.php?id=$carrousel_id&status=$_GET[status]&is_block=$_GET[is_block]'>Opslaan</a></div>";
   }else{
     $save_link="<div class='video_tab_new'><a href='hard_redirect.php?page=mededelingen&is_block=$_GET[is_block]'>Opslaan</a></div>";
   }
     
    if(trim($_REQUEST['type'])==""){
     $verwijderen="<div class='video_tab_new'><a href='delete_mededeoverlay.php?path=$pathfor_image&temp_mededelingen_id=$temp_mededelingen_id&is_block=$_GET[is_block]'>Verwijderen</a></div>"; 
   }else{
     $verwijderen="";
   } 
     
     
    $showvideo="<div class='video_section' id='width_932'>
        	 <div class='video_img'><img src='./mededelingen/$pathfor_image?time=".time()."' width='264' height='auto'></div>
            	  <div class='video_tabs'>
		  <div class='video_tab_new'><a href='./mededelingen/$pathfor_image?time=".time()."' rel='single'  class='pirobox' style='color:white;'>Preview</a></div>
		  <div class='video_tab_new'><a href='mededelingen_overlay.php?carrousel_id=$carrousel_id&come_from=$come_from&request_for=template_edit&template_id=$template_id&temp_mededelingen_id=$temp_mededelingen_id&edit_image_name=$pathfor_image&type=$_REQUEST[type]&is_block=$_GET[is_block]&status=$_GET[status]&crop_type=$_REQUEST[crop_type]&sip=$_REQUEST[sip]&crop_type_2=$_REQUEST[crop_type_2]&sip_2=$_REQUEST[sip_2]'>Bewerken</a></div>
		  $verwijderen
		  $save_link
                </div>
             </div>";
 }
 
 
 
 function getMeddilingenImageName(){
  global $text1,$text2,$text3,$text4,$text5,$text6,$text7,$text8,$pathfor_image;
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
	}else if($text7!=""){
  $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text7);
}else if($text8!=""){
  $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$text8);
  }else{
    $newMedImageName=preg_replace('/[^A-Za-z0-9\_]/',"_",$pathfor_image);
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
     
      $inputStringArray = explode('$', $inputString);
     // print_r($inputStringArray);
    //  exit;
      $finalInputString=array('height'=>0,'multilineString'=>array());
      for($l=0;$l<=(count($inputStringArray)-1);$l++)
      {
        if($center==1 || $center == 2){
	  $result=breakStringByCentreWidth($fontSize,$fontFace, $inputStringArray[$l], $xEnd,$xStart);
	  $finalInputString['multilineString']=array_merge($finalInputString['multilineString'],$result['multilineString']);
	  $finalInputString['height']=$result['height'];
	}else{
	
	  $result=breakStringByWidth($fontSize,$fontFace, $inputStringArray[$l], $width,$xStart);
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
   
	  global $LINE_MARGIN,$CurrentYposition,$BLOCK_MARGIN,$previousField;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition+$BLOCK_MARGIN[$previousField];
	  }else if($CurrentYposition>$yStart && intval($yStart)!=0){
	    //$yStart=$CurrentYposition;//+$BLOCK_MARGIN[$previousField];
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
	 
	  
       function breakStringByWidth($fontSize,$fontFace, $inputString, $width,$xStart){
	  global $ImageWidth;
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
	  //echo $currentWidth;
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
          // echo('CurrentYposition=='.$CurrentYposition.' yStart='.$yStart.'<br>');
	   //print_r($inputStringArray); echo '<br>'.$currentField.'   '.$y3.'   '.$x3.' ysatrt'.$yStart.' CurrentYposition'.$CurrentYposition.'<br>';
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	  
	    $wordWidth=$wordDetails['width'];
	    // print_r($wordDetails);echo $xStart.'  '.$MaxWriteWidth.'  '.$wordWidth.'<br>';
	    if($wordWidth>=$MaxWriteWidth){
	     $dimention = getStringBoxDimention($fontSize,$font, $wordDetails['text']);
	    $widthDiff=($MaxWriteWidth-$dimention['width']);
	     $newXStart=$xStart+floor($widthDiff/2);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
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
	  $CurrentYposition=$yStart;
	  
	 }
      
      function writeStringOnImageRight($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField){
   
    
	  global $LINE_MARGIN,$CurrentYposition,$BLOCK_MARGIN,$previousField,$ImageWidth;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition+$BLOCK_MARGIN[$previousField];
	  }
	  if($xEnd>$ImageWidth){
	    $xEnd=$ImageWidth;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
	  
          // echo('CurrentYposition=='.$CurrentYposition.' yStart='.$yStart.'<br>');
	   //print_r($inputStringArray); echo '<br>'.$currentField.'   '.$y3.'   '.$x3.' ysatrt'.$yStart.' CurrentYposition'.$CurrentYposition.'<br>';
	  
	  // echo "<pre>"; print_r($inputStringArray); echo "</pre>";
	  // exit;
	 
	  foreach ($inputStringArray['multilineString'] as $key=>$wordDetails ){
	  
	    $wordWidth=$wordDetails['width'];
	     //print_r($wordDetails);echo $xStart.' '.$xEnd.'  '.$ImageWidth.' '.$wordWidth.'  '.$MaxWriteWidth.'<br>';
	    
	    if($wordWidth >= $MaxWriteWidth){
	    $dimention = getStringBoxDimention($fontSize,$font, $wordDetails['text']);
	    $widthDiff=($MaxWriteWidth-$dimention['width']);
	    $newXStart=abs($xStart+$widthDiff);
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	  }else{
	      $widthDiff=($MaxWriteWidth-$wordWidth);
	      $newXStart=abs($xStart+$widthDiff);
	      imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }
	    $yStart+=($LINE_MARGIN[$currentField]+$inputStringArray['height']);
	    $i++;
	  }
	  // removing added space after last line
	  if(count($inputStringArray['multilineString'])>0 && is_array($inputStringArray['multilineString'])){
	   $yStart-=($LINE_MARGIN[$currentField]);
	  }
	  $CurrentYposition=$yStart;
	  
	 }
      
      
       function breakStringByCentreWidth($fontSize,$fontFace, $inputString, $xEnd,$xStart){
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
	 // echo "<pre>"; print_r($inputStringArr); echo "</pre>";
	  
	  foreach ($inputStringArr as $key => $word){
              $previousCurrentWidth=$currentWidth;
	      $teststring = $ret.' '.$word;
	      $dimention = getStringBoxDimention($fontSize,$fontFace, $teststring);
	      $currentWidth = $dimention['width'];
	      $currentHeight = $dimention['height'];
	      //echo $currentWidth."  ".$width;
	     // exit;
	      if ($currentWidth > $width ){
	       
	         if($key==0){
		 
		    $multilineString[$counter]=array('text'=>$word,'width'=>$currentWidth);
		    $ret="";
		 }else if($ret==""){
		  
		    $multilineString[$counter]=array('text'=>$word,'width'=>$previousCurrentWidth);
		    $ret="";
		 }else{
		  $newWidth=$previousCurrentWidth-$currentWidth;
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
	  
	  return $dimention;
      }	

?>
<?php
// for edit when come back from edit carrousal link
 if(isset($_GET['template_id']) and $_GET['request_for']=='template_edit'){
  $tempsid    = session_id();
  $tempquery  = "select * from temp_mededelingen where id='$temp_mededelingen_id'";
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
   $templine7    = stripslashes($tempresult['line7']);
   $templine8    = stripslashes($tempresult['line8']);
   
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
   
   $text_center1 = intval($tempresult['text_center1']);
   $text_center2 = intval($tempresult['text_center2']);
   $text_center3 = intval($tempresult['text_center3']);
   $text_center4 = intval($tempresult['text_center4']);
   $text_center5 = intval($tempresult['text_center5']);
   $text_center6 = intval($tempresult['text_center6']);
   $text_center7 = intval($tempresult['text_center7']);
   $text_center8 = intval($tempresult['text_center8']);
   
   $tempwrite_image_path    = $tempresult['write_image_path'];
   
   $tempwrite_image_path_2    = $tempresult['write_image_path_2'];
   

   $tempwrite_image_path_array=explode("/",$tempwrite_image_path);
   $_GET[sip]=$tempresult[sip];
   $_GET[crop_type]=$tempresult[crop_type];
   if($tempwrite_image_path_array[0]==""){
    $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[1].'/thumb/'.$tempwrite_image_path_array[2];
   }
   else{
    $tempwrite_image_path_thumbs='/'.$tempwrite_image_path_array[0].'/thumb/'.$tempwrite_image_path_array[1];
   }
   
   
   $tempwrite_image_path_array_2=explode("/",$tempwrite_image_path_2);
   $_GET[sip_2]=$tempresult[sip_2];
   $_GET[crop_type_2]=$tempresult[crop_type_2];
   if($tempwrite_image_path_array_2[0]==""){
    $tempwrite_image_path_thumbs_2='/'.$tempwrite_image_path_array_2[1].'/thumb/'.$tempwrite_image_path_array_2[2];
   }
   else{
    $tempwrite_image_path_thumbs_2='/'.$tempwrite_image_path_array_2[0].'/thumb/'.$tempwrite_image_path_array_2[1];
   } 
   
   // getting static data from theater templateb table
   $tempquery1  = "select * from mededelingen where id=' $template_id'";
   $tempeditvalues1 =$db->query($tempquery1);
   $tempresult1 = mysql_fetch_array($tempeditvalues1);
   
   
   $selected_template_label     = $tempresult1['name_t'];
   $label1        = $tempresult1['label1'];
   $label2        = $tempresult1['label2'];
   $label3        = $tempresult1['label3'];
   $label4        = $tempresult1['label4'];
   $label5        = $tempresult1['label5'];
   $label6        = $tempresult1['label6'];
   $label7        = $tempresult1['label7'];
   $label8        = $tempresult1['label8'];
   
   $limit1        = $tempresult1['limit1'];
   $limit2        = $tempresult1['limit2'];
   $limit3        = $tempresult1['limit3'];
   $limit4        = $tempresult1['limit4'];
   $limit5        = $tempresult1['limit5'];
   $limit6        = $tempresult1['limit6'];
   $limit7        = $tempresult1['limit7'];
   $limit8        = $tempresult1['limit8'];
	
   $status1    	  = $tempresult1['status1']; 
   $status2       = $tempresult1['status2'];
   $status3       = $tempresult1['status3'];
   $status4       = $tempresult1['status4'];
   $status5       = $tempresult1['status5'];
   $status6       = $tempresult1['status6'];
   $status7       = $tempresult1['status7'];
   $status8       = $tempresult1['status8'];
   
   $field_type1   = $tempresult1['field_type1']; 
   $field_type2   = $tempresult1['field_type2'];
   $field_type3   = $tempresult1['field_type3'];
   $field_type4   = $tempresult1['field_type4'];
   $field_type5   = $tempresult1['field_type5'];
   $field_type6   = $tempresult1['field_type6'];
   $field_type7   = $tempresult1['field_type7'];
   $field_type8   = $tempresult1['field_type8'];
   $is_dropdown   = $tempresult1['is_dropdown'];
   
   ## setting all variables for writting  image on template
   $write_image_status      = $tempresult1['write_image_status'];
   $write_image_st_pos_x    = $tempresult1['write_image_st_pos_x'];
   $write_image_st_pos_y    = $tempresult1['write_image_st_pos_y'];
   $write_image_width       = $tempresult1['write_image_width'];
   $write_image_height      = $tempresult1['write_image_height'];
   $aspect_ratio = $write_image_width / $write_image_height;
   
   $write_image_status_2      = $tempresult1['write_image_status_2'];
   $write_image_st_pos_x_2    = $tempresult1['write_image_st_pos_x_2'];
   $write_image_st_pos_y_2    = $tempresult1['write_image_st_pos_y_2'];
   $write_image_width_2       = $tempresult1['write_image_width_2'];
   $write_image_height_2      = $tempresult1['write_image_height_2'];
   $write_image_resize_option_2 = $tempresult1['write_image_resize_option_2'];
   $write_image_resize_option = $tempresult1['write_image_resize_option'];
   $aspect_ratio_2 = $write_image_width_2 / $write_image_height_2; 
   
    // $temp_no=rand(1111,9999);
     $pathfor_image=$newpath."_".$temp_no.".png";
     $create_image_path='/var/www/html/narrowcasting/mededelingen/'.$pathfor_image;
     $create_image_thumb_path='/var/www/html/narrowcasting/mededelingen/thumb/'.$pathfor_image;
     //unlink($deleteimage);
  }
 
 }
 // end of code for edit when come back from edit carrousal link
 
 $tempfont8 = (isset($tempfont8)) ? $tempfont8 : '';
 $tempfont7 = (isset($tempfont7)) ? $tempfont7 : '';
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
  
  $tempquery  = "select * from mededelingen where id='$template_id'";
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
   $write_image_status_2      = $tempresult['write_image_status_2'];
   $write_image_st_pos_x    = $tempresult['write_image_st_pos_x'];
   $write_image_st_pos_y    = $tempresult['write_image_st_pos_y'];
   
   $write_image_st_pos_x_2    = $tempresult['write_image_st_pos_x_2'];
   $write_image_st_pos_y_2    = $tempresult['write_image_st_pos_y_2'];
   
   $write_image_width       = $tempresult['write_image_width'];
   $write_image_height      = $tempresult['write_image_height'];   
   
    $write_image_width_2       = $tempresult['write_image_width_2'];
   $write_image_height_2      = $tempresult['write_image_height_2'];   
   
   $aspect_ratio = $write_image_width / $write_image_height;
   
   $aspect_ratio_2 = $write_image_width_2 / $write_image_height_2;
   
     ## setting all variables for writting  text on center of 2 points
   $text_center1 = intval(trim($tempresult['text_center1']));
   $text_center2 = intval(trim($tempresult['text_center2']));
   $text_center3 = intval(trim($tempresult['text_center3']));
   $text_center4 = intval(trim($tempresult['text_center4']));
   $text_center5 = intval(trim($tempresult['text_center5']));
   $text_center6 = intval(trim($tempresult['text_center6']));
   $text_center7 = intval(trim($tempresult['text_center7']));
   $text_center8 = intval(trim($tempresult['text_center8']));
   $is_dropdown = $tempresult['is_dropdown'];
   $dropdown = $tempresult['dropdown'];
  }}


?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Maak hier uw huishoudelijke mededelingen</title>

<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" type="text/css" href="./css_pirobox/style_1/style.css"/>
<!--::: it depends on which style you choose :::-->
<?php include('jsfiles.html')?>
<script type="text/javascript" src="./js/datetimepicker.js"></script>

<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->


<style type="text/css">
	
	#alle_carrousels,#maak_carrousel,#nieuwe_twitterfeed,#uploaden_bestand,#nieuw_bestand,#spreekuur,#tot_ziens{
		  <?php echo $css_color_rule;?>
	}
	
	#mededelingen{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
	
	#alle_carrousels{font-size:14px;line-height:30px;}
	#maak_carrousel{font-size:14px;line-height:30px;}
	
	#uploaden_bestand{font-size:14px;line-height:30px;}
	#nieuw_bestand{font-size:14px;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
	#iets_nieuws{font-size:14px;color: #b5b5b5;line-height:30px;}
	#spreekuur{font-size:14px;line-height:30px;}
	#mededelingen{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
	#tot_ziens{font-size:14px;line-height:30px;}
	.nav{float:right;}
	.main_container{margin-top:100px;}
	.single_row textarea {color: #272727;font-size: 15px;border: 1px solid #b5b5b5;}
	.loading {  background: url("./img/<?php echo $color_scheme;?>/loader.gif") no-repeat scroll center transparent;
	  display: block; height: 223px;opacity: 0.6; opacity: 0.6;  position: absolute;top: 0px; left:0px;width: 94%;z-index: 99;
	  
	  background-color: rgba(69, 71, 71, 0.5);
	  }
	 
	
        @media screen and (-webkit-min-device-pixel-ratio:0) {
  .delete_image_icon{
	  left:130px; 
	 }
	 /* Safari only override */
::i-block-chrome,.delete_image_icon{
	  left:130px; 
	 }
}

</style>
<script type="text/javascript">
function get_data_from_csv(id){
 
 var filetype=$('#filetype').val();
     $.ajax({  
              type: "POST", url: 'givecsvdata.php', data: {id : id, filetype: filetype},
              complete: function(data){
		  var obj = $.parseJSON(data.responseText);
		   var maxlength1 = $('#line1').attr('maxlength')
		   var valuelength1= obj.line1.length;
		   $('#line1').val(obj.line1);
		 
		    if(valuelength1>maxlength1){
	   $('#line1').addClass('timeline_length_error ');
	 }else{
	  $('#line1').removeClass('timeline_length_error');
	  $('#line1').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line1').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		  
		  
		  var maxlength2 = $('#line2').attr('maxlength')
		   var valuelength2= obj.line2.length;
		   $('#line2').val(obj.line2);
		 
		    if(valuelength2>maxlength2){
	   $('#line2').addClass('timeline_length_error ');
	 }else{
	  $('#line2').removeClass('timeline_length_error');
	  $('#line2').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line2').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		  
		    var maxlength3 = $('#line3').attr('maxlength')
		   var valuelength3= obj.line3.length;
		   $('#line3').val(obj.line3);
		 
		    if(valuelength3>maxlength3){
	   $('#line3').addClass('timeline_length_error ');
	 }else{
	  $('#line3').removeClass('timeline_length_error');
	  $('#line3').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line3').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		  
		  
		     var maxlength4 = $('#line4').attr('maxlength')
		   var valuelength4= obj.line4.length;
		   $('#line4').val(obj.line4);
		 
		    if(valuelength4>maxlength4){
	   $('#line4').addClass('timeline_length_error ');
	 }else{
	  $('#line4').removeClass('timeline_length_error');
	  $('#line4').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line4').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
	 
	   var maxlength5 = $('#line5').attr('maxlength')
		   var valuelength5= obj.line5.length;
		   $('#line5').val(obj.line5);
		 
		    if(valuelength5>maxlength5){
	   $('#line5').addClass('timeline_length_error ');
	 }else{
	  $('#line5').removeClass('timeline_length_error');
	  $('#line5').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line5').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
	 
	   var maxlength6 = $('#line6').attr('maxlength')
		   var valuelength6= obj.line6.length;
		   $('#line6').val(obj.line6);
		 
		    if(valuelength6>maxlength6){
	   $('#line6').addClass('timeline_length_error ');
	 }else{
	  $('#line6').removeClass('timeline_length_error');
	  $('#line6').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line6').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
	 
	 
	   var maxlength7 = $('#line7').attr('maxlength')
		   var valuelength7= obj.line7.length;
		   $('#line7').val(obj.line7);
		 
		    if(valuelength7>maxlength7){
	   $('#line7').addClass('timeline_length_error ');
	 }else{
	  $('#line7').removeClass('timeline_length_error');
	  $('#line7').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line7').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
		
	 var maxlength8 = $('#line8').attr('maxlength')
		   var valuelength8= obj.line8.length;
		   $('#line8').val(obj.line8);
		 
		    if(valuelength8>maxlength8){
	   $('#line8').addClass('timeline_length_error ');
	 }else{
	  $('#line8').removeClass('timeline_length_error');
	  $('#line8').parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $('#line8').parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
              }  
          }); 
}
$(document).ready(function() {
 
   $('#csvdata').combobox({
               create: function (evt, ui) {
                 console.log(ui);
		 $('.ui-autocomplete-input').val('--Optioneel: selecteer een voorstelling--');
		 //alert("cc");
	       }
             });
   
   <?php if(isset($_GET['edit_image_name']) && $_GET['edit_image_name']!='' && $_GET[sip]!='undefined'):?>
    $("#crop_frame").show();
   <?php endif; ?>
   
    <?php if(isset($_GET['edit_image_name']) && $_GET['edit_image_name']!='' && $_GET[sip_2]!='undefined'):?>
    $("#crop_frame_2").show();
   <?php endif; ?>
   
   
   <?php if($write_image_status_2==1 &&  $_GET['sip_2']!='undefined'): ?>
   <?php if($_GET['sip_2']!=''):?>
   $(".delete_image_icon_2").show();
   <?php endif; ?>
   $("#video_tab_new_2").show();
   <?php endif; ?>
   
    $().piroBox_ext({
        piro_speed : 900,
	attribute: 'data-pirobox',
        bg_alpha : 0.1,
        piro_scroll : true //pirobox always positioned at the center of the page
    });
   
   <?php if(isset($_GET['sip']) && !empty($_GET['sip'])): ?>
   $("#selected_image_path").val('<?php echo $_GET['sip']; ?>')
   <?php endif; ?>
   
    <?php if(isset($_GET['sip_2']) && !empty($_GET['sip_2'])): ?>
   $("#selected_image_path_2").val('<?php echo $_GET['sip_2']; ?>')
   <?php endif; ?>
   
   $('#line1,#line2,#line3,#line4,#line5,#line6,#line7,#line8').blur(function(){
	var valueLength=$.trim($(this).val()).length;
	var maxLength=$(this).attr('maxlength');
	var isVisible=$(this).parent('div').css('display');
	if(isVisible=='block'){
	 if(valueLength>maxLength){
	   $(this).addClass('timeline_length_error ');
	 }else{
	  $(this).removeClass('timeline_length_error');
	  $(this).parent('div').find('div.timeline_inputsize_error_msg').hide('slow', function(){ $(this).parent('div').find('div.timeline_inputsize_error_msg').remove(); });
	 }
	}
      });
  
    
    
});



 
function checkvalidation(input){
  var maxlengthExceed=false;
  $('#line1,#line2,#line3,#line4,#line5,#line6,#line7,#line8').each(function(){
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
   alert('U heeft het maximaal aantal toegestane karakters van één of meer velden overschreden.');
  }
   return !maxlengthExceed;
 }
 
 function test(){
  alert("Test");
 }
 
 
 function showcontent(theatre_id,template_id, type) {

    document.getElementById('showcreate').style.display="block";
   // document.getElementById('crop_frame').style.display="block";
    var line1 = $('#line1').val();
    var line2 = $('#line2').val();//document.getElementById('line2').value; 
    var line3 = $('#line3').val();//document.getElementById('line3').value;
    var line4 = $('#line4').val();//document.getElementById('line4').value;
    var line5 = $('#line5').val();//document.getElementById('line5').value; 
    var line6 = $('#line6').val();//document.getElementById('line6').value;
	var line7 = $('#line7').val();//document.getElementById('line7').value;
	var line8 = $('#line8').val();//document.getElementById('line8').value;
    var selected_image_path=$('#selected_image_path').val();
    var edit_image_name = $('#edit_image_name').val();
    var temp_mededelingen_id=$('#temp_mededelingen_id').val();
    var carrousel_id=$('#carrousel_id').val();
    var come_from=$('#come_from').val();
    var initial_image_path = $("#initial_image_path").val();
    var initial_image_path_2 = $("#initial_image_path_2").val();
    var selected_image_path_2 = $('#selected_image_path_2').val();
    var sip_2
    var src;
     <?php if(isset($_GET['request_for']) && !empty($_GET['request_for'])) :?>
      document.getElementById('selected_image_thumb').src="."+initial_image_path;
       src="crop.php?aspect_ratio=<?php echo isset($aspect_ratio) ? $aspect_ratio :''; ?>"+"&img="
      +initial_image_path;
    $('#open_url').val(src);
   
    <?php endif; ?>
    <?php if(isset($_GET['sip']) && $_GET['sip'] !='undefined'):?>
    var sip='<?php echo $_GET['sip']?>';
    <?php else: ?>
    var sip = initial_image_path;
    <?php endif; ?>
  
    <?php if(isset($_GET['sip_2']) && $_GET['sip_2'] !='undefined'):?>
    sip_2='<?php echo $_GET['sip_2']?>';
    <?php else: ?>
    var sip_2 = initial_image_path_2;
    <?php endif; ?>
    
    $("#selected_image_path").val(initial_image_path);
    //$("#selected_image_path_2").val(initial_image_path2);
   //$('#crop_frame').show();
    //var template_id = document.getElementById('template_id').value;
    window.location = 'mededelingen_overlay.php?carrousel_id='+carrousel_id+'&request_for=template_switch&template_id='+template_id+'&line1='+line1+'&line2='+line2+'&line3='+line3+'&line4='+line4+'&line5='+line5+'&line6='+line6+'&line7='+line7+'&line8='+line8+
    '&write_image_path='+selected_image_path+'&write_image_path_2='+selected_image_path_2+'&temp_mededelingen_id='+temp_mededelingen_id+"&edit_image_name="+edit_image_name+'&come_from='+come_from+"&type="+type+"&type="+type+"&status=<?php echo isset($_GET[status]) ? $_GET[status] : ''; ?>"+"&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>"+"&is_change=1&sip="+sip+"&sip_2="+sip_2+"&open_url="+src;
   
 }
 
function popitup(path,id) {
    var url='previewvideo.php?id='+id+'&path='+path;
    var newwindow=window.open(url,'Preview','height=500,width=640,left=190,top=150,screenX=200,screenY=100');
}

function changefont(id1,id2) {
  var fonttype=id1.split(".");
  document.getElementById("font"+id2).innerHTML='<span style="font-family:'+fonttype[0]+'">Voorbeeld</span>';
}

function openitup(id){
    if(id=='custom_btn'){
        var url = $("#open_url").val();
    }else{
        var url = $("#open_url_2").val();
    }
 window.open(url,'Crop Image','height=550,width=650,left=190,top=150,screenX=200,screenY=100,scrollbars=yes');
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
      
      <?php if($_GET['sip']!='undefined' && !empty($_GET['sip'])):?>
      $('.delete_image_icon').css('display','block');
      $('.crop_frame').css('display','block');
      
      <?php endif; ?>
      <?php if(isset($_GET['crop_type']) && $_GET['crop_type']=='custom') :?>
     $("#auto_btn").removeClass('btn_active');
	 $("#auto_btn").addClass('btn_inactive');
       $("#custom_btn").addClass('btn_active');
	   $("#custom_btn").removeClass('btn_inactive');
	   
      <?php elseif(isset($_GET['crop_type']) && $_GET['crop_type']=='auto'):?>
       $("#custom_btn").removeClass('btn_active');
	   $("#custom_btn").addClass('btn_inactive');
       $("#auto_btn").addClass('btn_active');
	   $("#auto_btn").removeClass('btn_inactive');
     <?php endif; ?>
      
       $('img.dir_icon,img.file_icon').live('click',function(){
	 var seletedItemClass=$(this).attr("class");
         var cursource = $('img#selected_image_thumb').attr('src');
         //alert(cursource);
	 var name=trim($(this).attr("data"));
	 if(seletedItemClass=='dir_icon'){
	   urlGoForward(name);
	   getDirectoryList();
	   // urlGoForward();
	 }else if(seletedItemClass=='file_icon'){
	  
	  var confirmAction=confirm("Wilt u deze afbeelding selecteren?");
	  if(confirmAction){
	  // alert($(this).attr("src"));
	   var src="crop.php?aspect_ratio=<?php echo isset($aspect_ratio) ? $aspect_ratio :''; ?>&img="+current_directory+'/'+name+"&carrousel_id=<?php echo isset($_GET["carrousel_id"]) ? $_GET["carrousel_id"] : ''; ?>&request_for=<?php echo isset($_GET["request_for"]) ? $_GET["request_for"] : ''; ?>&template_id=<?php echo isset($_GET["template_id"]) ? $_GET["template_id"] : ''; ?>&line1=<?php echo isset($_GET["line1"]) ? $_GET["line1"] : ''; ?>&line2=<?php echo isset($_GET["line2"]) ? $_GET["line2"] : ''; ?>&line3=<?php echo isset($_GET["line3"]) ? $_GET["line3"] : ''; ?>&line4=<?php echo isset($_GET["line4"]) ? $_GET["line4"] : ''; ?>&line5=<?php echo isset($_GET["line5"]) ? $_GET["line5"] : ''; ?>&line6=<?php echo isset($_GET["line6"]) ? $_GET["line6"] : ''; ?>&line7=<?php echo isset($_GET["line7"]) ? $_GET["line7"] : ''; ?>&line8=<?php echo isset($_GET["line8"]) ? $_GET["line8"] : ''; ?>&write_image_path=<?php echo isset($_GET["write_image_path"]) ? $_GET["write_image_path"] : '';?>&temp_mededelingen_id=<?php echo isset($_GET["temp_mededelingen_id"]) ? $_GET["temp_mededelingen_id"] : '';?>&edit_image_name=<?php echo isset($_GET["temp_mededelingen_id"]) ? $_GET["temp_mededelingen_id"] : ''; ?>'&come_from=<?php echo isset($_GET["come_from"]) ? $_GET["come_from"] : ''; ?>&type=<?php echo isset($_GET["type"]) ?$_GET["type"] : '';  ?>";
	   if(cursource==".undefined" || cursource==""){
            $('#selected_image_path').val(current_directory+'/'+name);
            $('#initial_image_path').val(current_directory+'/'+name);
            $('#sip').val(current_directory+'/'+name);
	    $('#block_img').show();
	    
	    $('.delete_image_icon').css('display','block');
          }else{
            $('#selected_image_path_2').val(current_directory+'/'+name);
            $('#initial_image_path_2').val(current_directory+'/'+name);
            $('#sip_2').val(current_directory+'/'+name);
	     $('#block_img_2').show();
          }
	   //var cursource = $('img#selected_image_thumb').attr('src');
	   if(cursource==".undefined" || cursource==""){
	    $('img#selected_image_thumb').attr('src',$(this).attr("src"));
	   }else{
	    $('img#selected_image_thumb_two').attr('src',$(this).attr("src"));
	    $('img#selected_image_thumb_two').css('display','block');
            //$('#selected_image_path_2').val($(this).attr("src"));
            //$('#initial_image_path_2').val($(this).attr("src"));
            
            $('#crop_frame_2').show();
	    $('.delete_image_icon_2').css('display','block');
	    
	   }
	   $('#open_url').val(src);
	   $('#crop_frame').show();
	   <?php if($_GET['sip']!="" && $_GET['sip']!="undefined"): ?>
	   $('img#selected_image_thumb').css('display','block');
	   //$('div#block_img').css('display','block');
	   <?php endif; ?>
	   if($('#selected_image_path').val() != ''){
	    $('img#selected_image_thumb').css('display','block');
	    
	   //$('div#block_img').css('display','block');
	    $('#crop_frame').show();
	   }
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
	   $('div#block_img').css('display','none');
	   $('#crop_frame').hide();
	   $("#auto_btn").addClass('btn_active');
	   $("#auto_btn").removeClass('btn_inactive');
	   $("#custom_btn").removeClass('btn_active');
	   $("#custom_btn").addClass('btn_inactive');
	   $("#crop_type").val('auto');
	   
	  }
	  
       });
       
        $('img#selected_image_thumb_remove_2').live('click',function(){
	  var confirmAction=confirm("Wilt u de selectie verwijderen?");
	  if(confirmAction){
	   $('#selected_image_path_2').val("");
	   $('img#selected_image_thumb_two').attr('src',"");
	   $('#initial_image_path_2').val("");
	   $('div#block_img_2').css('display','none');
	   $('#crop_frame_2').hide();
	   $("#auto_btn_2").addClass('btn_active');
	   $("#auto_btn_2").removeClass('btn_inactive');
	   $("#custom_btn_2").removeClass('btn_active');
	   $("#custom_btn_2").addClass('btn_inactive');
	   $("#crop_type_2").val('auto');
	   
	   
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
	  //var windowHeight=$(window).height();
	  // var windowScrollHeight=$("html,body").scrollTop();//+windowHeight;
	  var windowScrollHeight=$("body").scrollTop();
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
    } 
	else if(id==8) {
 window.location='delay_mededelingen_overlay.php';
}
	else {
     
    }
}

 function switchcrop(val, id){
 
 if(val=='custom'){
  $("#cropbtn").show();
  //alert(id);
    if(id=='custom_btn'){
      var selected_image = $("#selected_image_path").val();
      var initial_image_path=$("#initial_image_path").val();

    var img = selected_image.split("/");  
    var croped_image = "/"+img[1]+"/cropped/"+img[2];
    $('#crop_type').val('custom');
    $('#sip').val(selected_image);  
    <?php //if(isset($_GET['edit_image_name']) && !empty($_GET['edit_image_name'])) : ?>
    var src="crop.php?aspect_ratio=<?php echo isset($aspect_ratio) ? $aspect_ratio :''; ?>"+"&img="+initial_image_path;
    $('#sip').val('<?php echo isset($_GET['sip']) ? $_GET['sip'] : ''; ?>');
    $('#open_url').val(src);
    <?php //endif; ?>
    //$("#selected_image_path").val(selected_image);
    $("#auto_btn").removeClass('btn_active');
	$("#auto_btn").addClass('btn_inactive');
    $("#custom_btn").addClass('btn_active');
	 $("#custom_btn").removeClass('btn_inactive');
    $("#sip").val(initial_image_path);
    openitup(id);
  }else{
      
      var selected_image_2 = $("#selected_image_path_2").val();
      var initial_image_path_2=$("#initial_image_path_2").val();
      initial_image_path_2 = initial_image_path_2.replace("./", "/");
//alert(selected_image_2);
    var img_2 = selected_image_2.split("/");  
    var croped_image_2 = "/"+img_2[1]+"/cropped/"+img_2[2];
    $('#crop_type_2').val('custom');
    $('#sip_2').val(selected_image_2);  
    <?php //if(isset($_GET['edit_image_name']) && !empty($_GET['edit_image_name'])) : ?>
    var src="crop.php?aspect_ratio=<?php echo isset($aspect_ratio_2) ? $aspect_ratio_2 :''; ?>"+"&img="+initial_image_path_2+"&imgno=two";
    $('#sip_2').val('<?php echo isset($_GET['sip_2']) ? $_GET['sip_2'] : ''; ?>');
    $('#open_url_2').val(src);
    <?php //endif; ?>
    //$("#selected_image_path").val(selected_image);
    $("#auto_btn_2").removeClass('btn_active');
	$("#auto_btn_2").addClass('btn_inactive');
    $("#custom_btn_2").addClass('btn_active');
	$("#custom_btn_2").removeClass('btn_inactive');
    $("#sip_2").val(initial_image_path_2);
    openitup(id);
  }
 }else{
 if(id=='auto_btn'){
  $("#cropbtn").hide();
  $('#crop_type').val('auto');
  var selected_image = $("#selected_image_path").val();
  var initial_image_path=$("#initial_image_path").val();
  var selected_image_thumb = $("#sip").val();
  $("#sip").val(initial_image_path);
  document.getElementById('selected_image_thumb').src="."+initial_image_path;
  var img = selected_image.split("/");
  var croped_image = "/"+img[1]+"/cropped/"+img[3];
  <?php if(isset($_GET['sip']) && !empty($_GET['sip'])) : ?>
  var src="crop.php?aspect_ratio=<?php echo isset($aspect_ratio) ? $aspect_ratio :''; ?>"+"&img=<?php echo isset($_GET['sip']) ? $_GET['sip'] : ''; ?>";
  $('#sip').val('<?php echo isset($_GET['sip']) ? $_GET['sip'] : ''; ?>');
  $('#open_url').val(src);
  $("#selected_image_path").val('<?php echo isset($_GET['sip']) ? $_GET['sip'] : ""; ?>');
  <?php endif; ?>
  $("#selected_image_path").val(initial_image_path);
  $("#auto_btn").addClass('btn_active');
  $("#auto_btn").removeClass('btn_inactive');
  $("#custom_btn").removeClass('btn_active');
  $("#custom_btn").addClass('btn_inactive');
 }
 else{
 $("#cropbtn_2").hide();
  $('#crop_type_2').val('auto');
  var selected_image_2 = $("#selected_image_path_2").val();
  var initial_image_path_2=$("#initial_image_path_2").val();
  var selected_image_thumb_2 = $("#sip_2").val();
  $("#sip_2").val(initial_image_path_2);
  
  document.getElementById('selected_image_thumb_two').src="."+initial_image_path_2;
  var img_2 = selected_image_2.split("/");
  var croped_image_2 = "/"+img_2[1]+"/cropped/"+img_2[3];
  <?php if(isset($_GET['sip_2']) && !empty($_GET['sip_2'])) : ?>
  var src="crop.php?aspect_ratio=<?php echo isset($aspect_ratio_2) ? $aspect_ratio_2 :''; ?>"+"&img=<?php echo isset($_GET['sip_2']) ? $_GET['sip_2'] : ''; ?>";
  $('#sip_2').val('<?php echo isset($_GET['sip_2']) ? $_GET['sip_2'] : ''; ?>');
  $('#open_url_2').val(src);
  $("#selected_image_path_2").val('<?php echo isset($_GET['sip_2']) ? $_GET['sip_2'] : ""; ?>');
  <?php endif; ?>
  $("#selected_image_path_2").val(initial_image_path_2);
  $("#auto_btn_2").addClass('btn_active');
  $("#auto_btn_2").removeClass('btn_inactive');
  $("#custom_btn_2").removeClass('btn_active');
  $("#custom_btn_2").addClass('btn_inactive');
 }
}
}



</script>
</head>
<?php
if(isset($temptname) and $temptname!='' and isset($_GET['method'])){
?>

<body onload="showcontent(<?php echo $temptname;?>)">
<?php }else{?>
 <body >
 
<!--------------- include header --------------->
   <?php
   
   if(trim($_REQUEST['come_from'])==''){
      include('header.html');
   }else{
    include('header_overlay.html');
   }
   ?>
   
    <div class="main_container" style="margin-top:30px;">
        	
     <?php if(trim($_REQUEST['come_from'])==''){?>
   <div class="content">
    	 <span class="title">Mededelingen</span>
    	 <p>Hier kunt u fullscreen huishoudelijke mededelingen maken.</p>
    	 <p>In het tekstveld kunt u uw boodschap invullen, indien u een regel handmatig wilt afbreken gebruikt u hiervoor een dollar teken "$".
    	<p>De gemaakte huishoudelijke mededelingen vindt u onder het tabblad "Mededelingen" op de pagina <a href="create_carrousel.php">"Maak carrousel"</a>.</p>
    	</div>
   <?php }?>
   
     <!--request_for=template_edit -->
     <?php
     if( $_GET['file']=='create') { ?>
      <div class="video_tab_new" style="margin-left:0px;"><a href='create_carrousel.php'>Terug</a></div>
     <?php }
     elseif($_GET['file']=='edit' || $_GET['type']=='edit'){ ?>
      <div class="video_tab_new" style="margin-left:0px;"><a href="edit.php?id=<?php echo $carrousel_id;?>&status=<?php echo isset($_GET[status]) ? $_GET[status] : ''; ?>&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>">Terug</a></div>

     <?php } ?>
     
        <?php echo $showvideo;
	?>
	
      
	<div class="vertical_gallery" style="display:<?php echo $data_display; ?> ;">
        	
	<span style="font-size:17px; font-weight:bold; ">Kies een template</span>
	<br /><br />
	
	<div class="blocks_container height_auto">
	

        <?php
	if(isset($_GET['template_id']) && !empty($_GET['template_id'])){
	 $crop_button_query="select write_image_resize_option, write_image_resize_option_2 from mededelingen where id=".$_GET['template_id'];
	 $crop_button_query_rs=$db->query($crop_button_query);
	 $crop_button_query_result = mysql_fetch_array($crop_button_query_rs);
	 $write_image_resize_option = $crop_button_query_result['write_image_resize_option'];
	 $write_image_resize_option_2 = $crop_button_query_result['write_image_resize_option_2'];
	}
	
	$getTemplates="select name_t,id,template_name,theatre_id, write_image_resize_option from mededelingen where theatre_id in (select id from theatre where status = '1') order by id";
        $get_templates=$db->query($getTemplates);
	//echo $status4.'>>>>>>>>>>>';
        while($template_result = mysql_fetch_array($get_templates)){
	 
	 $loop_template_label = $template_result['name_t'];
	 $loop_template_name  = $template_result['template_name'];
	 $loop_template_id    = $template_result['id']; 
	 $loop_theatre_id     = $template_result['theatre_id']; 
	 $loop_put_text       = $template_result['name_t'];
	
	?>
	
	
	<form name="c_create" action="mededelingen_overlay.php?thumb_page=1&template_id=<?php echo $template_id; ?>&status=<?php echo $_GET['status']?>&is_block=<?php echo trim($_GET['is_block']); ?>" method="POST" enctype="multipart/form-data" onsubmit="return checkvalidation()">
	
            	<div class="block" id="block_overlay">
		 <?php
		 if(isset($selected_template_label) and $selected_template_label ==$loop_template_label){$checked ="checked"; $flag=1;}else{$checked='';} ?>
		    <p class="blockTitle"><?php echo $loop_put_text;?></p>
                    <div class="block_img"><img src="../narrowcasting/mededelingen_template/<?php echo $loop_template_name;?>" alt="" width="165" height="100"/></div>
                    <div class="radioButton_sec"><input id='check_<?php echo $loop_template_id;?>' type="radio" name="tname" value="<?php echo $loop_template_name;?>" onclick="showcontent('<?php echo $loop_theatre_id;?>','<?php echo $loop_template_id;?>', '<?php echo $_REQUEST['type']?>');" class="radioButton" <?php echo $checked; ?>/> </div>
                    <div class="btn2"><a href="./mededelingen_template/<?php echo $loop_template_name;?>" rel='single'  class='pirobox'>Preview</a></div>
                    <div class="clear"></div>
                </div>
		<?php } ?>
            </div>
 <!-- IMG BLOCKS END -->
		<?php
		$dd_query="select dropdown from theatre where status=1";
		$rst=$db->query($dd_query);
		$result_dd=mysql_fetch_array($rst);
		$dropdown = $result_dd['dropdown'];
	      if($dropdown!=""){
	       if($dropdown=="csv"){
	         $query_dd = "select * from csvimport order by line1";
	       }elseif($dropdown=="xml"){
		$query_dd = "select * from xmlimport order by line1";
	       }
	      $conn=$db->query($query_dd);
	      ?>
		  <div id="dropdown_wrapper">
	     <select name="csvdata" id="csvdata" onchange="get_data_from_csv(this.value)">
	      <option value="">--Optioneel: selecteer een voorstelling--</option>
	      <?php
	      while($result_dd=mysql_fetch_array($conn)){
			  
	      ?>
	      <option value="<?php echo $result_dd['id']?>"><?php echo htmlentities($result_dd[$dropdown_label1].' '.$result_dd[$dropdown_label2], ENT_QUOTES, "UTF-8");?></option>
	      <?php
	      }
	      ?>
	     </select>
		  </div>
		  <?php }?>
	     <input type="hidden" name="filetype" id="filetype" value="<?php echo $dropdown; ?>" />
	     
        <div class="overlay_fields">
	   
	 <?php if($flag=='1') {?>
	      <div class="overlay_wrap" id="with_agedot" >
	       
	      
	       <div class="single_row"  style="display:<?php if(intval($write_image_status)==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
	   
	      <span style="width:150px;float:left;"><label for="line1">Kies een afbeelding:</label></span>
                    <!--<div class="video_tab_new" style="display:none;margin-left:146px; margin-top:-3px; margin-bottom:20px;">
		    
		        <a href="#directory_container" rel="inline-940-350" class="pirobox"  id="abc">Bladeren</a>
		     
		     </div>-->
		    
		     
		    
		    
		    <div class="video_tab_new">
		     <a href="#directory_container" rel="inline-940-350" class="pirobox"  id="abc">Bladeren</a>
		    </div>
		     <?php if($write_image_status_2==1 ):?> 
		      <div class="video_tab_new_2" id="video_tab_new_2">
		       <a href="#directory_container" rel="inline-940-350" class="pirobox"  id="abc">Bladeren</a>
		      </div>
		     <?php endif; ?>
		     
                     <input type="hidden" name="write_image_path" value="<?php echo isset($_GET['sip']) ? $_GET['sip'] : ""; ?>" id="selected_image_path" />
		     <input type="hidden" name="initial_image_path" value="<?php echo isset($_GET['sip']) ? $_GET['sip'] : ""; ?>" id="initial_image_path" />
                     
                     <input type="hidden" name="write_image_path_2" value="<?php echo isset($_GET['sip']) ? $_GET['sip'] : ""; ?>" id="selected_image_path_2" />
		     <input type="hidden" name="initial_image_path_2" value="<?php echo isset($_GET['sip_2']) ? $_GET['sip_2'] : ""; ?>" id="initial_image_path_2" />
		     
		     <input type="hidden" name="open_url" value="<?php echo $_GET['open_url']."&img=".$_GET['img']; ?>" id="open_url" />
                     <input type="hidden" name="open_url_2" value="<?php echo $_GET['open_url']."&img=".$_GET['img']; ?>" id="open_url_2" />
                     
		     <?php
		     $show_image='none';
		      if(trim($tempwrite_image_path)!="" && $_GET['sip']!='undefined'){
		      $show_image="block";
		      }
		      
		      $show_image_2='none';
		      if(trim($tempwrite_image_path_2)!="" && $_GET['sip_2']!='undefined'){
		      $show_image_2="block";
		      }
		      ?>
		      
		    <div id="selected_image_block">
		      <div class="block_img" id="block_img">
		       <?php if(isset($_GET['crop_type']) && $_GET['crop_type']=='custom'): 
		           $selected_image_thump=$tempwrite_image_path;
			   else:
			   $selected_image_thump=isset($_GET['sip'])?$_GET['sip']:'';
		        endif;
			if($_GET['sip']==""):
			$selected_image_thump=$tempwrite_image_path;
			endif;
			?>
			
			 <?php if(isset($_GET['crop_type_2']) && $_GET['crop_type_2']=='custom'): 
		           $selected_image_thump_two=$tempwrite_image_path_2;
			   else:
			   $selected_image_thump_two=isset($_GET['sip_2'])?$_GET['sip_2']:'';
		        endif;
			if($_GET['sip_2']==""):
			$selected_image_thump_two=$tempwrite_image_path_2;
			endif;
			?>
			  <img style="display:<?php echo $show_image;?>;" src="<?php echo '.'.$selected_image_thump?>" id="selected_image_thumb" width="135" height="135">
			  
			  <?php
			  
			   if($write_image_resize_option=='crop' && $_GET['sip']!='undefined'){
			    $display_a = "block";
			   }else{
			    $display_a = "none";
			   }
			   
			  ?>
                              <?php if($write_image_resize_option=='crop'): ?>  
  	            <div id="crop_frame" style="display:<?php echo $display_a; ?>;">
	  
  	   	  <div><input type="button" class="button btn_active" id="auto_btn" name="auto" onclick="switchcrop('auto', this.id)" value="Auto crop" /></div>
  	   	   <div><input type="button" class="button btn_inactive" id="custom_btn" name="custom" onclick="switchcrop('custom', this.id)" value="Custom crop"></div>
		   <div><input type="hidden" name="crop_type" id="crop_type" value="<?php echo isset($_GET['crop_type']) ? $_GET['crop_type'] : 'auto'; ?>" /></div>
		   
  	   	 <div class="clear"></div>
  	   	</div>
	      <?php endif; ?>  
	           <div class="delete_image_icon" style="display:none;">
		    <a href="javascript:void(0);">
	               <img alt="" src='<?php echo $close_button_path; ?>' id="selected_image_thumb_remove">
		    </a>
		   </div>
		    <br><br>
                </div>
		<?php if($write_image_status_2==1 ):?>      
	      <div id="block_img_2">
	       
	        <img style="display:<?php echo $show_image_2;?>;" src="<?php echo '.'.$selected_image_thump_two ?>" id="selected_image_thumb_two" width="135" height="135">
			  <?php if($write_image_resize_option_2=='crop'): ?>  
  	            <div id="crop_frame_2" <?php if($_GET['sip_2']!='' && $_GET['sip_2']!='undefined'):?>style="display:block;"<?php else:?>style="display:none;"<?php endif;?>>
	  
  	   	  <div><input type="button" class="button btn_active" id="auto_btn_2" name="auto" onclick="switchcrop('auto', this.id)" value="Auto crop" /></div>
  	   	   <div><input type="button" class="button btn_inactive" id="custom_btn_2" name="custom" onclick="switchcrop('custom',this.id)" value="Custom crop"></div>
		   <div><input type="hidden" name="crop_type_2" id="crop_type_2" value="<?php echo isset($_GET['crop_type_2']) ? $_GET['crop_type_2'] : 'auto'; ?>" /></div>
		   
  	   	 <div class="clear"></div>
  	   	</div>
		  <?php endif; ?>  
	      <?php endif; ?> 
			  <input type="hidden" name="selected_image_thumb" id="selected_image_thumb_a" value="" />
			  <?php if($write_image_status_2==1):?>
			  <div class="delete_image_icon_2" style="display:none;">
		    <a href="javascript:void(0);">
	               <img alt="" src='<?php echo $close_button_path; ?>' id="selected_image_thumb_remove_2">
		    </a>
		   </div>
			  <?php endif; ?>
		  
			  
		      </div>
	       
	      </div>
	      
		
	      <div>
                  <input type="hidden" name="sip" id="sip" value="<?php echo isset($_GET['sip']) ? $_GET['sip'] : ''; ?>" />
                  <input type="hidden" name="sip_2" id="sip_2" value="<?php echo isset($_GET['sip_2']) ? $_GET['sip_2'] : ''; ?>" />
              </div>
  		   <div class="clear"></div>
		
  		 </div>
	      
	      
	      
	        <div class="single_row"  style="display:<?php if(intval($status1)==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:150px;float:left;"><label for="line1"><?php echo $label1;?>:</label></span>
		     
	             <?php if(intval($field_type1)==1){?>
                     <input type="text" name="line1" id="line1" style="width:270px;" maxlength="<?php echo $limit1;?>" value="<?php if(isset($templine1)){echo htmlentities($templine1, ENT_QUOTES , "UTF-8");}?>"  <?php if(strlen($templine1)>$limit1) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" style="width:270px; vertical-align:middle;" name="line1" id="line1" cols="33" maxlength="<?php echo $limit1;?>" <?php if(strlen($templine1)>$limit1) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine1)){echo trim($templine1);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color1" class="color" size="6" readonly="" value="<?php if(isset($tempcolor1)){echo $tempcolor1;}?>" >
                     <label for="line1">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font1" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont1); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Uitlijning:</label>
		     <select name="text_center1"  style="width:85px;">
                      <?php echo text_center_options($text_center1);?>
                     </select>&nbsp;&nbsp;

		    
		    
		     <?php if(strlen(trim($templine1))>$limit1) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
	        <div class="single_row"  style="display:<?php if($status2==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:150px;float:left;"><label for="line2"><?php echo $label2;?>:</label></span>
		     
	             <?php if(intval($field_type2)==1){?>
                     <input type="text" name="line2" id="line2" style="width:270px;" maxlength="<?php echo $limit2;?>" value="<?php if(isset($templine2)){echo htmlentities($templine2, ENT_QUOTES , "UTF-8");}?>"  <?php if(strlen($templine2)>$limit2) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line2" id="line2" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit2;?>" <?php if(strlen($templine2)>$limit2) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine2)){echo trim($templine2);}?></textarea>
		     
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color2" class="color" size="6" readonly="" value="<?php if(isset($tempcolor2)){echo $tempcolor2;}?>" >
                     <label for="line2">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font2" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont2); ?>
                     </select>&nbsp;&nbsp;
		    <label for="line1">Uitlijning:</label>
		     <select name="text_center2"  style="width:85px;">
                      <?php echo text_center_options($text_center2);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine2)>$limit2) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		<div class="single_row"  style="display:<?php if($status3==1) { echo 'block';  }else {echo 'none';} ?>">
                     <span style="width:150px;float:left;"><label for="line3"><?php echo $label3;?>:</label></span>
		     
	             <?php if(intval($field_type3)==1){?>
                     <input type="text" name="line3" id="line3" style="width:270px;" maxlength="<?php echo $limit3;?>" value="<?php if(isset($templine3)){echo htmlentities($templine3, ENT_QUOTES, "UTF-8" );}?>"  <?php if(strlen($templine3)>$limit3) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		      <textarea name="line3" id="line3" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit3;?>" <?php if(strlen($templine3)>$limit3) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine3)){echo trim($templine3);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color3" class="color" size="6" readonly="" value="<?php if(isset($tempcolor3)){echo $tempcolor3;}?>" >
                     <label for="line1">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font3" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont3); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Uitlijning:</label>
		     <select name="text_center3"  style="width:85px;">
                       <?php echo text_center_options($text_center3);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine3)>$limit3) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
		<div class="single_row"  style="display:<?php if($status4==1) { echo 'block';  }else {echo 'none';} ?>">
                     <span style="width:150px;float:left;"><label for="line4"><?php echo $label4;?>:</label></span>
		     
	             <?php if(intval($field_type4)==1){?>
                     <input type="text" name="line4" id="line4" style="width:270px;" maxlength="<?php echo $limit4;?>" value="<?php if(isset($templine4)){echo htmlentities($templine4, ENT_QUOTES, "UTF-8" );}?>"  <?php if(strlen($templine4)>$limit4) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line4" id="line4" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit4;?>" <?php if(strlen($templine4)>$limit4) {?> class="timeline_length_error" <?php }?>><?php if(isset($templine4)){echo trim($templine4);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color4" class="color" size="6" readonly="" value="<?php if(isset($tempcolor4)){echo $tempcolor4;}?>" >
                     <label for="line4">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font4" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont4); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Uitlijning:</label>
		     <select name="text_center4" style="width:85px;">
		       <?php echo text_center_options($text_center4);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine4)>$limit4) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
		<div class="single_row"  style="display:<?php if($status5==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:150px;float:left;"><label for="line5"><?php echo $label5;?>:</label></span>
		     
	             <?php if(intval($field_type5)==1){?>
                     <input type="text" name="line5" id="line5" style="width:270px;" maxlength="<?php echo $limit5;?>" value="<?php if(isset($templine5) ){echo htmlentities($templine5, ENT_QUOTES, "UTF-8" );}?>"  <?php if(strlen($templine5)>$limit5) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		    <textarea rows="4" name="line5" id="line5" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit5;?>" <?php if(strlen($templine5)>$limit5) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine5)){echo trim($templine5);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color5" class="color" size="6" readonly="" value="<?php if(isset($tempcolor5)){echo $tempcolor5;}?>" >
                     <label for="line5">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font5" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont5); ?>
                     </select>&nbsp;&nbsp;
		    <label for="line1">Uitlijning:</label>
		     <select name="text_center5"  style="width:85px;">
                       <?php echo text_center_options($text_center5);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine5)>$limit5) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		<div class="single_row"  style="display:<?php if($status6==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:150px;float:left;"><label for="line6"><?php echo $label6;?>:</label></span>
		     
	             <?php if(intval($field_type6)==1){?>
                     <input type="text" name="line6" id="line6" style="width:270px;" maxlength="<?php echo $limit6;?>" value="<?php if(isset($templine6) ){echo htmlentities($templine6, ENT_QUOTES, "UTF-8" );}?>"  <?php if(strlen($templine6)>$limit6) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" name="line6" id="line6" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit6;?>" <?php if(strlen($templine6)>$limit6) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine6)){echo trim($templine6);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color6" class="color" size="6" readonly="" value="<?php if(isset($tempcolor6)){echo $tempcolor6;}?>" >
                     <label for="line6">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font6" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont6); ?>
                     </select>&nbsp;&nbsp;
		     <label for="line1">Uitlijning:</label>
		     <select name="text_center6"  style="width:85px;">
                      <?php echo text_center_options($text_center6);?>
                     </select>&nbsp;&nbsp;
		     <?php if(strlen($templine6)>$limit6) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
						
						
		       <div class="single_row"  style="display:<?php if($status7==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
			    <span style="width:150px;float:left;"><label for="line7"><?php echo $label7;?>:</label></span>

			    <?php if(intval($field_type7)==1){?>
			    <input type="text" name="line7" id="line7" style="width:270px;" maxlength="<?php echo $limit7;?>" value="<?php if(isset($templine7) ){echo htmlentities($templine7, ENT_QUOTES , "UTF-8");}?>"  <?php if(strlen($templine7)>$limit7) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
			    <?php }else {?>
			    <textarea rows="4" name="line7" id="line7" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit7;?>" <?php if(strlen($templine7)>$limit7) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine7)){echo trim($templine7);}?></textarea>
			    <?php }?>
			    <label for="color">Kleur:</label>
			    <input type="text" name="color7" class="color" size="6" readonly="" value="<?php if(isset($tempcolor7)){echo $tempcolor7;}?>" >
			    <label for="line7">&nbsp;Lettertype:</label>
			    <select style="width:140px;" name="font7" onchange="changefont(this.value,1);">
			       <?php echo font_listing($tempfont7); ?>
			    </select>&nbsp;&nbsp;
			    <label for="line1">Uitlijning:</label>
			    <select name="text_center7"  style="width:85px;">
			     <?php echo text_center_options($text_center7);?>
			    </select>&nbsp;&nbsp;
			    <?php if(strlen($templine7)>$limit7) {?>
			    <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
			    <?php }?>
			   <br><br>
		       </div>

		
										<div class="single_row"  style="display:<?php if($status8==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
								                     <span style="width:150px;float:left;"><label for="line8"><?php echo $label8;?>:</label></span>
		     
									             <?php if(intval($field_type8)==1){?>
								                     <input type="text" name="line8" id="line8" style="width:270px;" maxlength="<?php echo $limit8;?>" value="<?php if(isset($templine8) ){echo htmlentities($templine8, ENT_QUOTES , "UTF-8");}?>"  <?php if(strlen($templine8)>$limit8) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
								                     <?php }else {?>
										     <textarea rows="4" name="line8" id="line8" style="width:270px; vertical-align:middle;" maxlength="<?php echo $limit8;?>" <?php if(strlen($templine8)>$limit8) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine8)){echo trim($templine8);}?></textarea>
										     <?php }?>
										     <label for="color">Kleur:</label>
								                     <input type="text" name="color8" class="color" size="6" readonly="" value="<?php if(isset($tempcolor8)){echo $tempcolor8;}?>" >
								                     <label for="line8">&nbsp;Lettertype:</label>
								                     <select style="width:140px;" name="font8" onchange="changefont(this.value,1);">
								                        <?php echo font_listing($tempfont8); ?>
								                     </select>&nbsp;&nbsp;
										     <label for="line1">Uitlijning:</label>
										     <select name="text_center8"  style="width:85px;">
								                      <?php echo text_center_options($text_center8);?>
								                     </select>&nbsp;&nbsp;
										     <?php if(strlen($templine8)>$limit8) {?>
										     <div class="timeline_inputsize_error_msg"><p>U heeft het maximaal aantal toegestane karakters overschreden.</p></div>
								                     <?php }?>
								                    <br><br>
								                </div>
	     
               <div class="clear"></div>
	    </div>
	    <?php } }?>
    	     
        <!-- input fields end -->
        
   		<!-- START FOOTER -->
    	<?php if($flag=='1') $display_button='block'; else $display_button='none';?>
       <div class="footer" style="display:<?php echo $display_button;?>;" id="showcreate">
	    <input type="hidden" name="template_name" value="<?php echo $template_name; ?>" id="template_name">
	    <input type="hidden" name="template_id" value="<?php echo $template_id; ?>" id="template_id">
	    <input type="hidden" name="temp_mededelingen_id" value="<?php echo $temp_mededelingen_id; ?>" id="temp_mededelingen_id">
	    <input type="hidden" name="theatre_id" value="<?php echo $theatre_id; ?>" id="theatre_id">
	    <input type="hidden" name="edit_image_name" value="<?php echo $edit_image_name; ?>" id="edit_image_name">
	    <input type="hidden" name="carrousel_id" value="<?php echo $carrousel_id; ?>" id="carrousel_id">
	     <input type="hidden" name="come_from" value="<?php echo $come_from; ?>" id="come_from">
	     <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" id="type">
	     <div class="footer_tab"><input type="submit" name="submit" value="Maak mededeling"></div>
	 </div>
	 
      </form>
  
  
  
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

    
     </div> 
</body>
</html>
