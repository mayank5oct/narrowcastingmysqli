<?php

error_reporting(1);
include('config/PHPMailerAutoload.php');
require_once('config/database.php');
require_once('config/get_theatre_label.php');

$sync_file="resources/complete_sync.txt";

# Write required media content from combined_carrousel_details table

if(!isset($start_script)){
	require_once('config/database.php');
	require_once('config/master_database.php');
    $db = new Database;
    $start_script = "cron";

}

# Do RSYNC work

 ob_start();
 passthru("rsync -avzr -e \"ssh -p 63811\" --progress --timeout=10 --log-file=/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/logs/rsync.log --files-from=/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/$sync_file pandora@10.100.0.74:/Applications/XAMPP/xamppfiles/htdocs/narrowcasting /Applications/XAMPP/xamppfiles/htdocs/narrowcasting", $exit_code);
ob_end_clean();

if($exit_code==0){
	
  echo $exit_code;

}else{
	echo "Fail";
	echo $sync_file;
	echo "<br />Exit code: $exit_code";
	
	
  //  $email = new PHPMailer();
	$bodytext="Rsync heeft een foutmelding gegeven.";
    $email->From      = 'joost@pandoraproducties.nl';
    $email->FromName  = $theatre_name;
    if ($start_script=='schedule'){
	$email->Subject   = "Een planning is mislukt: $theatre_name";
} elseif ($start_script=='start_carrousel'){
	$email->Subject   = "Een handmatige sync door de klant is mislukt: $theatre_name";
} else {
	$email->Subject   = "Een automatische nachtelijke sync is mislukt: $theatre_name";
}
    $email->Body      = $bodytext;
    $email->AddAddress( 'joost@pandoraproducties.nl' );   
    $file_to_attach = '/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/logs/'; // you can give root as '/'
    $email->addAttachment( '/Applications/XAMPP/xamppfiles/htdocs/narrowcasting/logs/rsync.log' );
	
    if(!$email->Send()){
		echo "Mailer error : ". $mail->ErrorInfo;
	}else{
		echo "Message Sent ! ";
	}
  
	exit;
}


?>
