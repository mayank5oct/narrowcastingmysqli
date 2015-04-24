<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(E_ALL);
$sid=session_id();
include('config/database.php');
include('config/master_database.php');
include('color_scheme_setting.php');
require_once('include/phplibsec/Net/SSH2.php');
$db = new Database;
 
if(isset($_POST) && $_POST['check_client']=='Check'){
    $status=array();
    $query = "select a.*, b.id as t_id, b.status from theatre_eppc a, theatre b where a.theatre_id=b.id and b.status=1";
    $con = $db->query($query);
    while($result=mysql_fetch_assoc($con)){
        $ssh = new Net_SSH2($result['theatre_ip']); 
        if (!$ssh->login($result['username'], $result['password']) ) {
          $status[$result['id']] = 'img/screen_inactive.png'; 
        }else{
          $output = $ssh->exec('pgrep chrome');
          if($output == ''){
            $status[$result['id']] = 'img/screen_inactive.png';
          }else{
            $status[$result['id']] = 'img/screen_active.png';  
          }
          
        }
    }
    
   // echo "<pre>"; print_r($status); echo "</pre>";
   // exit;
}
function get_carrausol_id($ip_address){
    global $db;
    //$query = "select a.*, b.* from theatre_eppc a, carrousel_listing b where a.id IN (b.ipaddress) and a.theatre_ip='".$ip_address."'";
    $query="select * from carrousel_listing where ipaddress In (select id from theatre_eppc where theatre_ip=$ip_address) ";
    $con=$db->query($query);
    if(mysql_num_rows($con)>0):
    $result=mysql_fetch_assoc($con);    
    return $result['id'];
    else:
        return 0;
    endif;
}
if(isset($_GET) && !empty($_GET['id'])){
    $query = "select * from theatre_eppc where id=".$_GET['id'];
    $con = $db->query($query);
    $result=mysql_fetch_assoc($con);
    $ssh = new Net_SSH2($result['theatre_ip']);
    if (!$ssh->login($result['username'], $result['password']) ) {
        echo '<script>alert("Login failed")</script>';
    }else{
        $ssh->exec($result['password'].'| sudo -S reboot');
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Hier vindt u een overzicht van uw carrousels</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<style type="text/css">       
        #maak_carrousel,#nieuw_bestand,#nieuwe_twitterfeed,#uploaden_bestand,#iets_nieuws,#tot_ziens,#spreekuur{
	  <?php echo $css_color_rule;?>;
	}
	
	#overzicht{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
	#alle_carrousels{font-size:14px;color: #b5b5b5;line-height:30px;}
	#maak_carrousel{font-size:14px;line-height:30px;}
	#nieuw_bestand{font-size:14px;line-height:30px;}
	#nieuwe_twitterfeed{font-size:14px;line-height:30px;}
	#uploaden_bestand{font-size:14px;line-height:30px;}
	#iets_nieuws{font-size:14px;line-height:30px;}
	#spreekuur{font-size:14px;line-height:30px;}
	#tot_ziens{font-size:14px;line-height:30px;}
	td{width:100px;}
</style>
<?php include('jsfiles.html')?>
<script>
    function showBlockUI(){
  $.blockUI({ 
            message: $('#domMessage'),
            css: { opacity: .9,
            padding: '20',
            textAlign: 'center',
            } 
          }); 
}
</script>
</head>
     <?php include('header.html'); ?>
     <div class="main_container">
         <div class="content">
             <span class="title">Overzicht players</span>
             <div class="grid_container" id="show">
               <div>
                <form name="check_status" action="maintenance.php" method="POST" onsubmit="showBlockUI();">
                    <div><input type="submit" name="check_client" value="Check" /></div>
                </form>
             <table>
                 <tr class='grid_header'>
                     <td><strong>Label</strong></td>
                     <td><strong>Status</strong></td>
                     <td><strong>Reboot</strong></td>
                     <td><strong>Preview</strong></td>
                 </tr>
             <?php
             $query = "select a.*, b.id as t_id, b.status from theatre_eppc a, theatre b where a.theatre_id=b.id and b.status=1";
             $con = $db->query($query);
             while($result = mysql_fetch_assoc($con)):
                
             ?>
                 <tr>
                     <td><?php echo $result['theatre_label']; ?></td>
                     <td><img src="<?php if(!isset($status[$result['id']])){echo 'img/screen_unknown.png';}else{echo $status[$result['id']];} ?>" /></td>
                     <td><a href="maintenance.php?id=<?php echo $result['id']; ?>">Reboot</a></td>
                     <td><a href="htmlfive.php?id=<?php echo get_carrausol_id($result['theatre_ip'])?>" target="_blank">Preview</a></td>
                 </tr>
             <?php endwhile; ?>
             </table>
        
             </div>
         </div> 
             <div style="display:none;" id="domMessage">Checking ...</div>
     </div>
         