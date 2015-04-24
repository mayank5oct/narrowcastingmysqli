<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(E_ALl);
include('config/database.php');
include('color_scheme_setting.php');
 $error="";
 $csv_filename = 'user_login_logs.csv';
 
## if already logged in redirect to user welcome page

if(isset($_SESSION['name']) && $_SESSION['name']!="" && $_SESSION['refferer']==""){
   
   echo"<script type='text/javascript'>window.location = 'welcome.php'</script>";
   exit;
 }
 
if (isset($_POST['submit'])) { 
 $username=$_POST['username'];
 $password=md5($_POST['password']);
 
  $db = new Database;
  $Query = "SELECT * FROM users where name='$username' and password='$password' LIMIT 1";
  $conn=$db->query($Query);
  $result=mysqli_fetch_array($conn);
  $count=mysqli_num_rows($conn);
 
  if($count>0) {
    $_SESSION['name']=$username;
    $_SESSION['usertype']=$result['usertype'];
    ## inserting new row in  csv file
    $date=date('Y-m-d');
    $time=date('H:i:s a');
    $current_login_record = array($username, $time,$date, "", "");
    $csvLoginData[$i]=$current_login_record;
    $fp = fopen($csv_filename, 'a+') or die("can't open use login log file!");
    fputcsv($fp, $current_login_record);
    fclose($fp); 
    chmod($csv_filename,0777);
    if($result['usertype']=='delay'){
      echo"<script type='text/javascript'>window.location = 'delay_mededelingen_overlay.php'</script>";
    exit;
    }
   if($_SESSION['refferer']==""){
    echo"<script type='text/javascript'>window.location = 'welcome.php'</script>";
    exit;
   }else{
      echo"<script type='text/javascript'>window.location = '".$_SESSION['refferer']."'</script>";
      unset($_SESSION['refferer']);  
   }
  } else {
    $error="Uw wachtwoord of gebruikersnaam is incorrect.";
  }    
}


if (isset($_GET['username']) && isset($_GET['password'])) { 
 $username=$_GET['username'];
 $password=$_GET['password'];
  $db = new Database;
  $Query = "SELECT * FROM users where name='$username' and password='$password' LIMIT 1";
  $conn=$db->query($Query);
  $result=mysql_fetch_array($conn);
  $count=mysql_num_rows($conn);
 
  if($count>0) {
    $_SESSION['name']=$username;
    $_SESSION['usertype']=$result['usertype'];
    ## inserting new row in  csv file
    $date=date('Y-m-d');
    $time=date('H:i:s a');
    $current_login_record = array($username, $time,$date, "", "");
    $csvLoginData[$i]=$current_login_record;
    $fp = fopen($csv_filename, 'a+') or die("can't open use login log file!");
    fputcsv($fp, $current_login_record);
    fclose($fp); 
    chmod($csv_filename,0777);
    if($result['usertype']=='delay'){
      echo"<script type='text/javascript'>window.location = 'delay_mededelingen_overlay.php'</script>";
    exit;
    }
   if($_SESSION['refferer']==""){
    echo"<script type='text/javascript'>window.location = 'welcome.php'</script>";
    exit;
   }else{
      echo"<script type='text/javascript'>window.location = '".$_SESSION['refferer']."'</script>";
      unset($_SESSION['refferer']);  
   }
  } else {
    $error="Uw wachtwoord of gebruikersnaam is incorrect.";
  }    
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Inlog pagina</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />


<style type="text/css">
	.main_container{margin-top:-123px;}
</style>

</head>

<body>
	<!--------------- include header --------------->
   <?php include('header_index.html'); ?>

        <!-- START MAIN CONTAINER -->
    <div class="main_container">
    	<div class="login_container">
        	<p class="login_title">Login narrowcasting</p>
        	<div class="login_img"><img src="./img/<?php echo $color_scheme;?>/login_img.png" alt="" /></div>
           
            <div class="login_fields">
            	<form name='login' action="index.php" method="POST">
                	<span style="color:red;"><?php echo $error; ?></span>
                  <p>Gebruikersnaam :</p>
                  <p><input type="text" name="username"  class="login_input_field" /> </p>
                  <p>Wachtwoord :</p>
                  <p><input type="password" name="password"  class="login_input_field" /> </p>
                  <p><input type="submit" class="login_submit" name="submit" value="LOGIN" /></p>
                </form>
            </div>
            <div class="clear"></div>
        </div>
    </div>   
  
</body>
</html>




