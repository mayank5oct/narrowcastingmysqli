<?php
   include('config/database.php');
   include('config/master_database.php');
   include('color_scheme_setting.php');
   $db = new Database;
   $master_db = new master_Database();
   $id=$_POST['id'];
   
   $update_query="update temp_carrousel set enddate=0 where id=$id";
   $con=$db->query($update_query);
   echo $color_scheme = trim($color_scheme);
   
?>