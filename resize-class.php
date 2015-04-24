<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
   # ========================================================================#
   #
   #  Author:    Jarrod Oberto
   #  Version:	 1.0
   #  Date:      17-Jan-10
   #  Purpose:   Resizes and saves image
   #  Requires : Requires PHP5, GD library.
   #  Usage Example:
   #                     include("classes/resize_class.php");
   #                     $resizeObj = new resize('images/cars/large/input.jpg');
   #                     $resizeObj -> resizeImage(150, 100, 0);
   #                     $resizeObj -> saveImage('images/cars/large/output.jpg', 100);
   #
   #
   # ========================================================================#


		Class resize
		{
		    // *** Class variables
		    private $image;
		    private $width;
		    private $height;
			private $imageResized;

			function __construct($fileName)
			{
				// *** Open up the file
			    $this->image = $this->openImage($fileName);

			    // *** Get width and height
			    $this->width  = imagesx($this->image);
			    $this->height = imagesy($this->image);
			}

			## --------------------------------------------------------

			private function openImage($file)
			{
				// *** Get extension
				$extension = strtolower(strrchr($file, '.'));

				switch($extension)
				{
					case '.jpg':
					case '.jpeg':
						$img = @imagecreatefromjpeg($file);
						break;
					case '.gif':
						$img = @imagecreatefromgif($file);
						break;
					case '.png':
						$img = @imagecreatefrompng($file);
						break;
					default:
						$img = false;
						break;
				}
				return $img;
			}

			## --------------------------------------------------------

			public function resizeImage($newWidth, $newHeight, $option="auto", $cropStartX='', $cropStartY='')
			{
				// *** Get optimal width and height - based on $option
				$optionArray = $this->getDimensions($newWidth, $newHeight, $option);
				
				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];


				// *** Resample - create image canvas of x, y size
				$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
				$background = imagecolorallocate($this->imageResized, 0, 0, 0);
				// removing the black from the placeholder
				imagecolortransparent($this->imageResized, $background);
				
				// turning off alpha blending (to ensure alpha channel information 
				// is preserved, rather than removed (blending with the rest of the 
				// image in the form of black))
				imagealphablending($this->imageResized, false);
				
				// turning on alpha channel information saving (to ensure the full range 
				// of transparency is preserved)
				imagesavealpha($this->imageResized, true);

				imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);


				// *** if option is 'crop', then crop too
				if ($option == 'crop') {
					$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
				}
				
				if ($option == 'crop_right') {
					$this->crop_right($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropStartX, $cropStartY);
				}
				
				if ($option == 'crop_top') {
					$this->crop_top($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropStartX, $cropStartY);
				}
			}

			## --------------------------------------------------------
			
			private function getDimensions($newWidth, $newHeight, $option)
			{

			   switch ($option)
				{
					case 'exact':
						$optimalWidth = $newWidth;
						$optimalHeight= $newHeight;
						break;
					case 'portrait':
						$optimalWidth = $this->getSizeByFixedHeight($newHeight);
						$optimalHeight= $this->getSizeByFixedWidth($newWidth);
						
						break;
					case 'landscape':
						$optimalWidth = $this->getSizeByFixedHeight($newHeight);
						$optimalHeight= $this->getSizeByFixedWidth($newWidth);
						break;
					case 'auto':
						$optionArray = $this->getSizeByAuto($newWidth, $newHeight);
						$optimalWidth = $optionArray['optimalWidth'];
						$optimalHeight = $optionArray['optimalHeight'];
						break;
					case 'crop':
						$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
						$optimalWidth = $optionArray['optimalWidth'];
						$optimalHeight = $optionArray['optimalHeight'];
						break;
					case 'crop_right':
						$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
						$optimalWidth = $optionArray['optimalWidth'];
						$optimalHeight = $optionArray['optimalHeight'];
						break;
					case 'crop_top':
						$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
						$optimalWidth = $optionArray['optimalWidth'];
						$optimalHeight = $optionArray['optimalHeight'];
						break;
					case 'custom':						
						$optionArray = $this->custom($newWidth, $newHeight);
						$optimalWidth = $optionArray['optimalWidth'];
						$optimalHeight = $optionArray['optimalHeight'];
						break;
					
						
				}
				return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
			}

			## --------------------------------------------------------

			private function getSizeByFixedHeight($newHeight)
			{
				$ratio = $this->width / $this->height;
				$newWidth = $newHeight * $ratio;
				return $newWidth;
			}

			private function getSizeByFixedWidth($newWidth)
			{
				$ratio = $this->height / $this->width;
				$newHeight = $newWidth * $ratio;
				return $newHeight;
			}

			private function getSizeByAuto($newWidth, $newHeight)
			{
				if ($this->height < $this->width)
				// *** Image to be resized is wider (landscape)
				{       if($this->width!=$newWidth){
					    $optimalWidth = $newWidth;
					    $optimalHeight= $this->getSizeByFixedWidth($newWidth);
				        }else{
					    $optimalHeight = $newHeight;
					    $optimalWidth  = $this->getSizeByFixedHeight($optimalHeight);	
					}
				}
				elseif ($this->height > $this->width)
				// *** Image to be resized is taller (portrait)
				{
				       if($this->height!=$newHeight){
					 $optimalWidth = $this->getSizeByFixedHeight($newHeight);
					 $optimalHeight= $newHeight;
				       }else{
					 $optimalWidth= $newWidth;
					 $optimalHeight = $this->getSizeByFixedWidth($optimalWidth);
				       }
				}
				else
				// *** Image to be resizerd is a square
				{
					if ($newHeight < $newWidth) {
						$optimalWidth = $newWidth;
						$optimalHeight= $this->getSizeByFixedWidth($newWidth);
					} else if ($newHeight > $newWidth) {
						$optimalWidth = $this->getSizeByFixedHeight($newHeight);
						$optimalHeight= $newHeight;
					} else {
						// *** Sqaure being resized to a square
						$optimalWidth = $newWidth;
						$optimalHeight= $newHeight;
					}
				}

				return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
			}

			## --------------------------------------------------------

			private function getOptimalCrop($newWidth, $newHeight)
			{

				$heightRatio = $this->height / $newHeight;
				$widthRatio  = $this->width /  $newWidth;

				if ($heightRatio < $widthRatio) {
					$optimalRatio = $heightRatio;
				} else {
					$optimalRatio = $widthRatio;
				}

				$optimalHeight = $this->height / $optimalRatio;
				$optimalWidth  = $this->width  / $optimalRatio;

				return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
			}

			## --------------------------------------------------------

			private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
			{
				
				// *** Find center - this will be used for the crop
				$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
				$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );

				$crop = $this->imageResized;
				//imagedestroy($this->imageResized);

				// *** Now crop from center to exact requested size
				$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
				$background = imagecolorallocate($this->imageResized, 0, 0, 0);
				// removing the black from the placeholder
				imagecolortransparent($this->imageResized, $background);
				
				// turning off alpha blending (to ensure alpha channel information 
				// is preserved, rather than removed (blending with the rest of the 
				// image in the form of black))
				imagealphablending($this->imageResized, false);
				
				// turning on alpha channel information saving (to ensure the full range 
				// of transparency is preserved)
				imagesavealpha($this->imageResized, true);

				

				imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
			}
			
			
			private function crop_right($optimalWidth, $optimalHeight, $newWidth, $newHeight)
			{
				
				
		//		if($cropStartX==0 && $cropStartY==''){
			//	   $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );	
			//	}
			//	if($cropStartX=='' && $cropStartY== 0){					
			//	    $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
			//	}
				
				$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );	
				
				$crop = $this->imageResized;
				
				//imagedestroy($this->imageResized);
                                  
				// *** Now crop from center to exact requested size
				$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
				$background = imagecolorallocate($this->imageResized, 0, 0, 0);
				// removing the black from the placeholder
				imagecolortransparent($this->imageResized, $background);
				
				// turning off alpha blending (to ensure alpha channel information 
				// is preserved, rather than removed (blending with the rest of the 
				// image in the form of black))
				imagealphablending($this->imageResized, false);
				
				// turning on alpha channel information saving (to ensure the full range 
				// of transparency is preserved)
				imagesavealpha($this->imageResized, true);
				imagecopyresampled($this->imageResized, $crop , 0, 0, 0, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
			}

			## --------------------------------------------------------
			
			private function crop_top($optimalWidth, $optimalHeight, $newWidth, $newHeight)
			{
				
				
			//	if($cropStartX==0 && $cropStartY==''){
			//	   $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );	
			//	}
			//	if($cropStartX=='' && $cropStartY== 0){					
			//	    $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
			//	}
				
				$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
				
				$crop = $this->imageResized;
				
				//imagedestroy($this->imageResized);
                                  
				// *** Now crop from center to exact requested size
				$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
				$background = imagecolorallocate($this->imageResized, 0, 0, 0);
				// removing the black from the placeholder
				imagecolortransparent($this->imageResized, $background);
				
				// turning off alpha blending (to ensure alpha channel information 
				// is preserved, rather than removed (blending with the rest of the 
				// image in the form of black))
				imagealphablending($this->imageResized, false);
				
				// turning on alpha channel information saving (to ensure the full range 
				// of transparency is preserved)
				imagesavealpha($this->imageResized, true);
				imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, 0, $newWidth, $newHeight , $newWidth, $newHeight);
			}
			
			
			
			
			##---------------------------------------------------------
			private function custom($inputwidth, $inputheight){
			   
			   $width = imagesx($this->image);
			   $height = imagesy($this->image);
			// So then if the image is wider rather than taller, set the width and figure out the height
			if (($width/$height) > ($inputwidth/$inputheight)) {
				    $outputwidth = $inputwidth;
				    $outputheight = ($inputwidth * $height)/ $width;
				}
			// And if the image is taller rather than wider, then set the height and figure out the width
				elseif (($width/$height) < ($inputwidth/$inputheight)) {
				    $outputwidth = ($inputheight * $width)/ $height;
				    $outputheight = $inputheight;
				}
			// And because it is entirely possible that the image could be the exact same size/aspect ratio of the desired area, so we have that covered as well
				elseif (($width/$height) == ($inputwidth/$inputheight)) {
				    $outputwidth = $inputwidth;
				    $outputheight = $inputheight;
				    }
				  return array('optimalWidth' => $outputwidth, 'optimalHeight' => $outputheight);  
			}
			##---------------------------------------------------------

			public function saveImage($savePath, $imageQuality="100")
			{
				// *** Get extension
        		$extension = strrchr($savePath, '.');
       			$extension = strtolower($extension);

				switch($extension)
				{
					case '.jpg':
					case '.jpeg':
						if (imagetypes() & IMG_JPG) {
							imagejpeg($this->imageResized, $savePath, $imageQuality);
						}
						break;

					case '.gif':
						if (imagetypes() & IMG_GIF) {
							imagegif($this->imageResized, $savePath);
						}
						break;

					case '.png':
						// *** Scale quality from 0-100 to 0-9
						$scaleQuality = round(($imageQuality/100) * 9);

						// *** Invert quality setting as 0 is best, not 9
						$invertScaleQuality = 9 - $scaleQuality;

						if (imagetypes() & IMG_PNG) {
							 imagepng($this->imageResized, $savePath, $invertScaleQuality);
						}
						break;

					// ... etc

					default:
						// *** No extension - No save.
						break;
				}

				imagedestroy($this->imageResized);
			}


			## --------------------------------------------------------

		}
?>
