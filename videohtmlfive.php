<?php
error_reporting(E_ALL);
   // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }


session_start();
error_reporting(0);
  include('config/database.php');

  $db = new Database;
$Query = "select c.*, cl.* from carrousel c, carrousel_listing cl where cl.id=c.c_id and cl.id=275 order by c.record_listing_id asc ";

 
  $conn=$db->query($Query);
  $sequence=array();
  $cource_content=array();
  $i=1;
  $count=mysql_num_rows($conn);
  $sequence_js_array_string = '[';
  $content_js_array_string = '[';
  $duration_js_array_string = '[';
  while($result=mysql_fetch_array($conn)){
  
       $file_type = get_file_type($result[2]);
        $sequence[$i]=$file_type;
        if($file_type=='url'){
            
             $get_url_query="select url from urls where dynamic_cues_id =".$result[2];
            $con_get_url=$db->query($get_url_query);
            $url_result = mysql_fetch_array($con_get_url);
           
            $result[2]=$url_result['url'];
            $duration = $result[3]*1000;
        }elseif($file_type=='video'){
            
            $srcFile="/var/www/html/narrowcasting/$result[2]";    
            ob_start();
            passthru("$ffmpeg_path -i $srcFile 2>&1");
            $output = ob_get_contents();
           
            ob_end_clean();
            preg_match_all('/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/', $output, $matches);
          
            $hour_to_second = $matches[1][0] * 60 * 60;
            $minute_to_second = $matches[2][0] * 60;
            $second_to_second = $matches[3][0];
            $total_seconds=$hour_to_second + $minute_to_second + $second_to_second;
            $duration = $total_seconds * 1000;
            
        }else{
            $duration = $result[3]*1000;
        }
        $source_content[$i]=$result[2];
        
        if($count == $i){
            $sequence_js_array_string .= '"'.$file_type.'"';
            $content_js_array_string .= '"'.$result[2].'"';
            $duration_js_array_string .= '"'.$duration.'"';
        }else{
           $sequence_js_array_string .= '"'.$file_type.'",';
           $content_js_array_string .= '"'.$result[2].'",';
           $duration_js_array_string .= '"'.$duration.'",';
        }
        $i++;
    
  }
 $sequence_js_array_string .= ']';
 $content_js_array_string .= ']';
 $duration_js_array_string .= ']';
 

 
  function get_file_type($file){
   $file_array = explode('.',$file);
   if($file_array[1]=='jpg' || $file_array[1]=='jpeg' || $file_array[1]=='png' || $file_array[1]=='gif'){
    $file_type='image';
   }elseif($file_array[1]=='mp4' || $file_array[1]=='mov'){
    $file_type='video';
    
   }else{
    $file_type = 'url';
   }
   return $file_type;
  }
?>
<!DOCTYPE html>
<html>
<head>
<title>Media Player Digital Signage</title>
<!--<link rel="stylesheet" href="./css/animate.css" type="text/css" />
<link rel="stylesheet" href="./css/multi-select.css" type="text/css" />-->
<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="js/LifemirrorPlayer.js"></script>
</head>

<body>
    <div class="wrapper">
        <section class="intro-banner movie">
        <div style="height: 609px; width: 1082px;">
            <div id="progress">
                <p style="text-align: center; margin-top: 245px">Lifemirror is synchronising with the audience.<br />The performance will begin shortly.</p>
            </div>
            <div style="cursor: pointer; width: 100%; height: 100%" onclick="screenfull.toggle($('#film')[0]);" id="film">
                <!-- film goes here -->
            </div>
        </div>
        <div align="right">
            <a onclick="LifemirrorPlayer.toggleMute()" style="cursor: pointer">Watch in silence</a>
        </div>
    </section>
    </div>
    <script>
        // Lifemirror Player
        //var data = <?php echo $content_js_array_string; ?>;
        var player = new LifemirrorPlayer();
        $.get("http://10.100.0.41/narrowcasting/getvideos.php", function(data) {        
            data = data.split(",");
            player.initialise(data, "film", "http://10.100.0.41/narrowcasting/", null);
            player.preloadVideos();
           
        });
    </script>
</body>
