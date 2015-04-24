<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(0);
ini_set('max_execution_time', 3600);
include('config/database.php');
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 //header('Location:  index.php');
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}

//--------------------------- used for saving new carroussel ------------------------------- 
 $error_message="";
 $sid=session_id();
 $db = new Database;
 
 //--------------------------- used for fetching video and img from upload folder ------------------------------- 
  $videoimage_listing="";
  if ($handle = opendir("./upload")) {
    $i=1;
     while (false !== ($entry = readdir($handle))) {
       if($entry!="" and $entry!="." and $entry!=".." and $entry!="Thumbs.db" and $entry!="thumb" and $entry!=".DS_Store") {
        $entry1=explode('.',$entry);
        $entry2=urlencode($entry);
         if($entry1[1]=="jpg" or $entry1[1]=="gif" or $entry1[1]=="png") {
           $itemname="<li class='horiz_block'>
			<div class='horiz_img'>
			<img src='./upload/".stripslashes($entry)."' width='164' height='93' alt=''/>
			<div class='close_btn'><a href='javascript:void(0);' onclick='deleteCarrousel(\"$entry\",1)'><img src='./img/close_btn.png' alt=''/></a></div>
		       </div>
		      </li>";
         } else if($entry1[1]=="mov" or $entry1[1]=="mp4"){
           $itemname="<li class='horiz_block'>
			<div class='horiz_img'>
			<img src='./upload/thumb/".str_replace(" ","_",$entry1[0]).".jpg' width='164' height='93' >
			<div class='close_btn'><a href='javascript:void(0);' onclick='deleteCarrousel(\"$entry\",2)'><img src='./img/close_btn.png' alt=''/></a></div>
		       </div>
		      </li>";
         } else {
          
         }
        $videoimage_listing.=$itemname;

      }
     }
     closedir($handle);
   }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora</title>
<link rel="stylesheet" href="./css/style.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="./css_pirobox/style_1/style.css"/>
<!--::: it depends on which style you choose :::-->

<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./js/pirobox_extended.js"></script>
<script type="text/javascript" src="./js/jquery-ui-1.7.1.custom.min.js"></script>

</head>
<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->
<script type="text/javascript">

 function additem(id1,id2) {
    $.ajax({  
              type: "POST", url: 'additem.php', data: "name1="+id1+"&name2="+id2,  
              complete: function(data){
                  $("#show").html(data.responseText);   
              }  
          });  
 }
 
 function deleteCarrousel(id,id1) {
    $.ajax({  
              type: "POST", url: 'deleteupload.php', data: "delete="+id+"&id="+id1,  
              complete: function(data){ 
                  $("#show").html(data.responseText);
              }  
          });  
 }

</script>
<body>
<!--------------- include header --------------->
   <?php include('header.html'); ?>
    
        <!-- START MAIN CONTAINER -->
    <div class="main_container">
        
        <!-- INPUT FIELD START -->
      <form name="c_create" action="create_carrousel.php" method="POST">

        <div class="horizon_gallery1">
        <div class="horizon_blocks_cont" id="show"><div id='contentLeft'><ul>
          <?php  echo $videoimage_listing; ?>
			
           </ul></div> <div class="clear">&nbsp;</div>
	</div>
          <div class="clear">&nbsp;</div>
        </div>
	<br/>
	<div id="result"  style="display:none"></div>
     </form>
    	<!-- END FOOTER -->
    </div>
  
</body>
</html>




