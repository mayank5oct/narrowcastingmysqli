<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(0);
ini_set('max_execution_time', 3600);
include('config/database.php');
include('config/master_database.php');
$db = new Database;
$master_db=new master_Database();

$folder_query = "select * from narrowcasting_folder";
$record_set = $db->query($folder_query);
$resultset = mysql_fetch_array($record_set);
$is_client = $resultset['is_client'];
$is_master = $resultset['is_master'];

$cid = $_REQUEST['cid'];
$name = $_REQUEST['name'];


// check for max carrousel count before careating a duplicate carrousel
    if ($is_master==1){
		$CountQuery = "SELECT * FROM master_carrousel_listing";
		$conn=$master_db->query($CountQuery);
   } elseif($_POST['is_client']==1){
      $CountQuery = "SELECT * FROM client_carrousel_listing";
	  $conn=$db->query($CountQuery);
   }else{
   $CountQuery = "SELECT * FROM carrousel_listing";
   $conn=$db->query($CountQuery);
   }
   
   
   $count=mysql_num_rows($conn);
   if($count>=30){
      echo $error_message="max carrousels count reached";
      die;
   }

// end of max carrousel check


if (isset($cid) and $cid != '' and isset($name) and $name != '') {
  if ($is_master==1){
     $Query = "select * from master_carrousel_listing where id = '$cid'"; 
	$conn = $master_db->query($Query);
   }elseif($_POST['is_client']==1){
     $Query = "select * from client_carrousel_listing where id = '$cid'";
    $conn = $db->query($Query);
   } else {
	$Query = "select * from carrousel_listing where id = '$cid'";
	$conn = $db->query($Query);
}
    
    $result = mysql_fetch_array($conn, MYSQL_ASSOC);
	if ($is_master==1){
    $checkname = "select * from master_carrousel_listing where name = '$name'";
	$conn1 = $master_db->query($checkname);
	} elseif($_POST['is_client']==1){
	   $checkname = "select * from client_carrousel_listing where name = '$name'";
	   $conn1 = $db->query($checkname);
	 }else {
	    $checkname = "select * from carrousel_listing where name = '$name'";
		$conn1 = $db->query($checkname);
	}
    
    if (mysql_num_rows($conn1) > 0) { 
        echo '1';
        exit;
    } else {
      if ($is_master==1){
        $Query = "insert into master_carrousel_listing (name,status) values('$name','1')";
		$conn = $master_db->query($Query);
	}else {
	 $Query = "insert into carrousel_listing (name,status) values('$name','1')";
	 $conn = $db->query($Query);
	}
        if ($is_master==1){
        $carrousel_id = mysql_insert_id($master_db->Link_ID_PREV);
	} else {
		$carrousel_id = mysql_insert_id($db->Link_ID_PREV);
	}
        $parent_id = $result['id'];
		if ($is_master==1){
         $Query = "select * from master_carrousel where c_id = '$parent_id'";
	
	
		$conn_carr = $master_db->query($Query);
		}elseif($_POST['is_client']==1){
		  $Query = "select * from client_carrousel where c_id = '$parent_id'";
		 $conn_carr = $db->query($Query);
		  }
		else {
			$Query = "select * from carrousel where c_id = '$parent_id'";
			$conn_carr = $db->query($Query);
		}
      
        while ($result_set = mysql_fetch_array($conn_carr, MYSQL_ASSOC)) { 
            $name = $result_set['name'];
            $secduration = $result_set['duration'];
            $corder = $result_set['record_listing_id'];
	  	  	$enddate = $result_set['enddate'];
	    
	if ($is_master==1){
             $carrlist_query = "insert into master_carrousel (c_id,name,duration,enddate,record_listing_id) values('$carrousel_id','$name','$secduration','$enddate','$corder')";
	    
	    
			$conn = $master_db->query($carrlist_query);
			
			
			}else {
				$carrlist_query = "insert into carrousel (c_id,name,duration,enddate,record_listing_id) values('$carrousel_id','$name','$secduration','$enddate','$corder')";
				$conn = $db->query($carrlist_query);
			}
            
	    
        }
    }
}


echo"<script type='text/javascript'>window.location = 'welcome.php'</script>";
?>



