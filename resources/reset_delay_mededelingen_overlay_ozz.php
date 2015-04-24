<?php

include('config/database.php');
require_once('thumb-functions.php');
$db = new Database;
$query ="update temp_delay_mededelingen set ";
$query .="is_emergency1=0, ";
$query .="is_emergency2=0, ";
$query .="is_emergency3=0, ";
$query .="is_emergency4=0, ";
$query .="is_emergency5=0, ";
$query .="is_emergency6=0, ";
$query .="is_emergency7=0, ";
$query .="is_emergency8=0, ";

$query .="not_present_1=0, ";
$query .="not_present_2=0, ";
$query .="not_present_3=0, ";
$query .="not_present_4=0, ";
$query .="not_present_5=0, ";
$query .="not_present_6=0, ";
$query .="not_present_7=0, ";
$query .="not_present_8=0, ";

$query .="minutes_1=0, ";
$query .="minutes_2=0, ";
$query .="minutes_3=0, ";
$query .="minutes_4=0, ";
$query .="minutes_5=0, ";
$query .="minutes_6=0, ";
$query .="minutes_7=0, ";
$query .="minutes_8=0 ";

$conn=$db->query($query);
if($conn){
    echo "temp_delay_mededelingen table updated !!";
}else{
    echo "temp_delay_mededelingen table not updated !!";
}

$mededelingen_query="select a.*, b.* from temp_delay_mededelingen as a,  delay_mededelingen as b where a.template_id=b.id";
$mededelingen_rs = $db->query($mededelingen_query);
 $num = mysql_num_rows($mededelingen_rs);
while($getCordinates = mysql_fetch_array($mededelingen_rs)){
    
    //echo "<pre>"; print_r($getCordinates); echo "</pre>";
    $template_name=$getCordinates['template_name'];
    $name_t_val	 = str_replace(" ","_",$getCordinates['name_t']);
    $name_t_val	 = str_replace(".","_",$getCordinates['name_t']);
    $template_nameArray = explode('.',$template_name);
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
   
    $imagetransparent='/var/www/html/narrowcasting/mededelingen_template/'.$template_name;   
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
    
    
        $delay_text_1 = $getCordinates['non_delay_text_1'];
        $delay_text_2 = $getCordinates['non_delay_text_2'];
        $delay_text_3 = $getCordinates['non_delay_text_3'];
        $delay_text_4 = $getCordinates['non_delay_text_4'];
        $delay_text_5 = $getCordinates['non_delay_text_5'];
        $delay_text_6 = $getCordinates['non_delay_text_6'];
        $delay_text_7 = $getCordinates['non_delay_text_7'];
        $delay_text_8 = $getCordinates['non_delay_text_8'];
        
        $label_1 = $getCordinates['label1'];
        $label_2 = $getCordinates['label2'];
        $label_3 = $getCordinates['label3'];
        $label_4 = $getCordinates['label4'];
        $label_5 = $getCordinates['label5'];
        $label_6 = $getCordinates['label6'];
        $label_7 = $getCordinates['label7'];
        $label_8 = $getCordinates['label8'];
        
        $ImageWidth=$width_1;
        $ImageHeight=$height_1;
        $LINE_MARGIN=array($line_height1,$line_height2,$line_height3,$line_height4,$line_height5,$line_height6,$line_height7,$line_height8);
	$previousField=0;
        
        if($status1==1){
	  
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
	     writeStringOnImage($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0);
	   }else{
	     writeStringOnImageCentre($inputStringArray,$x1,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0);
	   }
	   
	   $previousField=0;
	
	 	  
	  writeLabelOnImage($label_1,$x1-740,$x2_1,$y1,$im,$color_1,$font1,$font_size1,0);
	  
	}
        
        
        if($status2==1){
	
	
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
	    writeStringOnImage($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x2,$x2_2,$y2,$im,$color_2,$font2,$font_size2,1);
	  }
	  
	  $previousField=1;
      
       
       writeLabelOnImage($label_2,$x2-740,$x2_2,$y2,$im,$color_2,$font2,$font_size2,0);
       
       }
       
       if($status3==1){
	
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
	    writeStringOnImage($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x3,$x2_3,$y3,$im,$color_3,$font3,$font_size3,2);
	  }
	   
	  $previousField=2;
	
	 writeLabelOnImage($label_3,$x3-740,$x2_3,$y3,$im,$color_3,$font3,$font_size3,0);
	}
        
        if($status4==1){
	 
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
	    writeStringOnImage($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x4,$x2_4,$y4,$im,$color_4,$font4,$font_size4,3);
	  }
	  
	  $previousField=3;
	
	writeLabelOnImage($label_4,$x4-740,$x2_4,$y4,$im,$color_4,$font4,$font_size4,0);
	}
        
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
	    writeStringOnImage($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x5,$x2_5,$y5,$im,$color_5,$font5,$font_size5,4);
	  }
	 
	  $previousField=4;
          writeLabelOnImage($label_5,$x5-740,$x2_5,$y5,$im,$color_5,$font5,$font_size5,0);
	}
        
        if($status6==1){
	
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
	    writeStringOnImage($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x6,$x2_6,$y6,$im,$color_6,$font6,$font_size6,5);
	  }
	  
	  $previousField=5;
	
	writeLabelOnImage($label_6,$x6-740,$x2_6,$y6,$im,$color_6,$font6,$font_size6,0);
	}
        
        if($status7==1){
	
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
	    writeStringOnImage($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x7,$x2_7,$y7,$im,$color_7,$font7,$font_size7,6);
	  }
	  
	  $previousField=6;
	
	writeLabelOnImage($label_7,$x7-740,$x2_7,$y7,$im,$color_7,$font7,$font_size7,0);
	}
        
        if($status8==1){
	
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
	    writeStringOnImage($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7);
	  }else{
	    writeStringOnImageCentre($inputStringArray,$x8,$x2_8,$y8,$im,$color_8,$font8,$font_size8,7);
	  }
	  
	  $previousField=7;
	
	writeLabelOnImage($label_8,$x8-740,$x2_8,$y8,$im,$color_8,$font8,$font_size8,0);
	}
        
        
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
    
  
    
     imagepng($source_gd_image,'/var/www/html/narrowcasting/delay_mededelingen/'.$name_t_val.".png");
	 
	 if ($landscape_portrait==0) {
	     createsingleThumb("./delay_mededelingen/".$name_t_val.".png","./delay_mededelingen/thumb/".$name_t_val.".png",135,135,'crop_right');
	 }
		    else {
			 createsingleThumb("./delay_mededelingen/".$name_t_val.".png","./delay_mededelingen/thumb/".$name_t_val.".png",135,135,'crop_top');
	}

   }
   
  
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
