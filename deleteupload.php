<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
session_start();
error_reporting(0);
include('config/database.php');
$db = new Database;
$delete_id=trim(urldecode($_REQUEST['delete']));
//$id=$_REQUEST['id'];
$dir=trim($_REQUEST['dir']);
$cid=$_REQUEST['cid'];
$status=$_REQUEST['status'];

$delete_item_fullname=$dir.'/'.$delete_id;
$used_carrousel_id=array();


$delete_query="";
$delete_query_status=true;
if($dir=="mededelingen"){
 $delete_query="delete from temp_mededelingen where temp_image_name='$delete_id'";
}else if($dir=="overlay_video"){
 $delete_query="delete from temp_overlay where temp_file_name='$delete_id'"; 
}



if($delete_query!=""){
 $delete_query_status=$db->query($delete_query);
}


##getting carrousel id of items that is going to delete
$used_carrousel_id=array();
$carrousel_query="select distinct(c_id) as carrousel_id from carrousel where name='$delete_item_fullname'";
$check_query_result=$db->query($carrousel_query);
while($result_row =mysql_fetch_array($check_query_result)){
   $used_carrousel_id[]=$result_row['carrousel_id'];
}

## deleting carrousel items
$carrousel_delete_query="delete from carrousel where name='$delete_item_fullname'";
$delete_query_status=$db->query($carrousel_delete_query);
$tmpcarrousel_delete_query="delete from temp_carrousel where name='$delete_item_fullname'";
$tmpdelete_query_status=$db->query($tmpcarrousel_delete_query);




if($delete_id!="") {
   if($delete_query_status && $delete_query_status && $tmpdelete_query_status){
      $del_id=explode(".",$delete_id);
      $delete_item="./$dir/thumb/".$delete_id;
      $delete_item1="./$dir/thumb/".$del_id[0].".jpg";
      $delete_item3="./$dir/thumb/".$del_id[0].".mp4";
      $delete_item4="./$dir/preview/".$del_id[0].".mov";
      $delete_item2="./$dir/".$delete_id;
      $delete_item5="./$dir/totalthumb/".$del_id[0].".jpg";
      @unlink($delete_item);
      @unlink($delete_item1);
      @unlink($delete_item2);
      @unlink($delete_item3);
      @unlink($delete_item4);
      @unlink($delete_item5);
      
   }
}


 ## redrdering the carropusel items when deletion is successful
if($delete_id!="" && $delete_query_status && $tmpdelete_query_status){
      if(count($used_carrousel_id)>0){
         $carrousel_query="select c_id,id  from carrousel where c_id in (".implode(',',$used_carrousel_id).") order by c_id asc ,record_listing_id asc";
         $carrousel_result_resource=$db->query($carrousel_query);
         $c_id=0;
         $previous_cid=0;
         $order_no=1;
          while($result_row_second =mysql_fetch_array($carrousel_result_resource)){
             $previous_cid=$c_id;
             $row_id=$result_row_second['id'];
             $c_id=$result_row_second['c_id'];
             $update_query="update carrousel set record_listing_id=$order_no where id=$row_id";
             $check_query_result=$db->query($update_query);
             $order_no++;
             if($previous_cid!=$c_id && $previous_cid!=0){
                $order_no=1;
                $c_id=0;
             }
          }  
      }

}



if(!is_numeric($cid)){
   $status = 2;
   echo"<script type='text/javascript'>window.location = 'create_carrousel.php?dir='+'$dir'+'&status='+'$status'</script>";
}
else{
   echo"<script type='text/javascript'>window.location = 'edit.php?id='+'$cid'+'&status='+'$status'+'&dir='+'$dir'+'&key=overlay'</script>";
}
?>
