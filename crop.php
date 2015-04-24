<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
 
	$src_image = explode("/",$_POST['source_image']);	
	$src = ".".$_POST['source_image'];
	$new_image_name=explode('.',$src_image['2']);
	
	//echo "<pre>"; print_r($new_image_name); echo "</pre>";
	
	$r_no=rand(1111,9999);
	echo $croped_image_name = $new_image_name[0]."_".$r_no.".".$new_image_name[1];
	if($new_image_name[1]=='png'){
	 $img_r = imagecreatefrompng($src);
	}else if($new_image_name[1]=='jpg' || $new_image_name[1]=='jpeg'){
	 $img_r = imagecreatefromjpeg($src);
	}else if($new_image_name[1]=='gif'){
	 $img_r = imagecreatefromgif($src);
	}else{
	
	}
	
	
	
	
	
	
	 $dst_r = ImageCreateTrueColor( $_POST['w'], $_POST['h'] );
	 if($new_image_name[1]=='png'){
  
    $background = imagecolorallocate($dst_r, 0, 0, 0);
				// removing the black from the placeholder
				imagecolortransparent($dst_r, $background);
				
				// turning off alpha blending (to ensure alpha channel information 
				// is preserved, rather than removed (blending with the rest of the 
				// image in the form of black))
				imagealphablending($dst_r, false);
				
				// turning on alpha channel information saving (to ensure the full range 
				// of transparency is preserved)
				imagesavealpha($dst_r, true);
}
	 
	 $dstination_r = "/Applications/XAMPP/htdocs/narrowcasting/".$src_image['1']."/cropped/".$croped_image_name;
         $put_to_parent="/".$src_image['1']."/cropped/".$croped_image_name;
	 
imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$_POST['w'],$_POST['h'],$_POST['w'],$_POST['h']);
if($new_image_name[1]=='png'){
  
    imagepng($dst_r,"$dstination_r");
}else if($new_image_name[1]=='jpg' || $new_image_name[1]=='jpeg' ){
     imagejpeg($dst_r,"$dstination_r");
}elseif($new_image_name[1]=='gif'){
   imagegif($dst_r,"$dstination_r");
}
if($_POST['imgno']!='two'){
   echo "<script>window.opener.document.getElementById('selected_image_path').value='$put_to_parent'</script>";
   echo "<script>window.opener.document.getElementById('selected_image_thumb').src='.$put_to_parent'</script>";
}else{
  echo "<script>window.opener.document.getElementById('selected_image_path_2').value='$put_to_parent'</script>";
   echo "<script>window.opener.document.getElementById('selected_image_thumb_two').src='.$put_to_parent'</script>";
}
    echo "<script>window.close();</script>";
    
}

// If not a POST request, display page below:

?><!DOCTYPE html>
<html lang="en">
<head>
  <title>Crop uw afbeelding</title>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery.Jcrop.js"></script>
  <link rel="stylesheet" href="css/style_common.css" type="text/css" />
  <link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />

<script type="text/javascript">

  $(function(){

    $('#cropbox').Jcrop({
      aspectRatio: <?php echo !empty($_GET['aspect_ratio']) ? $_GET['aspect_ratio'] : 1 ; ?>,
      onSelect: updateCoords,
      setSelect:   [ 200, 200, 100, 100 ],
      boxWidth:500,
      boxHeight:500
    });

  });

  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
  };

</script>
<style type="text/css">
  #target {
    background-color: #ccc;
    width: 500px;
    height: 330px;
    font-size: 24px;
    display: block;
  }


</style>

</head>
<body>

<div class="container">
<div class="row">
<div class="span12">
<div class="jc-demo-box">



		<!-- This is the image we're attaching Jcrop to -->
		<img src=".<?php echo $_GET['img']; ?>" id="cropbox" />

		<!-- This is the form that our event handler fills -->
		<form action="crop.php" method="post" onsubmit="return checkCoords();">
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="hidden" id="imgno" name="imgno" value="<?php echo $_GET['imgno']?>" />
			<input type="hidden" id="source_image" name="source_image" value="<?php echo $_GET['img']; ?>" />
			<input type="submit" id="crop_btn" value="Crop afbeelding" />
		</form>

		
	</div>
	</div>
	</div>
	</div>
	</body>

</html>
