<?php

error_reporting(1);
include('config/PHPMailerAutoload.php');
include('config/PHPMailerSMTP.php');
require_once('config/database.php');
require_once('config/get_theatre_label.php');

# Write required media content from combined_carrousel_details table

if(!isset($start_script)){
	require_once('config/database.php');
	require_once('config/master_database.php');
    $db = new Database;
    $start_script = "cron";
}
$media_content_query="select cc.id, ccl.id as c_id, ccd.name, ccd.type from client_carrousel_listing ccl
LEFT JOIN client_carrousel cc ON cc.c_id=ccl.id LEFT JOIN combined_carrousel_details ccd ON  ccd.id = cc.id where ccl.status=0";

$media_content_query_rs=$db->query($media_content_query);
 $media_content_query_count=mysql_num_rows($media_content_query_rs);

if($media_content_query_count > 0){
    $folder_name="";
    $file_data="";
    $is_url=0;
    $type="";
   while($media_content_query_result=mysql_fetch_array($media_content_query_rs)){
   // echo "<pre>"; print_r($media_content_query_result); echo "</pre>";
      if($media_content_query_result['type']=='url'){
        $is_url=1;        
      }
      if($media_content_query_result['type']!='url'){
        $file_data_array = explode('/',$media_content_query_result['name']);
        $file_data.=$media_content_query_result['name'].PHP_EOL;
        $thumb_file=explode(".","$file_data_array[1]");
        if($thumb_file[1]=="mov" || $thumb_file[1]=="mp4"){
            $thumb_file_name = $thumb_file[0].".jpg";
        }else{
            $thumb_file_name = $thumb_file[0].".png";
        }
        $file_data.=$file_data_array[0]."/thumb/".$thumb_file_name.PHP_EOL;
        $file_data.=$file_data_array[0]."/cropped".PHP_EOL;
      }
   }
   if($is_url==1){
        $file_data.='dynamic_cues'.PHP_EOL;
        $file_data.='urls'.PHP_EOL;
        //$file_data.='url/thumb/'.PHP_EOL;
   }
   
  if(isset($start_script) && ($start_script=='schedule' || $start_script=='start_carrousel')){
       $sync_file='tmp/media_content.txt';
       $file_handle=fopen($sync_file,'w');
        if(fwrite($file_handle, $file_data)){
          //echo "Media files written to $sync_file";
        }else{
          //echo "There are some errors in writting";  
        }
        fclose($file_handle);
   }elseif(isset($start_script) && $start_script=='cron'){
        $sync_file='resources/complete_sync.txt';
   }
   else{
       $sync_file='';
   }
  
   
}



# Do RSYNC work

 //passthru("rsync -avzr -e \"ssh -p 63811\" --progress --log-file=/var/www/html/narrowcasting/logs/rsync.log --files-from=/var/www/html/narrowcasting/$sync_file TPS@5.9.106.253:/var/www/html/narrowcasting /var/www/html/narrowcasting", $exit_code);
 ob_start();
 passthru("rsync -avzr -e \"ssh -p 63811\" --progress --timeout=10 --log-file=/var/www/html/narrowcasting/logs/rsync.log --files-from=/var/www/html/narrowcasting/$sync_file TPS@192.168.76.89:/var/www/html/narrowcasting /var/www/html/narrowcasting", $exit_code);
ob_end_clean();

if($exit_code==0){
  echo $exit_code;
}else{
	echo "Fail";
	echo "<br />Exit code: $exit_code";
	
	
    $email = new PHPMailer();
    $email->IsSMTP();
    
  //  $email->SMTPAuth = false;
	$email->Host = 'mail.oogziekenhuiszonnestraal.nl';
	$email->SMTPAuth = false;
	$bodytext="Rsync heeft een foutmelding gegeven.";
    $email->From      = 'joost@pandoraproducties.nl';
    $email->FromName  = $theatre_name;
    if ($start_script=='schedule'){
	$email->Subject   = "Een planning is mislukt: $theatre_name";
} elseif ($start_script=='start_carrousel'){
	$email->Subject   = "Een handmatige sync door de klant is mislukt: $theatre_name - $city";
} else {
	$email->Subject   = "Een automatische nachtelijke sync is mislukt: $theatre_name - $city";
}
    $email->Body      = $bodytext;
    $email->AddAddress( 'joost@pandoraproducties.nl' );   
    $file_to_attach = '/var/www/html/narrowcasting/logs/'; // you can give root as '/'
    $email->addAttachment( '/var/www/html/narrowcasting/logs/rsync.log' );
	
    if(!$email->Send()){
		echo "Mailer error : ". $mail->ErrorInfo;
	}else{
		echo "Message Sent ! " ;
	}
  echo $mail->ErrorInfo;
	exit;
}


?>
