<?php
session_start();
error_reporting(E_ALL);
ini_set('max_execution_time', 3600);// 6 min

header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");



include('config/database.php');
include('color_scheme_setting.php');
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}
$db = new Database;

$theatreDetailsSql  = "select * from theatre where status=1 limit 1";
$theatreDetails =$db->query($theatreDetailsSql);
$theatreDetails = mysql_fetch_array($theatreDetails);

$monitor_size_x = $theatreDetails['monitor_size_x'];
$monitor_size_y = $theatreDetails['monitor_size_y'];
$monitor_position_x = $theatreDetails['monitor_position_x'];



//---------------------------Code used for upload video and images ------------------------------- 
 $error_message="";
 $sid=session_id();
 $db = new Database;
 $selected_url_id=trim($_REQUEST['url_id']);
 
  //exec(" cd /");
  //exec("cd /Applications/xampp/htdocs/narrowcasting/urls");

 // form submit on on create caurrsal page
 if(isset($_POST) && count($_POST)>0 	&& $selected_url_id!="") {  
   
   $url_id=$_POST['url_id'];
   $new_url=$_POST['current_url'];
   $current_name=$_POST['current_name'];
   
   if(isset($_POST['search_term']) && !empty($_POST['search_term'])){
    $update_qry="update urls set twitter_search_term='$_POST[search_term]' where id=$url_id";
    $conn=$db->query($update_qry);
    if($_SESSION['twitter_search_term']!=$_POST[search_term]){
     $_SESSION['twitter_search_term']="";
     echo '<script>window.location="../dynamic/twitter/get-tweets.php"</script>';
    }
   }
   // if url has no http/https add at start
   if(!preg_match('/^http(s)?:\/\//', $new_url)){
    $new_url='http://'.$new_url;
   }
   // validate url
   $valid_url=true;
   if (filter_var($new_url, FILTER_VALIDATE_URL) === false){
     $valid_url=false;
   }
   
   // getting filename and prevoius url
   
     $selectQuery =  "select url,image from urls where id=$url_id limit 1";	          
     $conn=$db->query($selectQuery);
     $url_result =mysql_fetch_array($conn);
     $previous_url=$url_result['url'];
     if(!preg_match('/^http(s)?:\/\//', $previous_url)){
      $previous_url='http://'.$previous_url;
     }
     $file_name = pathinfo('./urls/'.$url_result['image']);
     $file_name=$file_name['filename'];
     
     //if($previous_url!=$new_url){
      create_webthumbs($new_url,$file_name);
    // }
   
   
  
   
 
   
   if($new_url!="" && $url_id!="" && $valid_url==true){
       $updateQuery =  "update urls set url='$new_url', name='$current_name' where id=$url_id";	          
       $conn=$db->query($updateQuery);
       
       
       // update apple script after update in db
      
       
    }else if($valid_url==false && $new_url!=""){
     $error_message="Deze URL is ongeldig.";
    }else if($new_url==""){
     $error_message="Een URL is verplicht.";
    }
  
  $selected_url_id=$url_id;
 }
 
 // begin function to kill a process after 10 seconds
 function PsExecute($command, $timeout, $sleep = 10) { 
        // First, execute the process, get the process ID 

        $pid = PsExec($command); 

        if( $pid === false ) 
            return false; 

        $cur = 0; 
        // Second, loop for $timeout seconds checking if process is running 
        while( $cur < $timeout ) { 
            sleep($sleep); 
            $cur += $sleep; 
            // If process is no longer running, return true;          

            if( !PsExists($pid) ) 
                return true; // Process must have exited, success! 
        } 

        // If process is still running after timeout, kill the process and return false 
        PsKill($pid); 
        return false; 
 }
 
 function PsExec($commandJob) { 

        $command = $commandJob.' > /dev/null 2>&1 & echo $!'; 
        exec($command ,$op); 
        $pid = (int)$op[0]; 

        if($pid!="") return $pid; 

        return false; 
    } 


   function PsExists($pid) { 

        exec("ps ax | grep $pid 2>&1", $output); 

        while( list(,$row) = each($output) ) { 

                $row_array = explode(" ", $row); 
                $check_pid = $row_array[0]; 

                if($pid == $check_pid) { 
                        return true; 
                } 

        } 

        return false; 
    }
    
    
    function PsKill($pid) { 
        exec("kill -9 $pid", $output); 
    } 
 // end function to kill a process after 10 seconds
 // work for changining apple script when url chnages
 
 
 function create_webthumbs($new_url,$file_name){
  
  // $command="/usr/local/bin/webkit2png  --clipwidth=200 --clipheight=200 -C -o urls/temp  $new_url";
$command="/usr/local/bin/webkit2png -o /var/www/html/narrowcasting/urls/temp.png -x 600 600 -g 600 600 $new_url";
  
  $output = array();
  
  if(PsExecute($command, 10)){
  
  $del2=unlink("./urls/thumb/$file_name.png");    
    rename("./urls/temp.png", "./urls/thumb/$file_name.png");
   
    $width=200;
    $height=200;
    $image_p = imagecreatetruecolor($width, $height);
    imagesavealpha($image_p , true);
    $trans_colour = imagecolorallocatealpha($image_p , 0, 0, 0, 127);
    imagefill($image_p , 0, 0, $trans_colour);
    
    $imagetransparent="./urls/thumb/$file_name.png";
    $im = imagecreatefrompng($imagetransparent);
    
    $width_1=imagesx($im);  
    $height_1=imagesy($im);
   
    imagecopyresampled($image_p, $im, 0, 0, 0, 0, $width, $height, $width_1, $height_1);
    imagepng($image_p,"./urls/thumb/$file_name.png");
  
  }   
  
 }
 
  
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Bewerk uw dynamische content</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />

<link rel="stylesheet" type="text/css" href="./css_pirobox/style_1/style.css"/>
<?php include('jsfiles.html')?>
<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->
<script type="text/javascript">
$(document).ready(function() {
   
    $().piroBox_ext({
        piro_speed : 900,
        bg_alpha : 0.1,
        piro_scroll : true //pirobox always positioned at the center of the page
    });
    
});



var newwindow;
function popitup(url) {
    if(newwindow!=undefined){
      newwindow.close()
    }
     newwindow=window.open(url,'Preview','height=800,width=1000,left=190,top=150,screenX=200,screenY=100');
}


function popitup2(url) {
    newwindow=window.open(url,'Preview','height=800,width=1000,left=190,top=150,screenX=200,screenY=100');
}
 
function checkvalidation(input){ 
	
   var theurl=document.getElementById('current_url').value;
    showBlockUI();
   //var searchterm = $("#search_term").val();   
   //  var tomatch= /http:\/\/[A-Za-z0-9\.-]{1,}\.[A-Za-z]{1}/
	 var tomatch= "^(http|https)://"
     if (tomatch.test(theurl) && theurl!="")
     {
		
	    
         return true;
     }
     else
     {
         window.alert("Dit is geen geldige url. Misschien bent u de http:// toevoeging vergeten.");
         return false; 
     }
 
  if(maxlengthExceed){
   alert('you have exceeded the max amount of characters for some of the fields');
   return !maxlengthExceed; 
  }
   
 }
 
function autosave(id) {
 if(id==1) {
     window.location='welcome.php';
    } else if(id==3) {
     window.location='upload_videoimage.php';
    } else if(id==4) {
     window.location='twitter_overlay.php';
    } else if(id==5) {
     window.location='mededelingen_overlay.php';
    } else if(id==6) {
     window.location='logout.php';
    }else if(id==7) {
     window.location='url.php';
    } 
    else if(id==2){
     window.location='create_carrousel.php';
    } else if(id==8){
      window.location='delay_mededelingen_overlay.php';
    } else {
    }
  }
     
 
    

 
</script>

<style type="text/css">

        #alle_carrousels,#nieuwe_twitterfeed,#maak_carrousel,#iets_nieuws,#uploaden_bestand,#spreekuur,#tot_ziens{
	      <?php echo $css_color_rule;?>
	}

	#url{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
	#alle_carrousels{font-size:14px;line-height:30px;}
	#maak_carrousel{font-size:14px;line-height:30px;}
	#nieuw_bestand{font-size:14px;color: #b5b5b5;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
	#uploaden_bestand{font-size:14px;line-height:30px;}
	#iets_nieuws{font-size:14px;line-height:30px;}
	#spreekuur{font-size:14px;line-height:30px;}
	#tot_ziens{font-size:14px;line-height:30px;}
	.nav{float:right;}
	.main_container{margin-top:100px;}
	
</style>

</head>

 <body >
	 
	
 
<!--------------- include header --------------->
   <?php include('header.html'); ?>
   
    <div class="main_container">
     <!--request_for=template_edit -->
   <div class="content">

                 <span class="title">URL's</span>
             <p>In de software is het mogelijk om dynamische content te tonen. Zo kunt u bijvoorbeeld complete websites laten zien maar zijn er ook specifieke oplossingen voor nieuws- en weerberichten, file-informatie of een Twitter-feed (informeer naar de mogelijkheden bij <a href="mailto:joost@pandoraproducties.nl">Joost Plas</a>).</p><p>Hieronder kunt u de URL's van de websites wijzigen, waarna u ze tijdens het aanmaken of bewerken van een carrousel kunt toevoegen aan de tijdlijn.
			 Indien u de Twitter feed heeft geactiveerd kunt u hieronder ook de Twitter zoekterm wijzigen.</p>
           
      
       </div>
 
		    <?php if($error_message!="") {?>
		    <div class="timeline_inputsize_error_msg"><img src="img/error.png" /><span><?php echo $error_message;?></span></div>
		    <?php } ?>
 
      <form name="c_create" id="c_create" action="url.php" method="POST"  onsubmit="return checkvalidation()">
	<div class="vertical_gallery">
               
	<div class="blocks_container height_auto">
	

        <?php
	$current_url="";
	$final_name="";
	$current_name="";
	$getUrlQuery="select * from urls where editable <> 0 limit 10";
        $url_data=$db->query($getUrlQuery);
	
	//echo $status4.'>>>>>>>>>>>';
        while($url_row =mysql_fetch_array($url_data)){
	 
	 //echo "Name : ".$url_row['name'];
	 //echo "<pre>"; print_r($url_row); echo "</pre>";
	 $row_url = preg_replace('/^http(s)?:\/\//','', $url_row['url']);
	 
	 //exit;
	 if(substr($row_url, -1)=='/')
	 $row_url=substr($row_url, 0,-1);
	 $row_image_name  = $url_row['image'];
	 $row_id    = $url_row['id'];
	 if(isset($selected_url_id)&& $selected_url_id==$row_id){
	  $current_url=$url_row['url'];
	  $current_name=$url_row['name'];
	  $check=$url_row['editable'];
	  $twitter_search_term=$url_row['twitter_search_term'];
	  $_SESSION['twitter_search_term'] = $twitter_search_term;
	  $url_id=$row_id;
	 }
	  
	  if($url_row['name']==""){
	    $final_name=$row_url;
	  }else{
	    $final_name=$url_row['name'];
	  }
	  $new_url=str_replace('http://localhost','..',$url_row["url"]);
	?>
	
	
	
            	<div class="block" id="block_url">
		 <?php
		 if(isset($selected_url_id) and $selected_url_id ==$row_id){$checked ="checked"; $flag=1;}else{$checked='';} ?>
		    <?php if($url_row['editable']<>2):?>
		    <p class="blockTitle"><?php echo $final_name;?></p>
                    <div class="block_img"><img src="./urls/thumb/<?php echo $row_image_name."?time=".time();?>" alt=""/></div>
		    <?php endif;?>
		    <?php if($url_row['editable']==2):?>
		    <p class="blockTitle"><?php echo $final_name;?></p>
		    	<div class="block_img"><img src="./urls/thumb/<?php echo $row_image_name."?time=".time();?>" alt=""/></div>
		    <?php endif;?>
		    
                    <div class="radioButton_sec"><input id='check_<?php echo $row_id;?>' type="radio" name="url_id" value="<?php echo $row_id;?>" onclick="changeUrl('<?php echo $row_id;?>');" class="radioButton" <?php echo $checked; ?>/> </div>
                    <div class="btn2"><a href="javascript:void(0);" rel='single' onclick="popitup('<?php echo $new_url;?>')">Preview</a></div>
                    <div class="clear"></div>
                </div>
		<?php
		  }
		?>
            </div>
        <!-- IMG BLOCKS END -->
	
	      
	        <div class="overlay_fields"  >
		 <div class="overlay_wrap"  >
                  
		  <div class="single_row" id="url_input_field">
		   <?php if($check!=2):?>
                     <span style="float: left;margin-top: 7px; width:160px;"><label for="line3">Url:</label></span>
	            <input type="text" style="padding-left:10px;" name="current_url" id="current_url" size="50" maxlength="250" value="<?php echo $current_url; ?>">&nbsp;&nbsp;
		    <!--
		    </br> </br>
		    <span style="width: 40px;float: left;margin-top: 7px;"><label for="line3">Name:</label></span>
	            <input type="text" style="padding-left:10px;" name="current_name" id="current_name" size="120" maxlength="250" value="<?php echo $current_name; ?>">&nbsp;&nbsp;-->
		  
		    </br> </br>
		    <?php
		    endif;
		    if($check==2):
		    ?>
		     <span style="float: left;margin-top: 7px; width:160px;"><label for="line3">Twitter zoekopdracht:</label></span>
	            <input type="text" style="padding-left:10px;" name="search_term" id="search_term"  maxlength="250" size="50" value="<?php echo $twitter_search_term;?>">
		     <?php endif;?>
                 </div>
		 </div>
        <!-- input fields end -->
        
   		<!-- START FOOTER -->
    	
       <div class="footer" style="display:block;" id="showcreate">
	     <input type="hidden" name="url_id" value="<?php echo $url_id; ?>" id="url_id">
	    <?php if($check!=2):?>
            <div class="footer_tab"><input type="submit" name="submit" value="Opslaan"></div>
	    <?php endif; ?>
        </div>
      </form>
      
	    <?php if($check==2):?>
	    
            <div class="footer_tab"><input type="button" name="save_search" id="save_search" value="Opslaan" style="position:relative;top:-68px;"></div>
	    <?php endif; ?>
     </div>
    <div id="domMessage" style="display:none;">

<img style="margin:20px 0px 10px 0px;" src="img/<?php echo $color_scheme;?>/loader.gif" />
    
    <h5 style="margin:3px 10px 10px 10px">Uw wijziging wordt doorgevoerd...</h5>
    
</div> 
<script>
function showBlockUI(){
  $.blockUI({ 
            message: $('#domMessage'),
            css: { opacity: .9,
            padding: '20',
            textAlign: 'center',
            } 
          }); 
}

function changeUrl(url_id){
	//showBlockUI();
 window.location.href="url.php?url_id="+url_id;
// $.unblockUI();
}



$(document).ready(function() { 
    $('#save_search').click(function() {
     showBlockUI();
     var val;
     val=$("#c_create").serialize();
      $.ajax({
           url: "../dynamic/twitter/get-tweets.php",            
           type: "post",
           data: val,
           success: function (responseText) {
	    $.unblockUI();
               //$("#currency_dd").html(responseText);
           }
       })  
      
    }); 
});
 
</script>
