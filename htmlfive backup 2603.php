<?php
error_reporting(E_ALL);

session_start();
error_reporting(0);
  include('config/database.php');

  $db = new Database;
  
  $ffmpeg_path = $db->ffmpeg_path;
    $enable_fade = $db->enable_fade;
  $fade_in_time = $db->fade_in_time;
  $fade_out_time = $db->fade_out_time;
  
 $Query = "select c.*, cl.* from carrousel c, carrousel_listing cl where cl.id=c.c_id and cl.id=$_REQUEST[id] order by c.record_listing_id asc ";

 
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
<style type="text/css">
    html, body { width: 100%; height: 100%; margin: 0; background-color: black; padding:0px; overflow:hidden; }
    div { position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; }
    #videolayer { z-index: 100; }
   /* #imagelayer { z-index: 200; }*/
    #textlayer  { z-index: 300; width: 100%; height: 100%; }
    video { position:absolute; top: 0%; left: 0%; width: 100%; height: 100%; }

marquee#text0 { position: fixed; top:    0px; left: 0px; width: 100%; color: white; background-color: blue; font-size: 30px; }
marquee#text1 { position: fixed; bottom: 0px; left: 0px; width: 100%; color: white; background-color: red;  font-size: 30px; }
img,video {}
</style>
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>

</head>

<body>
<div id="videolayer"  >
 
    <video id="video1">
        <source id="source1" src="" type="video/mp4" />
        
    </video>
    
 
</div>

<div id="imagelayer" >    
     <img id="img0" src="" starttime="0"  displaytime="10" />
</div>
<?php
$inc=0;
foreach($sequence as $key=>$val):


if($val=='url'):

?>
<div id="url_layer_<?php echo $inc; ?>" style="display:none;">
    <iframe src="<?php echo $source_content[$key]; ?>" id="url_iframe" height="1080px;" width="1920px;"></iframe>
</div>

<?php
endif;
$inc++;
endforeach;

?>
<div id="textlayer" style="display:none;">

    <marquee id="text0" behavior="scroll" loop="infinite" starttime="20" displaytime="30">
        This is HTMl AREA Testing
    </marquee>
    <marquee id="text1" behavior="scroll" loop="infinite" starttime="15" displaytime="30">
         This is HTMl AREA Testing
    </marquee>

</div>
<script type="text/javascript">
$(document).ready(function(){
   
    var sequence_array = <?php echo $sequence_js_array_string; ?>;
    var content_array = <?php echo $content_js_array_string; ?>;
    var duration_array = <?php echo $duration_js_array_string; ?>;
    var cnt = 1;
    var inc = 1;
    if(sequence_array[0]=='video'){
      
        play_video(content_array[0], duration_array[0]);
       
        
    }else if(sequence_array[0]=='image'){
        
        $('#img0').attr("src",content_array[0]);
       
        var duration = duration_array[0];
        startImage(duration_array[0]);
        
        setTimeout(
            function() 
            {          
               $("video").trigger("ended");
               
            }, duration_array[0]
        );
    }else if(sequence_array[0]=='url'){
      
                       startText(0, duration_array[0]);
                  
                
                setTimeout(
                    function() 
                    {
                        $("video").trigger("ended");
                    }, duration_array[0]
                );
        
        }
        
        $('video').bind('ended', function() {
        if(cnt < sequence_array.length){
           
            if(sequence_array[cnt]=='image'){
               
               
                 $('#img0').attr("src",content_array[inc]);
                  
                        var duration = duration_array[inc];
                        startImage(duration_array[inc]);
                    setTimeout(
                    function() 
                    {           
                        $("video").trigger("ended");
                    
                    }, duration_array[inc]
                    );
            }else if(sequence_array[cnt]=='video'){
                play_video(content_array[inc], duration_array[inc]);
                
            }else if(sequence_array[cnt]=='url'){ 
                
                startText(cnt, duration_array[inc]);
                setTimeout(
                    function(cnt) 
                    {
                        $("video").trigger("ended");
                    }, duration_array[inc]
                );
            }
            inc = inc + 1;
            cnt = cnt + 1;
        }else{
            inc = 0;
            cnt = 0;
            if(sequence_array[cnt]=='image'){
             
                 $('#img0').attr("src",content_array[inc]);
                   
                    var duration = duration_array[inc];
                    startImage(duration_array[inc]);
                    setTimeout(
                    function() 
                    {
                        $("video").trigger("ended");
                    
                    }, duration_array[inc]
                    );
            }else if(sequence_array[cnt]=='video'){
             
            
                play_video(content_array[inc], duration_array[inc]);
                
            
            }else if(sequence_array[cnt]=='url'){           
          
                startText(cnt, duration_array[inc]);
                setTimeout(
                    function() 
                    {
                        
                        $("video").trigger("ended");
                    }, duration_array[inc]
                );
               
        }
            inc = inc + 1;
            cnt = cnt + 1;
            
            
        }
        
   });
    
    function play_video(file, duration){
      
        $("#video1 #source1").attr("src",file);
        $("#video1")[0].load();
        $("#video1")[0].play();
        <?php if($enable_fade==1):?>
         $('#videolayer').fadeIn(<?php echo $fade_in_time; ?>);
             setTimeout(
                    function() 
                    {
                      $("#videolayer").fadeOut(<?php echo $fade_out_time; ?>);
                    }, duration - <?php echo $fade_out_time; ?>
                    
                );
                  <?php else: ?>
         $('#videolayer').show();
             setTimeout(
                    function() 
                    {
                      $("#videolayer").hide();
                    }, duration
                    
             );
        <?php endif; ?>
    }
    
    function play_image(file){
     
        $("#imagelayer #img0").attr("src",file);
        startImage();
    }
    
    
    
        function startImage(dur) {
           <?php if($enable_fade==1):?> 
            $('#imagelayer').fadeIn(<?php echo $fade_in_time;?>);
             setTimeout(
                    function() 
                    {
                       $('#imagelayer').fadeOut(<?php echo $fade_out_time;?>)
                    }, dur - <?php echo $fade_out_time; ?>
                    
                );
            <?php else: ?>
            $('#imagelayer').show();
             setTimeout(
                    function() 
                    {
                       $('#imagelayer').hide()
                    }, dur
                    
             );
            <?php endif; ?>
           
        };
       
        function startText(incr, dur) {
            <?php if($enable_fade==1):?>
            $("#url_layer_"+incr).fadeIn(<?php echo $fade_in_time; ?>);
            setTimeout(
                    function() 
                    {
                       $("#url_layer_"+incr).fadeOut(<?php echo $fade_out_time; ?>);
                    }, dur - <?php echo $fade_out_time; ?>
                );
            <?php else: ?>
            $("#url_layer_"+incr).show();
            setTimeout(
                    function() 
                    {
                       $("#url_layer_"+incr).hide();
                    }, dur
            );
            <?php endif;?>
        }
       
        

    });
 


</script>
</body>
</html>
