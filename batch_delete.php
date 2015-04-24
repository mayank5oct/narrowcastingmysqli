<?php
//echo "<pre>"; print_r($_POST); echo "</pre>";
//exit;
error_reporting(0);
session_start();

include('config/database.php');
$db = new Database;
$post=$_POST;
if($_POST['batch_delete_folder']=='videos'){
    foreach($post as $key=>$val){
        $thumb=explode(".",$val);
        //echo "<pre>"; print_r($thumb); echo "</pre>";
        $thumb_file=$thumb[0].".jpg";
        unlink('/var/www/html/narrowcasting/videos/'.$val);
		unlink('/var/www/html/narrowcasting/videos/preview/'.$val);
        unlink('/var/www/html/narrowcasting/videos/thumb/'.$thumb_file);
    }
}

if($_POST['batch_delete_folder']=='images'){
    foreach($post as $key=>$val){
        unlink('/var/www/html/narrowcasting/images/'.$val);
        unlink('/var/www/html/narrowcasting/images/thumb/'.$val);
    }
}

if($_POST['batch_delete_folder']=='mededelingen'){
    foreach($post as $key=>$val){
        unlink('/var/www/html/narrowcasting/mededelingen/'.$val);
        unlink('/var/www/html/narrowcasting/mededelingen/thumb/'.$val);
        $delete_medelingen_query="delete from temp_mededelingen where temp_image_name='$val'";
        $db->query($delete_medelingen_query);
    }
}

if($_POST['batch_delete_folder']=='delaymededelingen'){
    foreach($post as $key=>$val){
        unlink('/var/www/html/narrowcasting/delay_mededelingen/'.$val);
        unlink('/var/www/html/narrowcasting/delay_mededelingen/thumb/'.$val);
    }
}

if($_POST['batch_delete_folder']=='overlay_video'){
    foreach($post as $key=>$val){
        
        unlink('/var/www/html/narrowcasting/overlay_video/'.$val);
        unlink('/var/www/html/narrowcasting/overlay_video/thumb/'.$val);
        $delete_overlay_query="delete from temp_overlay where temp_file_name='$val'";
        $db->query($delete_overlay_query);
    }
   
   
}

if($_POST['batch_delete_folder']=='commercials'){
    foreach($post as $key=>$val){
       // echo $val."<br>";
        unlink('/var/www/html/narrowcasting/commercials/'.$val);
        unlink('/var/www/html/narrowcasting/commercials/thumb/'.$val);
    }
   
}


if($_POST['batch_delete_folder']=='promotie'){
    foreach($post as $key=>$val){
       // echo $val."<br>";
        unlink('/var/www/html/narrowcasting/promotie/'.$val);
        unlink('/var/www/html/narrowcasting/promotie/thumb/'.$val);
    }
   
}


if($_POST['batch_delete_folder']=='upload'){
    foreach($post as $key=>$val){
       // echo $val."<br>";
        unlink('/var/www/html/narrowcasting/upload/'.$val);
        unlink('/var/www/html/narrowcasting/upload/thumb/'.$val);
    }
   
}

echo '<script>window.location="create_carrousel.php"</script>';
?>
