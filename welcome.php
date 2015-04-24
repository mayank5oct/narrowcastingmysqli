<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
//ALTER TABLE  `theatre` ADD  `eppc_status` INT( 11 ) NOT NULL
session_start();
error_reporting(0);
$sid=session_id();
include('config/database.php');
include('config/master_database.php');

include('color_scheme_setting.php');

if($_SESSION['name']=="" or empty($_SESSION['name'])) {
 //header('Location:  index.php');
 echo"<script type='text/javascript'>window.location = 'index.php'</script>";
 exit;
}
   $db = new Database;
   $master_db=new master_Database();
   $folder_query = "select * from narrowcasting_folder";
   $record_set = $db->query($folder_query);
   $resultset = mysqli_fetch_array($record_set);
   $is_client = $resultset['is_client'];
   $is_master = $resultset['is_master'];
   $regular_carrousel_allowed = $resultset['regular_carrousel_allowed'];
   $master_ip = $db->master_ip;
   ## delete old schedule entry from schedule table
   
  if($_REQUEST['delete_old_schedule']!="" && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
     $current_hour_timestamp=mktime(date('H'),0,0,date('n'),date('d'),date('Y'));
     $query="delete from schedule where schedule<$current_hour_timestamp";
     $result=$db->query($query);
     $affected_row = mysqli_affected_rows($db->Link_ID_PREV);
     if($affected_row>0){
        echo 'success';
     }else{
        echo 'no_delete';
     }
     die;
   }
   ## end of code for old schedule deletion
   
   ## master: delete old schedule entry from schedule table
   
  if($_REQUEST['delete_old_master_schedule']!="" && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
     $current_hour_timestamp=mktime(date('H'),0,0,date('n'),date('d'),date('Y'));
     $query="delete from master_schedule where schedule<$current_hour_timestamp";
     $result=$db->query($query);
     $affected_row = mysqli_affected_rows($db->Link_ID_PREV);
     if($affected_row>0){
        echo 'success';
     }else{
        echo 'no_delete';
     }
     die;
   }
   ## end of code for old schedule deletion
   

//-------------- code for check eppc status -------------------------
  $eppc_query="select  b.eppc_status, b.id from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
  $conn3=$db->query($eppc_query);
  $eppc_result=mysqli_fetch_array($conn3);
  $eppc_newstatus=$eppc_result['eppc_status'];

  if($eppc_newstatus==1) {
    $eppc_column="<td style='text-align:center;' width='30%'>Scherm</td>";
  } else {
    $eppc_column="";
  }
  
    //-------------- code for check multiscreen status-------------------------
  $multiscreen_query="select a.label, b.multiscreen_status,b.export_status from theatre_multiscreen as a right join theatre as b on b.id=a.theatre_id where  b.status=1";

  $conn4=$db->query($multiscreen_query);
  $multiscreen_result=mysqli_fetch_array($conn4);
  
  //echo "<pre>"; print_r($multiscreen_result); echo "</pre>";
 $multiscreen_check = $multiscreen_result['multiscreen_status'];
  $multiscreen_status=trim($multiscreen_result['multiscreen_status']);
  // for issue of getting empty multiscreen_status while it has 0 in db
  if($multiscreen_status==""){
    $multiscreen_status=0;
  }
  
  if($multiscreen_status==1) {
    $multiscreen_column="<td style='text-align:center;' width='30%'>Schermgroep</td>";
  } else {
    $multiscreen_column="";
  }
  
  ## for export carrausel.........
  $export_status = $multiscreen_result['export_status'];
   if($multiscreen_result['export_status']==1) {
    $export_column="<td style='text-align:center;' width='30%'>Export</td>";
  } else {
    $export_column="";
  }
  
  

if($_GET['id']!="") {
  $carr_id=$_GET['id'];
  $ipaddress=$_GET['ipaddress'];
  
  //----- script for deleteing previous cues from Qlab player -------------------
  
 // exec("osascript delete_selected_cue.scpt");
 //------------- code for running cue start by carrousal listing --------------- 
  $update_query="update carrousel_listing set status=1 where id!=$carr_id";
  $db->query($update_query);
  
  $update_query1="update carrousel_listing set status=0 ,ipaddress='$ipaddress' where id=$carr_id";
  $db->query($update_query1);
  
  if(isset($_GET['edit']) and $_GET['edit']==1){
   $update_query_edit="update carrousel_listing set edit_status=0 where id=$carr_id";
   $db->query($update_query_edit);
   
  }
  
  $select_query="select a.*, b.* from carrousel_listing as a, theatre_eppc as b where a.id=$carr_id and a.ipaddress=b.id";
  $connc=$db->query($select_query);
  $selectresult=mysqli_fetch_array($connc);
  $carr_name= stripslashes($selectresult['name']);
  $carr_name=str_replace(" ","_",$carr_name);
  $carr_name=str_replace("'","__",$carr_name);
  $carr_name=$carr_name.".scpt";
  $newipaddress=$selectresult['theatre_ip'];
  $username=$selectresult['username'];
  $password=$selectresult['password'];
  $db->writeLog($carr_name);
  
  //------- script writing for system boot time ------------------------
  $ournewFileHandle = fopen("script/".$carr_name, 'r') or die("can't open file");
  $theData = fread($ournewFileHandle,filesize("script/".$carr_name));
  $startpos = strpos($theData, '"QLab"');
   $endpos = strpos($theData, 'tell front');
   $replace_string=substr($theData, $startpos, $endpos-$startpos-3);
  if($newipaddress!="") {
   $newrep_string='"QLab" of machine "eppc://'.$username.':'.$password.'@'.$newipaddress.'"';
   $theData=str_replace($replace_string,$newrep_string,$theData,$count);
  } else {
    $newrep_string='"QLab"';
    $theData=str_replace($replace_string,$newrep_string,$theData,$count);
  }
  
  $ourFileHandle2 = fopen("script/".$carr_name, 'w') or die("can't open file");
  fwrite($ourFileHandle2, $theData);
  fclose($ourFileHandle2);

  $ourFileHandle1 = fopen('script_reboot.scpt', 'w') or die("can't open file");
  fwrite($ourFileHandle1, $theData);
  fclose($ourFileHandle1);

  exec("osascript script/$carr_name");
}
require_once('client_carrausel.php');
require_once('client_content_block.php');


  
 //---------- code for fetching listing of carrousals-------------------
 if($is_master==0):
 $Query = "SELECT * FROM carrousel_listing ORDER BY name LIMIT 0,30";
 $conn=$db->query($Query);
 $count=mysqli_num_rows($conn);
 else:
 $Query = "SELECT * FROM master_carrousel_listing ORDER BY name LIMIT 0,30";
 $conn=$master_db->query($Query);
 $count=mysqli_num_rows($conn);
 endif;

 $carrousel_list="";
 $b=0;
 $carrousel_list.="<table>";
 if($is_master==1):
  $carrousel_list .="<tr class='grid_header_top'><td colspan='6'><h2>Master carrousels</h2></td></tr>";
  elseif($is_client==1):
  $carrousel_list .="<tr class='grid_header_top'><td colspan='6'><h2 style=\"margin-top:40px;\">Client carrousels</h2></td></tr>";
else:  
	$carrousel_list .="";
 endif;
   $carrousel_list.="<tr class='grid_header'><td height='25' width='35%'>Naam</td>
	 <!-- <td height='25' width='10%' align='center'>Export</td> -->
	    $export_column
	   <td height='25' width='10%' align='center'>Bewerken</td>
           <td width='10%' align='center'>Kopiëren</td>
           <td width='10%' align='center'>Verwijderen</td>
          <td width='10%' align='center'>Preview</td>
	   <td width='10%' align='center'>Planning</td>
            $eppc_column
	    $multiscreen_column
	  
           <td width='10%' align='center'>Start</td>
          </tr>";
  if($count==0) {
    $carrousel_list.="<tr><td colspan='5' align='center'><span style='margin:0px 0 0 0px;'>U heeft nog geen carrousel aangemaakt.</span></td></tr>";
    $carrousel_list.="</table>";
  } else {
    
    while($result=mysqli_fetch_array($conn)) {
      
      //echo "<pre>"; print_r($result); echo "</pre>";
      if($b%2==0) { $sty_class="gray"; } else {$sty_class="white";}
      
      //-------------- code for getting ipaddress for theatre-----------------------------
      
      
     /* $multiscreen_image=array("A.png","B.png","C.png","D.png","E.png");
      $multiscreen_activeimage=array("A_active.png","B_active.png","C_active.png","D_active.png","E_active.png");
      $sel_multiscreen=explode(',',$result['ipaddress']);
       $multiscreen_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn2=$db->query($multiscreen_query);
       $m=0;
        while($multiscreen_result=mysqli_fetch_array($conn2)) {
         echo "<pre>"; print_r($multiscreen_result); echo "</pre>";
         $eppc_status1=$multiscreen_result['eppc_status'];
         
	 if(in_array($multiscreen_result['id'],$sel_multiscreen)) {
	  $showmultiscreen_image.="<a href='javascript:void(0)' onclick=\"addipaddress('".$multiscreen_result['id']."','1','$m','$b','".$result['id']."')\"><img id='$m$b' src='img/".$multiscreen_activeimage[$m]."' width='20' height='20'></a>";
	 } else {
          $showmultiscreen_image.="<a href='javascript:void(0)' onclick=\"addipaddress('".$multiscreen_result['id']."','0','$m','$b','".$result['id']."')\"><img id='$m$b' src='img/".$multiscreen_image[$m]."' width='20' height='20'></a>";
	 }
	 $m++;
       }
       $showmultiscreen_image.="<input type='hidden' id='ipid".$result['id']."' value='".$result['ipaddress']."' >";*/
    
       $theatre_dropdown="";
       $theatre_image=array("A.png","B.png","C.png","D.png","E.png");
       $theatre_activeimage=array("A_active.png","B_active.png","C_active.png","D_active.png","E_active.png");
       
      /* $theatre_dropdown.="<select name='IP$b' id='IP$b'>";
       $theatre_dropdown.="<option value=''>Kies een scherm</option>";
       if($result['ipaddress']=='0') {
        $theatre_dropdown.="<option value='0' selected>Allemaal</option>";
       } else {
	$theatre_dropdown.="<option value='0' >Alle schermen</option>";
       }
       $theatre_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn2=$db->query($theatre_query);
        while($result2=mysqli_fetch_array($conn2)) {
         $eppc_status1=$result2['eppc_status'];

	 if($result2['id']==$result['ipaddress']) {
	  $theatre_dropdown.="<option value='".$result2['id']."' selected>".$result2['theatre_label']."</option>";
	 } else {
          $theatre_dropdown.="<option value='".$result2['id']."'>".$result2['theatre_label']."</option>";
	 }
       }
      $theatre_dropdown.="</select>";*/
      $theatre_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
      $conn2=$db->query($theatre_query);
      $k=0;
       $theatre_ipaddress=explode(",",$result["ipaddress"]);
        while($result2=mysqli_fetch_array($conn2)) {
         $eppc_status1=$result2['eppc_status'];
         if(in_array($result2['id'],$theatre_ipaddress)) {
          $theatre_dropdown.="<a href='javascript:void(0)' onclick=\"addscreens('".$result2['id']."','0','$k','$b','".$result['id']."')\"><img id='$k$b' src='img/".$color_scheme."/".$theatre_activeimage[$k]."' width='20' height='20'></a>";
	 } else {
          $theatre_dropdown.="<a href='javascript:void(0)' onclick=\"addscreens('".$result2['id']."','0','$k','$b','".$result['id']."')\"><img id='$k$b' src='img/".$color_scheme."/".$theatre_image[$k]."' width='20' height='20'></a>";
	 }
       $k++;
        }
        $theatre_dropdown .="<input type='hidden' name='IPo$result[id]' id='IPo$result[id]' value='$result[ipaddress]'/>";
      
      //-------------- code for getting ipaddress for theatre-----------------------------
    
      /* $multiscreen_dropdown="";
       $multiscreen_dropdown.="<select name='multiscreen$b' id='multiscreen$b'>";
       $multiscreen_dropdown.="<option value=''>Kies Schermgroep</option>";
       $multiscreen_query="select a.label, a.id, b.multiscreen_status from theatre_multiscreen as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn2=$db->query($multiscreen_query);
        while($result2=mysqli_fetch_array($conn2)) {
         $multiscreen_status=$result2['multiscreen_status'];
	 if($result2['id']==$result['multiscreen']) {
	  $multiscreen_dropdown.="<option value='".$result2['id']."' selected>".$result2['label']."</option>";
	  
	 } else {
          $multiscreen_dropdown.="<option value='".$result2['id']."'>".$result2['label']."</option>";
	 }
       }
      $multiscreen_dropdown.="</select>";*/
       //--------------- code for getting multiscreens for theatre ------------------------
      $multiscreen_image=array("A.png","B.png","C.png","D.png","E.png");
      $multiscreen_activeimage=array("A_active.png","B_active.png","C_active.png","D_active.png","E_active.png");
      $sel_multiscreen=explode(',',$result['multiscreen']);
       $multiscreen_query="select a.id, a.label, b.multiscreen_status from theatre_multiscreen as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn2=$db->query($multiscreen_query);
       $m=0;
        while($multiscreen_result=mysqli_fetch_array($conn2)) {
         
         $multiscreen_status=$multiscreen_result['multiscreen_status'];
         
	 if(in_array($multiscreen_result['id'],$sel_multiscreen)) {
	  $showmultiscreen_image.="<a href='javascript:void(0)' onclick=\"addscreens('".$multiscreen_result['id']."','1','$m','$b','".$result['id']."')\"><img id='$m$b' src='img/".$color_scheme."/".$multiscreen_activeimage[$m]."' width='20' height='20'></a>";
	 } else {
          $showmultiscreen_image.="<a href='javascript:void(0)' onclick=\"addscreens('".$multiscreen_result['id']."','0','$m','$b','".$result['id']."')\"><img id='$m$b' src='img/".$color_scheme."/".$multiscreen_image[$m]."' width='20' height='20'></a>";
	 }
	 $m++;
       }
       if($result['multiscreen']!=""){
         $multiscreen_value=$result['multiscreen'];
       }else{
         $multiscreen_value="";
       }
       
       $showmultiscreen_image.="<input type='hidden' id='IPo$result[id]' value='".$multiscreen_value."' >";
      
      

    //------------ code for getting schedule for each carrousel--------------------------
    if($is_master==0):
      $sch_Query = "select * from schedule  where cid='".$result['id']."' order by schedule asc";
      $schedule='schedule';
      $conn2=$db->query($sch_Query);
    else:
      $sch_Query = "select * from master_schedule  where cid='".$result['id']."' order by schedule asc";
      $schedule='schedule';
      $conn2=$master_db->query($sch_Query);
    endif;
      
      while($result_sch=mysqli_fetch_array($conn2)) {
        //echo "<pre>"; print_r($result_sch); echo "</pre>";
        $date = getDutchdate($result_sch[$schedule],$result_sch['every_half']);
	
	if($result_sch['ipaddress']=='0') {
	 $ip_address="Alle schermen";
	} 
	
	else if($result_sch['ipaddress']!="") {
	  $select_sch="select theatre_label from theatre_eppc  where id IN ($result_sch[ipaddress])";
	  $conn1=$db->query($select_sch);
	  $result3=mysqli_fetch_array($conn1);
	  //$ip_address=$result3['theatre_label'];
          $theatre_ipaddress = "";
       $sel_ipaddress=explode(',',$result_sch['ipaddress']);
       $theatre_ip_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn_theatre_ip=$db->query($theatre_ip_query);
       $t=0;
       while($theatre_ip_result=mysqli_fetch_array($conn_theatre_ip)) {
         //$eppc_status1=$theatre_result['eppc_status'];
         if(in_array($theatre_ip_result['id'],$sel_ipaddress)) {
	  $theatre_ipaddress.="<a href='javascript:void(0)'><img id='$ta$b' src='img/".$color_scheme."/".$theatre_activeimage[$t]."' width='20' height='20'></a>";
	 } else {
          $theatre_ipaddress.="<a href='javascript:void(0)'><img id='$ta$b' src='img/".$color_scheme."/".$theatre_image[$t]."' width='20' height='20'></a>";
	 }
	 $t++;
	}
}
	else if($result_sch['multiscreen']!="") {
        // $result_sch[multiscreen] = substr($result_sch[multiscreen],0,1);
  	  $select_sch="select label from theatre_multiscreen  where id IN ($result_sch[multiscreen])";
  	  $conn1=$db->query($select_sch);
  	  $result3=mysqli_fetch_array($conn1);
  	  //$ip_address=$result3['theatre_label'];
            $theatre_ipaddress = "";
         $sel_ipaddress=explode(',',$result_sch['multiscreen']);
         $theatre_ip_query="select a.label, a.id, b.multiscreen_status from theatre_multiscreen as a, theatre as b where b.id=a.theatre_id and b.status=1";
         $conn_theatre_ip=$db->query($theatre_ip_query);
         $t=0;
         while($theatre_ip_result=mysqli_fetch_array($conn_theatre_ip)) {
           //$eppc_status1=$theatre_result['eppc_status'];
           if(in_array($theatre_ip_result['id'],$sel_ipaddress)) {
  	  $theatre_ipaddress.="<a href='javascript:void(0)'><img id='$ta$b' src='img/".$color_scheme."/".$theatre_activeimage[$t]."' width='20' height='20'></a>";
  	 } else {
            $theatre_ipaddress.="<a href='javascript:void(0)'><img id='$ta$b' src='img/".$color_scheme."/".$theatre_image[$t]."' width='20' height='20'></a>";
  	 }
  	 $t++;
  	}
	}
	

		else {
	 $ip_address="";
	}
       // $theatre_ipaddress .="<input type='hidden' name='IP$b' id='IP$b' value='$result_sch[ipaddress]'/>";
       
       if($eppc_status1==1){
       $scheduling_list.= $date." ".$theatre_ipaddress."<hr>";
       
       }
	   elseif($multiscreen_status==1){
		 $scheduling_list.= $date." ".$theatre_ipaddress."<hr>";  
	   }
		   else{
         $scheduling_list.= $date."<hr>";
       }
      }

      $id = $result['id'];      
      $resstatus = $result['status'];
      
      
      
      if($result['status']==0){
        if($eppc_status1==1) {
          $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><a  href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','1',$multiscreen_status)\"><br />Start opnieuw</a>";	  
	 } else {
	  $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','0',$multiscreen_status)\"><br />Start opnieuw</a>";
	 }
       } else {
	 if($eppc_status1==1) {
          $status="<a  href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','1',$multiscreen_status)\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";	  
	 } else {
	  $status="<a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','0',$multiscreen_status)\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";
	 }
       }
       
       if($result['edit_status']==1){
	if($result['status']==0){
	  if($eppc_status1==1) {
	   $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><br />  <a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','1','1',$multiscreen_status)\">Start opnieuw</a>";
	  } else {
	   $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><br />  <a  id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','1','0',$multiscreen_status)\">Start opnieuw</a>";
	  }
	}else{
	  if($eppc_status1==1) {
	   $status="<a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','1',$multiscreen_status)\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";
	  } else {
	   $status="<a  id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','0',$multiscreen_status)\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";
	  }
	}
       }
	
  	  $schedule="<a href=\"javascript:void(0)\" onclick='opensch(".$result['id'].")'><img src='./img/$color_scheme/inplannen.png' border='0' width='29' height='29' ></a>";
  
      
     if($eppc_status1==1) { 
       $show_eppc="<td align='center'>$theatre_dropdown</td>";
     } else {
       $show_eppc="";
     }
     
      if($multiscreen_status==1) { 
       $show_multiscreen="<td align='center'>$showmultiscreen_image</td>";
     } else {
       $show_multiscreen="";
     }
    // echo $multiscreen_status." @@@@@@@@@@@@ ";
     $multiscreen_status=0;
     if($result['edit_status']==0){
      $edit_status= "<td align='center'> <a href='edit.php?id=".$result['id']."&status=$resstatus&is_block=0'><img src='./img/$color_scheme/edit03.png' border='0' width='30' height='28' align='center' /></a></td>";
      
     }else{
     $edit_status= "<td align='center'> <a href='edit.php?id=".$result['id']."&status=$resstatus&is_block=0' onclick=\"return confirm('Deze carrousel wordt op dit moment mogelijk door een andere gebruiker bewerkt. Het wordt afgeraden om een carrousel gelijktijdig door meerdere mensen te laten bewerken. Klik op Annuleren om terug te gaan of op OK om de carrousel te bewerken.');\"><img src='./img/edit_greyed.png' border='0' width='30' height='28' align='center' /></a></td>";
      }
     
     if($export_status==1) {
      
       $export_column_data="<td align='center'><a id='startexport' href='javascript:void(0)' onclick=\"exportCarrousel('$id')\"><img src='./img/$color_scheme/export_icon.png' border='0' width='30' height='28' align='center' /></a></td>";
     } else {
       $export_column_data="";
     }
     
     if($is_master==1){
      $status='';   
	  $delete_text="Pas op! Weet u zeker dat deze carrousel niet actief is bij één van uw clients? U dient alleen in-actieve carrousel te verwijderen.";   
     }
	 else {$delete_text="Weet u zeker dat u deze carrousel wilt verwijderen?";}
	 
      $carrousel_list.="<tr class=".$sty_class.">
      <td>".stripcslashes($result['name'])."</td>
     <!--<td align='center'><a id='startexport' href='javascript:void(0)' onclick=\"exportCarrousel('$id')\"><img src='./img/$color_scheme/export_icon.png' border='0' width='30' height='28' align='center' /></a></td>
     -->
      $export_column_data
      $edit_status
      
      <td align='center'><a href='javascript:void(0)' onclick=\"duplicateCarrousel('$id')\"><img src='./img/$color_scheme/duplicate_button.png' border='0' width='37' height='32' title='Duplicate Carrousel'></a></td>
	  
                        <td align='center'><a href='delete.php?id=".$result['id']."&master=".$is_master."' onclick=\"return confirm('$delete_text');\"><img src='./img/$color_scheme/delete.png' border='0' width='33' height='31' ></td>
              
              <td align='center'><a href='delete.php?id=".$result['id']."&master=".$is_master."' onclick=\"return confirm('$delete_text');\"><a target='_blank' href=http://".$master_ip."/narrowcasting/newhtmlfive.php?id=".$result['id'].">Preview</a></td>
			
			
			<td align='center'id='schedule01'>$scheduling_list $schedule</td>
			
			
			
			$show_eppc
			$show_multiscreen
			<td align='center'>$status</td>
                       </td>";
     $scheduling_list="";
     $showmultiscreen_image="";
     $b++;
   }
   $carrousel_list.="</table>";
 }

//----------- for empty temp crrousel table ----------------------------
$deletequery="delete from temp_carrousel where s_id='$sid'";
$db->query($deletequery);
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
</head>

<script type="text/javascript">
/*$(document).ready(function() { 
    $('a#startcarrousel').click(function() { 
        $.blockUI({ 
            message: $('#domMessage'),
            css: { opacity: .9,
            padding: '20',
            textAlign: 'center'
            } 
        }); 
     
    }); 
}); */

$(document).ready(function() { 
    $('a#startexport').click(function() { 
        $.blockUI({ 
            message: $('#expMessage'),
            css: { opacity: .9,
            padding: '20',
            textAlign: 'center'
            } 
        }); 
     
    });
    
});</script>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script type="text/javascript">
 function opensch(id) {
    var url='savesch_multi.php?id='+id;
    var newwindow=window.open(url,'Inplannen','height=700,width=700,left=190,top=150,screenX=200,screenY=100');
	if (window.focus) {newwindow.focus()}
 }
 
 function openclientsch(id) {
    var url='savesch_multi.php?id='+id+"&is_client=1";
    var newwindow=window.open(url,'Inplannen','height=700,width=700,left=190,top=150,screenX=200,screenY=100');
	if (window.focus) {newwindow.focus()}
 }
 
 function showshc(id) {
  document.getElementById('sdiv'+id).style.display="none";
  document.getElementById('ddiv'+id).style.display="block";
 }
 
 function editshc(id) {
  document.getElementById('e_div'+id).style.display="none";
  document.getElementById('ediv'+id).style.display="block";
 }
 
 function savesch(id,id1) {
  sdate=document.getElementById('demo'+id).value;
  shour=document.getElementById('shour'+id).value;

  var checkdate=sdate.split("-");
  var yy1=checkdate[2];
  var mm1=checkdate[0];
  var dd1=checkdate[1];

  var today = new Date();
  var dd2 = today.getDate();
  var mm2 = today.getMonth();
  var yy2 = today.getFullYear();
  var hh2 = today.getHours();

  var mydate2=new Date(yy2,mm2+1,dd2);
  var curr_date=Date.parse(mydate2);
  var mydate1=new Date(yy1,mm1,dd1);
  var userdate=Date.parse(mydate1);
  
  //alert(userdate);
  //alert(curr_date);
  
  if(sdate=="") {
   alert('U dient een datum in te vullen.');
  } else if(userdate<curr_date) {
   alert('U kunt geen datum of tijd uit het verleden invullen.')
  } else if(userdate==curr_date && shour<=hh2) {
   alert('U kunt geen datum of tijd uit het verleden invullen.')
  }
  else {
   $.ajax({
     
      type: "POST", url: 'savesch.php', data: {id : id, sdate : sdate, shour : shour, id1 : id1},
              
              complete: function(data){ 
                  $("#show").html(data.responseText);
              }  
          }); 
  }
 }
 
  function duplicateCarrousel(id)
    {
        $("#error").hide();
            var carr_name = prompt('Kies een naam voor de carrousel','');
            var letters = /^[a-zA-Z0-9 ]+$/; 
            var max_carrousel_no_exceed_error="U heeft het maximum aantal van 30 carrousels bereikt. U moet eerst een carrousel verwijderen voordat u een nieuwe kunt aanmaken.";
        if(carr_name!==null){
            if(carr_name!=="" && carr_name.search(letters)!==-1){
                $.ajax({  
                    type: "POST", url: 'duplicateCarrousel.php', data: "cid="+id+"&name="+carr_name+"&is_client=<?php echo $is_client; ?>",  
                    complete: function(data){
                        if(data.responseText==1){
                            $("#error").show();
                        }
			else if($.trim(data.responseText)=="max carrousels count reached"){
			  alert(max_carrousel_no_exceed_error);
			  return false;
			}
                        else{
                            $("#show").html(data.responseText);
                        }
                    }  
                });
            }else{
                 alert('U kunt alleen letters en cijfers in uw carrousel naam gebruiken (dus geen speciale karakters of interpunctie), U kunt het veld ook niet leeglaten.')
            }
        }
    }
	
    function duplicateClientCarrousel(id)
      {
          $("#error").hide();
          var carr_name = prompt('Kies een naam voor de carrousel','');
  	var max_carrousel_no_exceed_error="U heeft het maximum aantal van 30 carrousels bereikt. U moet eerst een carrousel verwijderen voordat u een nieuwe kunt aanmaken.";
          if(carr_name!=null){
              if(carr_name==''){
                  alert('Voer een naam in.');
              }else{
                  $.ajax({  
                      type: "POST", url: 'duplicateCarrousel.php', data: "cid="+id+"&name="+carr_name+"&is_client=1",  
                      complete: function(data){
                          if(data.responseText==1){
                              $("#error").show();
                          }
  			else if($.trim(data.responseText)=="max carrousels count reached"){
  			  alert(max_carrousel_no_exceed_error);
  			  return false;
  			}
                          else{
                              $("#show").html(data.responseText);
                          }
                      }  
                  }); 
              }
          }
      }
	
   
 
  function exportCarrousel(id) {
    var url_alert_msg="U kunt geen carrousels met dynamische content (url's) exporteren.";
    $("#expMessage").show();
    $.ajax({  
	    type: "POST", url: 'export_carrousel.php', data: "cid="+id,  
	    complete: function(data){ 
              $.unblockUI();
              if($.trim(data.responseText)=='url in carrousel'){
               setTimeout(function(){  alert(url_alert_msg);   $("#expMessage").hide(); },500); 
              }else{
               window.location='showexp_carrousel.php?id='+id;
              }
	    }  
	}); 
    
  }
 
 function autosave(id) {
 if(id==1) {
     window.location='welcome.php';
    } else if(id==3) {
     window.location='upload_videoimage.php';
    } else if(id==4) {
     window.location='twitter_overlay.php';
    } else if(id==5) {
     window.location='mededelingen_overlay.php';
    } else if(id==6) {
     window.location='logout.php';
    }else if(id==7) {
     window.location='url.php';
    }  else if(id==2){
     window.location='create_carrousel.php';
         } else if(id==8){
      window.location='delay_mededelingen_overlay.php';
    } else {
     
    }
}

function startCarrousal(id1,id2,id3,id4,multiscreen_status,mode,skip_mediasync) {
 //alert(id1+"-"+id2+"-"+id3+"-"+id4);
  if(id1==0) {
    var ipaddress="";
   
   } else {
      
      var ipaddress=document.getElementById("IPo"+id1).value;
      
   }
   if(multiscreen_status==1 ){
      if(document.getElementById("IPo"+id1).value!=""){
          var multiscreen_value=document.getElementById("IPo"+id1).value;
         }else{
          var multiscreen_value ="";
         }
   }else{
      var multiscreen_value ="";
   }
   
 
 
   if(multiscreen_status!="" && multiscreen_value==""){
      alert('Please select screen');
      return false;
   }
   
  if(id4==1 && ipaddress=="") {
   alert('U dient een scherm te kiezen');
   return false;
   } else {
   if(id3==1) {
    if(mode=='is_block'){
    var send_data='id='+id1+'&edit=1&ipaddress='+ipaddress+'&multiscreen='+multiscreen_value+'&is_block=1'+'&skip_mediasync='+skip_mediasync+'&manual=yes';
    }else{
      var send_data='id='+id1+'&edit=1&ipaddress='+ipaddress+'&multiscreen='+multiscreen_value+'&manual=yes';
    }
   } else {
    if(mode=='is_block'){
    var send_data='id='+id1+'&edit=0&ipaddress='+ipaddress+'&multiscreen='+multiscreen_value+'&is_block=1'+'&skip_mediasync='+skip_mediasync+'&manual=yes';
    }else{
      var send_data='id='+id1+'&edit=0&ipaddress='+ipaddress+'&multiscreen='+multiscreen_value+'&manual=yes';
    }
   }
   
   // $(document).ready(function() { 
    //$('a#startcarrousel').click(function() { 
        $.blockUI({ 
            message: $('#domMessage'),
            css: { opacity: .9,
            padding: '20',
            textAlign: 'center',
            } 
        }); 
     
   // }); 
  // });
  
   $.ajax({
         <?php
        
         if($multiscreen_check==1):?>
	   type: "POST", url: 'start_carrousel_multiscreen.php', data: send_data,
           <?php else:?>
           type: "POST", url: 'start_carrousel.php', data: send_data,
           <?php endif; ?>
	   complete: function(data){
           
           var obj = jQuery.parseJSON(data.responseText);
        // console.log(obj[0].screen);
            //alert(obj.screen[1])
        var error_msg="";
         if(obj.length > 1){
             var ig = 1;
             
             $.each(obj, function(index,item) {
                // here you can extract the data
                error_msg += ig+". Login Failed for "+item.screen+"\n";
                ig = ig+1;
            });           
             alert(error_msg);
             location.reload();
         }else{
             var exit_code = obj[0].result;
         
            <?php if($is_client==1):?>
            if(exit_code!=0){
              
              r = confirm("Er kan geen verbinding gemaakt worden met de mediaserver. Mogelijk missen er mediabestanden uit de carrousel. Klik op 'Annuleren' om het starten van de carrousel te onderbreken of op 'OK' om de carrousel te starten zonder mediasynchronisatie.");
              if(r == true){
                startCarrousal(id1,id2,id3,id4,multiscreen_status,mode,1);
                setTimeout(function() {
                    // Do something after 2 seconds
                   // location.reload();
                }, 20000);
                
                location.reload();
              }else{
                location.reload();
              }
            }else if(exit_code=='Login Failed'){
            alert("Login Failed")
            
            }else{
                location.reload();
            }
            <?php else: ?>
            
            if(exit_code==2){
                alert("Login Failed for "+obj[0].screen);
                location.reload();

            }
              //     location.reload();
            <?php endif; ?>
           
	   }
           }
       }); 
  }
 }
 
  // on submit this function is called
function clearOldSchedule(){
   var delete_confirmation=confirm('Weet je zeker dat je alle oude planningen wilt verwijderen?');
  if(delete_confirmation){
      $.ajax({  
              type: "get", url: 'welcome.php?delete_old_schedule=1',
	      async: false,
              complete: function(data){ 
                 var response=$.trim(data.responseText);
		 if(response=='success'){
		  window.location.href='welcome.php';
		 }else{
		  alert('Er zijn geen oude planningen');
		 }
		 
              },
	      error:function(error){
	         alert('Er is iets fout gegaan.....');
	      }
	      
          });
	  }
}

function del_voorstellingsinfo(){
	var delete_confirmation=confirm('Weet je zeker dat je de oude voorstellingsinformatie wilt verwijderen?');
	
    if(delete_confirmation){
		window.open("truncate_csvimport.php");
		alert('De oude voorstellingsinformatie is verwijderd.');
  	  }
	  
	  
}

function addscreens(id1,id2,id3,id4,id5) {
  //console.log(id1+id2+id3+id4+id5);
  //alert('IPo'+id4);
  var imagevalue=document.getElementById(id3+id4).src;
 // alert(imagevalue);
  var screenvalue=document.getElementById('IPo'+id5).value;
  var screen_value=screenvalue.split(","); ;
  var myarray = imagevalue.split("/");
  var newimage=myarray[6].split(".");
  var colorscheme = myarray[5].split(".");
 // alert(newimage);
  var new_image=newimage[0].substr(0,1);
  if(newimage[0].length>1) {
   document.getElementById(id3+id4).src='./img/'+colorscheme+'/'+new_image+'.png';
   screen_value.splice(screen_value.indexOf(id1), 1);
  } else {
   document.getElementById(id3+id4).src='./img/'+colorscheme+'/'+new_image+'_active.png';
   screen_value.splice(-1, 0,id1);
  }
  var newScreenVal = screen_value.join(",");
 
  document.getElementById('IPo'+id5).value=newScreenVal;
 }
 
 
function addipaddress(id1,id2,id3) {
   console.log("id 1 : "+id1);
   console.log("id 2 : "+id2);
   console.log("id 3 : "+id3);
   
   
  var imagevalue=document.getElementById(id3).src;
  var ipvalue=document.getElementById('ipid').value;
  var ip_value=ipvalue.split(","); ;
  var myarray = imagevalue.split("/");
  var newimage=myarray[5].split(".");
  var new_image=newimage[0].substr(0,1);
  if(newimage[0].length>1) {
   document.getElementById(id3).src='./img/'+new_image+'.png';
   ip_value.splice(ip_value.indexOf(id1), 1);
  } else {
   document.getElementById(id3).src='./img/'+new_image+'_active.png';
   ip_value.splice(-1, 0,id1);
  }
  var newIpVal = ip_value.join(",");
  document.getElementById('ipid').value=newIpVal;
 }

function uploadcsvpopup(){
   window.open("csvimport.php","","height=700px, width=1000px");
}

function go_to_client(val){
  var label = $('#clients').find(":selected").text();
  var ip = val;
  var username = $('#username').val();
  var password = $('#password').val();
  var client_url = "http://"+ip+"/narrowcasting/?username="+username+"&password="+password;
  if(val!=''){
   window.open(client_url,'_blank');
  }
}

function clearOldMasterSchedule(){
   var delete_confirmation=confirm('Weet je zeker dat je alle oude planningen wilt verwijderen?');
  if(delete_confirmation){
      $.ajax({  
              type: "get", url: 'welcome.php?delete_old_master_schedule=1',
	      async: false,
              complete: function(data){ 
                 var response=$.trim(data.responseText);
		 if(response=='success'){
		  window.location.href='welcome.php';
		 }else{
		  alert('Er zijn geen oude planningen');
		 }
		 
              },
	      error:function(error){
	         alert('Er is iets fout gegaan.....');
	      }
	      
          });
	  }
}



</script>
<body>


<div id="domMessage" style="display:none;">

<img style="margin:20px 0px 10px 0px;" src="img/<?php echo $color_scheme;?>/loader.gif" />
    <h3 style="margin:10px 10px 3px 10px;">De carrousel wordt gestart. Een moment geduld a.u.b.</h3>
    <h5 style="margin:3px 10px 10px 10px">Sluit dit venster niet, deze boodschap verdwijnt vanzelf als de carrousel gestart is.</h5>
    
</div> 

<div id="expMessage" style="display:none;">

<img style="margin:20px 0px 10px 0px;" src="img/<?php echo $color_scheme;?>/loader.gif" />
    <h3 style="margin:10px 10px 3px 10px;">De carrousel wordt op dit moment geëxporteerd. Een moment geduld a.u.b.</h3>
    <h5 style="margin:3px 10px 10px 10px">Sluit dit venster niet, u wordt vanzelf doorgestuurd.</h5>
    
</div> 
   <!--------------- include header --------------->
   <?php include('header.html'); ?>
    
        <!-- START MAIN CONTAINER -->
    <div class="main_container">
       <div class="content">
         
         
	    <?php if($is_master==1):?>    
			
			<span class="title">Overzicht clients</span>
			  		        <p>Selecteer één van de clients om naar de desbetreffende client-webinterface te gaan:  
			  			  <?php
			                             $users_query="select * from users where name='".$_SESSION[name]."'";
			                             $rs_users=$db->query($users_query);
			                             $user_record = mysqli_fetch_array($rs_users, mysqliI_ASSOC);
                           
			  			   $client_query="select a.ip, a.label, a.theatre_id,b.id, b.status from clients a, theatre b where a.theatre_id=b.id and b.status=1";
			  			   $rs=$db->query($client_query);
			  			  ?>
          
			  			  <select name="clients" id="clients" onchange="go_to_client(this.value)">
			  			    <option value="">--Selecteer een client--</option>
			  			    <?php
			  			     while($client_record=mysqli_fetch_array($rs, mysqliI_ASSOC)){
			  			    ?>
			  			    <option value="<?php echo $client_record['ip']?>"><?php echo $client_record['label']; ?></option>
			  			    <?php
			  			     }
			  			    ?>
			  			  </select>
			  			  <input type="hidden" name="username" id="username" value="<?php echo trim($user_record[name]); ?>" />
			  		          <input type="hidden" name="password" id="password" value="<?php echo trim($user_record[password]); ?>" /><br />
			  			<span style="font-size:10px;">(Indien er geen nieuwe pagina opent wordt dit misschien tegen gehouden door een popup-blocker)</span></p><br />
            <span class="title">Overzicht carrousels</span>			
		       
			
            <p>Hieronder vindt u een overzicht van de door u aangemaakte carrousels.
               U kunt hier de carrousels bewerken, kopiëren en verwijderen.</p>
               <p>Tevens kunt u de carrousels inplannen voor uw clients. Elk half uur kunt u een nieuwe carrousel starten. Om een startdatum en tijd in te stellen kunt u op de kalender klikken.</p>
		   <?php elseif($is_client==1):?>
	           
	           <?php
	           $client_label_query = "select * from clients where is_active=1";
	           $client_label_rs=$db->query($client_label_query);
	           $client_label_record=mysqli_fetch_assoc($client_label_rs)
	           ?>
	           <span class="title"><?php echo $client_label_record['label']; ?></span><br>	
	          
           <span class="title">Overzicht carrousels</span>
           <p>Hieronder vindt u een overzicht van de master carrousels, uw client content block en de door u aangemaakte carrousels.
              U kunt hier de master carrousels inzien en uw client content block bewerken. Daarnaast kunt u hier uw eigen carrousels verwijderen, bewerken of handmatig starten.</p>
              <p>Tevens kunt u de carrousels inplannen. Elk half uur kunt u een nieuwe carrousel starten. Om een startdatum en tijd in te stellen kunt u de kalender klikken.</p>
		   <?php else: ?>
           <span class="title">Overzicht carrousels</span>
           <p>Hieronder vindt u een overzicht van de door u aangemaakte carrousels.
              U kunt hier de carrousels verwijderen, bewerken of handmatig starten.</p>
              <p>Tevens kunt u de carrousels inplannen. Elk half uur kunt u een nieuwe carrousel starten. Om een startdatum en tijd in te stellen kunt u de kalender klikken.</p>
		   <?php endif ?>
        </div>
        
     
	
    	<div class="grid_container" id="show">    	
    	<div id="error" class="grid_container" style="display:none;color:red; font-size:17px;">
                <b>Deze naam bestaat al. U dient een andere naam voor de carrousel te kiezen. </b>
            </div>
          <?php if($is_client==1):?>
          <?php echo $client_carrousel_list; ?>
          <?php echo $client_content_block?>          
          <?php endif; ?>
		  <?php if($is_client==1 && $regular_carrousel_allowed==1):?>
          <?php echo $carrousel_list; ?>
		 
	  	  <?php elseif($is_client==1 && $regular_carrousel_allowed==0):?>
			
			   <?php else: ?>
				  <?php echo $carrousel_list; ?> 
		  <?php endif; ?>
        </div>
	   <div class="clear_schedule_container">
		   
	   <input class="del_voorstellingsinfo_button" type="button" value="Oude voorstellingsinformatie verwijderen" 		onclick="del_voorstellingsinfo();">
		
	   <span class="deleteicon">
		  <?php if($is_master==1):?>
               <input class="clear_schedule_button" type="button" value="Oude planningen verwijderen" onclick="clearOldMasterSchedule();">
		   <?php elseif($is_client==1):?>
			   <input class="clear_schedule_button" type="button" value="Oude client planningen verwijderen" onclick="clearOldSchedule();">
		   <?php else: ?>
			    <input class="clear_schedule_button" type="button" value="Oude planningen verwijderen" onclick="clearOldSchedule();">
	     <?php endif; ?>
	 </span>	 
   <?php
               $dd_query="select dropdown from theatre where status=1";
  	       $rst=$db->query($dd_query);
  	       $result_dd=mysqli_fetch_array($rst);
  	       $dropdown = $result_dd['dropdown'];
                 if($dropdown=="csv"):
              ?>
			    <input class="upload_csv_button" type="button" value="Upload CSV" onclick="uploadcsvpopup();">
				
	            <?php
	              endif;
	            ?> 
	</div>
	<br/>
    </div>
   
  
</body>
</html>
<?php
/* For date and month calculation*/
  function getDutchdate($data,$every_half) {
   
    if(trim($data)!=''){
    
   /* For day calculation*/
    $days = array(
            1 => 'Ma',
            2 => 'Di',
            3 => 'Wo',
            4 => 'Do',
            5 => 'Vr',
            6 => 'Za',
            7 => 'Zo',
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
            '/01', '/02', '/03', '/04',
            '/05', '/06', '/07', '/08',
            '/09', '/10', '/11', '/12'
        );
        $iso_month = date("m",$data);
	$mon = (int) $iso_month;
	$month = $months[$mon-1];
	if($every_half==0)
	$finaldate = $day.' '.date('j',$data).''.$month.' '. date('H',$data).'.00<br>';
	else
	$finaldate = $day.' '.date('j',$data).''.$month.' '. date('H',$data).'.30<br>';
	return $finaldate;
    }else{
      return '';
    }
    
   
  }
?>
