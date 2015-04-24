<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
//error_reporting(0);
error_reporting(E_ALL|E_STRICT);
ini_set('max_execution_time', 3600);
include('config/database.php');
include('color_scheme_setting.php');
include('config/get_theatre_label.php');
include('thumb-functions.php');

$ffmpeg_path = $db->ffmpeg_path;


if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 //header('Location:  index.php');
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}

if(isset($_REQUEST['temp_dispaly']) and $_REQUEST['temp_dispaly']=='temp'){
 
 $data_display ="none";
 
} else{
 
 $data_display= "block";
 
}

//---------------------------Code used for upload video and images ------------------------------- 
 $error_message="";
 $sid=session_id();
 $db = new Database;
 $path=$_GET['path'];
 $tempid=$_GET['id'];
 $cid=$_GET['cid'];
 $templine1=urldecode($_GET['line1'])!=undefined?trim(urldecode($_GET['line1'])):"";
 $templine2=urldecode($_GET['line2'])!=undefined?trim(urldecode($_GET['line2'])):"";
 $templine3=urldecode($_GET['line3'])!=undefined?trim(urldecode($_GET['line3'])):"";
 $templine4=urldecode($_GET['line4'])!=undefined?trim(urldecode($_GET['line4'])):"";
 $templine5=urldecode($_GET['line5'])!=undefined?trim(urldecode($_GET['line5'])):"";
 $templine6=urldecode($_GET['line6'])!=undefined?trim(urldecode($_GET['line6'])):"";
 $templine7=urldecode($_GET['line7'])!=undefined?trim(urldecode($_GET['line7'])):"";
 $templine8=urldecode($_GET['line8'])!=undefined?trim(urldecode($_GET['line8'])):"";
 $theatre_tempid=$_GET['theatre_tempid'];
 $showvideo="";
 $temp_overlay_id=intval(trim($_GET['temp_overlay_id']));

 $edit_image_name=trim(urldecode($_REQUEST['edit_image_name']))!=undefined?trim(urldecode($_REQUEST['edit_image_name'])):"";
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
 
 //echo "<pre>";print_r($_POST);die;
 
 if(isset($_POST['submit'])) {
 // echo "<pre>";print_r($_POST);die;
  if(isset($_POST[type])){
    unlink($_SESSION['deletevideo']);
    unlink($_SESSION['deleteimage']);
	unlink($_SESSION['deletetotalimage']);
	unlink($_SESSION['deletepreview']);
    $_SESSION['deletevideo']="";
    $_SESSION['deleteimage']="";
	$_SESSION['deletetotalimage']="";
	$_SESSION['deletepreview']="";
 }
 
//$date=explode("-",$_POST['enddate_new']);
//$enddate=mktime(0,0,0,$date[0],$date[1],$date[2]);
if($_POST['enddate']!=''){
$enddate = strtotime($_POST['enddate']);
//$enddate = strtotime($_POST['enddate']);
$enddate = $_POST['enddate_new'];
}else{
 $enddate=0;
}
    //$videopath="videos/Video2.mp4";
    $videopath=$_REQUEST['imagepath'];
  
    $path=$_REQUEST['imagepath'];
    $template=$_REQUEST['tname'];
    $tempid=$_REQUEST['tempid'];
    $cid=$_REQUEST['cid'];
      $selected_template_id=$_REQUEST['selected_template_id'];
       $theatre_tempid=$selected_template_id;
    //$agedot_status =$_REQUEST['agedot_status'];
    $theatre_id =$_REQUEST['theatre_id'];
    
   
    //$getCordinates  = "select * from theatre_template where theatre_id='$theatre_id' and template_name='$template'";
    $getCordinates  = "select * from theatre_template where theatre_id='$theatre_id' and id=$selected_template_id";
    $getCordinates =$db->query($getCordinates);
    $getCordinates = mysqli_fetch_array($getCordinates);
   
    
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
	
    $text_background1 = $getCordinates['text_background1'];
    $text_background2 = $getCordinates['text_background2'];
    $text_background3 = $getCordinates['text_background3'];
    $text_background4 = $getCordinates['text_background4'];
    $text_background5 = $getCordinates['text_background5'];
    $text_background6 = $getCordinates['text_background6'];
    $text_background7 = $getCordinates['text_background7'];
    $text_background8 = $getCordinates['text_background8'];
    
    $text_background_color1 = $getCordinates['text_background_color1'];
    $text_background_color2 = $getCordinates['text_background_color2'];
    $text_background_color3 = $getCordinates['text_background_color3'];
    $text_background_color4 = $getCordinates['text_background_color4'];
    $text_background_color5 = $getCordinates['text_background_color5'];
    $text_background_color6 = $getCordinates['text_background_color6'];
    $text_background_color7 = $getCordinates['text_background_color7'];
    $text_background_color8 = $getCordinates['text_background_color8'];
    
    $source_file_position_x = $getCordinates['source_file_position_x'];
    $source_file_position_y = $getCordinates['source_file_position_y'];
    $source_file_width = $getCordinates['source_file_width'];
    $special_ffmpeg = $getCordinates['special_ffmpeg'];
    
    $text_center1 = intval(trim($_REQUEST['text_center1']));
    $text_center2 = intval(trim($_REQUEST['text_center2']));
    $text_center3 = intval(trim($_REQUEST['text_center3']));
    $text_center4 = intval(trim($_REQUEST['text_center4']));
    $text_center5 = intval(trim($_REQUEST['text_center5']));
    $text_center6 = intval(trim($_REQUEST['text_center6']));
	$text_center7 = intval(trim($_REQUEST['text_center7']));
	$text_center8 = intval(trim($_REQUEST['text_center8']));
	
    $temp_overlay_id=intval(trim($_REQUEST['temp_overlay_id']));
    $edit_image_name=trim(urldecode($_REQUEST['edit_image_name']))!=undefined?trim(urldecode($_REQUEST['edit_image_name'])):"";
    
    
    
    $srcFile="/var/www/html/narrowcasting/$videopath";    
    ob_start();
    passthru("$ffmpeg_path -i $srcFile 2>&1");
    $output = ob_get_contents();
    //echo"<pre>";
    //print_r($output);
    //echo"</pre>";
    //exit;
    ob_end_clean();
    preg_match_all('/(\d+)x(\d+)/', $output, $matches);
    $width = $matches[1][0]."<br>";
    $height = $matches[2][0];
  
    
    if(($width == 0) || ($height == 0) || (strlen($width)>4) || (strlen($height)>4))
    {
     $width = $matches[1][1];
     $height = $matches[2][1];
    }
    //print_r($matches);
    //$width = $matches[1][1];
    //$height = $matches[2][1];
    //exit;
    //$image_p = imagecreatetruecolor($width, $height);
	
    if($special_ffmpeg==1 && $landscape_portrait==1){
     $width=1080;
     $height=1920;
    }
    $image_p = imagecreatetruecolor($width, $height);
    imagesavealpha($image_p , true);
    $trans_colour = imagecolorallocatealpha($image_p , 0, 0, 0, 127);
    imagefill($image_p , 0, 0, $trans_colour);
    
    $replace_word = array('');
    $newimage='/var/www/html/narrowcasting/template/'.$template;
    //exit;
    $im = imagecreatefrompng($newimage);
    $width_1=imagesx($im);
    $height_1=imagesy($im);
    $template_width = $width_1;
    // print_r($newimage);
   // print_r($width_1);
    // print_r($height_1);
   // die;
    $text1=str_replace($replace_word,"",trim($_REQUEST['line1']));
    $text2=str_replace($replace_word,"",trim($_REQUEST['line2']));
    $text3=str_replace($replace_word,"",trim($_REQUEST['line3']));
    $text4=str_replace($replace_word,"",trim($_REQUEST['line4']));
    $text5=str_replace($replace_word,"",trim($_REQUEST['line5']));
    $text6=str_replace($replace_word,"",trim($_REQUEST['line6']));
	$text7=str_replace($replace_word,"",trim($_REQUEST['line7']));
	$text8=str_replace($replace_word,"",trim($_REQUEST['line8']));
	
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
	
    $color_1 =imagecolorallocate($im, $colorcode1[0], $colorcode1[1], $colorcode1[2]);
    $color_2= imagecolorallocate($im, $colorcode2[0], $colorcode2[1],$colorcode2[2]);
    $color_3= imagecolorallocate($im, $colorcode3[0], $colorcode3[1],$colorcode3[2]);
    $color_4= imagecolorallocate($im, $colorcode4[0], $colorcode4[1],$colorcode4[2]);
    $color_5= imagecolorallocate($im, $colorcode5[0], $colorcode5[1],$colorcode5[2]);
    $color_6= imagecolorallocate($im, $colorcode6[0], $colorcode6[1],$colorcode6[2]);
    $color_7= imagecolorallocate($im, $colorcode7[0], $colorcode7[1],$colorcode7[2]);
    $color_8= imagecolorallocate($im, $colorcode8[0], $colorcode8[1],$colorcode8[2]);
    
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
	    writeStringOnImage($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0,$text_background1, $text_background_color1 );
	  }elseif($text_center1==1){
	    writeStringOnImageCentre($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0,$text_background1, $text_background_color1 );
	  }elseif($text_center1==2){
	   writeStringOnImageRight($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0,$text_background1, $text_background_color1 );
	  }else{
	   writeStringOnImage($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0,$text_background1, $text_background_color1 );
	  }
	  // echo "<pre>";print_r($inputStringArray);
	  $previousField=0;
     }
     else if(intval($status1)==1){
	  if($y1>0){
	   $CurrentYposition=($y1+$BLOCK_MARGIN[0]);
	  }
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
	    writeStringOnImage($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1, $text_background2, $text_background_color2);
	  }elseif($text_center2==1){
	    writeStringOnImageCentre($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1, $text_background2, $text_background_color2);
	  }elseif($text_center2==2){
	   writeStringOnImageRight($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1, $text_background2, $text_background_color2);
	  }else{
	   writeStringOnImage($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1, $text_background2, $text_background_color2);
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
	   writeStringOnImage($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2, $text_background3, $text_background_color3);
	  }elseif($text_center3==1){
	    writeStringOnImageCentre($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2, $text_background3, $text_background_color3);
	  }elseif($text_center3==2){
	   writeStringOnImageRight($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2, $text_background3, $text_background_color3);
	  }else{
	   writeStringOnImage($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2, $text_background3, $text_background_color3);
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
	   writeStringOnImage($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3, $text_background4, $text_background_color4);
	  }elseif($text_center4==1){
	    writeStringOnImageCentre($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3, $text_background4, $text_background_color4);
	  }elseif($text_center4==2){
	   writeStringOnImageRight($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3, $text_background4, $text_background_color4);
	  }else{
	   writeStringOnImage($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3, $text_background4, $text_background_color4);
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
	   writeStringOnImage($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4, $text_background5, $text_background_color5);
	  }elseif($text_center5==1){
	    writeStringOnImageCentre($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4, $text_background5, $text_background_color5);
	  }elseif($text_center5==2){
	   writeStringOnImageRight($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4, $text_background5, $text_background_color5);
	  }else{
	   writeStringOnImage($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4, $text_background5, $text_background_color5);
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
	   writeStringOnImage($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5, $text_background6, $text_background_color6);
	  }elseif($text_center6==1){
	    writeStringOnImageCentre($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5, $text_background6, $text_background_color6);
	  }elseif($text_center6==2){
	   writeStringOnImageRight($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5, $text_background6, $text_background_color6);
	  }else{
	   writeStringOnImage($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5, $text_background6, $text_background_color6);
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
 	    writeStringOnImage($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6, $text_background7, $text_background_color7);
 	  }elseif($text_center7==1){
 	    writeStringOnImageCentre($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6, $text_background7, $text_background_color7);
 	  }elseif($text_center7==2){
 	   writeStringOnImageRight($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6, $text_background7, $text_background_color7);
 	  }else{
 	    writeStringOnImage($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6, $text_background7, $text_background_color7);
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
 	    writeStringOnImage($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7, $text_background8, $text_background_color8);
 	  }elseif($text_center8==1){
 	    writeStringOnImageCentre($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7, $text_background8, $text_background_color8);
 	  }elseif($text_center8==2){
 	   writeStringOnImageRight($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7, $text_background8, $text_background_color8);
 	  }else{
 	    writeStringOnImage($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7, $text_background8, $text_background_color8);
 	  }
 	}
	
    imagecopyresampled($image_p, $im, 0, 0, 0, 0, $width, $height, $width_1, $height_1);
  
    $savepath=explode("/",$videopath);
   
    if($edit_image_name!=""){
      $thumb_image=explode(".",$edit_image_name);
      $newthumb_image=$thumb_image[0];
    }else{
       $r_no=rand(1111,9999);
       $thumb_image=explode(".",$savepath[1]);
       $newthumb_image=$thumb_image[0]."_".$r_no;
    }
    if($thumb_image[1]=='mov'){
    $newsavepath=$newthumb_image.".mov";
    }elseif($thumb_image[1]=='mp4'){
     $newsavepath=$newthumb_image.".mp4";
    }
    $pathfor_image=$newthumb_image.".jpg";
    if(empty($_SESSION['newsavevideo'])){
     $_SESSION['newsavevideo']=$newsavepath;
    }
  //  header('Content-Type: image/png');   
    
    imagepng($image_p,'/var/www/html/narrowcasting/tmp/test2.png');
    
        $template_gd_image= imagecreatefrompng($videopath);
	$template_width= imagesx( $source_gd_image );
      
    if($special_ffmpeg==1){
     
	      if($landscape_portrait==0) {
	        //////-----Resize test2.png Image According to the template width ---------/////
	      $test2="/var/www/html/narrowcasting/tmp/test2.png";
	      $test2_image = imagecreatefrompng($test2);
	      $orig_test2_width = imagesx($test2_image);
	      $orig_test2_height = imagesy($test2_image);
	      $test2_height = (($orig_test2_height * $template_width) / $orig_test2_width);
	      $new_test2_image = imagecreatetruecolor($template_width, $test2_height);
    
    
	      imagealphablending($new_test2_image, false);
	      imagesavealpha($new_test2_image, true);
	      $transparent = imagecolorallocatealpha($new_test2_image, 255, 255, 255, 127);
	      imagefilledrectangle($new_test2_image, 0, 0, $width, $height, $transparent);

	      imagecopyresampled($new_test2_image, $test2_image,0, 0, 0, 0,$template_width, $test2_height, $orig_test2_width, $orig_test2_height);
	      imagepng($new_test2_image,'/var/www/html/narrowcasting/tmp/test2.png');
	      //////-----Resize test2.png Image According to the template width ---------/////
    
	        exec("".$ffmpeg_path." -i /var/www/html/narrowcasting/$videopath -vf scale=$source_file_width:-1,pad=1920:1080:$source_file_position_x:$source_file_position_y:0x000000 -an -y /var/www/html/narrowcasting/tmp/temp_video_masker.mov"); 
	        exec("".$ffmpeg_path." -i /var/www/html/narrowcasting/tmp/temp_video_masker.mov -vcodec libx264 -b 8M -r 25 -an -vf \"movie=/var/www/html/narrowcasting/tmp/test2.png [wm];[in][wm] overlay=0:0 [out]\" /var/www/html/narrowcasting/overlay_video/$newsavepath");
		
		}
		
		else {		
    
      exec("".$ffmpeg_path." -i /var/www/html/narrowcasting/$videopath -vf scale=$source_file_width:-1,pad=1080:1920:$source_file_position_x:$source_file_position_y:0x000000 -an -y /var/www/html/narrowcasting/tmp/temp_video_masker.mov"); 
      exec("".$ffmpeg_path." -i /var/www/html/narrowcasting/tmp/temp_video_masker.mov -vcodec libx264 -b 8M -r 25 -an -vf \"movie=/var/www/html/narrowcasting/tmp/test2.png [wm];[in][wm] overlay=0:0 [out]\" /var/www/html/narrowcasting/overlay_video/$newsavepath");
     }
	 
 }
	 
      else{
      exec("".$ffmpeg_path." -i /var/www/html/narrowcasting/$videopath -vcodec libx264 -b 8M -r 25 -acodec copy -vf \"movie=/var/www/html/narrowcasting/tmp/test2.png [wm];[in][wm] overlay=0:0 [out]\" /var/www/html/narrowcasting/overlay_video/$newsavepath");
     }
       $pathToThumbs = "overlay_video/thumb/";
   $pathToTotalThumb = "overlay_video/totalthumb/";
   $video_path_split=explode('/',$_GET['path']);
   $filename=explode('.',$video_path_split[1]);
   
   //$video_path=$video_path.'.'.$video_path_split[1];
   
   //createsingle_video_Thumb($_GET['path'], $pathToThumbs, 135, 135, $filename, $r_no, $pathToTotalThumb);
   if($r_no!=''){
   $sourcefilename=$filename[0]."_".$r_no.".".$filename[1];
   $destinationfilename=$filename[0]."_".$r_no.".jpg";
   }else{
    $sourcefilename=$newsavepath;
   $destinationfilename=$pathfor_image;
   }
   $sourcepath='/var/www/html/narrowcasting/overlay_video/'.$sourcefilename;
   $destinationpath = '/var/www/html/narrowcasting/overlay_video/thumb/'.$destinationfilename;
   $totalthumbpath = '/var/www/html/narrowcasting/overlay_video/totalthumb/'.$destinationfilename;
   
   if ($landscape_portrait==0) {
      create_overlay_thumb($sourcepath, $destinationpath, $totalthumbpath, 135, 135,'crop_right');
  		}
  	else {
  		create_overlay_thumb($sourcepath, $destinationpath, $totalthumbpath, 135, 135,'crop_top');
  	}
   
   //$video_file_name=$filename."_".$r_no.
    //unlink('/var/www/html/narrowcasting/tmp/test2.png');
    /* code for edit */
   $tempsid       = session_id();
   $tempcid   	  = $_REQUEST['cid'];
   $temppath      = addslashes($_REQUEST['path']);
   $tempidss      = $_REQUEST['id'];
   $temptname     = addslashes($_REQUEST['tname']);
   $tempimagepath = addslashes($_REQUEST['imagepath']);
   $temptempid    = $_REQUEST['tempid'];
  
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
   
  
   
      ## for creation  of mededelingen image(path?)
   if($temp_overlay_id==0){
  //  $Querytemp = "insert into temp_overlay(cid,path,imagepath,tname,line1,color1,font1,line2,color2,font2,line3,color3,font3,line4,
  //                                         color4,font4,line5,color5,font5,line6,color6,font6,line7,color7,font7,line8,color8,font8,temp_file_name,
	//				   text_center1,text_center2,text_center3,text_center4,text_center5,text_center6,text_center7,text_center8
	//				  )
	//	   values('$tempcid','$temppath','$tempimagepath','$temptname','$templine1','$tempcolor1','$tempfont1','$templine2',
	//	   '$tempcolor2','$tempfont2','$templine3','$tempcolor3','$tempfont3','$templine4','$tempcolor4','$tempfont4',
	///	   '$templine5','$tempcolor5','$tempfont5','$templine6','$tempcolor6','$tempfont6','$templine7','$tempcolor7','$tempfont7','$templine8','$newsavepath'
	//	   ,$text_center1,$text_center2,$text_center3,$text_center4,$text_center5,$text_center6,$text_center7,$text_center8)";
    $Querytemp = "insert into temp_overlay(cid,path,imagepath,tname,line1,color1,font1,line2,color2,font2,line3,color3,font3,line4,color4,font4,line5,color5,font5,line6,color6,font6,line7,color7,font7,line8,color8,font8,temp_file_name,
     text_center1,text_center2,text_center3,text_center4,text_center5,text_center6,text_center7,text_center8,template_id
 					)
 		   values('$tempcid','$temppath','$tempimagepath','$temptname','$templine1','$tempcolor1','$tempfont1','$templine2',
 		   '$tempcolor2','$tempfont2','$templine3','$tempcolor3','$tempfont3','$templine4','$tempcolor4','$tempfont4',
 		   '$templine5','$tempcolor5','$tempfont5','$templine6','$tempcolor6','$tempfont6','$templine7','$tempcolor7','$tempfont7','$templine8','$tempcolor8','$tempfont8','$newsavepath',
 		   $text_center1,$text_center2,$text_center3,$text_center4,$text_center5,$text_center6,$text_center7,$text_center8,$selected_template_id)";
		   //exit;
	
	
		   
    $conn=$db->query($Querytemp);
    $temp_overlay_id=mysqli_insert_id($db->Link_ID_PREV);
   }
   else if($temp_overlay_id>0 && is_int($temp_overlay_id)){ ## for editing of mededelingen image
    
    ## delelting previously created  overlay file 
    $tempdeletequery  = "select tmpoverlay.id,tmpoverlay.temp_file_name from temp_overlay as tmpoverlay where tmpoverlay.id=$temp_overlay_id limit 1";
    $tempeditvalues =$db->query($tempdeletequery);
    $tempresult_check_record = mysqli_fetch_array($tempeditvalues);
    if($tempresult_check_record){
     $updateQueryTemp =   "update temp_overlay set cid='$tempcid',path='$temppath',imagepath='$tempimagepath',tname='$temptname'
                           ,line1='$templine1',color1='$tempcolor1',font1='$tempfont1',line2='$templine2',
		           color2='$tempcolor2',font2='$tempfont2',line3='$templine3',color3='$tempcolor3',font3='$tempfont3',line4='$templine4',
		           color4='$tempcolor4',font4='$tempfont4',line5='$templine5',color5='$tempcolor5',font5='$tempfont5',line6='$templine6',
		           color6='$tempcolor6',font6='$tempfont6',line7='$templine7',
			     color7='$tempcolor7',font7='$tempfont7',line8='$templine8',
			     color8='$tempcolor8',font8='$tempfont8',temp_file_name='$newsavepath'
			   ,text_center1=$text_center1,text_center2=$text_center2,text_center3=$text_center3
			   ,text_center4=$text_center4,text_center5=$text_center5,text_center6=$text_center6,text_center7=$text_center7,text_center8=$text_center8, template_id=$selected_template_id
			   where id=$temp_overlay_id";
    $conn=$db->query($updateQueryTemp);
    }else{
      ## if update key record not exist
     /* unlink('overlay_video/'.$newsavepath);
      unlink('overlay_video/thumb/'.$pathfor_image);
      unlink('overlay_video/totalthumb/'.$pathfor_image);*/
    }
    
   }
 
    /*end */
     // add to video overlay link will be available only if come from create/edit carrousel.
   $toevoegen="";
   if($tempid!="" && $_POST['is_enddate']=='on'){
    $opslaan="<div class='video_tab_new'><a href='add_overlay.php?path=$newsavepath&id=$tempid&cid=$cid&enddate=$enddate&status=$_GET[status]&is_block=$_GET[is_block]&show_cal=$_GET[show_cal]' style='color:white;'>Opslaan</a></div>";
   }elseif($tempid!="" && $_POST['is_enddate']!='on'){
    $opslaan="<div class='video_tab_new'><a href='add_overlay.php?path=$newsavepath&id=$tempid&cid=$cid&status=$_GET[status]&is_block=$_GET[is_block]&show_cal=$_GET[show_cal]' style='color:white;'>Opslaan</a></div>";
   }else if($cid!=""){
     $opslaan="<div class='video_tab_new'><a href='hard_redirect.php?id=$cid&status=$_GET[status]&is_block=$_GET[is_block]&show_cal=$_GET[show_cal]'>Opslaan</a></div>";
   }else{
     $opslaan="<div class='video_tab_new'><a href='hard_redirect.php'>Opslaan</a></div>";
   }
   if(trim($_REQUEST['type'])==""){
    $verwijderen="<div class='video_tab_new'><a href='delete_overlay.php?path=$newsavepath&oldpath=$videopath&cid=$cid&type=1&id=$tempid&temp_overlay_id=$temp_overlay_id&status=$_GET[status]&is_block=$_GET[is_block]&show_cal=$_GET[show_cal]' >Verwijderen</a></div>"; 
   }else{
     $verwijderen="";
   }
   

   
   
   
    $preview_video_path = '';
    $video_name_array = explode("/",$videopath);
    $video_name = $video_name_array[1];
    $overlay_preview_path="./overlay_video/preview".$newsavepath;
    if(file_exists($overlay_preview_path)){
     $preview_video_path = $newsavepath;
    }else{
     $preview_video_path = $_SESSION['newsavevideo'];
    }
    
    $_SESSION['newsavevideo']="";
   
    $showvideo="<div class='video_section' id='width_932'>
        	 <div class='video_img'><img src='./overlay_video/totalthumb/$newthumb_image.jpg?time=".time()."' width='264' height='auto'></div>
            	<div class='video_tabs'>
		  <div class='video_tab_new'><a href='javascript:void(0);' onclick='popitup(\"overlay_video\",\"$preview_video_path\")' >Preview</a></div>
		  $verwijderen
		   <div class='video_tab_new'><a href='create_overlay.php?path=$videopath&cid=$cid&id=$tempid&edit_image_name=$newsavepath&method=edit&temp_overlay_id=$temp_overlay_id&theatre_tempid=$_POST[selected_template_id]&type=$_REQUEST[type]&status=$_GET[status]&is_block=$_GET[is_block]&show_cal=$_GET[show_cal]&enddate=$enddate'>Bewerken</a></div>
		  $opslaan
                  </div>
             </div>";
 }
 
 
 
 
  //------- function for breaking string by available width and special char $----------------------------
    function splitStringByWidthAndSpecialChar($fontSize,$fontFace, $inputString, $width,$xStart,$xEnd,$center){
      $inputStringArray = explode('$', trim($inputString));
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
 
 
  
      // write multiline string on image at (xstart,ystart) position 
      function writeStringOnImage($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField, $text_background, $text_background_color){
   
	  global $LINE_MARGIN,$CurrentYposition,$BLOCK_MARGIN,$previousField;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition+$BLOCK_MARGIN[$previousField];
	  }else if($CurrentYposition>$yStart && intval($yStart)!=0){
	    //$yStart=$CurrentYposition;//+$BLOCK_MARGIN[$previousField];
	  }
	  $color_code = explode(",", $text_background_color);
	  //echo "<pre>"; print_r($color_code); echo "</pre>";
	 
	  $background_color = imagecolorallocatealpha($image, $color_code['0'], $color_code['1'], $color_code['2'], $color_code['3']);
          // echo('CurrentYposition=='.$CurrentYposition.' yStart='.$yStart.'<br>');
	   //print_r($inputStringArray); echo '<br>'.$currentField.'   '.$y3.'   '.$x3.' ysatrt'.$yStart.' CurrentYposition'.$CurrentYposition.'<br>';
	  foreach ($inputStringArray['multilineString'] as $word ){
	   if($text_background == 1){
		// echo "in background condition";
		// echo "<pre>"; print_r($word); echo "</pre>";
		 $dimention = getStringBoxDimention($fontSize,$font, $word);
		 $currentWidth = $dimention['width'];
		
		 
		 if($currentWidth>2){
		     imagefilledrectangle($image,$xStart-(0.5 * $fontSize), $yStart-$fontSize-(0.5 * $fontSize), $xStart + $currentWidth + (0.5 * $fontSize),$yStart+(0.5 * $fontSize), $background_color);
		    
	      }
	    }
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
	  return $finalResult;
      }
      
      
   function writeStringOnImageCentre($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField, $text_background, $text_background_color){
   
	  global $LINE_MARGIN,$CurrentYposition,$BLOCK_MARGIN,$previousField,$ImageWidth;
	  if(intval($yStart)==0){
	    $yStart=$CurrentYposition+$BLOCK_MARGIN[$previousField];
	  }
	  if($xEnd>$ImageWidth){
	    $xEnd=$ImageWidth;
	  }
	  $MaxWriteWidth=abs($xEnd-$xStart);
	   $color_code = explode(",", $text_background_color);
	  //echo "<pre>"; print_r($color_code); echo "</pre>";
	 
	  $background_color = imagecolorallocatealpha($image, $color_code['0'], $color_code['1'], $color_code['2'], $color_code['3']);
          // echo('CurrentYposition=='.$CurrentYposition.' yStart='.$yStart.'<br>');
	   //print_r($inputStringArray); echo '<br>'.$currentField.'   '.$y3.'   '.$x3.' ysatrt'.$yStart.' CurrentYposition'.$CurrentYposition.'<br>';
	  foreach ($inputStringArray['multilineString'] as $wordDetails ){
	  
	    $wordWidth=$wordDetails['width'];
	     $dimention = getStringBoxDimention($fontSize,$font, $wordDetails['text']);
	    // print_r($wordDetails);echo $xStart.'  '.$MaxWriteWidth.'  '.$wordWidth.'<br>';
	    if($wordWidth>=$MaxWriteWidth){
	    
	     $widthDiff=($MaxWriteWidth-$dimention['width']);
	     $newXStart=$xStart+floor($widthDiff/2);
	     if($text_background == 1){
		// echo "in background condition";
		// echo "<pre>"; print_r($word); echo "</pre>";
		
		 $currentWidth = $dimention['width'];
		
		 
		 if($currentWidth>2){
		     imagefilledrectangle($image,$newXStart-(0.5 * $fontSize), $yStart-$fontSize-(0.5 * $fontSize), $newXStart + $currentWidth + (0.5 * $fontSize),$yStart+(0.5 * $fontSize), $background_color);
		    
	      }
	    }
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{
	     $widthDiff=($MaxWriteWidth-$wordWidth);
	     $newXStart=$xStart+floor($widthDiff/2);
	     if($text_background == 1){
		// echo "in background condition";
		// echo "<pre>"; print_r($word); echo "</pre>";
		 
		 $currentWidth = $dimention['width'];
		
		 
		 if($currentWidth>2){
		     imagefilledrectangle($image,$newXStart-(0.5 * $fontSize), $yStart-$fontSize-(0.5 * $fontSize), $newXStart + $currentWidth + (0.5 * $fontSize),$yStart+(0.5 * $fontSize), $background_color);
		    
	      }
	    }
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
      
      function writeStringOnImageRight($inputStringArray,$xStart,$xEnd,$yStart,$image,$color,$font,$fontSize,$currentField, $text_background, $text_background_color){
   
    
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
	   $color_code = explode(",", $text_background_color);
	  //echo "<pre>"; print_r($color_code); echo "</pre>";
	 
	  $background_color = imagecolorallocatealpha($image, $color_code['0'], $color_code['1'], $color_code['2'], $color_code['3']);
	   
	  foreach ($inputStringArray['multilineString'] as $key=>$wordDetails ){
	  
	    $wordWidth=$wordDetails['width'];
	    $dimention = getStringBoxDimention($fontSize,$font, $wordDetails['text']);
	    // print_r($wordDetails);echo $xStart.'  '.$MaxWriteWidth.'  '.$wordWidth.'<br>';
	    if($wordWidth>=$MaxWriteWidth){
	    
	    $widthDiff=($MaxWriteWidth-$dimention['width']);
	    $newXStart=abs($xStart+$widthDiff);
	    if($text_background == 1){
		// echo "in background condition";
		// echo "<pre>"; print_r($word); echo "</pre>";
		 
		 $currentWidth = $dimention['width'];
		
		 
		 if($currentWidth>2){
		     imagefilledrectangle($image,$newXStart-(0.5 * $fontSize), $yStart-$fontSize-(0.5 * $fontSize), $newXStart + $currentWidth + (0.5 * $fontSize),$yStart+(0.5 * $fontSize), $background_color);
		    
	      }
	    }
	     imagettftext($image, $fontSize, 0, $newXStart, $yStart, $color, $font, $wordDetails['text']);
	    }else{	     
	      $widthDiff=($MaxWriteWidth-$wordWidth);
	    
	      $newXStart=$xStart+floor($widthDiff);
	    if($text_background == 1){
		// echo "in background condition";
		// echo "<pre>"; print_r($word); echo "</pre>";
		 
		 $currentWidth = $dimention['width'];
		
		 
		 if($currentWidth>2){
		     imagefilledrectangle($image,$newXStart-(0.5 * $fontSize), $yStart-$fontSize-(0.5 * $fontSize), $newXStart + $currentWidth + (0.5 * $fontSize),$yStart+(0.5 * $fontSize), $background_color);
		    
	      }
	    }
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
	  foreach ($inputStringArr as $key => $word){
              $previousCurrentWidth=$currentWidth;
	      $teststring = $ret.' '.$word;
	      $dimention = getStringBoxDimention($fontSize,$fontFace, $teststring);
	      $currentWidth = $dimention['width'];
	      $currentHeight = $dimention['height'];
	      if ($currentWidth > $width ){
	         if($key==0){
		    $multilineString[$counter]=array('text'=>trim($word),'width'=>$currentWidth);
		    $ret="";
		 }else if($ret==""){
		    $multilineString[$counter]=array('text'=>trim($word),'width'=>$previousCurrentWidth);
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
	  //echo $currentWidth;
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
 if(isset($_GET['method']) and $_GET['method']=='edit'){
  $tempsid    = session_id();
  $tempquery  = "select * from temp_overlay  where temp_overlay.id=$temp_overlay_id limit 1";
  $tempeditvalues =$db->query($tempquery);
  $tempresult = mysqli_fetch_array($tempeditvalues);
  if($tempresult){
   
   $tempidss      = $tempresult['id'];
   $tempcid   	  = $tempresult['cid'];
   $temppath      = stripslashes($tempresult['path']);
   $tname         = stripslashes($tempresult['tname']);
   $temptname     = stripslashes($tname);
   $tempimagepath = stripslashes($tempresult['imagepath']);
   $temptempid    = $tempresult['tempid'];
   $theatre_tempid= $tempresult['template_id'];
   
   
   $templine1    = stripslashes($tempresult['line1']);
   $templine2    = stripslashes($tempresult['line2']);
   $templine3    = stripslashes($tempresult['line3']);
   $templine4    = stripslashes($tempresult['line4']);
   $templine5    = stripslashes($tempresult['line5']);
   $templine6    = stripslashes($tempresult['line6']);
   $templine7    = stripslashes($tempresult['line7']);
   $templine8    = stripslashes($tempresult['line8']);
   $enddate = date('d-m-Y',stripslashes($tempresult['enddate']));
   $enddate_new = date('m-d-Y',stripslashes($tempresult['enddate']));
   
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
   
   $tempquery1  = "select * from theatre_template where id='$theatre_tempid'";
   $tempeditvalues1 =$db->query($tempquery1);
   $tempresult1 = mysqli_fetch_array($tempeditvalues1);
   
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
   
   $image_path=explode(".",$_REQUEST['edit_image_name']);
 
   $deleteimage="./overlay_video/thumb/".$image_path[0].".jpg";
   $deletetotalimage="./overlay_video/totalthumb/".$image_path[0].".jpg";
   $deletevideo="./overlay_video/".$_REQUEST['edit_image_name'];
   $deletepreview="./overlay_video/preview/".$_REQUEST['edit_image_name'];
   $_SESSION['deleteimage']=$deleteimage;
   $_SESSION['deletetotalimage']=$deletetotalimage;
   $_SESSION['deletevideo']=$deletevideo;
   $_SESSION['deletepreview']=$deletepreview;
   
  }
 
 }
 
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
#code for setting font
    #add by Himani Agarwal
  if($theatre_tempid!='' && $_GET['method']!='edit'){ 
   $tempquery  = "select * from theatre_template where id='$theatre_tempid'";
   $tempeditvalues =$db->query($tempquery);
   $tempresult = mysqli_fetch_array($tempeditvalues);
   if($tempresult){
    
      $temptname     = $tempresult['template_name'];
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
      
      ## setting all variables for writting  text on center of 2 points
      $text_center1 = intval(trim($tempresult['text_center1']));
      $text_center2 = intval(trim($tempresult['text_center2']));
      $text_center3 = intval(trim($tempresult['text_center3']));
      $text_center4 = intval(trim($tempresult['text_center4']));
      $text_center5 = intval(trim($tempresult['text_center5']));
      $text_center6 = intval(trim($tempresult['text_center6']));
      $text_center7 = intval(trim($tempresult['text_center7']));
      $text_center8 = intval(trim($tempresult['text_center8']));
            //echo "<pre>"; print_r($tempresult); echo "</pre>";
      //exit;
   }
  }


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Voeg informatie toe aan uw video</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="./css_pirobox/style_1/style.css"/>


<?php include('jsfiles.html')?>
<script type="text/javascript" src="./js/datetimepicker.js"></script>
 <!--<script src="http://code.jquery.com/jquery.js"></script>
   <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>-->
  <!-- <script type="text/javascript" src="./js/pirobox_extended.js"></script>-->
    <!-- <link rel="stylesheet" href="./css./themes/base/jquery.ui.combobox.css">
   <script src="./js/jquery.ui.combobox.js"></script>-->
<!--<script src="./js/jquery.searchabledropdown-1.0.8.min.js"></script>-->
<!--<script src="http://code.jquery.com/jquery.js"></script>-->



<script type="text/javascript">

$(document).ready(function() {

 
	    $('#csvdata').combobox({
               create: function (evt, ui) {
                 console.log(ui);
		 $('.ui-autocomplete-input').val('--Optioneel: selecteer een voorstelling--');
		 //alert("cc");
	       }
             });
});

$(document).ready(function() { 
    $('input#startoverlay').click(function() { 
        
     
    }); 
});

function showBlockUI(){
  $.blockUI({ 
            message: $('#domMessage'),
            css: { opacity: .9,
            padding: '20',
            textAlign: 'center',
            } 
          }); 
}



</script>


<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->
<script type="text/javascript">
 $(document).ready(function() {
    $().piroBox_ext({
        piro_speed : 900,
        bg_alpha : 0.1,
        piro_scroll : true //pirobox always positioned at the center of the page
    });
    
  
   $('#line1,#line2,#line3,#line4,#line5,#line6,,#line7,#line8').blur(function(){
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
   
   alert('U heeft het maximale aantal karakters bij een van de velden overschreden.');
   $.unblockUI();
  }else{
   showBlockUI();
  }
   return !maxlengthExceed;
 }
 
 
 function get_data_from_csv(id){
	
 var filetype=$('#filetype').val();
 var obj;
     $.ajax({  
              type: "POST", url: 'givecsvdata.php', data: {id : id, filetype: filetype},
              complete: function(data){
		  obj = $.parseJSON(data.responseText);
		  //alert(data.responseText);
		 //alert(obj.enddate);
		 $('#enddate').val(obj.enddate);
		 $('#enddate_new').val(obj.selected_date);
		 <?php if($_GET[show_cal]!="" || $_GET[show_cal]!=0):?>
		 NewCal('enddate_new','mmddyyyy','overlay');
		 <?php endif;?>
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
 function showcontent(id,templateId, type) { 
    document.getElementById('showcreate').style.display="block";
    var line1 = $('#line1').val();
    var line2 = $('#line2').val();
    var line3 = $('#line3').val();
    var line4 = $('#line4').val();
    var line5 = $('#line5').val();
    var line6 = $('#line6').val();
    var line7 = $('#line7').val();
    var line8 = $('#line8').val();
    var temp_overlay_id = $('#temp_overlay_id').val();
    var edit_image_name = $('#edit_image_name').val();
    var path = document.getElementById('imagepath').value; 
    var tid = document.getElementById('tempid').value; 
    var cid = document.getElementById('cid').value;
    var checkbox = $('#is_enddate').is(":checked")
    var enddate = document.getElementById('enddate_new').value;
    if(checkbox){
     window.location = 'create_overlay.php?path='+path+'&id='+tid+'&cid='+cid+'&theatre_tempid='+templateId+'&line1='+line1+'&line2='+line2+'&line3='+line3+'&line4='+line4+'&line5='+line5+'&line6='+line6+'&line7='+line7+'&line8='+line8+'&temp_overlay_id='+temp_overlay_id+"&edit_image_name="+edit_image_name+"&type="+type+"&enddate="+enddate+"&status=<?php echo isset($_GET[status]) ? $_GET[status] : ''; ?>"+"&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>&show_cal=<?php echo $_GET['show_cal']?>";
    }else{

    window.location = 'create_overlay.php?path='+path+'&id='+tid+'&cid='+cid+'&theatre_tempid='+templateId+'&line1='+line1+'&line2='+line2+'&line3='+line3+'&line4='+line4+'&line5='+line5+'&line6='+line6+'&line7='+line7+'&line8='+line8+'&temp_overlay_id='+temp_overlay_id+"&edit_image_name="+edit_image_name+"&type="+type+"&status=<?php echo isset($_GET[status]) ? $_GET[status] : ''; ?>"+"&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>&show_cal=<?php echo $_GET['show_cal']?>";
    }
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


 
function popitup(path,id) {
    var url='previewvideo.php?id='+id+'&path='+path+"&time="+new Date().getTime();
    var newwindow=window.open(url,'Preview','height=500,width=640,left=190,top=150,screenX=200,screenY=100');
}

function changefont(id1,id2) {
  var fonttype=id1.split(".");
  document.getElementById("font"+id2).innerHTML='<span style="font-family:'+fonttype[0]+'">Voorbeeld</span>';
}
function create_image_overlay(path, id, cid,show_cal,status,block){
      var enddate = $('#date_'+id).val();
      window.location = 'createimage_overlay.php?path='+path+'&id='+id+'&cid='+cid+'&show_cal=1&enddate='+enddate+'&status='+status+'&is_block='+block;
    }
    function create_video_overlay(path, id, cid,show_cal,status,block){
      var enddate = $('#date_'+id).val();
      window.location = 'create_overlay.php?path='+path+'&id='+id+'&cid='+cid+'&show_cal=1&enddate='+enddate+'&status='+status+'&is_block='+block;
    }


</script>

<style type="text/css">

        #alle_carrousels,#nieuw_bestand, #nieuwe_twitterfeed,#iets_nieuws{
		  <?php echo $css_color_rule;?>
	}

	#overzicht{display:block;color:#272727;background-color:#ffffff;border:none;}
	#alle_carrousels{font-size:14px;line-height:30px;}
	#maak_carrousel{font-size:14px;color: #b5b5b5;line-height:30px;}
	#nieuw{display:block;color:#ffffff;background-color:#2e3b;border:none;}
	#nieuw_bestand{font-size:14px;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
	#iets_nieuws{font-size:14px;line-height:30px;}
	#spreekuur{font-size:14px;line-height:30px;}
	.nav{float:right;}
	.main_container{margin-top:100px;}
	.single_row textarea {color: #272727;font-size: 15px;border: 1px solid #b5b5b5;}
</style>

</head>
<?php
if(isset($temptname) and $temptname!='' and isset($_GET['method'])){
?>
<body onload="showcontent(<?php echo $temptname;?>)">
<?php }else{?>
 <body>
 <?php } ?>
<div id="domMessage" style="display:none;">

<img style="margin:20px 0px 10px 0px;" src="img/<?php echo $color_scheme;?>/loader.gif" />
    <h3 style="margin:10px 10px 3px 10px;">De overlay wordt gemaakt. Een moment geduld a.u.b.</h3>
    <h5 style="margin:3px 10px 10px 10px">Sluit dit venster niet, deze boodschap verdwijnt vanzelf als de overlay klaar is.</h5>
    
</div> 

 
<!--------------- include header --------------->
   <?php include('header_overlay.html'); ?>
   
    <div class="main_container" style="margin-top:30px;">
  
         <?php if($cid=="" && !isset($_POST['submit'])) { ?>
      <div class="video_tab_new" style="margin-left:0px;"><a href='create_carrousel.php?id=<?php echo $cid; ?>&status=<?php echo isset($_GET[status]) ? $_GET[status] : ''; ?>&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : '0'; ?>&show_cal=<?php echo isset($_GET[show_cal]) ? $_GET[show_cal] : '1'; ?>'">Terug</a></div>
     <?php } else if(!isset($_POST['submit'])) { ?>
      <div class="video_tab_new" style="margin-left:0px;"><a style="margin-left:10px;" href='edit.php?id=<?php echo $cid; ?>&status=<?php echo isset($_GET[status]) ? $_GET[status] : ''; ?>&is_block=<?php echo isset($_GET[is_block]) ? $_GET[is_block] : ''; ?>'>Terug</a></div>

     <?php } ?>
 
        <?php echo $showvideo;
	/*$getAgeDotStatus="select agedot_status from theatre where status = '1'";
        $getAgeDotStatus=$db->query($getAgeDotStatus);
	$agedot_status = mysqli_fetch_array($getAgeDotStatus);
	$agedot_status = $agedot_status['agedot_status'];
	*/
	?>
	
	<div class="vertical_gallery" style="display:<?php echo $data_display; ?> ;">
        	<span style="font-size:17px; font-weight:bold; ">Kies een template</span>
		<br /><br />
           <div  class="blocks_container height_auto">
       <?php
        $srcFile="/var/www/html/narrowcasting/$_GET[path]";    
    ob_start();
    passthru("$ffmpeg_path -i $srcFile 2>&1");
    $output = ob_get_contents();    
    ob_end_clean();
    preg_match_all('/(\d+)x(\d+)/', $output, $matches);    
   $width = $matches[1][1];
   $height = $matches[2][1];
   
   if($width > $height){
     $landscape_portrait=0;
   }elseif($height > $width){
    $landscape_portrait=1;
   }else{
    $landscape_portrait=0;
   }
   
   // Temp disabled portrait_landscape function 
  // $getTemplates="select name_t,id,template_name,theatre_id from theatre_template where landscape_portrait= $landscape_portrait and theatre_id in (select id from theatre where status = '1') order by id";
	
	$getTemplates="select name_t,id,template_name,theatre_id from theatre_template where theatre_id in (select id from theatre where status = '1') order by id";
        $get_templates=$db->query($getTemplates);
        while($template_result =mysqli_fetch_array($get_templates)){
	 $template_name = $template_result['template_name'];
	 $template_id = $template_result['id']; 
	 $theatre_id = $template_result[theatre_id];
	 $put_text = $template_result['name_t']; 
	?>
	
	<form name="c_create" action="create_overlay.php?cid=<?php echo $cid; ?>&path=<?php echo $path; ?>&id=<?php echo $tempid; ?>&status=<?php echo $_GET['status']?>&is_block=<?php echo $_GET['is_block']?>&show_cal=<?php echo $_GET['show_cal']?>" method="POST" enctype="multipart/form-data" onsubmit="return checkvalidation()">
                <div class="block" id="block_overlay">
		 <?php if( ($theatre_tempid!=0 && ($theatre_tempid ==$template_id)) || ($theatre_tempid==0 && ($temptname ==$template_name)) ){$checked ="checked"; $flag=1;}else{$checked='';}  ?>
		    <p class="blockTitle"><?php echo $put_text;?></p>
                    <div class="block_img"><img src="./template/<?php echo $template_name;?>" alt=""/></div>
                    <div class="radioButton_sec"><input id='check_<?php echo $template_id;?>' type="radio" name="tname" value="<?php echo $template_name;?>" onclick="showcontent('<?php echo $theatre_id;?>','<?php echo $template_id;?>', '<?php echo $_GET['type']?>');" class="radioButton" <?php echo $checked; ?>/> </div>
		 
                    <div class="btn2"><a href="./template/<?php echo $template_name;?>" rel='single'  class='pirobox'>Preview</a></div>
		 
                    <div class="clear"></div>
                </div>
            	<?php } ?>
        </div>
       <!-- IMG BLOCKS END -->
       <?php
                $dd_query="select dropdown from theatre where status =1";
		$rst=$db->query($dd_query);
		$result_dd=mysqli_fetch_array($rst);
		$dropdown = $result_dd['dropdown'];
              if($dropdown!=""){
	       if($dropdown=="csv"){
		
	        $query_dd = "select * from csvimport";
	       }elseif($dropdown=="xml"){
		$query_dd = "select * from xmlimport";
	       }
	      $conn=$db->query($query_dd);
	      ?>
	    

<div id="dropdown_wrapper">
	     <select name="csvdata" id="csvdata" style="margin-top:200px;" onchange="get_data_from_csv(this.value)">
	     
	     <br /> <option value="">--Optioneel: selecteer een voorstelling--</option>
	      <?php
	      while($result_dd=mysqli_fetch_array($conn)){	      
	      ?>
	      <option value="<?php echo $result_dd['id']?>"><?php echo $result_dd['line1'].' '.$result_dd['line3']?></option>
	      <?php
	      }
	      ?>
	     </select>
<?php } ?>
</span>
	     
	     </div>
	      
	     <input type="hidden" name="filetype" id="filetype" value="<?php echo $dropdown; ?>" />
	  
	     
         <div class="overlay_fields">
	      <div class="overlay_wrap" id="with_agedot">		                
                
		  <div class="single_row"  style="display:<?php if(intval($status1)==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line1"><?php echo $label1;?>:</label></span>
		     
	             <?php if(intval($field_type1)==1){?>
                     <input type="text" name="line1" id="line1" style="width:270px;" maxlength="<?php echo $limit1;?>" value='<?php if(isset($templine1)){echo htmlentities($templine1, ENT_QUOTES, "UTF-8");}?>'  <?php if(strlen($templine1)>$limit1) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
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
		     <input type="hidden" name="limit1" value="<?php echo $limit1;?>">
		     <input type="hidden" name="status1" value="<?php echo $status1;?>">
		     
		     <?php if(strlen(trim($templine1))>$limit1) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
	      
		
		
		
		
	        <div class="single_row"  style="display:<?php if($status2==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line2"><?php echo $label2;?>:</label></span>
		     
	             <?php if(intval($field_type2)==1){?>
                     <input type="text" name="line2" id="line2" style="width:270px;" maxlength="<?php echo $limit2;?>" value='<?php if(isset($templine2)){echo htmlentities($templine2, ENT_QUOTES, "UTF-8");}?>'  <?php if(strlen($templine2)>$limit2) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" style="width:270px; vertical-align:middle;" name="line2" id="line2" cols="33" maxlength="<?php echo $limit2;?>" <?php if(strlen($templine2)>$limit2) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine2)){echo trim($templine2);}?></textarea>
		     
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
		     <input type="hidden" name="limit2" value="<?php echo $limit2;?>">
		     <input type="hidden" name="status2" value="<?php echo $status2;?>">
		     
		     <?php if(strlen($templine2)>$limit2) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		<div class="single_row"  style="display:<?php if($status3==1) { echo 'block';  }else {echo 'none';} ?>">
                     <span style="width:145px;float:left;"><label for="line3"><?php echo $label3;?>:</label></span>
		     
	             <?php if(intval($field_type3)==1){?>
                     <input type="text" name="line3" id="line3" style="width:270px;" maxlength="<?php echo $limit3;?>" value='<?php if(isset($templine3)){echo htmlentities($templine3, ENT_QUOTES, "UTF-8");}?>'  <?php if(strlen($templine3)>$limit3) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		      <textarea rows="4" style="width:270px; vertical-align:middle;" name="line3" id="line3" cols="33" maxlength="<?php echo $limit3;?>" <?php if(strlen($templine3)>$limit3) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine3)){echo trim($templine3);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color3" class="color" size="6" readonly="" value="<?php if(isset($tempcolor3)){echo $tempcolor3;}?>" >
                     <label for="line1">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font3" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont3); ?>
                     </select>&nbsp;&nbsp;
		       <label for="line1">Uitlijning:</label>
		     <select name="text_center3"   style="width:85px;">
                       <?php echo text_center_options($text_center3);?>
                     </select>&nbsp;&nbsp;
		     <input type="hidden" name="limit3" value="<?php echo $limit3;?>">
		     <input type="hidden" name="status3" value="<?php echo $status3;?>">
		     
		     <?php if(strlen($templine3)>$limit3) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
		<div class="single_row"  style="display:<?php if($status4==1) { echo 'block';  }else {echo 'none';} ?>">
                     <span style="width:145px;float:left;"><label for="line4"><?php echo $label4;?>:</label></span>
		     
	             <?php if(intval($field_type4)==1){?>
                     <input type="text" name="line4" id="line4" style="width:270px;" maxlength="<?php echo $limit4;?>" value="<?php if(isset($templine4)){echo htmlentities($templine4, ENT_QUOTES, "UTF-8");}?>"  <?php if(strlen($templine4)>$limit4) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea style="width:270px; vertical-align:middle;" rows="4" name="line4" id="line4" cols="33" maxlength="<?php echo $limit4;?>" <?php if(strlen($templine4)>$limit4) {?> class="timeline_length_error" <?php }?>><?php if(isset($templine4)){echo trim($templine4);}?></textarea>
		     <?php }?>
		     <label for="color">Kleur:</label>
                     <input type="text" name="color4" class="color" size="6" readonly="" value="<?php if(isset($tempcolor4)){echo $tempcolor4;}?>" >
                     <label for="line4">&nbsp;Lettertype:</label>
                     <select style="width:140px;" name="font4" onchange="changefont(this.value,1);">
                        <?php echo font_listing($tempfont4); ?>
                     </select>&nbsp;&nbsp;
		       <label for="line1">Uitlijning:</label>
		     <select name="text_center4"  style="width:85px;">
                       <?php echo text_center_options($text_center4);?>
                     </select>&nbsp;&nbsp;
		     <input type="hidden" name="limit4" value="<?php echo $limit4;?>">
		     <input type="hidden" name="status4" value="<?php echo $status4;?>">
		     
		     <?php if(strlen($templine4)>$limit4) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		
		<div class="single_row"  style="display:<?php if($status5==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line5"><?php echo $label5;?>:</label></span>
		     
	             <?php if(intval($field_type5)==1){?>
                     <input type="text" name="line5" id="line5" style="width:270px;" maxlength="<?php echo $limit5;?>" value='<?php if(isset($templine5) ){echo htmlentities($templine5, ENT_QUOTES, "UTF-8");}?>'  <?php if(strlen($templine5)>$limit5) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		    <textarea rows="4" style="width:270px; vertical-align:middle;" name="line5" id="line5" cols="33" maxlength="<?php echo $limit5;?>" <?php if(strlen($templine5)>$limit5) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine5)){echo trim($templine5);}?></textarea>
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
		     <input type="hidden" name="limit5" value="<?php echo $limit5;?>">
		     <input type="hidden" name="status5" value="<?php echo $status5;?>">
		     
		     <?php if(strlen($templine5)>$limit5) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>
		
		
		
		<div class="single_row"  style="display:<?php if($status6==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
                     <span style="width:145px;float:left;"><label for="line6"><?php echo $label6;?>:</label></span>
		     
	             <?php if(intval($field_type6)==1){?>
                     <input type="text" name="line6" id="line6" style="width:270px;" maxlength="<?php echo $limit6;?>" value='<?php if(isset($templine6) ){echo htmlentities($templine6, ENT_QUOTES, "UTF-8");}?>'  <?php if(strlen($templine6)>$limit6) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
                     <?php }else {?>
		     <textarea rows="4" style="width:270px; vertical-align:middle;" name="line6" id="line6" cols="33" maxlength="<?php echo $limit6;?>" <?php if(strlen($templine6)>$limit6) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine6)){echo trim($templine6);}?></textarea>
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
		     <input type="hidden" name="limit6" value="<?php echo $limit6;?>">
		     <input type="hidden" name="status6" value="<?php echo $status6;?>">
		     
		     <?php if(strlen($templine6)>$limit6) {?>
		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
                     <?php }?>
                    <br><br>
                </div>                    
              
              
			  
			  
	  		<div class="single_row"  style="display:<?php if($status7==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
	                       <span style="width:145px;float:left;"><label for="line7"><?php echo $label7;?>:</label></span>
		     
	  	             <?php if(intval($field_type7)==1){?>
	                       <input type="text" name="line7" id="line7" style="width:270px;" maxlength="<?php echo $limit7;?>" value='<?php if(isset($templine7) ){echo htmlentities($templine7, ENT_QUOTES, "UTF-8");}?>'  <?php if(strlen($templine7)>$limit7) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
	                       <?php }else {?>
	  		     <textarea rows="4" style="width:270px; vertical-align:middle;" name="line7" id="line7" cols="33" maxlength="<?php echo $limit7;?>" <?php if(strlen($templine7)>$limit7) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine7)){echo trim($templine7);}?></textarea>
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
	  		     <input type="hidden" name="limit7" value="<?php echo $limit7;?>">
	  		     <input type="hidden" name="status7" value="<?php echo $status7;?>">
		     
	  		     <?php if(strlen($templine7)>$limit7) {?>
	  		     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
	                       <?php }?>
	                      <br><br>
	                  </div> 
			  
					<div class="single_row"  style="display:<?php if($status8==1) { $display = 'block';  }else {$display = 'none';} echo $display;?>">
			                     <span style="width:145px;float:left;"><label for="line8"><?php echo $label8;?>:</label></span>
		     
				             <?php if(intval($field_type8)==1){?>
			                     <input type="text" name="line8" id="line8" style="width:270px;" maxlength="<?php echo $limit8;?>" value='<?php if(isset($templine8) ){echo htmlentities($templine8, ENT_QUOTES, "UTF-8");}?>'  <?php if(strlen($templine8)>$limit8) {?> class="timeline_length_error" <?php }?>>&nbsp;&nbsp;
			                     <?php }else {?>
					     <textarea rows="4" style="width:270px; vertical-align:middle;" name="line8" id="line8" cols="33" maxlength="<?php echo $limit8;?>" <?php if(strlen($templine8)>$limit8) {?> class="timeline_length_error" <?php }?> ><?php if(isset($templine8)){echo trim($templine8);}?></textarea>
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
					     <input type="hidden" name="limit8" value="<?php echo $limit8;?>">
					     <input type="hidden" name="status8" value="<?php echo $status8;?>">
		     
					     <?php if(strlen($templine8)>$limit8) {?>
					     <div class="timeline_inputsize_error_msg"><p>U heeft het maximum aantal karakters van dit veld overschreden.</p></div>
			                     <?php }?>
			                    <br><br>
			                </div>
					<?php
					if($_GET['show_cal']==1):
					?>
				<div id="calendar_container">	
						<label style="float:left; width:145px;">Einddatum:<br />(item verdwijnt de volgende dag)</label>	
			         <input type="checkbox" name="is_enddate" id="is_enddate"  <?php if($_GET['enddate']!=''){echo 'checked="checked"';}?>/>
				 <div id="cal1Container"></div>
				</div>
				<?php endif; ?>
			   <div class="clear"></div>
			  </div>
			  
               
	    </div>
        <!-- input fields end -->
        <?php $geteditTemplate="select t.id from theatre_template t, temp_overlay a where t.template_name = a.tname and a.temp_file_name='".$_GET[edit_image_name]."'";
	$get_selected_template = $db->query($geteditTemplate);
	$selected_template_result = mysqli_fetch_array($get_selected_template);
	$selected_template=$selected_template_result[id];
	

	?>
	 <?php
	    $ed=explode("-",$_GET['enddate']);
	    $enddate=$ed['1']."-".$ed['0']."-".$ed['2'];
	    ?>
   		<!-- START FOOTER -->
	<?php if($flag=='1') $display_button='block'; else $display_button='none';?>
    	<div class="footer" style="display:<?php echo $display_button;?>;" id="showcreate">
	    <input type="hidden" name="imagepath" value="<?php echo $path; ?>" id="imagepath">
	   <input type="hidden" name="tempid" value="<?php echo $tempid; ?>" id="tempid">
	  <input type="hidden" name="cid" value="<?php echo $cid; ?>" id="cid">
	      <input type="hidden" name="temp_dispaly" value="temp" id="temp_dispaly">
	     <!--<input type="hidden" name="agedot_status" value="<?php echo $agedot_status; ?>"> -->
	     <input type="hidden" name="theatre_id" value="<?php echo $theatre_id; ?>">
	     <input type="hidden" name="temp_overlay_id" value="<?php echo $temp_overlay_id; ?>" id="temp_overlay_id">
	      <input type="hidden" name="edit_image_name" value="<?php echo $edit_image_name; ?>" id="edit_image_name">
	      <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" id="type">
	      <input type="hidden" name="enddate" id="enddate" value="<?php if($_GET['enddate']==''){echo date('d-m-Y');}else{echo $enddate;} ?>" />
	    <input type="hidden" name="enddate_new" id="enddate_new" value="<?php if($_GET['enddate']==''){echo date('m-d-Y');}else{echo $_GET['enddate'];} ?>"/>
	      <?php
	      if($theatre_tempid=="" && $_GET['theatre_tempid']==""){
	       $theatre_tempid=$selected_template;
	      }elseif($_GET['theatre_tempid']!=''){
	       $theatre_tempid=$_GET['theatre_tempid'];
	      }else{
	       $theatre_tempid=$selected_template;
	      }
	      ?>
	      <input type="hidden" name="selected_template_id" value="<?php echo $theatre_tempid; ?>" id="selected_template_id">
	      
            <div class="footer_tab"><input type="submit" name="submit" id="startoverlay" value="Maak overlay"></div>
	    <!--<div class="column left">
		
		<p id="datePicked"> </p>
	</div>-->
	
        </div>
      </form>
    	<!-- END FOOTER -->
    </div>
  
</body>
</html>
