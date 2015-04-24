<?php
include('config/database.php');
$db = new Database;
error_reporting(0);

if($_POST['filetype']=='csv'){
    $query="select line1, line2,line3,line4,line5,line6,line7,line8,enddate from csvimport where id=".$_POST['id'];
}elseif($_POST['filetype']=='xml'){
    $query="select line1, line2,line3,line4,line5,line6,line7,line8, enddate from xmlimport where id=".$_POST['id'];
}
$conn=$db->query($query);

$result=mysql_fetch_array($conn, MYSQL_ASSOC);
$selected_date=date('m-d-Y',$result['enddate']);
$result['selected_date']=$selected_date;
//$string = mysql_escape_string($result['line1']);

//var_dump($result['line1']);
//echo strlen($string);
//exit;
//echo $length = strlen($result['line1']);
//exit;
//$result['line1']=htmlentities($result['line1'],ENT_QUOTES,'ISO-8859-1');
//echo "<pre>"; print_r($result); echo "</pre>";
//$db->pr($_POST);
//echo "<pre>"; print_r($result); echo "</pre>";
//exit;

$json_string=json_encode($result);
echo $json_string;

?>
