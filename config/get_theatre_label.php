<?php
$db = new Database;
## get all directory name from db
$theatreDetailsSql  = "select * from theatre where status=1 limit 1";
$theatreDetails =$db->query($theatreDetailsSql);
 $theatreDetails = mysql_fetch_array($theatreDetails);
 $directoryMappings=array();
 if(is_array($theatreDetails) && count($theatreDetails)){		 
   $directoryMappings[$actualDirectory['theatre_name']]     =$theatreDetails['theatre_name'];			 
   
 }
 $city = $theatreDetails['city_name'];
 $theatre_name = $theatreDetails['theatre_name'];
 $landscape_portrait = $theatreDetails['landscape_portrait'];
 $is_poster = $theatreDetails['is_poster'];

 ?>
