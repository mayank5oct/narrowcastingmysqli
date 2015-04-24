<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
date_default_timezone_set('Europe/Amsterdam');
error_reporting(0);
 $id=$_REQUEST['id'];
 require('config/database.php');
 require('config/master_database.php');
 include('color_scheme_setting.php');
 $delete_button_path="./img/$color_scheme/delete.png";
 $calender_button_path="./img/$color_scheme/cal.gif";
   
   $db = new Database;
   $master_db = new master_Database();
   $folder_query = "select * from narrowcasting_folder";
   $record_set = $db->query($folder_query);
   $resultset = mysql_fetch_array($record_set);
   $is_regular = $resultset['is_regular'];
   $is_client = $resultset['is_client'];
   $is_master = $resultset['is_master'];
   
   $multiscreen_query="select a.label, b.multiscreen_status,b.export_status from theatre_multiscreen as a right join theatre as b on b.id=a.theatre_id where  b.status=1";

   $conn4=$db->query($multiscreen_query);
   $multiscreen_result=mysql_fetch_array($conn4);
   $multiscreen_check = $multiscreen_result['multiscreen_status'];
 
   
 if($_GET['delete_id']!="") {
  $del_id=$_GET['delete_id'];
  if($_GET['is_master']==1){
   $delete_query="delete from master_schedule where id=$del_id";
   $master_db->query($delete_query);
  }else{
   $delete_query="delete from schedule where id=$del_id";
   $db->query($delete_query);
  }
  
 }
 
 $theatre_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
 $conn2=$db->query($theatre_query);
 $k=0;
  $theatre_ipaddress=explode(",",$result["ipaddress"]);
   while($result2=mysql_fetch_array($conn2)) {
    $eppc_status1=$result2['eppc_status'];
}
 
 
 function give_match_multiscreen($schedule, $multiscreen){
 global $db;
 $q="select multiscreen from schedule where schedule='$schedule'";
 $con=$db->query($q);
 $new_multscr=explode(',',$multiscreen);
 while($r=mysql_fetch_array($con)){
  $multiscr = explode(',',$r['multiscreen']);
   foreach($multiscr as $key=>$val){
    if(in_array($new_multscr[$key],$multiscr)){
     $is_exist = 1;
     break;
    }else{
     $is_exist = 0;
    }
   }
  
 }
 return $is_exist;
}
 
 $sch_error="";
 
  if($_POST['submit']!="") {
   $id=$_REQUEST['id'];    
    $carr_sch=$_REQUEST['sdate'];
    $every_half=$_REQUEST['every_half'];
    $carr_hr=$_REQUEST['shour'];
    $ipaddress=$_REQUEST['IP'];
    $multi_check=$_REQUEST['check_multi'];
    $ip_id=$_REQUEST['ipid'];
    $new_multi_ipaddress=substr($ip_id,-1);
    if($new_multi_ipaddress==",") {
     $ipaddress=substr($ip_id,0,-1);
    } else {
      $ipaddress=$_REQUEST['ipid'];
    }
    
    $multiscreen=substr($_REQUEST['multiscreen'],0,-1);
    //$multiscreen=$_REQUEST['multiscreen'];
    $carr_sch=explode("-",$carr_sch);
    
    if($_POST['all_client']=='all'){
     $clientsa=$_POST['all_clients'];
    }else{
     $clientsa=$_POST['clients'];
    }
    $clientsa = substr($clientsa, 1);
     
    if($every_half==1)
    $schedule=mktime($carr_hr,30,0,$carr_sch[0],$carr_sch[1],$carr_sch[2]);
    else
     $schedule=mktime($carr_hr,0,0,$carr_sch[0],$carr_sch[1],$carr_sch[2]);
     
      if($ipaddress=="" && $multi_check==1) {
     $sch_error="<table><tr><td><span style='color:#c30f0f;'>U moet een scherm kiezen</span></td></tr></table>";
    } else {
     //-------------- code check for the same screen in two carrousel ------------------------------------
      $chk_true="";
      $check_screen=explode(',',$ipaddress);
      $chk_query="select * from schedule where schedule='$schedule'";
      $conn2=$db->query($chk_query);
      while($chk_result=mysql_fetch_array($conn2)) {
       $chk_multi=$chk_result['ipaddress'];
       $chk_multi=explode(',',$chk_multi);

       for($c=0; $c<count($chk_multi); $c++) {
         if(in_array($chk_multi[$c],$check_screen)) {
         // $chk_true.="1";
         }
       }
       $chk_multi="";
      }
      
    }
     
    if(intval($ipaddress)!=0 && $multiscreen==""){
      $select_query="select * from schedule where schedule='$schedule' and ipaddress='$ipaddress'";
    }else if(intval($ipaddress)==0 && $multiscreen==""){
     $select_query="select * from schedule where schedule=".$schedule;
    }else if(intval($ipaddress)!=0 && $multiscreen!=""){
     $select_query="select * from schedule where schedule='$schedule' and ipaddress='$ipaddress' and multiscreen='$multiscreen'";
    }else if(intval($ipaddress)==0 && $multiscreen!=""){
     $select_query="select * from schedule where schedule=".$schedule." and multiscreen='$multiscreen'";
    }
    
    if($is_master==1){
     $clients_multi_query="select * from master_schedule where schedule='$schedule' and clients='$clientsa'";
    //echo $clients_multi_query="select * from master_schedule where schedule='$schedule'";
     $clients_conn1=$master_db->query($clients_multi_query);
     $clients_row=mysql_fetch_array($clients_conn1);
     $clients_count1=mysql_num_rows($clients_conn1);
     
     $clients_multi_query_check="select * from master_schedule where schedule='$schedule'";
     $clients_conn1_check=$master_db->query($clients_multi_query_check);
     $all_clients=explode(",",$clientsa);
     while($clients_row_check=mysql_fetch_array($clients_conn1_check)){
     $client_check="";
     $all_existing_clients=explode(",",$clients_row_check['clients']);
     $check_client_arrays=array_intersect($all_existing_clients,$all_clients);
      $client_check="";
      if(count($check_client_arrays) > 0){
       $client_check=true;
       
      }
      
     }
    
 } elseif ($_GET['is_client']==1){
     $select_query="select * from client_schedule where schedule='$schedule' and ipaddress='$ipaddress' and cid='$id'";
    $conn1=$db->query($select_query);
 
    $count1=mysql_num_rows($conn1);
}

else{
	
$ipcheck_query="select eppc_status,multiscreen_status from theatre where status=1";
         $conn_ip=$db->query($ipcheck_query);
         $result_ip=mysql_fetch_array($conn_ip);
         $eppc_check=$result_ip['eppc_status'];
if($eppc_check==1){         
$select_query="select * from schedule where schedule='$schedule' and ipaddress IN ($ipaddress) and cid='$id'";
}
elseif($multiscreen_check==1) {

	$multiscreen_array=explode(",",$multiscreen);
      $exist = give_match_multiscreen($schedule, $multiscreen);
    
      $select_query="select * from schedule where schedule='$schedule' and multiscreen IN ($multiscreen)";  
      
      
}
else{
 $select_query="select * from schedule where schedule='$schedule' and cid='$id'";
}





$conn1=$db->query($select_query);
    
$count_records=mysql_num_rows($conn1);
    }
    if($is_master!=1){
   if($chk_true!="") {
      $sch_error="<table><tr><td><span style='color:#c30f0f;'>Er is al een andere carrousel ingepland voor deze tijd.</span></td></tr></table>";    
     } else {
     
     
        if($count_records>0 || $exist==1) {
      $sch_error="<table><tr><td><span style='color:#c30f0f;'>Er is al een andere carrousel ingepland voor deze tijd.</span></td></tr></table>";
     } else {
         $insert_query="insert into schedule (cid,schedule,ipaddress,every_half,multiscreen) values('$id','$schedule','$ipaddress',$every_half,'$multiscreen')";
         //$master_insert_query="insert into master_schedule (cid,schedule,ipaddress,every_half,multiscreen,clients) values('$id','$schedule','$ipaddress',$every_half,'$multiscreen','$clientsa')";
         $db->query($insert_query);
         //$db->query($master_insert_query);
     }
    
    }
    }else{
     
     
     
     
     
      if($clients_count1 > 0){
       $sch_error.="<table><tr><td><span style='color:#c30f0f;'>Er is al een andere carrousel ingepland voor deze tijd.</span></td></tr></table>";
      }else if($client_check){
       $sch_error.="<table><tr><td><span style='color:#c30f0f;'>Er is al een andere carrousel ingepland voor deze tijd.</span></td></tr></table>";
      }elseif($count1>0){
       $sch_error.="<table><tr><td><span style='color:#c30f0f;'>Er is al een andere carrousel ingepland voor deze tijd.</span></td></tr></table>";
      }else{
        
        $master_insert_query="insert into master_schedule (cid,schedule,ipaddress,every_half,multiscreen,clients) values('$id','$schedule','$ipaddress',$every_half,'$multiscreen','$clientsa')";
        //$db->query($insert_query);
        $master_db->query($master_insert_query);
      }
    }
  }
  
 //----------- code for ip check for theatre---------------------------------------
 

 if($is_master==1){
  $select_query1="select * from master_schedule  where cid='$id' order by schedule asc";
  $conn=$master_db->query($select_query1);
 $count=mysql_num_rows($conn);
 }elseif ($_GET['is_client']==1){
	 $select_query1="select c.*, cs.* from client_schedule cs, clients c where find_in_set(c.id,cs.clients) and c.is_active=1 and cs.cid='$id' order by cs.schedule asc";
	   $conn=$db->query($select_query1);
	  $count=mysql_num_rows($conn);
} else {
 $select_query1="select * from schedule  where cid='$id' order by schedule asc";
 $conn=$db->query($select_query1);
 $count=mysql_num_rows($conn);
 }
 
 $b=0;
 
 $client_list=array();
 $clients_query="";
   $clients_result_set="";
   $client_result="";
   $client_list_table="";
   $sch_date="";
 if($count>0) {
  while($result=mysql_fetch_array($conn)) {
   
   $client_list=array();
   $clients_query="";
   $clients_result_set="";
   $client_result="";
   $client_list_table="";
   $sch_date="";
    //echo "<pre>"; print_r($result); echo "</pre>";
	
	if($is_master==1):
    $clients_query="select * from clients where id in($result[clients])";
    $clients_result_set = $db->query($clients_query);
    while($client_result=mysql_fetch_array($clients_result_set)){
      $client_list[]=$client_result[label];
    }
   endif;
    
    if($b%2==0) { $sty_class="gray"; } else {$sty_class="white";}
    if($is_master==1){
     $sch_date= getDutchdate($result['schedule']);
     //$sch_date = implode(",",$client_list)." ".$sch_date;
     $clients=implode("<br />",$client_list);
    
}elseif($is_client==1){
    $sch_date= getDutchdate($result['schedule']);
    //$sch_date = implode(",",$client_list)." ".$sch_date;
    $clients=implode("<br />",$client_list);
   }else{
   $sch_date= getDutchdate($result['schedule']);
   }
    
    if($result['ipaddress']=='0') {
     $ip_address="Alle schermen";
    } /*else if($result['ipaddress']!="") {
      $select_query2="select theatre_label from theatre_eppc  where id IN ($result[ipaddress])";
      $conn1=$db->query($select_query2);
      $result2=mysql_fetch_array($conn1);
      $ip_address=$result2['theatre_label'];
    } */else{
     $ip_address="";
    }
    
   if($result['ipaddress']!="") {
	   // begin showing multiscreen icons on eppc=1
      $showmultiscreen_image="";
      $multiscreen_image=array("A.png","B.png","C.png","D.png","E.png");
      $multiscreen_activeimage=array("A_active.png","B_active.png","C_active.png","D_active.png","E_active.png");
      $sel_multiscreen=explode(',',$result['ipaddress']);
       // $multiscreen_query="select a.*, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
     // echo $multiscreen_query = "select a.*, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id=$id and b.id IN($result[ipaddress])";
     $multiscreen_query = "select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
       //$multiscreen_query = "select * from schedule where cid=".$id;
       $conn2=$db->query($multiscreen_query);
       $m=0;
        while($multiscreen_result=mysql_fetch_array($conn2)) {
         
         //echo "<pre>"; print_r($multiscreen_result); echo "</pre>";
        // echo "<pre>"; print_r($sel_multiscreen); echo "</pre>";
         
         $multiscreen_status=$multiscreen_result['eppc_status'];
         
	 if(in_array($multiscreen_result['id'],$sel_multiscreen)) {
          
	  $showmultiscreen_image.="<img id='$m$b' src='img/$color_scheme/".$multiscreen_activeimage[$m]."' width='25' height='25'>";
	 } else {
          
          $showmultiscreen_image.="<img id='$m$b' src='img/$color_scheme/".$multiscreen_image[$m]."' width='25' height='25'>";
	 }
	 $m++;
       }
    }
	elseif($result['multiscreen']!=""){
		// begin showing multiscreen icons on multiscreen=1
        $showmultiscreen_image="";
        $multiscreen_image=array("A.png","B.png","C.png","D.png","E.png");
        $multiscreen_activeimage=array("A_active.png","B_active.png","C_active.png","D_active.png","E_active.png");
        $sel_multiscreen=explode(',',$result['multiscreen']);
         // $multiscreen_query="select a.*, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
       // echo $multiscreen_query = "select a.*, b.* from carrousel_listing as a left join `schedule` on (schedule.ipaddress=a.ipaddress) , theatre_eppc as b  where a.id=$id and b.id IN($result[ipaddress])";
       $multiscreen_query = "select a.label, a.id, b.multiscreen_status from theatre_multiscreen as a, theatre as b where b.id=a.theatre_id and b.status=1";
         //$multiscreen_query = "select * from schedule where cid=".$id;
         $conn2=$db->query($multiscreen_query);
         $m=0;
          while($multiscreen_result=mysql_fetch_array($conn2)) {
             
               $multiscreen_status=$multiscreen_result['multiscreen_status'];
             
             if(in_array($multiscreen_result['id'],$sel_multiscreen)) {
              
                $showmultiscreen_image.="<img id='$m$b' src='img/$color_scheme/".$multiscreen_activeimage[$m]."' width='25' height='25'>";
             } else {
              
                $showmultiscreen_image.="<img id='$m$b' src='img/$color_scheme/".$multiscreen_image[$m]."' width='25' height='25'>";
             }
             $m++;
         }
    }
	
	
    $on_early_half="";
    if($result['every_half']==1){
     $sch_date.=".30 <br>";
    }else{
      $sch_date.=".00 <br>";
    }
    if($is_master==1):
    $sch_list.="<tr class='$sty_class'><td>$clients</td><td style='padding-left:17px;'>$sch_date $ip_address $screen_name $on_every_half</td>";
    elseif($eppc_status1==1):
		$sch_list.="<tr class='$sty_class'><td style='padding-left:17px;'>$sch_date $ip_address $showmultiscreen_image</span></td>";
		elseif($multiscreen_check==1):
			$sch_list.="<tr class='$sty_class'><td style='padding-left:17px;'>$sch_date $ip_address $showmultiscreen_image</span></td>";
		
   // $sch_list.="<tr class='$sty_class'><td style='padding-left:17px;'>$sch_date $ip_address $screen_name $on_every_half</td>";
else:
   $sch_list.="<tr class='$sty_class'><td style='padding-left:17px;'>$sch_date $ip_address </td>";
   
    endif;
    
	
	
    if($_GET['is_client']==1):
		
	else:
    $sch_list.="<td style='width:112px;'><a href='savesch_multi.php?delete_id=".$result['id']."&id=$id&is_master=$is_master' onclick=\"return confirm('Weet u zeker dat u deze planning wilt verwijderen?');\"><img src='./img/delete.png' border='0' width='20' height='20' style='margin-left:40px;'></td></tr>";
    endif;            
         
    $b++;
    $showmultiscreen_image="";
  
  }
 } else {
    $sch_list.="<tr><td colspan='2' align='center'>Deze carrousel is nog niet ingepland.</td></tr>";

 }
 
 
 
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="./css_pirobox/style_1/style.css"/>
<!--::: it depends on which style you choose :::-->
<?php include('jsfiles.html')?>
<script type="text/javascript" src="./js/datetimepicker.js"></script>
</head>
<script type="text/javascript">


 function checkvalidation() {
  
  var sdate=document.getElementById('demo').value;
  var shour=document.getElementById('shour').value;
  var every_half=document.getElementById('every_half').value;
  
  var checkdate=sdate.split("-");

  var yy1=checkdate[2];
  var mm1=checkdate[0];
  var dd1=checkdate[1];
  console.log("selected day : "+dd1);
  console.log("selected month : "+mm1);
  console.log("selected year : "+yy1);

  var today = new Date();
 
  var dd2 = today.getDate();
 
  var mm2 = today.getMonth();
  var yy2 = today.getFullYear();
 
  var hh2 = today.getHours();

  var mydate2=new Date(yy2,mm2,dd2);
  var curr_date=Date.parse(mydate2);
  
  var mydate1=new Date(yy1,mm1-1,dd1);
  var userdate=Date.parse(mydate1);
 
  if(every_half==1)
  shour=shour+0.5;
  
  if(sdate=="") {
   alert('U dient een datum in te vullen.');
   return false;
  } else if(userdate<curr_date) {
   alert('U kunt geen datum of tijd uit het verleden invullen.');
   return false;
  } else if(userdate==curr_date && shour<=hh2) {
   alert('U kunt geen datum of tijd uit het verleden invullen.');
   return false;
  }else if($("#ipid").val()==""){
   alert('U dient een scherm te selecteren.');
   return false;
  }
  <?php if($is_master==1): ?>
  else if($("input:checked").length == 0){	  
   alert("U dient een client te selecteren.")
   return false;
  }
<?php endif; ?>
  else{
  return true;
  }
 }
 
 function closeandredirect() {
  window.close();
  window.opener.location='welcome.php';
 }
 
 window.onunload = reloadOnClose;
 function reloadOnClose(){
     window.opener.location.reload();
 }
    
 function addipaddress(id1,id2,id3) {
  var imagevalue=document.getElementById(id3).src;
  var ipvalue=document.getElementById('ipid').value;
  var ip_value=ipvalue.split(",");
  var myarray = imagevalue.split("/");
  var newimage=myarray[6].split(".");
  var colorscheme = myarray[5].split(".");
  var new_image=newimage[0].substr(0,1);
  if(newimage[0].length>1) {
   document.getElementById(id3).src='./img/'+colorscheme+"/"+new_image+'.png';
   ip_value.splice(ip_value.indexOf(id1), 1);
  } else {
   document.getElementById(id3).src='./img/'+colorscheme+"/"+new_image+'_active.png';
   ip_value.splice(-1, 0,id1);
  }
  var newIpVal = ip_value.join(",");
  document.getElementById('ipid').value=newIpVal;
 }
 
 
 
 
 
 
 
</script>
<body style="background-color:#ffffff;">
  <div>
   <div class="grid_container2" id="show">
	   <?php if($_GET['is_client']==1): ?>
  
<?php else: ?>
	   
    
      <table width='632px' align="center" border="0" top="-30px">
        <tr>
			<td style="font-size:26; color:<?php echo $css_color_code;?>; width:300px; font-weight:bold;">
				Carrousel plannen
			</td>
		</tr>
	  </table>
     <div style="margin-left:40px;">
      <form action="savesch_multi.php" method="POST" onsubmit="return checkvalidation()">
		
           <span>Kies de startdatum via de kalender:</span>
           <input readonly style="margin-left:13px;" type='text' name='sdate' id="demo" class="datum_plannen" size='10' readonly'>&nbsp;
            <a href="javascript:NewCal('demo','mmddyyyy')" style="margin:3px 3px 0px 0px;">
            <img src='<?php echo $calender_button_path;?>' style="position:relative;top:5px;" width='23' height='18' border='0' alt='Pick a date' /></a>
          <br />
           <span>Kies de starttijd (heel of half uur):</span>
            <select style='margin-left:23px; margin-top:10px;' name='shour' id="shour"'>
             <?php
             for($i=1; $i<=24; $i++) {
               $schedule .="<option value='$i'>$i</option>";
              }
              echo $schedule;
            ?>
            </select>
            <select style='margin-left:10px; margin-top:10px;' name='every_half' id="every_half"'>
             <option value='0'>00</option>"
             <option value='1'>30</option>"
            </select>
           
            <br />
        
            <?php
             $ipcheck_query="select eppc_status,multiscreen_status from theatre where status=1";
             $conn_ip=$db->query($ipcheck_query);
             $result_ip=mysql_fetch_array($conn_ip);
             $eppc_check=$result_ip['eppc_status'];
             $multiscreen_status=$result_ip['multiscreen_status'];
             $theatre_dropdown="";
             if($eppc_check==1) {
              $theatre_dropdown="";
              
              $showmult_ipaddress_image="<span style='margin-right:123px;'>Kies een scherm:</span>";
              $multi_ipaddress_image=array("A.png","B.png","C.png","D.png","E.png");
              
              //$theatre_dropdown.="<select style='margin-left:130px; margin-top:10px;' name='IP' id='IP'>";
              
              //$theatre_dropdown.="<option value='0' >Alle schermen</option>";
              $theatre_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
              // $theatre_query="select a.label, a.id, b.eppc_status from theatre_multiscreen as a, theatre as b where b.id=a.theatre_id and b.status=1";
              $conn2=$db->query($theatre_query);
              $i_no=0;
               while($result2=mysql_fetch_array($conn2)) {
                // $theatre_dropdown.="<option value='".$result2['id']."'>".$result2['theatre_label']."</option>";
                $showmult_ipaddress_image.="<a href='javascript:void(0)' onclick=\"addipaddress('".$result2['id']."','0','$i_no')\"><img id='$i_no' src='img/$color_scheme/".$multi_ipaddress_image[$i_no]."' width='25' height='25' style='padding-top:10px;'></a>";
                $i_no++;
              }
              $showmult_ipaddress_image.="<input type='hidden' name='ipid' id='ipid' value='' >";
              //$theatre_dropdown.="</select>";
             } else {
              //$theatre_dropdown="";
              $showmult_ipaddress_image = "";
             }
             echo $showmult_ipaddress_image."</br>";             
             
             if($multiscreen_status==1) {
              
              
              $multiscreen_image=array("A.png","B.png","C.png","D.png","E.png");
      $multiscreen_activeimage=array("A_active.png","B_active.png","C_active.png","D_active.png","E_active.png");
      $sel_multiscreen=explode(',',$result['multiscreen']);
       $multiscreen_query="select a.id, a.label, b.multiscreen_status from theatre_multiscreen as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn2=$db->query($multiscreen_query);
       $m=0;
        while($multiscreen_result=mysql_fetch_array($conn2)) {
         $multiscreen_status=$multiscreen_result['multiscreen_status'];
         
	 if(in_array($multiscreen_result['id'],$sel_multiscreen)) {
	  $showmultiscreen_image.="<a href='javascript:void(0)' onclick=\"addscreens('".$multiscreen_result['id']."','1','$m','$b','".$result['id']."')\"><img id='$m$b' src='img/$color_scheme/".$multiscreen_activeimage[$m]."' width='25' height='25' ></a>";
	 } else {
          $showmultiscreen_image.="<a href='javascript:void(0)' onclick=\"addscreens('".$multiscreen_result['id']."','1','$m','$b','".$result['id']."')\"><img id='$m$b' src='img/$color_scheme/".$multiscreen_image[$m]."' width='25' height='25' ></a>";
	 }
	 $m++;
       }
       $showmultiscreen_image.="<input name = 'multiscreen' type='hidden' id='screenid".$result['id']."' value='".$result['multiscreen']."' >";
       
             } else {
              $showmultiscreen_image="";
             }
             echo $showmultiscreen_image."</br>";
             
            ?>
         <?php if($is_master==1): ?>
          <span>Clients : </span>
          <div style="margin-left:250px; margin-top:-5px;">
           <input type="checkbox" id="all_client" name="all_client" value="all" onclick="empty_clients(this.id)" /> Alle clients </br>
            <?php
            $i=1;
            $all_clients="";
            $clients_query="select c.id, c.label from clients c ,  theatre t where t.id=c.theatre_id and t.status=1";
            $client_record_set=$db->query($clients_query);
            while($result=mysql_fetch_array($client_record_set))
            {
              $all_clients=$all_clients.",".$result['id'];
            ?>
            
           <input type="checkbox" id="client_<?php echo $i; ?>" onclick="getvalue(this.value, this.id)" class="client" name="client_<?php echo $i; ?>" value="<?php echo $result['id']; ?>" />
            <?php echo $result['label']; ?><br/ >
           <?php $i++;  } ?>
          </div>
            <input style="margin-top:10px;" type="hidden" name="all_clients" id="all_clients"  value="<?php echo $all_clients; ?>" />
            <input style="margin-top:10px;" type="hidden" name="clients" id="clients" value="<?php  //echo $all_clients; ?>" />
            <input style="margin-top:10px;" type="hidden" name="check_clients" id="check_clients" value="<?php echo $all_clients; ?>" />
            <input type="hidden" name="check_multi" value="<?php echo $multi_ip_check; ?>">
		<?php endif ?>
            <input style="margin-top:10px;" type="submit" name="submit" value="Toevoegen" class="find_btn">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            </form>
			
            </div>
      <br/>
      <?php echo $sch_error; ?>
      <br/>
      
      <?php endif; ?>
      <table width='632px' align="center" border="0">
          <tr valign="middle" class='grid_header'>
           <?php if($is_master==1): ?>
          <td>Clients</td>
          <?php endif; ?>
           <td style="padding-left:20px;">Planning</style></td>
           <?php if($_GET['is_client']==1): ?>
			   <?php else: ?>
           <td style="padding-left:20px;">Verwijderen</td>
           <?php endif; ?>
          </tr>
          <?php echo $sch_list ; ?>
      </table>
      <br/>
           <span style="float: right; margin-right: 30px; margin-bottom: 10px;"><input type="button" name="submit" value="Sluiten" class="find_btn" onclick="closeandredirect()"></span>
   </div>
  </div>
</body>
</html>

<?php
/* For date and month calculation*/
  function getDutchdate($data) {
   
    if(trim($data)!=''){
    
   /* For day calculation*/
    $days = array(
            1 => 'Maandag',
            2 => 'Dinsdag',
            3 => 'Woensdag',
            4 => 'Donderdag',
            5 => 'Vrijdag',
            6 => 'Zaterdag',
            7 => 'Zondag',
        );
         $iso_dow = strftime('%u', $data);
        $num = $iso_dow;
	if (isset($days[$num]))
        {
             $day = $days[$num];
            
        }
        else
        {
            $day ='';
        }
        /* For month calculation*/
       $months = array(
            'Januari', 'Februari', 'Maart', 'April',
            'Mei', 'Juni', 'Juli', 'Augustus',
            'September', 'Oktober', 'November', 'December'
        );
        $iso_month = date("m",$data);
	$mon = (int) $iso_month;
	$month = $months[$mon-1];
	$finaldate = $day.' '.date('j',$data).' '.$month.' '. date('Y',$data).' | '.date('H',$data);
	return $finaldate;
    }else{
      return '';
    }
    
   
  }
?>

<SCRIPT language="javascript">
function addscreens(id1,id2,id3,id4,id5) {
  var imagevalue=document.getElementById(id3+id4).src;
  var screenvalue=document.getElementById('screenid'+id5).value;
  var screen_value=screenvalue.split(",");
  var myarray = imagevalue.split("/");
  var newimage=myarray[6].split(".");
  var colorscheme = myarray[5].split(".");
  var new_image=newimage[0].substr(0,1);
  if(newimage[0].length==1) {
   document.getElementById(id3+id4).src='./img/'+colorscheme+"/"+new_image+'_active.png';
   screen_value.splice(-1, 0,id1);
  } else if( newimage[0].length > 1 ) {
   document.getElementById(id3+id4).src='./img/'+colorscheme+"/"+new_image+'.png';
   screen_value.splice(-1, 0,id1);
  }
  var newScreenVal = screen_value.join(",");
  document.getElementById('screenid'+id5).value=newScreenVal;
 }

$(function(){
 
    // add multiple select / deselect functionality
    $("#all_client").click(function () {
          $('.client').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(".client").click(function(){
 
        if($(".client").length == $(".client:checked").length) {
            $("#all_client").attr("checked", "checked");
        } else {
            $("#all_client").removeAttr("checked");
        }
 
    });
});

function getvalue(val, id){
 
 if(document.getElementById("all_client").checked==false){
 
  //$("#clients").val("");
 }
 
 var initval=$("#clients").val();
 if(document.getElementById(id).checked==false){
   var all_clients=$("#check_clients").val()
  var split_val = removeValue(all_clients, val);
  $("#check_clients").val(split_val);
  $("#clients").val(split_val);
 }
 if(document.getElementById(id).checked==true){
  //$("#clients").val("");
  $("#clients").val(initval+","+val);
 }else{
  if(initval!=""){
   
   str=initval.replace(","+val,"");
   $("#clients").val(str);   
  }
 }
}


function removeValue(list, value) {
  return list.replace(new RegExp(",?" + value + ",?"), function(match) {
      var first_comma = match.charAt(0) === ',',
          second_comma;

      if (first_comma &&
          (second_comma = match.charAt(match.length - 1) === ',')) {
        return ',';
      }
      return '';
    });
};


function empty_clients(id){
 if(document.getElementById(id).checked==false){
  // $("#clients").val("");
 }
}
</SCRIPT>
