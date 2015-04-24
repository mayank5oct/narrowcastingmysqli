<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
 ini_set('max_execution_time', 360000000);
 
  include('config/database.php');
  
 $name=$_GET['id'];
 $path=$_GET['path'];
 create_preview_video($path,$name);
 $newname=explode(".",$name);
 $basepath = getcwd();
 $newpath="./".$path."/preview/".$name;
 if(!file_exists($newpath)){
  $newpath = "./".$path."/".$name;
 }
 
 
 //http://localhost/narrowcasting/previewvideo.php?id=41.mp4&path=videos&time=1386750101085
 //http://localhost/narrowcasting/previewvideo.php?id=41.mp4&path=videos&time=1386750101085
// create_preview_video($path,$name);
 
 $ffmpeg_path = $db->ffmpeg_path;
 
 // create preview video if that is not available
 function create_preview_video($directory,$video_name){
  $directory_is_ok=1;
  $already_created_is_ok=1;
  if(!file_exists("/var/www/html/narrowcasting/$directory/preview")) {
     $directory_is_ok= mkdir("/var/www/html/narrowcasting/$directory/preview", 0777);
   
  }
  if(!file_exists("/var/www/html/narrowcasting/$directory/preview/".$video_name)) {
    $inputfilename="/var/www/html/narrowcasting/$directory/".$video_name;
    $outputfilename="/var/www/html/narrowcasting/$directory/preview/".$video_name;
    $command="$ffmpeg_path -i '$inputfilename' -acodec copy -vcodec libx264 -maxrate 900k -bufsize 500k  -threads 0 -crf 20 $outputfilename";
    //echo $command;
    exec($command,$output,$error);
  }
}
 
 
?>
<!DOCTYPE HTML>
<html>
<body>
<table width="620" cellspacing="2" cellpading="2" border="0">
  <tr>
    <td align="center">
      <!-- START OF THE PLAYER EMBEDDING TO COPY-PASTE -->
	<div id="mediaplayer">laden...</div>
	
	<script type="text/javascript" src="./js/jwplayer.js"></script>
	<script type="text/javascript">
		jwplayer("mediaplayer").setup({
			flashplayer: "player.swf",
			file: "<?php echo $newpath; ?>",
                        autostart : true,
                        width : "550",
                        height : "500"
		});
	</script>
	<!-- END OF THE PLAYER EMBEDDING -->
<!--      <video width="550" height="450" controls="controls" autoplay="autoplay">
        <source src="./<?php echo $path; ?>/<?php echo $name; ?>" type="video/mov" />
      <object width="550" height="450" src="./<?php echo $path; ?>/<?php echo $name; ?>">
      </object>
      </video>-->
    </td>
  </tr>
</table>

</body>
</html>
