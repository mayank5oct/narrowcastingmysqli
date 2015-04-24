<?php
require_once('config/database.php');
require_once('config/get_theatre_label.php');
require_once('color_scheme_setting.php');
$db = new Database;
error_reporting(0);
        ## IT CONTAIN NAMES OF ALL PERMITTED DIRECTORIES,  IN THE ROOT PATH OF NARROCASTING
	if($theatreDetails['is_poster']==1){
      $qry = "select `overlays`, `images`, `mededelingen`,`promotie`, `commercials`, `upload`  from theatre where status = 1";
		}
	else {
		  $qry = "select `overlays`, `images`, `mededelingen`,`videos`,`promotie`, `commercials`, `upload`  from theatre where status = 1";
}
	 $getDirectoriesa =$db->query($qry);
	$getDirectories = mysql_fetch_array($getDirectoriesa, MYSQL_ASSOC);
	$allowed_directory=array();
	$allowed_directory_name=array();
	foreach($getDirectories as $key=>$val){
	   $allowed_directory[]= $key;
	   $allowed_directory_name[$key]=$val;
	}
	
        require_once("resize-class.php");
        $root_path='/var/www/html/narrowcasting';
       // echo "<pre>"; print_r($allowed_directory_name); echo "</pre>";
        $aa = file_exists($root_path."/thumb_images");
       // var_dump($aa);
       // exit;
        //setting 0777 permission for thumb_root_directy
       if( ! file_exists($root_path."/thumb_images")) {
            mkdir($root_path."/thumb_images", 0777);
         }
         if(isset($_REQUEST['directory_name'])){
           $next_directory=trim($_REQUEST['directory_name']);  
         }else{
           $next_directory="";    
         }
       
      //  chmod($root_path."", 777);  
        if($next_directory!=""){
             if(substr($next_directory,0,1)=="/"){
               $current_directory=$root_path.$next_directory;      
             }
             else{
               $current_directory=$root_path.'/'.$next_directory;        
             }
        }else{
            $current_directory=$root_path;     
        }
      
      
       //Please wait while the page is loading�
        // Opens directory
        $allowedDirectory=opendir($root_path);
        // Gets each entry
        $current_directory_handler=opendir($current_directory);
	$dirArray = array();
        while($entryName=readdir($current_directory_handler)) {
          if($current_directory==$root_path && is_dir($current_directory.'/'.$entryName) && in_array($entryName,$allowed_directory) ){
             $dirArray[]=$entryName;
          }else if($current_directory!=$root_path && $entryName!='thumb' && $entryName!='totalthumb' && $entryName!='cropped' && $entryName!='preview' && $entryName!='thumbs' && !in_array($entryName,array('.','..','.DS_Store','.gitignore'))){
             $dirArray[]=$entryName;
          }
 
        }
      
      
      
      
        // Finds extensions of files
        function findexts ($filename) {
          $filename=strtolower($filename);
          $exts=explode(".", $filename);
          $n=count($exts)-1;
          $exts=$exts[$n];
          return $exts;
        }
       
        // Closes directory
	
        closedir($current_directory_handler);
       
        // Counts elements in array
    
      $indexCount=count($dirArray);
      
        
        // Loops through the array of files
        for($index=0; $index < $indexCount; $index++) {
        
          $hide='.';
          if(substr("$dirArray[$index]", 0, 1) != $hide) {
          
          // Gets File Names
          $name=$dirArray[$index];
          $namehref=$dirArray[$index];
          // Gets Extensions 
          $extn=findexts($dirArray[$index]); 
          
          $is_dir=is_dir($current_directory."/".$dirArray[$index]);
         // echo $is_dir.'sss'.$root_path."/".$dirArray[$index];
          
          // Separates directories
          if($is_dir) {
            $extn="Folder"; 
            $class="dir_icon";
          } else {
            $class="file_icon";
          }
          //setting 0777 permission for new created direcly
         if( ! file_exists($root_path.$next_directory)) {
            mkdir($root_path.$next_directory, 0777);
         }
        if(in_array($extn,array('png','jpeg','jpg','gif'))){
                
           if(! file_exists($root_path.$next_directory.'/thumb')){
             mkdir($root_path.$next_directory.'/thumb', 0777);  
           }
           if(! file_exists($root_path.$next_directory.'/thumb/'.$namehref)){
             $crop_image_path=$root_path.$next_directory.'/thumb/'.$namehref;
	     $resizeObj = new resize($current_directory."/".$namehref);
	     // *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
	     $resizeObj -> resizeImage(200, 100, 'landscape');
	     // *** 3) Save image
	     $resizeObj -> saveImage($crop_image_path, 100);
            }
          $image_path='.'.$next_directory.'/thumb/'.$namehref;
        }else{
                
          $image_path='./img/folder_image.jpg';      
                
        }
          
         /*echo "Image Path : ".$image_path;
         var_dump($is_dir);*/
          if($is_dir || in_array($extn,array('png','jpeg','jpg','gif'))){
              
         ?>
            
                
                <li class='horiz_block' id='recordsArray_1922'>
	  <div class='horiz_img'>
	 <img style="margin:4px 0px 0px 4.5px;cursor:pointer;" src='<?php echo $image_path; ?>' width='130' height='130'  class='<?php echo $class; ?>' data='<?php echo $namehref; ?>' style='cursor:pointer;'>
	 </div>
	<p ><?php
          foreach($allowed_directory_name as $keys=>$vals){
	    if($keys==$name){
		$name=$vals;
	    }
	  }
	//echo $name;
        // str_replace('--', '-', $challenge)
               $name=explode(".",$name);
               $splitPosition=strrpos($name[0],'_');
               if($splitPosition){
                 $name_inner=trim(str_replace('_', ' ',substr($name[0],0,$splitPosition)));
                 if(strlen($name_inner)>20){
                   echo substr($name_inner,0,20).'...';     
                 }else{
                   echo substr($name_inner,0,20);   
                 }
               }else{
                 $name_inner=trim(str_replace('_', ' ',$name[0]));
                 if(strlen($name_inner)>20){
                   echo substr($name_inner,0,20).'...';     
                  }else{
                   echo substr($name_inner,0,20);   
                  }
               }
               
               ?></p>
      
               <!--str_replace("_"," ",substr($newname[0],0,40)) -->
	
       </li>
         <?php }       
          }
        }
         //print_r($dirArray);die;
      ?>
