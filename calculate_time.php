<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
$id=session_id();
error_reporting(0);
time();
include('config/database.php');
 $db = new Database;
 
 $ffmpeg_path = $db->ffmpeg_path;
 
//---------------------- code used for ajax call to calculate time in carrousal---------------
  $query="select duration,name from temp_carrousel where s_id='$id'";
  $res=$db->query($query);
  $cnt=0;
  if($res){
	
	  while($result=mysqli_fetch_array($res)) {
		
		if($result['duration']==1){
			
	         ob_start();
		 $carr_name=$result['name'];
		passthru("$ffmpeg_path -i /var/www/html/narrowcasting/$carr_name 2>&1");
		$duration = ob_get_contents();
		ob_end_clean();
		preg_match('/Duration: (.*?),/', $duration, $matches);
		$duration = $matches[1];
		$cnt= $cnt+ getsecond($duration);	
		}else{
		$cnt = $cnt+$result['duration'];
		}
		
	  }
	  echo $cnt;
  }else{
	 echo 0;
  }
$cnt=0;

/*function to get seconds*/

function getsecond($time){
	list ($hr, $min, $sec) = explode(':',$time);
        $time = 0;
        $time = (((int)$hr) * 60 * 60) + (((int)$min) * 60) + ((int)$sec);
       return $time;
}
die;
?>





