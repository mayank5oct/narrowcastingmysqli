<?php

error_reporting(1);
include('../config/PHPMailerAutoload.php');
require_once('../config/database.php');
require_once('../config/get_theatre_label.php');

# Write required media content from combined_carrousel_details table


	
    $email = new PHPMailer();
	$bodytext="Er is een applicatie gecrashed.";
    $email->From      = 'joost@pandoraproducties.nl';
    $email->FromName  = $theatre_name;
   
	$email->Subject   = "Crash report: $theatre_name";

    $email->Body      = $bodytext;
    $email->AddAddress( 'joost@pandoraproducties.nl');  
	$email->AddCC('joostplas@gmail.com'); 
 
$email->Send()

?>
