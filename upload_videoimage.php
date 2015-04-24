<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(0);
ini_set('max_execution_time', 360000000);
ini_set('upload_max_filesize', '500M');
include('config/database.php');
include('color_scheme_setting.php');
include('thumb-functions.php');
require_once("resize-class.php"); 
require_once("config/get_theatre_label.php"); 
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}

   $ffmpeg_path = $db->ffmpeg_path;

if($_REQUEST['uploading_file_path']!="" && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
   //echo $_REQUEST['uploading_file_path'];
   $filePath=$_REQUEST['uploading_file_path'];
   $filePathArray=explode("/",$filePath);
   $uploadDirectory=$filePathArray[0];
  // echo $filePathArray[1].'<br>';
 
   $getfilename=$filePathArray[1];
   $get_filename=explode(".",$getfilename);
 
   $newfilename_1= stripcslashes(preg_replace("/[^A-Za-z0-9]/", '_', $get_filename[0]));
   
  
   if($get_filename[1]=="JPG") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="Jpg") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="jpeg") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="JPEG") {
    $newfilename1=$newfilename_1.".jpg";
} else if($get_filename[1]=="Jpeg") {
 $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="PNG") {
    $newfilename1=$newfilename_1.".png";
} else if($get_filename[1]=="Png") {
 $newfilename1=$newfilename_1.".png";
   } else if($get_filename[1]=="MOV") {
    $newfilename1=$newfilename_1.".mov";
   } else if($get_filename[1]=="MP4") {
    $newfilename1=$newfilename_1.".mp4";
   } else if($get_filename[1]=="GIF") {
    $newfilename1=$newfilename_1.".gif";
} else if($get_filename[1]=="FLV") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Flv") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="swf") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Swf") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="SWF") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="avi") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Avi") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="avi") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="wmv") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Wmv") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="WMV") {
 $newfilename1=$newfilename_1.".mov";
   } else {
    $newfilename1=$newfilename_1.".".$get_filename[1];
   }
 
   if(file_exists("/var/www/html/narrowcasting/$uploadDirectory/$newfilename1")){
    echo 'yes';
     die;
    
   }else{
    echo 'no';
    die;
   }
}



//$max_upload_size = min(let_to_num(ini_get('post_max_size')), let_to_num(ini_get('upload_max_filesize')));

//echo "Maximum upload file size is ".($max_upload_size/(1024*1024))."MB.";
 $db = new Database;
// list of actual directory in root directory

 
	   $actualDirectory=array(
	       'commercials'  =>'commercials',		       
	       'images'        =>'images',
	      // 'mededelingen' =>'mededelingen',
	       'promotie'     =>'promotie',		       
	       'upload'       =>'upload',
   
	   	'videos'       =>'videos'
  
	      );
   
    ## get all directory name from db
    $theatreDetailsSql  = "select * from theatre where status=1 limit 1";
    $theatreDetails =$db->query($theatreDetailsSql);
    $theatreDetails = mysqli_fetch_array($theatreDetails);
    $directoryMappings=array();
    if(is_array($theatreDetails) && count($theatreDetails)){	
       $directoryMappings[$actualDirectory['upload']]       =$theatreDetails['upload'];	 
       $directoryMappings[$actualDirectory['commercials']]  =$theatreDetails['commercials'];			 
       $directoryMappings[$actualDirectory['images']]       =$theatreDetails['images'];		     
      // $directoryMappings[$actualDirectory['mededelingen']] =$theatreDetails['mededelingen'];			 
       $directoryMappings[$actualDirectory['promotie']]     =$theatreDetails['promotie'];
	    if($theatreDetails[is_poster]==0){
       $directoryMappings[$actualDirectory['videos']]       =$theatreDetails['videos'];
   }
     }
   //echo "<pre>";print_r($directoryMappings);
    
  function createSelectDirOption(){
     global $directoryMappings;
  //   $options="<option value=''>-Kies de doelmap-</option>";
     $option_data=$directoryMappings;
     foreach ($option_data  as $label=>$optionValue) {
      $options.="<option value='$label'>$optionValue</option>";
     }
    return $options;
  }


//---------------------------Code used for upload video and images ------------------------------- 
 $error_message="";
 $sid=session_id();

 //print_r($_POST);die;
 if(isset($_POST['submit'])) {
 
   $upload_dir=trim($_POST['upload_dir']);
   $target_path = "/var/www/html/narrowcasting/$upload_dir/";
  
   foreach($_FILES["uploadfile"]["name"] as $key=>$val){
    $getfilename[]=basename($val);
   }
   
   foreach($getfilename as $key=>$val){
    $get_filename[]=explode(".",$val);
   }
   
  // exit;
  // $get_filename=explode(".",$getfilename);
   $array_file_type_image = array('jpg', 'Jpg', 'JPG', 'jpeg', 'Jpeg', 'JPEG', 'png', 'PNG', 'Png');
   $array_file_type_jpg = array('jpg', 'Jpg', 'JPG', 'jpeg', 'Jpeg', 'JPEG');
   $array_file_type_png = array('png','PNG', 'Png');
   $array_file_type_video = array('mp4', 'mov', 'flv', 'Mp4', 'MP4', 'Mov', 'MOV', 'Flv', 'FLV', 'SWF', 'Swf', 'swf', 'avi', 'Avi', 'AVI', 'wmv', 'Wmv', 'WMV');
   //$char_search_array=array('!','@','#','$','&','�','.');
   //$newfilename_1=str_replace($char_search_array,"_",$get_filename[0]);
   foreach($get_filename as $key=>$val){
    $newfilename[]=stripcslashes(preg_replace("/[^A-Za-z0-9]/", '_', $val[0]));
    $ext[]=$val[1];
   }
 
  // exit;
  // $newfilename_1= stripcslashes(preg_replace("/[^A-Za-z0-9]/", '_', $get_filename[0]));
  
  foreach($get_filename as $key=>$val){
  // echo $ext[$key];
   //exit;
      switch ($ext[$key]) {
       case 'JPG':
           $newfilename1[]=$newfilename[$key].".jpg";
           break;
       case 'jpg':
           $newfilename1[]=$newfilename[$key].".jpg";
           break;
       case 'JPEG':
           $newfilename1[]=$newfilename[$key].".jpg";
           break;
       case 'jpeg':
           $newfilename1[]=$newfilename[$key].".jpg";
           break;
       case 'PNG':
           $newfilename1[]=$newfilename[$key].".png";
           break;
       case 'png':
           $newfilename1[]=$newfilename[$key].".png";
           break;
       case 'GIF':
           $newfilename1[]=$newfilename[$key].".gif";
           break;   
       case 'MOV':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'mov':
           $newfilename1[]=$newfilename[$key].".mov";
           break;   
       case 'MP4':
           $newfilename1[]=$newfilename[$key].".mp4";
           break;
       case 'mp4':
           $newfilename1[]=$newfilename[$key].".mp4";
           break;
       case 'Mp4':
           $newfilename1[]=$newfilename[$key].".mp4";
           break;   
       case 'FLV':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'flv':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'Flv':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'SWF':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'Swf':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'swf':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'AVI':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'Avi':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'avi':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'WMV':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'Wmv':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       case 'wmv':
           $newfilename1[]=$newfilename[$key].".mov";
           break;
       default:
           $newfilename1[]=$newfilename[$key].".".$ext[$key];
    }
  }
 
 foreach($newfilename1 as $key=>$val){
  if(in_array($ext[$key], $array_file_type_image) == true && $_FILES["uploadfile"]["size"][$key]>10000000)  {
    $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : U kunt geen afbeeldingen van meer dan 10MB uploaden.</span></div>";
   } else if(in_array($ext[$key], $array_file_type_video) == true && $_FILES["uploadfile"]["size"][$key]>500000000) {
    $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : U kunt geen video's van meer dan 500MB uploaden.</span></div>";
   }
   else if(in_array($ext[$key], $array_file_type_image)==true && in_array($upload_dir, $actualDirectory)==true && $upload_dir=="videos") {
    $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>U kunt geen afbeeldingen naar de 'Video' map uploaden.</span></div>";
   }
   else if(in_array($ext[$key], $array_file_type_video)==true && in_array($upload_dir, $actualDirectory)==true && $upload_dir=="images") {
    $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : U kunt geen video's naar de 'Afbeeldingen' map uploaden.</span></div>";
   }
   else if(!in_array($upload_dir, $actualDirectory)) {
    $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>De map kan niet worden gevonden.</span></div>";
   }
   else if(in_array($ext[$key], $array_file_type_video)==false && in_array($ext[$key], $array_file_type_image)==false) {
    $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : U kunt alleen jpg, png, mov of mp4 bestanden uploaden.</span></div>";
   }
   else{
  
   // exit;
    if(move_uploaded_file($_FILES['uploadfile']['tmp_name'][$key],"/var/www/html/narrowcasting/tmp/$newfilename1[$key]")){
    
     //exit;
      $new_path=str_replace("./","",$target_path);
      $new_path=$new_path.$newfilename[$key].".".$ext[$key];
      
      $file_getsize=getimagesize("/var/www/html/narrowcasting/tmp/$newfilename1[$key]");
      
      
      if(in_array($ext[$key], $array_file_type_image) == true && $file_getsize[0]>7000) {
     // echo "<pre>"; print_r($file_getsize); echo "</pre>";
     // exit;
      $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : U kunt geen afbeelding die breder is dan 7000 pixels uploaden.</span></div>";
      @unlink($target_path.$newfilename1[$key]);
     } else if(in_array($ext[$key], $array_file_type_image) == true && $file_getsize[1]>7000) {
      $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : U kunt geen afbeelding die hoger is dan 7000 pixels uploaden.</span></div>";
      @unlink($target_path.$newfilename1[$key]);
     } else if(in_array($ext[$key], $array_file_type_jpg) == true && $file_getsize['channels']!=3) {
      $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : U dient dit bestand op te slaan als RGB (waarschijnlijk is deze nu CMYK).</span></div>";
      @unlink($target_path.$newfilename1[$key]);
     } else {
      
        $newfilename_video=explode(".",$newfilename1[$key]);
	 if (in_array($ext[$key], $array_file_type_image) == false) {
        
	    @unlink("/var/www/html/narrowcasting/$upload_dir/thumb/$newfilename_video[0].jpg");
           $command = "".$ffmpeg_path."  -i /var/www/html/narrowcasting/tmp/".$newfilename1[$key]." -vcodec libx264 -b:v 10M -r 25 -acodec libfaac -vf scale=\"'if(gt(a,1920/1920),1920,-1)':'if(gt(a,1920/1920),-1,1920)'\" ".$new_path." -y";
           
	
	exec($command);
        $newfilename_video=explode(".",$newfilename1[$key]);
        @unlink("/var/www/html/narrowcasting/$upload_dir/thumb/$newfilename_video[0].jpg");
       
        createsingle_video_Thumb("$upload_dir/$newfilename1[$key]","$upload_dir/thumb/",135,135);
       }else{
       
       createsingleThumb("/var/www/html/narrowcasting/tmp/$newfilename[$key].$ext[$key]","./$upload_dir/$newfilename[$key].$ext[$key]",1920,1920,'custom');
        if($_POST['x']!='' && $_POST['y'] !='' && $_POST['w'] !='' && $_POST['h']!=''){
        
       $valid_exts = array('jpeg', 'jpg', 'png', 'gif','JPG','Jpg');
       $max_file_size = 200 * 1024; #200kb 
       $nw = $_POST['w'];
       $nh = $_POST['h'];
      //echo "<pre>"; print_r($_POST); echo "</pre>";

     
                                       
                   $src= "/var/www/html/narrowcasting/tmp/$newfilename1[$key]";
			 if (in_array($ext[$key], $valid_exts)) {
                         
                         	$path = "/var/www/html/narrowcasting/$upload_dir/$newfilename1[$key]";
					//$size = getimagesize($_FILES['uploadfile']['tmp_name']);
                                        $x = (int) $_POST['x'];
					$y = (int) $_POST['y'];
					$w = (int) $_POST['w'] ;
					$h = (int) $_POST['h'] ;
					
                                       if($ext[$key]=='png'){                                        
                                        $vImg = imagecreatefrompng($src);
                                       }else if($ext[$key]=='jpg' || $ext[$key]=='jpeg'){
                                        $vImg = imagecreatefromjpeg($src);
                                       }else if($ext[$key]=='gif'){
                                        $vImg = imagecreatefromgif($src);
                                       }else{
                                       
                                       }
                                       
					$dstImg = imagecreatetruecolor($nw, $nh);
                                       
					imagecopyresampled($dstImg, $vImg, 0, 0, $x, $y, $nw, $nh, $w, $h);
                                        if($ext[$key]=='jpg' || $ext[$key]=='jpeg'){
					imagejpeg($dstImg, $path);
                                        }elseif($ext[$key]=='png'){
                                         imagepng($dstImg, $path);
                                        }elseif($ext[$key]=='gif'){
                                         imagegif($dstImg, $path);
                                        }
                                        //echo "asdasd";
                                       $uploaded_image_size = getimagesize("/var/www/html/narrowcasting/$upload_dir/$newfilename1[$key]");
                                      //echo "<pre>"; print_r($uploaded_image_size); echo "</pre>";
                                       $minimum_size=1920;
                                      
                                       if($uploaded_image_size[0] > $minimum_size){                                       
                                        createsingleThumb("/var/www/html/narrowcasting/$upload_dir/$newfilename1[$key]","./$upload_dir/$newfilename1[$key]",1920,1920,'custom');
                                       
                                      }
				}
	
       // exit;
         #crop image end here
         
   }
  // createsingleThumb("/var/www/html/narrowcasting/$upload_dir/$newfilename1[$key]","./$upload_dir/$newfilename1[$key]",1920,1920,'custom');
       }
        
        
  
    createsingleThumb("/var/www/html/narrowcasting/$upload_dir/$newfilename1[$key]","/var/www/html/narrowcasting/$upload_dir/thumb/$newfilename1[$key]", 135, 135,'custom');
    $error_message[]="<div class='upload_success'><img src='./img/error_transparant.png' style='margin-right:8px;'/><span>$newfilename[$key].$ext[$key] : Uw bestand is succesvol geupload. U kunt het bestand nu gebruiken in uw carrousels.</span></div>";
     }
      
     
    }else{
      $error_message[]="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>Er is iets fout gegaan bij het uploaden. Mocht het probleem zich blijven voordoen kunt u contact opnemen met Pandora Producties (info@pandoraproducties of 020 8200162).</span></div>";
    }
    
   }
 
 }
 
  }  /*if($ext[$key]=="JPG"){
    $newfilename1[]=$newfilename[$key].".jpg";
   }elseif($ext[$key]=="jpg"){
    $newfilename1[]=$newfilename[$key].".jpg";
   }
  }
   if($get_filename[1]=="JPG") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="Jpg") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="jpeg") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="JPEG") {
    $newfilename1=$newfilename_1.".jpg";
} else if($get_filename[1]=="Jpeg") {
 $newfilename1=$newfilename_1.".jpg";
   } else if($get_filename[1]=="PNG") {
    $newfilename1=$newfilename_1.".png";
} else if($get_filename[1]=="Png") {
 $newfilename1=$newfilename_1.".png";
   } else if($get_filename[1]=="MOV") {
    $newfilename1=$newfilename_1.".mov";
   } else if($get_filename[1]=="MP4") {
    $newfilename1=$newfilename_1.".mp4";
   } else if($get_filename[1]=="GIF") {
    $newfilename1=$newfilename_1.".gif";
} else if($get_filename[1]=="FLV") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Flv") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="flv") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="SWF") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Swf") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="swf") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="AVI") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Avi") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="avi") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="WMV") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="Wmv") {
 $newfilename1=$newfilename_1.".mov";
} else if($get_filename[1]=="wmv") {
 $newfilename1=$newfilename_1.".mov";
   } else {
    $newfilename1=$newfilename_1.".".$get_filename[1];
   }
   */
   # Crop Image code starts here
   /*foreach($_FILES["uploadfile"]["tmp_name"] as $key=>$val){
    
    if(move_uploaded_file($val,"/var/www/html/narrowcasting/tmp/$newfilename[$key].$ext[$key]")){
      $new_path=str_replace("./","",$target_path);
      $new_path=$new_path.$newfilename[$key].$ext[$key];
      $file_getsize[]=getimagesize("/var/www/html/narrowcasting/tmp/$newfilename[$key].$ext[$key]");
      if($_POST['x']!='' && $_POST['y'] !='' && $_POST['w'] !='' && $_POST['h']!=''){
       $valid_exts = array('jpeg', 'jpg', 'png', 'gif');
       $max_file_size = 200 * 1024; #200kb 
       $nw = $_POST['w'];
       $nh = $_POST['h'];
      //echo "<pre>"; print_r($_POST); echo "</pre>";

   if ( isset($_FILES['uploadfile']) ) {
                                       
                    $src= "/var/www/html/narrowcasting/tmp/$newfilename1";
			//echo "<pre>"; print_r($ext); echo "</pre>";
			 if (in_array($ext[1], $valid_exts)) {
					$path = "/var/www/html/narrowcasting/$upload_dir/$newfilename1";
					//$size = getimagesize($_FILES['uploadfile']['tmp_name']);

					
                                        $x = (int) $_POST['x'];
					$y = (int) $_POST['y'];
					$w = (int) $_POST['w'] ;
					$h = (int) $_POST['h'] ;
		
                                       

					///$data = file_get_contents($_FILES['uploadfile']['tmp_name']);
					//$vImg = imagecreatefromstring($data);
                                       if($ext[1]=='png'){
                                        $vImg = imagecreatefrompng($src);
                                       }else if($ext[1]=='jpg' || $ext[1]=='jpeg'){
                                        $vImg = imagecreatefromjpeg($src);
                                       }else if($ext[1]=='gif'){
                                        $vImg = imagecreatefromgif($src);
                                       }else{
                                       
                                       }
                                       
					$dstImg = imagecreatetruecolor($nw, $nh);
					imagecopyresampled($dstImg, $vImg, 0, 0, $x, $y, $nw, $nh, $w, $h);
                                        if($ext[1]=='jpg' || $ext[1]=='jpeg'){
					imagejpeg($dstImg, $path);
                                        }elseif($ext[1]=='png'){
                                         imagepng($dstImg, $path);
                                        }elseif($ext[1]=='gif'){
                                         imagegif($dstImg, $path);
                                        }  
                                       $uploaded_image_size = getimagesize("/var/www/html/narrowcasting/$upload_dir/$newfilename1");
                                      // echo "<pre>"; print_r($uploaded_image_size); echo "</pre>";
                                       $minimum_size=1920;
                                      
                                       if($uploaded_image_size[0] > $minimum_size){
                                        createsingleThumb("/var/www/html/narrowcasting/$upload_dir/$newfilename1","./$upload_dir/$newfilename1",1920,1920,'custom');
                                        
                                       }
				}
	}
         #crop image end here
         createsingleThumb("/var/www/html/narrowcasting/$upload_dir/$newfilename1","./$upload_dir/$newfilename1",1920,1920,'custom');
   }else{
      createsingleThumb("/var/www/html/narrowcasting/tmp/$newfilename[$key].$ext[$key]","./$upload_dir/$newfilename[$key].$ext[$key]",1920,1920,'custom');
   }
    createsingleThumb("/var/www/html/narrowcasting/tmp/$newfilename[$key].$ext[$key]","./$upload_dir/thumb/$newfilename[$key].$ext[$key]", 135, 135,'custom');
    $error_message="<div class='upload_success'><img src='./img/error_transparant.png' style='margin-right:8px;'/><span>Uw bestand is succesvol geupload. U kunt het bestand nu gebruiken in uw carrousels.</span></div>";
    }else{
      $error_message="<div class='upload_error'><img src='./img/error.png' style='margin-right:8px;'/><span>Er is iets fout gegaan bij het uploaden. Mocht het probleem zich blijven voordoen kunt u contact opnemen met Pandora Producties (info@pandoraproducties of 020 8200162).</span></div>";
    }
    
    
   }

  
   
   
  
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Upload uw eigen afbeeldingen en video's</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<?php include('jsfiles.html')?>
<script src="./js/jquery.Jcrop.js"></script>
<script type="text/javascript">



$(document).ready(function() {
 $("#selected_dir").change(function(){
 if($(this).val()=='videos'){
  $("#cropbutton").hide();
 } 
})
    var InvalidFileNameMsg    ="Geen geldige bestandsnaam, probeer het opnieuw. U kunt proberen om alle speciale tekens uit de bestandsnaam te verwijderen.";
    var invalidFileMsg        ='U kunt alleen jpg, png, mov, mp4, flv, swf, avi of wmv bestanden uploaden.'
    var imageDisAllowedDir    =['videos'];
    var videoDidAllowedDir    =['images'];
    var nonMediaDisAllowedDir =['images','videos','commercials','upload'];
    
   
    
    // check on file type and manage directory select
    $('input#uploadfile').change(function(e) {
     
      
    
      var ele = document.getElementById($(this).attr('id'));
    var result = ele.files;
    
    var myform=document.getElementById('myform');
var fileType=getFileType();

$.ajax({
url: "check_image_dimension.php", // Url to which the request is send
type: "POST",             // Type of request to be send, called as method
data: new FormData(myform), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
//data: $('#myform').serialize(),
contentType: false,       // The content type used when sending data to the server.
cache: false,             // To unable request pages to be cached
processData:false,        // To send DOMDocument or non processed data file it is set to false
success: function(data)   // A function to be called if request succeeds
{
var data_returned = JSON.parse(data);
//alert(data_returned);
//alert(data_returned[1])
var i = 0;
//alert(data_returned.length);
for(i;i<=data_returned.length;i++){ 

 if(data_returned[i] > 7000){
  alert("U kunt geen afbeelding die breder is dan 7000 pixels uploaden");
  return false;
  break;
 }
}
//alert(fileType);
<?php
if($is_poster==1):
?>
if(fileType=='video'){  
 alert("U kunt alleen jpg en png bestanden uploaden.");
 return false;
 }
<?php endif;?>

 if(fileType!='video'){  
 if(document.getElementById("uploadfile").files[0]['size'] > 10000000){
  alert('U kunt geen afbeeldingen van meer dan 10MB uploaden.');
  return false;
   }
 }
 
}
});

    for(var x = 0;x< result.length;x++){
     var fle = result[x];
    }
    if(x>1){
     $('#cropbutton').hide();
    }
     
    
     return false;
     if(fileType=='invalid_file_name'){
      $('input#uploadfile').val('');
      $('select#selected_dir').val("");
      alert(InvalidFileNameMsg);
      changeSelectDirStatus(['']);
      //return false;
     }else if(fileType=='video'){
      changeSelectDirStatus(videoDidAllowedDir);
      return false;
     }
     else if(fileType=='image'){
       changeSelectDirStatus(imageDisAllowedDir);
       //console.log('image....');
       return false;
     }else{
        $('input#uploadfile').val('');
	$('select#selected_dir').val("");
        alert(invalidFileMsg);
	changeSelectDirStatus([]);
	return false;
     }
     
    });
 
});

   


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
    }
    else if(id==7) {
     window.location='url.php';
    }
    else if(id==2){
     window.location='create_carrousel.php';
    } 
	else if(id==8) {
 window.location='delay_mededelingen_overlay.php';
	
	} else {
     
    }
}



 function changeSelectDirStatus(disAllowedDir){
  var selectedVal=$('select#selected_dir').val();
  $('select#selected_dir option').each(function(index){
   var currentOptionVal=$(this).val();
  
   if(index!=0){
      if(disAllowedDir.indexOf(currentOptionVal)!=-1){
         $(this).attr('disabled', true);
        if(currentOptionVal==selectedVal){
	  $('select#selected_dir').val("");
	}
      }else{
        $(this).attr('disabled', false);
      }
   }
   
  });
  
  
 }

// on submit this function is called
function validateFileOnServer(){
   var oFReader = new FileReader();
  //alert(document.getElementById("uploadfile").files[0]);
  var oFReader = new FileReader();
  //alert(document.getElementById("uploadfile").files[0]['type']);
  
 if(document.getElementById("uploadfile").files[0]['type']=='image/png' || document.getElementById("uploadfile").files[0]['type']=='image/jpg'||document.getElementById("uploadfile").files[0]['type']=='image/gif'){
   if(document.getElementById("uploadfile").files[0]['size'] > 10000000){
  alert('U kunt geen afbeeldingen van meer dan 10MB uploaden.');
  return false;
   }
 }else{
  if(document.getElementById("uploadfile").files[0]['size'] > 500000000){
   alert('U kunt geen video\'s van meer dan 500MB uploaden.');
   return false;
  }
 }
    if(Checkfiles()){
      var fup = document.getElementById('uploadfile');
      var fileName = fup.value;
      var directory = $('select#selected_dir').val();
      
      var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
     
      /*if(ext=='mp4' || ext=='mov'){
       if(directory != 'videos'){
        alert('Please select video directory to upload videos');
        return false;
       }
      }*/
      var directory_label = $('select#selected_dir option:selected').text();
      var fileNameArray=fileName.split("\\");
      fileName=	fileNameArray[(fileNameArray.length)-1];		       
      var uploading_file_path=directory+"/"+fileName;
      var noReplace=false;
      var fileType = getFileType();
      <?php
if($is_poster==1):
?>
if(fileType=='video'){  
 alert("U kunt alleen jpg en png bestanden uploaden.");
 return false;
 }
<?php endif;?>
      $('img#ajax_loader_image').show();
      $.ajax({  
              type: "get", url: 'upload_videoimage.php?uploading_file_path='+uploading_file_path+'',
	      async: false,
              complete: function(data){ 
                 var response=$.trim(data.responseText);
		  $('img#ajax_loader_image').hide();		   
                   showBlockUI();
		 if(response=='yes'){
		  var confirmResult=confirm('Dit bestand bestaat al in de "'+directory_label+'" map.\nWilt u dit bestand vervangen?');
		  if(!confirmResult){
		    noReplace=true;
                   $.unblockUI();
		  }
		 }
                
              },
	      error:function(error){
	        $('img#ajax_loader_image').hide();
	      }
	      
          });
      
          if(!noReplace){
          
	    return true;
	  }else{
	    $('input#uploadfile').val('');
	    return false;
	  }
     
    }else{
      return false;
    }
 
}

  function Checkfiles()
  {
    var letters = /^[a-zA-Z0-9-_ ]+$/;
   
    var fup = document.getElementById('uploadfile');
    var fileName = fup.value;
    var directory = $('select#selected_dir').val();
  
    if(fileName==""||directory==""){
     alert('Selecteer een bestand en de doelmap');
     return false;
    }
   /*add by Himani Agarwal for resricted files having more than one dot*/
    var lastPosDot = fileName.lastIndexOf('.');
    var posDot = fileName.indexOf('.');
     if(lastPosDot != posDot){
      alert('Geen geldige bestandsnaam, probeer het opnieuw. U kunt proberen om alle speciale tekens uit de bestandsnaam te verwijderen.');
      return false;
     }
     /*end here*/
     var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
   
     if(ext == "jpg" || ext == "JPG" || ext == "Jpg" || ext == "JPEG" || ext == "Jpeg"  || ext == "jpeg" || ext == "png" || ext == "Png"  || ext == "PNG" ||ext == "mov" || ext == "MOV" || ext =="mp4" || ext == "MP4" ||  ext == "FLV" || ext == "flv" || ext == "Flv" || ext == "SWF" || ext == "Swf" || ext == "swf" || ext == "WMV" || ext == "Wmv" || ext == "wmv" || ext == "AVI" || ext == "Avi" || ext == "avi")
     {
     return true;
     } 
     else
     {
     alert("U kunt alleen jpg, png, mov, mp4, flv, swf, avi of wmv bestanden uploaden.");
     fup.focus();
     return false;
     }
  }
  
  
  function getFileType(){
  
    var fup = document.getElementById('uploadfile');
    var fileName = fup.value;
    var lastPosDot = fileName.lastIndexOf('.');
    var posDot = fileName.indexOf('.');
    if(lastPosDot != posDot){
      return "invalid_file_name";
    }
    var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

    if(ext == "jpg" || ext == "Jpg" || ext == "JPG" || ext == "jpeg" || ext == "Jpeg"  || ext == "JPEG"  || ext == "png" || ext == "Png"  || ext == "PNG")
    {
      return 'image';
     
    }else if(ext == "mov" || ext == "MOV" || ext =="mp4" || ext == "MP4" || ext == "flv" || ext == "FLV" || ext == "Flv" || ext == "SWF" || ext == "Swf" || ext == "swf" || ext == "AVI" || ext == "Avi" || ext == "avi" || ext == "WMV" || ext == "Wmv" || ext == "wmv")
    {
      return 'video';
    }
    else
    {
      return 'un_supported';
    }
    
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
  // prepare HTML5 FileReader
  var oFReader = new FileReader();
  //alert(document.getElementById("uploadfile").files[0]);
  
  if(document.getElementById("uploadfile").files[0]['size'] > 10000000){
   alert('U kunt geen afbeeldingen van meer dan 10MB uploaden.');
   return false;
  }else{
   var url="crop_upload.php"; 
   window.open(url, 'formpopup', 'height=550,width=650,left=190,top=150,screenX=200,screenY=100,scrollbars=yes');
  }
}



</script>
<style type="text/css">

.wrap{
		width: 700px;
		margin: 10px auto;
		padding: 10px 15px;
		background: white;
		border: 2px solid #DBDBDB;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		text-align: center;
		overflow: hidden;
	}
img#uploadPreview{
		border: 0;
		border-radius: 3px;
		-webkit-box-shadow: 0px 2px 7px 0px rgba(0, 0, 0, .27);
		box-shadow: 0px 2px 7px 0px rgba(0, 0, 0, .27);
		margin-bottom: 30px;
		overflow: hidden;
	}
        #alle_carrousels,#nieuw_bestand,#nieuwe_twitterfeed,#maak_carrousel,#iets_nieuws,#spreekuur,#tot_ziens{
	      <?php echo $css_color_rule;?>
	}
	#uploaden{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
	#alle_carrousels{font-size:14px;line-height:30px;}
	#maak_carrousel{font-size:14px;line-height:30px;}
	#nieuw_bestand{font-size:14px;line-height:30px;}
	#uploaden_bestand{font-size:14px;color: #b5b5b5;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
	#iets_nieuws{font-size:14px;line-height:30px;}
	#spreekuur{font-size:14px;line-height:30px;}
	#tot_ziens{font-size:14px;line-height:30px;}
</style>

</head>
<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->

<body>

   <?php include('header.html'); ?>
   
    <div class="main_container">
    <div class="content">
          <span class="title">Uploaden</span>
         
		
		   
		   <?php if($theatreDetails[is_poster]==1): ?>
	           <p>Hier kunt u foto's en video's uploaden die u wilt gebruiken in uw carrousel. Na het uploaden vindt u de bestanden in de map "Uploads" op de pagina <a href="create_carrousel.php">"Maak carrousel"</a>.
	           Voor het beste resultaat dienen uw bestanden de volgende specificaties te hebben:<br /><br />
           <div style="float:left; width:400px;"><span style="font-weight:bold; color:<?php echo $css_color_code;?>;">Afbeeldingen</span><br />
           - jpg of png<br />
           - 1080 x 1920 pixels (voor fullscreen afbeeldingen)<br />
		   - 1080 x 1527 pixels (voor digitale posters)<br />
		   - maximaal 7000 pixels<br />
           - maximaal 10MB<br />
           - RGB kleuren<br />
           - 72DPI<br />
           </div>
        
			<br /><br /><br /><br />	<br /><br /><br /><br /><br />
		<?php else:?>
            <p>Hier kunt u foto's en video's uploaden die u wilt gebruiken in uw carrousel. Na het uploaden vindt u de bestanden op de pagina <a href="create_carrousel.php">"Maak carrousel"</a>.
            Voor het beste resultaat dienen uw bestanden de volgende specificaties te hebben:<br /><br />
            <div style="float:left; width:300px;"><span style="font-weight:bold; color:<?php echo $css_color_code;?>;">Afbeeldingen</span><br />
            - jpg of png<br />
            - 16:9 verhouding<br />
 		   - maximaal 7000 pixels<br />
            - maximaal 10MB<br />
            - RGB kleuren<br />
            - 72DPI<br />
            </div>
            <span style="font-weight:bold; color:<?php echo $css_color_code;?>;">Video's</span><br />
            - mov, mp4, flv, swf, avi of wmv<br />
            - maximaal 500 MB<br />
            - maximale bitrate: 15mbps<br />
 			<br /><br /><br /><br />
		<?php endif ?>
			
			
			
			Na het selecteren van uw afbeelding kunt u op 'Upload' klikken om de afbeelding in de originele verhouding te uploaden. Indien u de afbeelding wilt croppen klikt u op 'Upload &amp; Crop',<br /><br />
			<strong>Tip:</strong> door de 'Shift' toets te gebruiken bij het selecteren van een bestand kunt u meerdere items tegelijk selecteren. Deze worden dan in 1 keer ge-upload.<br /><br />
           Mocht u problemen ondervinden bij het uploaden van materiaal kunt u contact opnemen met Pandora Producties<br />(<a href="mailto:info@pandoraproducties.nl">info@pandoraproducties.nl</a> of 020 8200162).
           </p>
      </div>
    	<div class="grid_container">
       <?php 
         foreach($error_message as $key=>$val){
          echo $val."<br>";
         }
       ?>
		<table>
		  <tr class='grid_header'>
		   <td height='25' width='35%'>Kies hieronder uw bestand</td>
		   <td height='25' width='10%'>&nbsp;</td>
		   <td width='10%'>&nbsp;</td>
		   <td width='10%'>&nbsp;</td>
		   <td width='10%'>&nbsp;</td>
		   <td width='10%'>&nbsp;</td>
		  </tr>
             <form name="c_create" id="myform" action="upload_videoimage.php" method="POST" enctype="multipart/form-data" onsubmit="return validateFileOnServer(); ">
              <table cellspacing="5" cellpadding="5" border="0" style="width:30%;">
               <tr>
                <td><input type="file" name="uploadfile[]" id="uploadfile" multiple="multiple">
                <input type="hidden" id="x" name="x" />
		<input type="hidden" id="y" name="y" />
		<input type="hidden" id="w" name="w" />
		<input type="hidden" id="h" name="h" />
                
               
                </td>
		<td>
		    <select style="width:140px;" name="upload_dir" id="selected_dir">
                        <?php echo createSelectDirOption(); ?>
                     </select>
		</td>
		 <td><img id="ajax_loader_image" src="img/loader.gif" style="width:20px;height:20px;display:none;"></td>
               </tr>
	       <tr>
                
               </tr>
              </table>
              <div class="footer_tab1"><input type="submit" name="submit" value="Upload" id="upload_image"> &nbsp; &nbsp; <input type="button" style="margin-bottom: 10px;"name="button" value="Upload &amp; Crop" onclick="openitup();" id="cropbutton"/></div>
             </form>
            <img id="uploadPreview" style="display:none;"/>
            <input type="hidden" name="uploadimage" id="uploadimage">
        </div>
        
        <div id="domMessage" style="display:none;">

<img style="margin:20px 0px 10px 0px;" src="img/<?php echo $color_scheme;?>/loader.gif" />
    
    <h5 style="margin:3px 10px 10px 10px;font-size:15px;">Uploading..</h5>
    
</div> 
    </div>
  
</body>
</html>

<script language="javascript">

  
  
  
  
</script>
