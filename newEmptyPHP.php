<?php
error_reporting(0);

require_once('config/database.php');
require_once('config/master_database.php');
require_once('include/phplibsec/Net/SSH2.php');


set_include_path(get_include_path() . PATH_SEPARATOR . 'include/phplibsec');

// maybe later add mediasync file? or try preload tag for video
// include('mediasync_html5.php');
$db = new Database();
$master_ip=$db->master_ip;

$multi_carr_query="select * from schedule where cid=".$carr_id;
$multi_carr_conn = $db->query($multi_carr_query);
$multi_carr_result=mysql_fetch_array($multi_carr_conn);
$current_schedule = $multi_carr_result['schedule'];
$query="select * from schedule where schedule = '".$current_schedule."'";
$con=$db->query($query);
$no_of_schedule=mysql_num_rows($con);
//echo "count : ".mysql_numrows($con);
if($no_of_schedule > 0 && $_REQUEST['manual']!='yes'){
    while($result=mysql_fetch_array($con)){
        $ids .= $result['cid'].",";
    }
   $ids=substr($ids, 0, -1);
 $select_query = "select a.id as carr_id, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id IN ($ids) and b.id IN($ipaddress) group by b.id";
}else{
    $select_query = "select a.id as carr_id, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id = $carr_id and b.id IN($ipaddress) group by b.id";
}



 $connc=$db->query($select_query);

 while($selectresult=mysql_fetch_array($connc)){    
 $newipaddress=$selectresult['theatre_ip'];
// command only for remote machine, works locally but may not be smart
$ssh = new Net_SSH2($newipaddress);
if (!$ssh->login('pandora', 'lksflj4518')) {
    exit('Login Failed');
}

$ssh->exec('DISPLAY=:0 nohup google-chrome --kiosk --args \'http://'.$master_ip.'/narrowcasting/htmlfive.php?id='.$selectresult[carr_id].'\' > /dev/null & ');
//$ssh->exec('printfs "google-chrome --kiosk --args \'http://'.$master_ip.'/narrowcasting/htmlfive.php?id='.$selectresult[carr_id].'\' " "joste" >>/home/pandora/.script_reboot.sh ');
/*$ssh->exec('echo -e "sed -i \'s/\"exited_cleanly\": false/\"exited_cleanly\": true/\' ~/.config/google-chrome/Default/Preferences\n
sed -i \'s/\"exit_type\": \"Crashed\"/\"exit_type\": \"Normal\"/\' ~/.config/google-chrome/Default/Preferences\n
google-chrome --kiosk --args \'http://'.$master_ip.'/narrowcasting/htmlfive.php?id='.$selectresult[carr_id].'\'" >/home/pandora/.script_reboot.sh ');*/
 //}

    
}
  exit('0');
?>
