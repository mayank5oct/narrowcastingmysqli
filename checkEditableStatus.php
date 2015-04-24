<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(0);
include('config/database.php');
$db = new Database;

$name_carrousel=$_REQUEST['carr_name'];
$carr_id=$_REQUEST['carr_id'];

$image_from=trim(urldecode($_POST['image_from']));
$image_name=trim(urldecode($_POST['image_name']));
$table_name="";


$responseArray=array();

 if($name_carrousel!=""){
     $update_query="update carrousel_listing set name='".$name_carrousel."' where id=".$carr_id;
     $conn=$db->query($update_query);
     
 }
if($image_from=="mededelingen"){
   $table_name="temp_mededelingen";
   $base_directory="mededelingen";
   $check_query="select temp_mededelingen.id,temp_mededelingen.temp_image_name,temp_mededelingen.template_id,mededelingen.template_name
   from temp_mededelingen left join mededelingen on (temp_mededelingen.template_id=mededelingen.id ) where temp_mededelingen.temp_image_name='$image_name' limit 1";
  
   $check_query_result=$db->query($check_query);
   $check_query_result_array = mysql_fetch_array($check_query_result);
   if(is_array($check_query_result_array) and count($check_query_result_array)>0){
        $db_ImageName = trim($check_query_result_array['temp_image_name']);
        $id = $check_query_result_array['id'];
        $template_id = $check_query_result_array['template_id'];
        $db_templateName=trim($check_query_result_array['template_name']);
   }
   
   ## check if record exist in mysql table and its template/mededelingen image is in its directory
   if($db_ImageName!="" && $db_templateName!="" && file_exists("mededelingen_template/".$db_templateName) && file_exists($base_directory."/".$db_ImageName)){
      $responseArray['id']=$id;
      $responseArray['template_id']=$template_id;
      $responseArray['created_file_name']=$db_ImageName;
      $responseArray['type']='mededelingen';
   }else if(!isset($id) || $id==""){## record does not exist in db(old mededelingen)
      $responseArray['error']="U kunt deze mededeling niet bewerken. Misschien is het een oude mededeling of is hij door iemand anders aangemaakt.";
   }
   else if(!file_exists("mededelingen_template/".$db_templateName)){## mededelingen template image not exist
      $responseArray['error']="De template van deze mededeling bestaat niet meer.";
   }
   else if($db_ImageName!="" && !file_exists($base_directory."/".$db_ImageName)){## mededelingen template image not exist
      $responseArray['error']="Deze mededeling is niet meer beschikbaar.";
   }
   else{## unknown case
      $responseArray['error']="Er is iets fout gegaan bij het bewerken van de mededeling. Neem alstublieft contact op met Pandora Producties.";
   }
   
}
else if($image_from=="overlays_video" || $image_from=="overlays_image" || $image_from=="overlay_video"){
    $table_name="temp_overlay";
    $base_directory="overlay_video";
    $fileType="image";
    $check_query="select * from temp_overlay where temp_file_name='$image_name' limit 1";
    
    if($image_from=="overlays_video" || $image_from=="overlay_video"){
      $fileType="video";
    }
    $check_query_result=$db->query($check_query);
    $check_query_result_array = mysql_fetch_array($check_query_result);
   
   
   if(is_array($check_query_result_array) and count($check_query_result_array)>0){
      
        $db_ImageName = trim($check_query_result_array['temp_file_name']);
        $id = $check_query_result_array['id'];
        $carrousel_id = $check_query_result_array['cid'];
        $db_templateName=trim($check_query_result_array['tname']);
        $selected_file=trim($check_query_result_array['imagepath']);
        
   }
     ## check if selected image/video  file exists and it is in mydsql db
     if(file_exists($selected_file) && $selected_file!=""){
          ## check if image exist in directory(might be deleted in another tab of browser etc...)
         if($db_ImageName!="" && file_exists($base_directory."/".$db_ImageName) && file_exists("template/".$db_templateName)){
             $responseArray['id']=$id;
             $responseArray['carrousel_id']="";//$carrousel_id;///let see from client end
             $responseArray['created_file_name']=$db_ImageName;
             $responseArray['selected_file_path']=htmlentities($selected_file);
             $responseArray['type']='overlays_'.$fileType;
         }else if(!file_exists($base_directory."/".$db_ImageName) && $db_ImageName!=""){
            $responseArray['error']="Deze overlay is al verwijderd. U kunt de pagina verversen.";
         }
         else if(!isset($db_ImageName)){
            $responseArray['error']="Dit is een oude overlay die u niet kunt bewerken.";
         }else if(! file_exists("template/".$db_templateName) && $db_templateName!=""){
            $responseArray['error']="De template van deze overlay is niet meer beschikbaar.";
         }
         else{
            $responseArray['error']="Er is iets fout gegaan bij het bewerken van de overlay. Neem alstublieft contact op met Pandora Producties.";
         }
   
    }else if(!file_exists($selected_file) && $selected_file!=""){
       $responseArray['error']="Het bronbestand van deze overlay is verwijderd.";
    }
    else{
      $responseArray['error']="Dit is een oude overlay die u niet kunt bewerken.";
     }
     
}else{
   $responseArray['error']="Er is iets fout gegaan bij het bewerken van de overlay. Neem alstublieft contact op met Pandora Producties.";
}

echo json_encode($responseArray);
die;

?>
