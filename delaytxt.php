<?php
session_start();
ini_set('max_execution_time', 3600);
error_reporting(1);

header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require('config/database.php');
include('color_scheme_setting.php');
$db = new Database;

if(isset($_POST) && $_POST['submit'] !=""){
    //echo "<pre>"; print_r($_POST); echo "</pre>";
    
    $update_qry = "update delay_mededelingen set ";
    if(isset($_POST['delay_text_1']) && !empty($_POST['delay_text_1'])){
        $update_qry .= "delay_text_1 = '".mysql_escape_string($_POST[delay_text_1])."'";
        
    }
    if(isset($_POST['delay_text_2']) && !empty($_POST['delay_text_2'])){
        $update_qry .= ", delay_text_2 = '".mysql_escape_string($_POST[delay_text_2])."'";
        
    }
    if(isset($_POST['delay_text_3']) && !empty($_POST['delay_text_3'])){
        $update_qry .= ", delay_text_3 = '".mysql_escape_string($_POST[delay_text_3])."'";
        
    }
    if(isset($_POST['delay_text_4']) && !empty($_POST['delay_text_4'])){
        $update_qry .= ", delay_text_4 = '".mysql_escape_string($_POST[delay_text_4])."'";
        
    }
    if(isset($_POST['delay_text_5']) && !empty($_POST['delay_text_5'])){
        $update_qry .= ", delay_text_5 = '".mysql_escape_string($_POST[delay_text_5])."'";
        
    }
    if(isset($_POST['delay_text_6']) && !empty($_POST['delay_text_6'])){
        $update_qry .= ", delay_text_6 = '".mysql_escape_string($_POST[delay_text_6])."'";
        
    }
    echo $update_qry .= " where id = '$_POST[template_id]'";
    $db->query($update_qry);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pandora | Maak hier uw huishoudelijke mededelingen</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />

</head>
<body>
    
    
    <?php
    $qry="select dm.id as tid , dm.name_t,dm.status1,dm.status2,dm.status3,dm.status4,dm.status5,dm.status6,dm.delay_text_1,dm.delay_text_2,dm.delay_text_3,dm.delay_text_4,dm.delay_text_5,dm.delay_text_6 from delay_mededelingen dm, theatre th where dm.theatre_id=th.id and th.status=1";
    $con=$db->query($qry);
    $i=1;
    while($result=mysql_fetch_array($con)):
    //
   //echo "<pre>"; print_r($result); echo "<pre>";
    ?>
    <form name="change_delay_<?php echo $i;?>"  method="POST" action"">
    <?php if($result['status1']==1):?>
    <table cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="2"><?php echo $result['name_t']?></td>
    </tr>
        <tr>
            <td>Delay Text 1</td>
            <td><input type="text" name="delay_text_1" id="delay_text_1" value="<?php echo htmlentities($result['delay_text_1'], ENT_QUOTES); ?>"/></td>
        </tr>
        <?php endif;?>
        
        <?php if($result['status2']==1):?>
        <tr>
            <td>Delay Text 2</td>
            <td><input type="text" name="delay_text_2" id="delay_text_2" value="<?php echo htmlentities($result['delay_text_2'], ENT_QUOTES); ?>"/></td>
        </tr>
        <?php endif;?>
        
         <?php if($result['status3']==1):?>
        <tr>
            <td>Delay Text 3</td>
            <td><input type="text" name="delay_text_3" id="delay_text_3" value="<?php echo htmlentities($result['delay_text_3'], ENT_QUOTES); ?>"/></td>
        </tr>
        <?php endif;?>
        
        <?php if($result['status4']==1):?>
        <tr>
            <td>Delay Text 4</td>
            <td><input type="text" name="delay_text_4" id="delay_text_4" value="<?php echo htmlentities($result['delay_text_4'], ENT_QUOTES); ?>"/></td>
        </tr>
        <?php endif;?>
        
        <?php if($result['status5']==1):?>
        <tr>
            <td>Delay Text 5</td>
            <td><input type="text" name="delay_text_5" id="delay_text_5" value="<?php echo htmlentities($result['delay_text_5'], ENT_QUOTES); ?>"/></td>
        </tr>
        <?php endif;?>
        
         <?php if($result['status6']==1):?>
        <tr>
            <td>Delay Text 6</td>
            <td><input type="text" name="delay_text_6" id="delay_text_6" value="<?php echo htmlentities($result['delay_text_6'], ENT_QUOTES); ?>"/></td>
        </tr>
        <?php endif;?>
        <tr>
            
            <td colspan="2">
                <input type="hidden" name="template_id" value="<?php echo $result['tid']?>" />
                <input type="submit" name="submit" value="Submit"/></td>
        </tr>
         </table>
    </form>
    <?php
   $i++;
    endwhile;
    ?>  
   
</body>
</html>