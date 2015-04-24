<?php
   include('config/database.php');
   include('config/master_database.php');
   include('color_scheme_setting.php');
   $db = new Database;
   $master_db = new master_Database();
   $id=$_POST['id'];
   $carr_sch=$_POST['enddate'];
   $carr_sch=explode("-",$carr_sch);
   $schedule=mktime(0,0,0,$carr_sch[0],$carr_sch[1],$carr_sch[2]);
   $update_query="update temp_carrousel set enddate=$schedule where id=$id";
   $con=$db->query($update_query);
   echo $color_scheme = trim($color_scheme);
   
?>
