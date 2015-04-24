<?php
include('config/database.php');
include('color_scheme_setting.php');
 $error="";
 $csv_filename = 'user_login_logs.csv';
 
## if already logged in redirect to user welcome page

if(isset($_SESSION['name']) && $_SESSION['name']!="" && $_SESSION['refferer']==""){
   
   echo"<script type='text/javascript'>window.location = 'welcome.php'</script>";
   exit;
 }
$db = new Database;
error_reporting(0);

if(isset($_POST['submit']) && $_POST['submit'] != '' )
{
    //$db->pr($_FILES);
    $query="select * from csvimport";
    $con=$db->query($query);
    $rows_count=mysql_numrows($con);
    $new_array=array();
    while($result_array = mysql_fetch_array($con)){
        $new_array[]=$result_array;
        
    }
   // echo "<pre>"; print_r($new_array); echo "</pre>";
    //$db->pr($new_array);
    //echo "New Array Val: ".$new_array[0][0];
$row = 1;

if (($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE) {
    $i=0;
    //fgetcsv($handle);
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    //while($data = iconv('windows-1252', 'utf-8', fgetcsv($handle, 1000, ";"))){
       // $db->pr($data);
        $num = count($data);
        $num=$num+1;
        $query="";
        //echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            
            $data[$c]=addslashes(trim($data[$c]));
            $data[$c] = iconv('ISO-8859-15', 'UTF-8', $data[$c]);
           // echo "<pre>"; print_r($data); echo "</pre>"; 
            if($c==3){
                 
                $data_fielda = explode(' ',$data[$c]);
               // echo "<pre>"; print_r($data_fielda); echo "</pre>"; 
                //$date_field = str_replace('/','.',$data[1]);
                 $date_field = $data[1];
                 $date_array = explode("-",$date_field);
                $date_field = $date_array[0]."-".$date_array[1]."-"."20".$date_array[2];
                //$date_field =  '17.09.2014';
                //echo strtotime($date_field);
                $day_name = date('l', strtotime($date_field));
                //$day = date('d', strtotime($date_field));
                $day = $date_array[0];
                $month = date('M', strtotime($date_field));
                $year = $date_array[2];
                 $final_date=convert_day($day_name).' '.$day.' '.convert_month($month);
                 //$final_date=convert_day($day_name).' '.convert_month($month).' '.$year;
                 $data[1]=$final_date;
               //  $data[5]=$data_fielda[1]." UUR";
                 
                
            }
            if($c > 0){
              
            }
          
        }
        //echo "<pre>"; print_r($data); echo "</pre>";
        //echo "<pre>"; print_r($new_array[0]); echo "</pre>";
        //$dp->pr($new_array);
        ///echo "New Array Val: ".$new_array[$i][5]."<br>";
// "Data : ".$data[5];
        //echo "<pre>"; print_r($data); echo "</pre>";
       
        //if($rows_count==0){
        
         $query="insert into csvimport set ";
         if(trim(addslashes($new_array[$i][1]))!=trim($data[4])){
            $query .= "line1 = "."'$data[4]'".",";
         }
         if(trim(addslashes($new_array[$i][2]))!=trim($data[3])){
            $query .= "line2 = "."'$data[3]'".",";
         }
         if(trim(addslashes($new_array[$i][3]))!=trim($data[1])){
            $query .= "line3 = "."'$data[1]'".",";
         }
         if(trim(addslashes($new_array[$i][4]))!=trim($data[2])){
            $query .= "line4 = "."'$data[2]'".",";
         }
         if(trim(addslashes($new_array[$i][5]))!=trim($data[5])){
            $query .= "line5 = "."'$data[5]'".",";
         }       
        
         if(trim(addslashes($new_array[$i][6]))!=trim($data[5])){
            $query .= "line6 = "."'$data[5]'";
         }
         $query = rtrim($query, ',');
        if($query!="insert into csvimport set "){ 
         if($query){
            if($db->query($query)){
               // echo "Inserted";
               // echo $row;
              }else{
                echo "error";
              }
         }
        }
        /*}else{
           if($new_array[$i][1]!=$data[0] || $new_array[$i][2]!=$data[1] || $new_array[$i][3]!=$data[2] || $new_array[$i][4]!=$data[3] || $new_array[$i][5]!=$data[5] || $new_array[$i][6]!=$data[4]) {
            $query="update csvimport set ";
         if($new_array[$i][1]!=$data[0]){
            $query .= "line1 = "."'$data[0]'".",";
         }
         if($new_array[$i][2]!=$data[1]){
            $query .= "line2 = "."'$data[1]'".",";
         }
         if($new_array[$i][3]!=$data[2]){
            $query .= "line3 = "."'$data[2]'".",";
         }
         if($new_array[$i][4]!=$data[3]){
            $query .= "line4 = "."'$data[3]'".",";
         }
         if($new_array[$i][5]!=$data[5]){
            $query .= "line5 = "."'$data[5]'".",";
         }       
        
         if($new_array[$i][6]!=$data[4]){
            $query .= "line6 = "."'$data[4]'";
         }
         $query = rtrim($query, ',');
         $query .= " where id=".$new_array[$i][0];
         
         //echo $query . "<br />";
         if($query){
            if($db->query($query)){
                echo "Updated";
                echo $row;
              }else{
                echo "error";
              }
              $query="";
              
         }
            
           }
            
           }*/
         
         //exit;
        
         
        
        $i++;
    }
   fclose($handle);
   echo "<script>alert('De csv is ge-upload.');</script>";
   echo "<script>window.close(); </script>";
}



}

function convert_month($str){
      
   
    switch($str){
       case 'Jan':
        $month_name='januari';
        break;
        
       case 'Feb':
        $month_name='februari';
        break;
    
       case 'Mar':
        $month_name='maart';
        break;
    
      case 'Apr':
        $month_name='april';
        break;
    
      case 'May':
        $month_name='mei';
        break;
    
      case 'Jun':
        $month_name='juni';
        break;
    
      case 'Jul':
        $month_name='juli';
        break;
    
      case 'Aug':
        $month_name='augustus';
        break;
    
      case 'Sep':
        $month_name='september';
        break;
    
      case 'Oct':
        $month_name='oktober';
        break;
    
      case 'Nov':
        $month_name='november';
        break;
    
      case 'Dec':
        $month_name='december';
        break;
        
    }
    return $month_name;
    
}

function convert_day($str){
    switch($str){
       case 'Monday':
        $day_name='Ma';
        break;
    case 'Tuesday':
        $day_name='Di';
        break;
    case 'Wednesday':
        $day_name='Wo';
        break;
    case 'Thursday':
        $day_name='Do';
        break;
    case 'Friday':
        $day_name='Vr';
        break;
    case 'Saturday':
        $day_name='Za';
        break;
    case 'Sunday':
        $day_name='Zo';
        break;
    
    }
    return $day_name;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | CSV Upload</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />


<style type="text/css">
	/*.main_container{margin-top:-123px;}*/
</style>
<script>
function get_data_from_csv(id){
     $.ajax({  
              type: "POST", url: 'givecsvdata.php', data: {id : id},
              complete: function(data){
                alert(data.responseText);
                  //$("#show").html(data.responseText);
              }  
          }); 
}
</script>
</head>



    <body>
	<!--------------- include header --------------->
   <?php include('header_index.html'); ?>
    </body>
   <div class="main_container">
<div class="content">
    <div class="grid_container">
	<p style="margin-top:-60px; margin-bottom:30px;">Op deze pagina kunt u een csv van uw programmering importeren. U dient deze vanuit Excel op te slaan als 'CSV gescheiden door lijstscheidingsteken (Windows)'.</p> 
    <form method="post" action="" enctype='multipart/form-data'>
        <table cellpadding="5" cellspacing="5" style="width:30%;">
            <tr class="grid_header">
                <td > Upload csv file </td>
            </tr>
            <tr>
                
                <td><input type="file" name="csv" id="csv" accept=".csv" /></td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
        </table>
        <div class="footer_tab1"><input type="submit" name="submit" value="Uploaden" /></div>
    </form>
</div>
   </div>
   </div>
</html>
