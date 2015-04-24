<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
error_reporting(0);
session_start();
ini_set('max_execution_time', 3600);
ini_set('upload_max_filesize', '200M');
include('config/database.php');
include('color_scheme_setting.php');
$db = new Database;
if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}

//---------------------------Code used for upload video and images -------------------------------
   $id=$_GET['id'];
   $select_carr="select * from carrousel_listing  where id='$id'";
   $res_carr=$db->query($select_carr);
   $result_carr=mysql_fetch_array($res_carr);
   $carr_name=str_replace(" ","_",$result_carr['name']);
   $filename="$carr_name.mp4";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Download hier de export van uw carrousel</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<script src="./js/jquery-1.6.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
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
    } else if(id==2){
     window.location='create_carrousel.php';
    } else {
     
    }
}
</script>
<style type="text/css">
 #maak_carrousel,#nieuw_bestand,#nieuwe_twitterfeed,#uploaden_bestand,#alle_carrousels,#iets_nieuws{
	  <?php echo $css_color_rule;?>;
	}
#alle_carrousels{font-size:14px;line-height:30px;}
	#maak_carrousel{font-size:14px;line-height:30px;}
	#nieuw_bestand{font-size:14px;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
	#uploaden_bestand{font-size:14px;line-height:30px;}
	#iets_nieuws{font-size:14px;line-height:30px;}
</style>

</head>
<!-- Global IE fix to avoid layout crash when single word size wider than column width -->
<!--[if IE]><style type="text/css"> body {word-wrap: break-word;}</style><![endif]-->

<body>
  	<!--------------- include header --------------->
   <?php include('header.html'); ?>
   
    <div class="main_container">
    <div class="content">
            <span class="title">Export carrousel</span>
           <p>Hieronder kunt u de export van uw carrousel downloaden.<br />
			<br />
           <a href="download_cexp.php?filename=<?php echo $filename; ?>">Klik hier om te downloaden.</a>
           </p>
      </div>
    </div>
  
</body>
</html>

<script language="javascript">
function Checkfiles()
{
 var letters = /^[a-zA-Z0-9-_ ]+$/;  
 var fup = document.getElementById('uploadfile');
 var fileName = fup.value;
/*add by Himani Agarwal for resricted files having more than one dot*/
 var lastPosDot = fileName.lastIndexOf('.');
 var posDot = fileName.indexOf('.');
  if(lastPosDot != posDot){
   alert('Geen geldige bestandsnaam, probeer het opnieuw. U kunt proberen om alle speciale tekens uit de bestandsnaam te verwijderen.');
   return false;
  }
  /*end here*/
  var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
  if(ext == "jpg" || ext == "JPG" || ext == "jpeg" || ext == "JPEG" || ext == "png"  || ext == "PNG" || ext == "Jpg")
  {
  return true;
  } 
  else
  {
  alert("U kunt alleen afbeeldingen uploaden.");
  fup.focus();
  return false;
  }
  
  /*var filename_chk=fileName.split('.');
  if(filename_chk[0].search(letters)==-1) {
    alert('U kunt alleen letters en cijfers in uw carrousel gebruiken (dus geen speciale karakters of interpunctie).');
    return false;
  } else {
    return true;
  }*/
}
</script>
