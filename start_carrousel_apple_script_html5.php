<?php
error_reporting(E_ALL);

require_once('config/database.php');
require_once('config/master_database.php');
require_once('include/phplibsec/Net/SSH2.php');


set_include_path(get_include_path() . PATH_SEPARATOR . 'include/phplibsec');

$db = new Database();
$master_ip=$db->master_ip;

$multi_carr_query="select * from schedule where cid=".$carr_id;
$multi_carr_conn = $db->query($multi_carr_query);
$multi_carr_result=mysql_fetch_array($multi_carr_conn);
$current_schedule = $multi_carr_result['schedule'];
$query="select * from schedule where schedule = '".$current_schedule."'";
$con=$db->query($query);
$no_of_schedule=mysql_num_rows($con);


// BEGIN QUERY FOR SCHEDULE.PHP
if($no_of_schedule > 0 && $_REQUEST['manual']!='yes'){

$cid=$result['cid'];
$ipaddress=$result['ipaddress'];
$select_query = "select a.id as carr_id, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id IN ($cid) and b.id IN($ipaddress) group by b.id";
}

//BEGIN QUERY FOR REMOVEITEMS.PHP
else{
    if(isset($carrousel_id) && !empty($carrousel_id)){
     // foreach($carrousel_id as $key=>$val){
 $select_query = "select a.id as carr_id, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id = $carr_id and b.id IN($ipaddress) group by b.id";
//}

}
//BEGIN QUERY FOR MANUAL START ON WELCOME.PHP
else {
$select_query = "select a.id as carr_id, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id = $carr_id and b.id IN($ipaddress) group by b.id";
}
}

 $connc=$db->query($select_query);
 
   while($selectresult=mysql_fetch_array($connc)){
      
	    $newipaddress=$selectresult['theatre_ip'];

$ssh = new Net_SSH2($newipaddress);
if (!$ssh->login($selectresult['username'], $selectresult['password'])) {
    $response[]=array('result'=>'2', 'screen' => $selectresult['theatre_label']);
   
   // exit;
  //  exit('Login Failed');
}

$ssh->exec('DISPLAY=:0 nohup google-chrome --kiosk --args \'http://'.$master_ip.'/narrowcasting/newhtmlfive.php?id='.$selectresult[carr_id].'\' > /dev/null & ');
$ssh->exec('echo -e "sed -i \'s/\"exited_cleanly\": false/\"exited_cleanly\": true/\' ~/.config/google-chrome/Default/Preferences\n
sed -i \'s/\"exit_type\": \"Crashed\"/\"exit_type\": \"Normal\"/\' ~/.config/google-chrome/Default/Preferences\n
google-chrome --kiosk --args \'http://'.$master_ip.'/narrowcasting/newhtmlfive.php?id='.$selectresult[carr_id].'\'" >/home/pandora/.script_reboot.sh ');
 //}

    
}

 echo $response_string = json_encode($response);
?>
