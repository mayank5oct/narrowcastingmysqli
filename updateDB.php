<?php 
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
$id=session_id();
error_reporting(0);
include('config/database.php');
 $db = new Database;

$action 		= trim($_REQUEST['action']); 
$updateRecordsArray 	= $_POST['recordsArray'];
$_SESSION['position_changed']=1;
if($action == "updateRecordsListings"){

	$listingCounter = 1;
	foreach ($updateRecordsArray as $recordIDValue) {
		
		$query = "UPDATE temp_carrousel SET record_listing_id = " . $listingCounter . " WHERE id=" . $recordIDValue." and s_id='$id'";
		$conn=$db->query($query);
		$listingCounter = $listingCounter + 1;	
	}
	
 
 echo 0;
 exit; 
}
?>
