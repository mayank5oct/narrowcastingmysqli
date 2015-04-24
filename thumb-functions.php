<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(0);
require_once('resize-class.php'); 
require_once('config/database.php'); 
 
                    function create_preview_video($directory,$video_name){
  
				if(substr($video_name,0,1)=="."){
				   return false;
				}
				
				$directory_is_ok=1;
				$already_created_is_ok=1;
				if(!file_exists("/var/www/html/narrowcasting/$directory/preview")) {
				   $directory_is_ok= mkdir("/var/www/html/narrowcasting/$directory/preview", 0777);
				}
				$already_created_is_ok=file_exists("/var/www/html/narrowcasting/$directory/preview/".$video_name);
				
				if( $already_created_is_ok!=1 && $directory_is_ok==1) {
					$inputfilename="/var/www/html/narrowcasting/$directory/".$video_name;
					$outputfilename="/var/www/html/narrowcasting/$directory/preview/".$video_name;
					//$command="/home/pandora/bin/ffmpeg -i '$inputfilename' -acodec copy -vcodec libx264 -maxrate 150k -bufsize 500k  -threads 0 -crf 20 $outputfilename 2>&1";
					$command = "/home/pandora/bin/ffmpeg -i $inputfilename -acodec copy -vcodec libx264 -maxrate 900k -bufsize 500k  -threads 0 -crf 20 $outputfilename";
					//exec("/home/pandora/bin/ffmpeg  -i $inputfilename -vcodec libx264 -b 8M -r 25 -acodec copy -vf \"movie=/var/www/html/narrowcasting/tmp/test2.png [wm];[in][wm] overlay=0:0 [out]\" $outputfilename",$output,$error);
					exec($command, $output, $error);
				  }			
		      }
		      
		      
		      
		      
		      
function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth,$thumbhight, $option='crop', $cropStartX='', $cropStartY='') 
{
  
 //--------- code for remane the files-------------
  if($handle= opendir("/var/www/html/narrowcasting/$pathToImages")) {
    
//  while (false !== ($entry_images = readdir($handle))) {
  while (($entry_images = readdir($handle)) !== false) {
   if($entry_images != '.' && $entry_images != '..' && $entry_images != 'Thumbs.db' && $entry_images != '.DS_Store' && $entry_images != 'thumb' && $entry_images != '.svn' && $entry_images != '.gitignore') 
     $entry_images1=explode(".",$entry_images);
   if($entry_images1[1]=="jpg" or $entry_images1[1]=="png" or $entry_images1[1]=="jpeg" or $entry_images1[1]=="JPEG" or $entry_images1[1]=="PNG" or $entry_images[1]=="Jpg" or $entry_images1[1]=="GIF" or $entry_images1[1]=="gif" or $entry_images1[1]=="JPG") {
    $currentpath = getcwd();
    if(!file_exists("$currentpath/$pathToImages")) {
         mkdir("$currentpath/$pathToImages", 0777);
     }
    chmod("$currentpath/$pathToImages", 0777);
    chmod("$currentpath/$pathToImages".$entry_images, 0777);
    $newfilename_1 = preg_replace("/[^A-Za-z0-9-]/", '_', $entry_images1[0]);
    
   if($entry_images1[1]=="JPG") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($entry_images1[1]=="Jpg") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($entry_images1[1]=="jpeg") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($entry_images1[1]=="jpg") {
    $newfilename1=$newfilename_1.".jpg";
   }else if($entry_images1[1]=="JPEG") {
    $newfilename1=$newfilename_1.".jpg";
   } else if($entry_images1[1]=="PNG") { 
    $newfilename1=$newfilename_1.".png";
   } else if($entry_images1[1]=="GIF") { 
    $newfilename1=$newfilename_1.".gif";
   }else {
    $newfilename1=$newfilename_1.".".$entry_images1[1];
   }
  
   rename("$currentpath/$pathToImages".$entry_images,"$currentpath/$pathToImages".$newfilename1);
 //  copy("$oldpath/$pathToImages/".$entry_images , "$oldpath/$pathToThumbs/".$entry_images );
   }
 }
  closedir($handle);
 } 

 //----------open thumb directory
 $thumbimages =array();
 $thumbdir= opendir($pathToThumbs);
 while (($thumbfname = readdir( $thumbdir )) !== false ) {
  $thumbimages[ ] = $thumbfname;
 }
 closedir( $thumbdir );
 
  // open the directory
  $dir = opendir($pathToImages);

  // loop through it, looking for any/all JPG/PNG files:
  while (($fname = readdir( $dir )) !== false) { 
   if(in_array($fname,$thumbimages)==false){
      
    // parse path for the extension
    $info = pathinfo($pathToImages . $fname);
    // continue only if this is a JPEG and PNG image
    if (strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'png' || strtolower($info['extension']) == 'gif') 
    {
      // load image and get image size
      $dest="$pathToImages"."$fname";
      if(strtolower($info['extension']) == 'jpg'){
       $img = imagecreatefromjpeg($dest);
      }elseif(strtolower($info['extension']) == 'gif'){
       $img = imagecreatefromgif($dest);
      }elseif(strtolower($info['extension']) == 'png'){
       $img = $img = imagecreatefrompng($dest);
      }else{
       $img = imagecreatefrompng($dest);
      }
      $new_height = $thumbhight;  //floor( $height * ( $thumbWidth / $width ) );
      //echo "Thumb Path : ".'/var/www/html/narrowcasting/'.$dest;
     // echo '/var/www/html/narrowcasting/'.$pathToImages.'/thumb/'.$fname;
      //die("XXX");
      // create a new temporary image
     // $tmp_img = imagecreatetruecolor($new_width, $new_height);
      // copy and resize old image into new image 
      //imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	  
	  $resizeObj = new resize('/var/www/html/narrowcasting/'.$dest);
	  //Resize image (options: exact, portrait, landscape, auto, crop)
	  $resizeObj -> resizeImage($thumbWidth, $thumbhight, $option, $cropStartX, $cropStartY);
	   // *** 3) Save image
	  $resizeObj -> saveImage('/var/www/html/narrowcasting/'.$pathToImages.'/thumb/'.$fname, 100);

    
    }
   }
  }
  // close the directory
  closedir( $dir );
 // echo"thumbs genrated for $pathToImages folder"."<br>";
}
		      

function createvideo_Thumbs($pathToVideo, $pathToThumbs, $thumbWidth,$thumbhight, $option='crop', $cropStartX='', $cropStartY='') {
   
  //--------------- code used for creating thumbnail of video and video conversion for promotie folder----------------------------
 if($handle= opendir("./$pathToVideo")) {
  while (false !== ($entry_video = readdir($handle))) {
  if($entry_video != '.' && $entry_video != '..' && $entry_video != 'Thumbs.db' && $entry_video != '.DS_Store' && $entry_video != 'thumb' && $entry_video != '.gitignore')
    $entry_video1=explode(".",$entry_video);
 if($entry_video1[1]=="mp4" or $entry_video1[1]=="mov" or $entry_video1[1] == "MOV" or $entry_video1[1] == "MP4") { 
    
    $oldpath =  getcwd();
    if(!file_exists("$oldpath/$pathToVideo")) {
         mkdir("$oldpath/$pathToVideo", 0777);
     }
    chmod("$oldpath/$pathToVideo", 0777);
    chmod("$oldpath/$pathToVideo".$entry_video, 0777);

    $newfilename= stripslashes(preg_replace("/[^A-Za-z0-9-]/", '_', $entry_video1[0]));
    if($entry_video1[1]=="MOV") {
     $newfilename1=$newfilename.".mov";
    } else if($entry_video1[1]=="MP4") {
     $newfilename1=$newfilename.".mp4";
    } else {
    $newfilename1=$newfilename.".".$entry_video1[1];
   }
    rename("$oldpath/$pathToVideo".$entry_video,"$oldpath/$pathToVideo".$newfilename1);
   }
 }
  closedir($handle);
 }
 
 if ($handle = opendir("./$pathToThumbs")) {
 while (false !== ($entry_video = readdir($handle))) {
 if($entry_video != '.' && $entry_video != '..' && $entry_video != 'Thumbs.db' && $entry_video != '.DS_Store')
  $video_images[] = $entry_video;
 }
  closedir($handle);
 }
//print_r($video_images);
 if ($handle = opendir("./$pathToVideo")) {
 while (false !== ($entry_thumb = readdir($handle))) {
 if($entry_thumb != '.' && $entry_thumb != '..' && $entry_thumb != 'thumb' && $entry_thumb != '.DS_Store' && !is_dir("$pathToVideo/$entry_thumb")){
   $entry_thumb1=explode('.',$entry_thumb);
 
   $index = $entry_thumb1[1];
   $entry_check_image=$entry_thumb1[0].".jpg";
   $array_file_type = array('jpg','gif','png','jpeg');
   
   if(in_array($index,$array_file_type) == false){
     
      create_preview_video(substr($pathToVideo,0,-1),$entry_thumb);
    }
   if(in_array($entry_check_image,$video_images) == false) { 
     if(in_array($index,$array_file_type) == false){
       chmod("/var/www/html/narrowcasting/$pathToVideo", 0777);
       $command = "/home/pandora/bin/ffmpeg -i /var/www/html/narrowcasting/$pathToVideo$entry_thumb -ss 2 -vframes 1 -f image2 -vf scale=250:-1 /var/www/html/narrowcasting/$pathToThumbs".$entry_thumb1[0].".jpg";      
       
       exec("/home/pandora/bin/ffmpeg -i /var/www/html/narrowcasting/$pathToVideo$entry_thumb -ss 2 -vframes 1 -f image2 -vf scale=250:-1 /var/www/html/narrowcasting/$pathToThumbs".$entry_thumb1[0].".jpg");
       
	  $resizeObj = new resize('/var/www/html/narrowcasting/'.$pathToThumbs.$entry_thumb1[0].".jpg");
	  //Resize image (options: exact, portrait, landscape, auto, crop)
	  $resizeObj -> resizeImage(135, 135, $option, $cropStartX, $cropStartY);
	   // *** 3) Save image
	  $resizeObj -> saveImage('/var/www/html/narrowcasting/'.$pathToThumbs.$entry_thumb1[0].".jpg", 100);
     
       
     }
   }
 }
   }
   
  closedir($handle);
 }  
  echo"thumbs genrated for video's from $pathToVideo folder"."<br>";
}			      
		      
		      
	
	
 function createsingleThumb($pathToImages, $pathToThumbs, $thumbWidth, $thumbhight, $option='crop', $cropStartX='', $cropStartY='', $transparent='') {

    // parse path for the extension
    $info = pathinfo($pathToImages);
    // continue only if this is a JPEG and PNG image
    if (strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'png' || strtolower($info['extension']) == 'gif') {
        // load image and get image size
        if (strtolower($info['extension']) == 'jpg') {
            $img = imagecreatefromjpeg("{$pathToImages}{$fname}");
        } elseif (strtolower($info['extension']) == 'gif') {
            $img = imagecreatefromgif("{$pathToImages}{$fname}");
        } else {
            $img = imagecreatefrompng("{$pathToImages}{$fname}");
        }
        $width = imagesx($img);
        $height = imagesy($img);

        // calculate thumbnail size
        $new_width = $thumbWidth;
        $new_height = $thumbhight;  //floor( $height * ( $thumbWidth / $width ) );
     
     
          $resizeObj = new resize($pathToImages);
	  //Resize image (options: exact, portrait, landscape, auto, crop)
	  $resizeObj -> resizeImage($thumbWidth, $thumbhight, $option, $cropStartX, $cropStartY, $transparent);
	   // *** 3) Save image
	  $resizeObj -> saveImage($pathToThumbs,100);

       
}

 }

function createsingle_video_Thumb($pathToVideo, $pathToThumbs, $thumbWidth,$thumbhight, $filename='', $random='', $totalthumb='', $option='crop', $cropStartX='', $cropStartY='') {
	
      $video_file=explode('/',$pathToVideo);
      $video_file_name=explode('.',$video_file[1]);
      //echo "<pre>"; print_r($video_file_name);
      //echo "/var/www/html/narrowcasting/$pathToVideo<br>";
     
      chmod("/var/www/html/narrowcasting/$pathToVideo", 0777);
      if($random==''){
	exec("/home/pandora/bin/ffmpeg -i /var/www/html/narrowcasting/$pathToVideo -ss 2 -vframes 1 -f image2 -vf scale=250:-1 /var/www/html/narrowcasting/$pathToThumbs".$video_file_name[0].".jpg");
	 $resizeObj = new resize('/var/www/html/narrowcasting/'.$pathToThumbs.$video_file_name[0].".jpg");			
       //Resize image (options: exact, portrait, landscape, auto, crop)
       $resizeObj -> resizeImage($thumbWidth, $thumbhight, $option, $cropStartX, $cropStartY);
       // *** 3) Save image
       $resizeObj -> saveImage('/var/www/html/narrowcasting/'.$pathToThumbs.$video_file_name[0].'.jpg', 100);
      }else{
	if($totalthumb==''){				
       exec("/home/pandora/bin/ffmpeg -i /var/www/html/narrowcasting/$pathToVideo -ss 2 -vframes 1 -f image2 -vf scale=250:-1 /var/www/html/narrowcasting/$pathToThumbs".$video_file_name[0]."_".$random.".jpg");
       $resizeObj = new resize('/var/www/html/narrowcasting/'.$pathToThumbs.$video_file_name[0]."_".$random.".jpg");
	  //Resize image (options: exact, portrait, landscape, auto, crop)
       $resizeObj -> resizeImage($thumbWidth, $thumbhight, $option, $cropStartX, $cropStartY);
       // *** 3) Save image
       $resizeObj -> saveImage('/var/www/html/narrowcasting/'.$pathToThumbs.$video_file_name[0]."_".$random.".jpg", 100);
	}
      
      }
      
 
}

function create_overlay_thumb($sourcepath, $destinationpath, $totalthumbpath='', $width, $height, $option='crop', $cropStartX='', $cropStartY=''){
	exec("/home/pandora/bin/ffmpeg -i $sourcepath -ss 2 -vframes 1 -f image2 -vf scale=250:-1 $totalthumbpath");			
	exec("/home/pandora/bin/ffmpeg -i $sourcepath -ss 2 -vframes 1 -f image2 -vf scale=250:-1 $destinationpath");			
	$resizeObj = new resize($destinationpath);
	$resizeObj -> resizeImage($width, $height, $option, $cropStartX, $cropStartY);
        // *** 3) Save image
        $resizeObj -> saveImage($destinationpath, 100);

 	
}

?>
