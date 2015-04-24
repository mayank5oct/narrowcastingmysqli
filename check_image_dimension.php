<?php
echo "<pre>"; print_r($_FILES); echo "</pre>";
$new_file_size = array();
foreach($_FILES['uploadfile']['tmp_name'] as $key=>$val){
    $file_getsize=getimagesize($val);
    $new_file_size[$key] = $file_getsize[1];
}

// echo "<pre>"; print_r($new_file_size); echo "</pre>";
// exit;
 echo $file_size_string = json_encode($new_file_size);
    exit;
?>
