<?php
error_reporting(E_ALL);
include('config/database.php');
$db = new Database;



$url = "http://eventbase.peppered.nl/feed/feed.php?externepartij=pandora&theater_id=107";
$xml = simplexml_load_file($url);

//echo "<pre>"; print_r($xml); echo "</pre>";
$del_query="truncate table xmlimport";
$con=$db->query($del_query);
 /*$query="select * from xmlimport order by id asc";
    $con=$db->query($query);
    $rows_count=mysql_numrows($con);
    $new_array=array();
    while($result_array = mysql_fetch_array($con)){
        $new_array[]=$result_array;
        
}*/
$i=0;
  
  foreach($xml->event as $xmlData ){
    //echo "<pre>"; print_r($xmlData); echo "</pre>";
    //echo $xmlData."<br>";
   $title =  (string) $xmlData->title;
   $artist = $xmlData->artist;
  $hall = $xmlData->location->hall;
   $date = $xmlData->dates->date->start;
   $date_array=explode("T",$date);
   //echo "<pre>"; print_r($date_array); echo "</pre>";
   $date_day_name = give_day_name(date('l',strtotime($date_array[0])));
   $date_month_name = give_month_name(date('F',strtotime($date_array[0])));
   $date_day_number = date('j',strtotime($date_array[0]));
   //$artist =
   $date_1=$date_day_name." ".$date_day_number." ".$date_month_name;
   $time_array=explode(':',$date_array[1]);
   $time=$time_array[0].":".$time_array[1];
   
    //echo "<pre>"; print_r($xmlData->location->venue); echo "</pre>";
   // $first_array =  explode(',',$xmlData->title);
   // $second_array = explode('-',$first_array[1]);
   
   
    $day = date('d', strtotime($date_array[0]));
    // $month = date('M', strtotime($date_1));
    $month_a = date('m', strtotime($date_array[0]));
    $year = date('Y', strtotime($date_array[0]));
   
 // $enddate = mktime(0,0,0,$day,$month_a,$year);
  $enddate = mktime(0,0,0,$month_a,$day,$year);
    $line1=$title;
    $line2=$artist;
    $line3=strtolower($date_1);
   // $new_date=explode('T',$date);
    
    //$line3=$new_date[0];
    
    $line4=$time;
    //$line5=addslashes($xmlData->link);
    $line5=$hall;
    $line6=$xmlData->description;
    $line6=addslashes($line6);
   
  //  $line7 = $xmlData->enclosure->attributes()->url;
    
    //$db->pr($new_array);
   
   //if($rows_count==0){
  // echo "New Array : ".addslashes($new_array[$i][6])."<br>";
  // echo "Line data : ".$line6;
   // if($new_array[$i][1]!=$line1 || $new_array[$i][2]!=$line2 || $new_array[$i][3]!=$line3 || $new_array[$i][4]!=$line4 || addslashes($new_array[$i][5])!=$line5 || addslashes($new_array[$i][6]) != $line6 || $new_array[$i][7]!=$line7){
    $query="insert into xmlimport set ";
        // if(addslashes($new_array[$i][1])!=$line1){
              $query .= "line1 = "."'$line1'".",";
        // }
        // if(addslashes($new_array[$i][2])!=$line2){
              $query .= "line2 = "."'$line2'".",";
         //}
       //  if(addslashes($new_array[$i][3])!=$line3){
              $query .= " line3 = "."'$line3'".",";
        // }
       //  if(addslashes($new_array[$i][4])!=$line4){
              $query .= " line4 = "."'$line4'".",";
       //  }
       //  if(addslashes($new_array[$i][5])!=$line5){
              $query .= " line5 = "."'$line5'".",";
//}
       //  if(addslashes($new_array[$i][6])!=$line6){
              $query .= " line6 = "."'$line6'".",";
              
              
       //  }
      //   if(addslashes($new_array[$i][7])!=$line7){
              $query .= " line7 = "."''".",";
              $query .= " enddate =  "."'$enddate'"."";
       //  }
          //if($query=="insert into csvimport set "){
         //   $query="";
          
         //}
         $query = rtrim($query, ',');
          if($query){              
              if($db->query($query)){
                 // echo $query."<br>";
                  echo "Inserted"."<br>";
                  //echo $row;
                }else{
                  echo "error";
                }
          }
       // }
      /*}else{
        if($new_array[$i][1]!=$line1 || $new_array[$i][2]!=$line2 || $new_array[$i][3]!=$line3 || $new_array[$i][4]!=$line4 || $new_array[$i][5]!=$line5 || $new_array[$i][6]!=$line6 || $new_array[$i][7]!=$line7){
        $query="update xmlimport set ";
         if($new_array[$i][1]!=$line1){
              $query .= "line1 = "."'$line1'".",";
         }
         if($new_array[$i][2]!=$line2){
              $query .= "line2 = "."'$line2'".",";
         }
         if($new_array[$i][3]!=$line3){
              $query .= " line3 = "."'$line3'".",";
         }
         if($new_array[$i][4]!=$line4){
              $query .= " line4 = "."'$line4'".",";
         }
         if($new_array[$i][5]!=$line5){
              $query .= " line5 = "."'$line5'".",";
         }
         if($new_array[$i][6]!=$line6){
              $query .= " line6 = "."'$line6'".",";
         }
         if($new_array[$i][7]!=$line7){
              $query .= " line7 = "."'$line7'"."";
         }
          
         $query = rtrim($query, ',');
         $query .= " where id=".$new_array[$i][0];
         
          if($query){              
              if($db->query($query)){
                  echo "Updated"."<br>";
                  echo $row;
                }else{
                  echo "error";
                }
          }
        }
        //echo "Date is same. No update required !!";
         
      }*/
         
         
        // echo "<pre>"; print_r($xmlData); echo "</pre>";
   // $db->pr($line2);
   $i++;
  }
  function give_month_name($mnth){
		switch($mnth){
			case 'January':
			$monthname = 'januari';
			break;
			
			case 'February':
			$monthname = 'februari';
			break;
			
			case 'March':
			$monthname = 'maart';
			break;
			
			case 'April':
			$monthname = 'april';
			break;
			
			case 'May':
			$monthname = 'mei';
			break;
			
			case 'June':
			$monthname = 'juni';
			break;
			
			case 'July':
			$monthname = 'juli';
			break;
			
			case 'August':
			$monthname = 'augustus';
			break;
			
			case 'September':
			$monthname = 'september';
			break;
			
			case 'October':
			$monthname = 'oktober';
			break;
			
			case 'November':
			$monthname = 'november';
			break;
			
			case 'December':
			$monthname = 'december';
			break;
			
			default:
			$monthname = $mnth;
			
			
		}
		
		return $monthname;
	}
        
        function give_day_name($dayname){
		switch($dayname){
			case 'Monday':
			$monthname = 'ma';
			break;
			
			case 'Tuesday':
			$monthname = 'di';
			break;
			
			case 'Wednesday':
			$monthname = 'wo';
			break;
			
			case 'Thursday':
			$monthname = 'do';
			break;
			
			case 'Friday':
			$monthname = 'vr';
			break;
			
			case 'Saturday':
			$monthname = 'za';
			break;
			
			case 'Sunday':
			$monthname = 'zo';
			break;
			
			
			default:
			$monthname = $mnth;
			
			
		}
		
		return $monthname;
	}

//echo $xml->channel->item[0]->title;

//$db->pr($xml);
?>
