<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
  $db = new Database;
//-------------- code for color scheme setting -------------------------
  $color_scheme_query="select  cs.color_name,cs.color_code,theatre.logo_name,cs.id from color_scheme as cs , theatre where cs.id=theatre.color_scheme_id and theatre.status=1 limit 1";
  $conn=$db->query($color_scheme_query);
  $color_scheme_result=mysqli_fetch_array($conn);
  $color_scheme=$color_scheme_result['color_name'];
  $css_color_code=$color_scheme_result['color_code'];
  $css_file_name="style_$color_scheme.css";
  $css_color_rule="color:$css_color_code !important;";
  $color_scheme_logo=$color_scheme_result['logo_name'];
 
 // blue #0f0fc3  red #c30f0f orange #FFA500
 
 
 
 
 ?>
 
 