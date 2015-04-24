<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
//---------- code for fetching listing of carrousals-------------------
 $Query = "SELECT * FROM client_content_block_listing ORDER BY id desc";
 $conn=$db->query($Query);
 $count=mysql_num_rows($conn);
 $client_content_block="";
 $b=0;
 $client_content_block.="<table>";
  $client_content_block .="<tr class='grid_header_top'><td colspan='6'><h2 style=\"margin-top:40px;\">Client content block</h2></td></tr>";
   $client_content_block.="<tr class='grid_header'><td height='25' width='35%'>Naam</td>
	 
	  <td height='25' width='10%' align='center'>Bewerken</td>
           <!--<td width='10%' align='center'>Kopi&euml;ren</td>
           <td width='10%' align='center'>Verwijderen</td>
	   <td width='10%' align='center'>Planning</td>-->
            $eppc_column
	    $multiscreen_column
	  
           <td width='10%' align='center'>Start</td>
          </tr>";
  if($count==0) {
    $client_content_block.="<tr><td colspan='5' align='center'><span style='margin:0px 0 0 0px;'>U heeft nog geen carrousel aangemaakt.</span></td></tr>";
    $client_content_block.="</table>";
  } else {
    
    while($result=mysql_fetch_array($conn)) {
      if($b%2==0) { $sty_class="gray"; } else {$sty_class="white";}
      
      //-------------- code for getting ipaddress for theatre-----------------------------
    
       $theatre_dropdown="";
       $theatre_dropdown.="<select name='IP$b' id='IP$b'>";
       $theatre_dropdown.="<option value=''>Kies een scherm</option>";
       if($result['ipaddress']=='0') {
        $theatre_dropdown.="<option value='0' selected>Allemaal</option>";
       } else {
	$theatre_dropdown.="<option value='0' >Alle schermen</option>";
       }
       $theatre_query="select a.theatre_label, a.id, b.eppc_status from theatre_eppc as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn2=$db->query($theatre_query);
        while($result2=mysql_fetch_array($conn2)) {
         $eppc_status1=$result2['eppc_status'];

	 if($result2['id']==$result['ipaddress']) {
	  $theatre_dropdown.="<option value='".$result2['id']."' selected>".$result2['theatre_label']."</option>";
	 } else {
          $theatre_dropdown.="<option value='".$result2['id']."'>".$result2['theatre_label']."</option>";
	 }
       }
      $theatre_dropdown.="</select>";
      
      
      //-------------- code for getting ipaddress for theatre-----------------------------
    
       $multiscreen_dropdown="";
       $multiscreen_dropdown.="<select name='multiscreen$b' id='multiscreen$b'>";
       $multiscreen_dropdown.="<option value=''>Kies Schermgroep</option>";
       $multiscreen_query="select a.label, a.id, b.multiscreen_status from theatre_multiscreen as a, theatre as b where b.id=a.theatre_id and b.status=1";
       $conn2=$db->query($multiscreen_query);
        while($result2=mysql_fetch_array($conn2)) {
         $multiscreen_status=$result2['multiscreen_status'];
	 if($result2['id']==$result['multiscreen']) {
	  $multiscreen_dropdown.="<option value='".$result2['id']."' selected>".$result2['label']."</option>";
	  
	 } else {
          $multiscreen_dropdown.="<option value='".$result2['id']."'>".$result2['label']."</option>";
	 }
       }
      $multiscreen_dropdown.="</select>";

    //------------ code for getting schedule for each carrousel-------------------------- 
      $sch_Query = "select * from schedule  where cid='".$result['id']."' order by schedule asc";
      $conn2=$db->query($sch_Query);
      while($result_sch=mysql_fetch_array($conn2)) {
        $date = getDutchdate($result_sch['schedule'],$result_sch['every_half']);
	
	if($result_sch['ipaddress']=='0') {
	 $ip_address="Alle schermen";
	} else if($result_sch['ipaddress']!="") {
	  $select_sch="select theatre_label from theatre_eppc  where id=".$result_sch['ipaddress']."";
	  $conn1=$db->query($select_sch);
	  $result3=mysql_fetch_array($conn1);
	  $ip_address=$result3['theatre_label'];
	} else{
	 $ip_address="";
	}
       $scheduling_list.= $date." ".$ip_address."<hr>";
      }

      $id = $result['id'];
      
      $resstatus = $result['status'];
      
      if($result['status']==0){
        if($eppc_status1==1) {
          $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><a  href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','1',$multiscreen_status,'is_block')\"><br />Start opnieuw</a>";	  
	 } else {
	  $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','0',$multiscreen_status,'is_block')\"><br />Start opnieuw</a>";
	 }
       } else {
	 if($eppc_status1==1) {
          $status="<a  href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','1',$multiscreen_status,'is_block')\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";	  
	 } else {
	  $status="<a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','0',$multiscreen_status,'is_block')\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";
	 }
       }
       
       if($result['edit_status']==1){
	if($result['status']==0){
	  if($eppc_status1==1) {
	   $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><br />  <a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','1','1',$multiscreen_status,'is_block')\">Start opnieuw</a>";
	  } else {
	   $status="<img src='./img/$color_scheme/loader01.gif' border='0' width='20' height='20' ><br />  <a  id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','1','0',$multiscreen_status,'is_block')\">Start opnieuw</a>";
	  }
	}else{
	  if($eppc_status1==1) {
	   $status="<a id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','1',$multiscreen_status,'is_block')\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";
	  } else {
	   $status="<a  id='startcarrousel' href='javascript:void(0)' onclick=\"startCarrousal('$id','$b','0','0',$multiscreen_status,'is_block')\"><img src='./img/$color_scheme/start01.png' border='0' width='30' height='27' ></a>";
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
       $show_multiscreen="<td align='center'>$multiscreen_dropdown</td>";
     } else {
       $show_multiscreen="";
     }
    // echo $multiscreen_status." @@@@@@@@@@@@ ";
     $multiscreen_status=0;
     if($result['edit_status']==0){
      $edit_status= "<td align='center'> <a href='edit.php?id=".$result['id']."&status=$resstatus&is_block=1'><img src='./img/$color_scheme/edit03.png' border='0' width='30' height='28' align='center' /></a></td>";
      
     }else{
     $edit_status= "<td align='center'> <a href='edit.php?id=".$result['id']."&status=$resstatus&is_block=1' onclick=\"return confirm('Deze carrousel wordt op dit moment mogelijk door een andere gebruiker bewerkt. Het wordt afgeraden om een carrousel gelijktijdig door meerdere mensen te laten bewerken. Klik op Annuleren om terug te gaan of op OK om de carrousel te bewerken.');\"><img src='./img/edit_greyed.png' border='0' width='30' height='28' align='center' /></a></td>";
     }    
     
      $client_content_block.="<tr class=".$sty_class.">
      <td>".stripcslashes($result['name'])."</td>
     
      
      $edit_status
      
      <!--<td align='center'><a href='javascript:void(0)' onclick=\"duplicateCarrousel('$id')\"><img src='./img/$color_scheme/duplicate_button.png' border='0' width='37' height='32' title='Duplicate Carrousel'></a></td>-->
                       <!-- <td align='center'><a href='delete.php?id=".$result['id']."' onclick=\"return confirm('Weet u zeker dat u deze carrousel wilt verwijderen?');\"><img src='./img/$color_scheme/delete.png' border='0' width='33' height='31' ></td>
		       <td align='center'id='schedule01'></td>
			<td align='center'></td>
			<td align='center'></td>	-->		
			<td align='center'>$status</td>
                       </td>";
     $scheduling_list="";
     $b++;
   }
   $client_content_block.="</table>";
 }

//----------- for empty temp crrousel table ----------------------------
