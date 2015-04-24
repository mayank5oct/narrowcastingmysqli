<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
//error_reporting(0);
error_reporting(E_ALL|E_STRICT);

ini_set('max_execution_time', 360000);
require_once('thumb-functions.php');
require_once('config/database.php');
require_once('config/get_theatre_label.php');
chmod("images", 0777);
chmod("commercials", 0777);
chmod("overlay_video", 0777);
chmod("mededelingen", 0777);
chmod("promotie", 0777);
chmod("videos", 0777);


if($landscape_portrait==0) {

//----------------------------code for creating thumbs of landscape videos--------------------------------

createvideo_Thumbs("videos/","videos/thumb/",135,135);
createvideo_Thumbs("commercials/","commercials/thumb/",135,135);
createvideo_Thumbs("promotie/","promotie/thumb/",135,135);
createvideo_Thumbs("mededelingen/","mededelingen/thumb/",135,135,'crop_right');
createvideo_Thumbs("overlay_video/","overlay_video/thumb/",135,135);
createvideo_Thumbs("upload/","upload/thumb/",135,135);

//----------------------------code for creating thumbs of landscape images--------------------------------

createThumbs("images/","images/thumb/",135,135);
createThumbs("commercials/","commercials/thumb/",135,135);
createThumbs("promotie/","promotie/thumb/",135,135);
createThumbs("mededelingen/","mededelingen/thumb/",135,135,'crop_right');
createThumbs("overlay_video/","overlay_video/thumb/",135,135,'crop_right');
}

else {
	
//----------------------------code for creating thumbs of portrait videos--------------------------------

createvideo_Thumbs("videos/","videos/thumb/",135,135);
createvideo_Thumbs("commercials/","commercials/thumb/",135,135);
createvideo_Thumbs("promotie/","promotie/thumb/",135,135);
createvideo_Thumbs("mededelingen/","mededelingen/thumb/",135,135,'crop_top');
createvideo_Thumbs("overlay_video/","overlay_video/thumb/",135,135,'crop_top');
createvideo_Thumbs("upload/","upload/thumb/",135,135);

//----------------------------code for creating thumbs of portrait images--------------------------------

createThumbs("images/","images/thumb/",135,135,'crop_top');
createThumbs("commercials/","commercials/thumb/",135,135,'crop_top');
createThumbs("promotie/","promotie/thumb/",135,135,'crop_top');
createThumbs("mededelingen/","mededelingen/thumb/",135,135,'crop_top');
createThumbs("overlay_video/","overlay_video/thumb/",135,135,'crop_top');
	
}
?>
