<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
date_default_timezone_set('Europe/Amsterdam');
    session_start();
    $csv_filename = 'user_login_logs.csv';
    ## getting previous data for updation
    if(!(fopen($csv_filename, 'r')==FALSE)){
     $fp = fopen($csv_filename, 'r');
     $csvLoginData=array();
     $i=0;
     while(($row = fgetcsv($fp, 1024, ",")) !== FALSE){
      $csvLoginData[$i]=$row;
      $i++;
     }
     fclose($fp);
    }
    ## updating csv file
    $date=date('m-d-Y');
    $time=date('H:i:s');
    $current_login_record = $csvLoginData[$i-1];
    if(is_array($current_login_record)){
     $current_login_record[3]=$time;
     $current_login_record[4]=$date;
    }else{
     $current_login_record=array($_SESSION['username'],$time,$date,$time,$date); 
    }
    $csvLoginData[$i-1]=$current_login_record;
    $fp = fopen($csv_filename, 'w');
    foreach($csvLoginData as $loginRow){
      fputcsv($fp, $loginRow);
    } 
   fclose($fp); 
  
  
   session_destroy();
  
  
  
  
  
  
  header('Location:  index.php');
  exit;
?>
