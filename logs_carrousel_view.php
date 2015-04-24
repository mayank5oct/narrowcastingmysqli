<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
include('config/database.php');
include('color_scheme_setting.php');
$conditions = '';
$start_date = '';
$end_date = '';
$selectval = '';
$selectcond = '';

if (isset($_POST['submit'])) {
//assign post data to variables
    $start_date = trim($_POST['from_date']);
    $end_date = trim($_POST['to_date']);
    $selectval = trim($_POST['sector_list']);
    if ($selectval != '') {
        $selectcond = "and theatre_id=$selectval";
    }
    if (($start_date != '' && $end_date == '')) {
        $start_date_in_seconds = strtotime($start_date);
        $end_date_in_seconds = strtotime("+1 day", $start_date_in_seconds);
        $conditions = "where start_time between $start_date_in_seconds and $end_date_in_seconds $selectcond";
    } else if ($start_date != '' && $end_date != '') {
        $start_date_in_seconds = strtotime($start_date);
        $end_date_in_seconds = strtotime($end_date);
        $conditions = "where start_time between $start_date_in_seconds and $end_date_in_seconds $selectcond";
    } else if (($start_date == '' || $end_date == '') && $selectval != '') {
        $conditions = "where theatre_id=$selectval";
    }
}
?>





<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pandora | Logs</title>
<link rel="stylesheet" href="./css/<?php echo $css_file_name; ?>" type="text/css" />
<link rel="stylesheet" href="./css/style_common.css" type="text/css" />

    <script type="text/javascript" src="./js/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="./js/jquery.blockUI.js?v2.38"></script>

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
    <style type="text/css">
        #overzicht{display:block;color:#ffffff;background-color:#2e3b3c;border:none;}
        #alle_carrousels{font-size:14px;color: #b5b5b5;line-height:30px;}
        #maak_carrousel{font-size:14px;color: #c30f0f;line-height:30px;}
        #nieuw_bestand{font-size:14px;color: #c30f0f;line-height:30px;}
        #nieuwe_twitterfeed{font-size:14px;color: #c30f0f;line-height:30px;}
        #iets_nieuws{font-size:14px;color: #c30f0f;line-height:30px;}
#spreekuur{font-size:14px;line-height:30px;}
        .title{font-weight: bold;font-size:32px;}

        td{width:100px;}
        .showNone{
            display:none;
        }
        .showTd{
            visibility:visible; 
        }
        .image{
            cursor:pointer;
        }
        .cname{
            margin-top: -18px;  
            margin-left:20px;
        }
        #show{
            border:0px;
        }
        .switchview{
            background: url("./img/carrousel/button_right.gif") no-repeat scroll right top transparent;
            color: #444444;
            display: block;
            float: right;
            font: 12px arial,sans-serif;
            height: 24px;
            margin-right: 403px;
            margin-top: -31px;
            padding-right: 18px;
            text-decoration: none;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            cursor:pointer;
        }
        .spanclass {
            background: url("./img/carrousel/button_left.gif") no-repeat scroll 0 0 transparent;
            display: block;
            line-height: 14px;
            padding: 5px 0 5px 18px;
            color:red;
        }
    </style>
    <script>
        $(document).ready(function(){
            $(".redirect").click(function() { 
                location.href="./logs.php";
            });
            $("#iconsdate").click(function() { 
                $("#sdate").datepicker( {dateFormat: 'yy-mm-dd'});
                $("#sdate").focus();
            });
            $("#iconndate").click(function() { 
                $("#ndate").datepicker( {dateFormat: 'yy-mm-dd'});
                $("#ndate").focus();
            });
 


        });
        function show_table(keyId){
            if($('.'+keyId).hasClass("showNone")){
                $('.'+keyId).removeClass("showNone");
                $('.'+keyId).addClass("showTd");
                document.getElementById("showimage_"+keyId).style.display="none";
                document.getElementById("hideimage_"+keyId).style.display="block";
            }else{
                $('.'+keyId).removeClass("showTd");
                $('.'+keyId).addClass("showNone"); 
                document.getElementById("showimage_"+keyId).style.display="block";
                document.getElementById("hideimage_"+keyId).style.display="none";
            }
        }
        function change_list(){
            $('#selectform').submit();
            $('#dateform').submit();
        }
    </script>
</head>
<body>
    <?php
    include('header_overlay.html');
   
    $db = new Database;
    $query_carrousels = "SELECT id,name FROM carrousel_listing ORDER BY name LIMIT 0,30";
    $conn_carrousel = $db->query($query_carrousels);
    while ($result = mysql_fetch_array($conn_carrousel)) {
        $array_name[$result['id']] = $result['name'];
    }
    $query_sub_carrousel = "SELECT id,name FROM carrousel ORDER BY name LIMIT 0,30";
    $conn_sub_carrousel = $db->query($query_sub_carrousel);
    while ($result = mysql_fetch_array($conn_sub_carrousel)) {
        $array_sub_carrousel_name[$result['id']]['name'] = $result['name'];
    }

    $theatres_dropdown = "SELECT id,theatre_name FROM theatre";
    $theatres_query = $db->query($theatres_dropdown) or die(mysql_error());
    ?>
    <!-- START MAIN CONTAINER -->
    <div class="main_container">
        <span><h2>Pandora logs - carrouseloverzicht</h2></span>
        <span class="switchview"><a class="redirect"><span class="spanclass">Ga naar het mediaoverzicht</span></a></span>
        <br/>
        <form id="dateform" method="post" action="" > 
            <table>
                <tr>
                    <td>
                        <select id="sector_list" name="sector_list" class="inputstandard"> 
                            <option value=" ">Kies een theater</option>
                            <?php
                            while ($theatres_list = mysql_fetch_array($theatres_query)) {
                                if ($theatres_list['id'] == $selectval) {
                                    echo '<option selected="' . $selected . '" size ="40" value=" ' . $theatres_list['id'] . '" name="' . $theatres_list['theatre_name'] . '">' . $theatres_list['theatre_name'] . '</option>';
                                } else {
                                    echo '<option  size ="40" value=" ' . $theatres_list['id'] . '" name="' . $theatres_list['theatre_name'] . '">' . $theatres_list['theatre_name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>

                    </td>
                    <td>Start datum</td><td>
                        <input name="from_date" type="text" value="<?php echo $start_date; ?>" id="sdate" />
                    </td>
                    <td>
                        <img  width='20px'  src='./img/carrousel/datetime.png' style="margin-top:6px;cursor:pointer;" id='iconsdate'/>
                    </td>

                    <td>Eind datum</td>
                    <td>
                        <input name="to_date" type="text" value="<?php echo $end_date; ?>" id="ndate" />
                    </td>
                    <td>
                        <img  width='20px'  src="./img/carrousel/datetime.png" style="margin-top:6px;cursor:pointer;" id='iconndate'/>
                    </td>

                    <td><input type="submit" value="Verzend" id="submit" name="submit"></td>
                </tr>
            </table>
        </form>

        <div class="grid_container" id="show">
            <table>
                <tr class='grid_header'>
                    <td height='25' width='35%'>Naam carrousel</td>
                    <td height='25' width='35%' align="center">Start</td>
                    <td height='25' width='35%' align="center">Einde</td>
                    <td height='25' width='35%' align="center">Duur</td>
                    <td height='25' width='35%' align="center"></td>
                </tr>

                <?php
                $select_query = "SELECT cl.carrousel_item_id,cl.theatre_label,cl.multiscreen_name,cl.carrousel_name,cl.multiscreen_label,csl.item_name,csl.carrousel_sub_item_id,csl.duration,SUM(csl.count) as count,cl.start_time,cl.end_time FROM `carrousel_log` AS cl LEFT JOIN `carrousel_sub_log` AS csl ON (cl.id=csl.carrousel_log_id) $conditions  GROUP BY csl.carrousel_sub_item_id,cl.start_time,cl.end_time ORDER BY cl.start_time";
                $connc = mysql_query($select_query, $db->Link_ID_NEW_LOG);
                $i = 0;
                while ($selectresult = mysql_fetch_assoc($connc)) {
                    // $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']][]=$selectresult['carrousel_sub_item_id'];
                   /* if (!empty($selectresult['multiscreen_label'])) {
                        $multi_after_explode = explode(',', $selectresult['multiscreen_label']);
                        $count_multi = count($multi_after_explode);
                    }
                    if ($count_multi != '' && $count_multi > 0) {
                        $count_val = $count_multi * $selectresult['count'];
                    } else {
                        $count_val = $selectresult['count'];
                    }*/
                    $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']][$selectresult['carrousel_sub_item_id']] = $selectresult['count'];
                     $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']][$selectresult['carrousel_sub_item_id']] = $selectresult['item_name'];
                    $array_carrousels[$selectresult['start_time']]['duration_sub_items'][$selectresult['carrousel_sub_item_id']]['duration'] = $selectresult['duration'];
                    $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']]['end_time'] = $selectresult['end_time'];
                    $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']]['multiscreen'] = $selectresult['multiscreen_label'];
                    $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']]['carrousel_name'] = $selectresult['carrousel_name'];
                    $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']]['theatre_label'] = $selectresult['theatre_label'];
                    $array_carrousels[$selectresult['start_time']][$selectresult['carrousel_item_id']]['theatre_label'] = $selectresult['theatre_label'];
                }
                ?>
                <?php
                $i = 1;
                if (!empty($array_carrousels)) {
                    foreach ($array_carrousels as $start_time => $values) {// key start_time

                        $duration_array = $values['duration_sub_items'];
                        unset($values['duration_sub_items']);

                        foreach ($values as $carrousel_item_id => $value) { // key carrousel_item_id
                            $multiscreen    = $value['multiscreen'];
                            $theatre_label  = $value['theatre_label'];
                            $carrousel_name = $value['carrousel_name'];
                            ?>
                            <tr>
                                <?php if (in_array($carrousel_item_id, array_keys($array_name))) {
                                    ?>
                                    <td id="<?php echo $i; ?>">
                                        <?php if (!empty($value['end_time'])) { ?>
                                            <a class="image" onclick="show_table('<?php echo $i; ?>')"><span  id="showimage_<?php echo $i; ?>"><img src='./img/carrousel/plus.jpg' border='0' width='15' height='15' ></span><span  id="hideimage_<?php echo $i; ?>" style="display:none"><img src='./img/carrousel/minus.jpg' border='0'  width="15" height="15" alt="Close" title="Close"></span></a>
                                        <?php } ?>
                                         <div class="cname"><?php echo $array_name[$carrousel_item_id]; ?></div>
                                    </td>
                                <?php } else{?>
                                
                                
                                 <td id="<?php echo $i; ?>">
                                        <?php if (!empty($value['end_time'])) { ?>
                                            <a class="image" onclick="show_table('<?php echo $i; ?>')"><span  id="showimage_<?php echo $i; ?>"><img src='./img/carrousel/plus.jpg' border='0' width='15' height='15' ></span><span  id="hideimage_<?php echo $i; ?>" style="display:none"><img src='./img/carrousel/minus.jpg' border='0'  width="15" height="15" alt="Close" title="Close"></span></a>
                                        <?php } ?>
                                        <div class="cname"><?php echo $carrousel_name; ?></div>
                                    </td>
                                
                                
                                
                                <?php } ?>

                                <td height = '25' width = '10%' align = 'center'><?php echo date("Y-m-d H:i:s", $start_time); ?></td>
                                <?php if (!empty($value['end_time'])) { ?>
                                    <td width = '10%' align = 'center'><?php echo date("Y-m-d H:i:s", $value['end_time']); ?></td>
                                <?php } else { ?>
                                    <td width = '10%' align = 'center'>--</td>
                                <?php } ?>

                                <?php
                                $diff = '';
                                if (!empty($value['end_time']))
                                    $diff = $value['end_time'] - $start_time;
                                if ($diff != '') {
                                    ?>
                                    <td width = '10%' align = 'center'><?php echo gmdate("H:i:s", $diff); ?></td>
                                <?php } else { ?>
                                    <td width = '10%' align = 'center'>--</td>
                                <?php } ?>
                            </tr>
                            <tbody class="<?php echo $i; ?> showNone">
                            <th>Bestanden</th>
                            <th>Duur</th>
                            <th>Telling</th>
                            <th>Multiscreen</th>
                            <th>EPPC</th>
                            <?php
                            foreach ($value as $sub_carrousel_key => $count) {
                                if ($sub_carrousel_key != 'end_time' && $sub_carrousel_key != 'multiscreen' && $sub_carrousel_key != 'theatre_label') {
                                    ?>
                                    <tr>
                                        <?php if (in_array($sub_carrousel_key, array_keys($array_sub_carrousel_name))) { ?>
                                            <td align="center"><?php echo $array_sub_carrousel_name[$sub_carrousel_key]['name'] ?></td>
                                        <?php }else { ?> <td align="center"><?php echo $count[1]; ?></td>
                                        
                                        <?php } if (in_array($sub_carrousel_key, array_keys($duration_array))) { ?>
                                            <td align="center"><?php echo $duration_array[$sub_carrousel_key]['duration'] ?></td>
                                        <?php }else{ ?>
                                            <td align="center"></td>
                                            <? }if($count[0] !=''){?>
                                        <td align="center"><?php echo $count[0]; ?></td>
                                        <?php }else{?>
                                        <td align="center"></td>
                                        <?php } if ($multiscreen != '') { ?>
                                            <td align="center"><?php echo $multiscreen; ?></td>
                                        <? } else { ?>
                                            <td align="center">--</td>
                                        <? }
                                        ?>
                                        <?php if ($theatre_label != '') { ?>
                                            <td align="center"><?php echo $theatre_label; ?></td>
                                        <? } else { ?>
                                            <td align="center">--</td>
                                        <? }
                                        ?>
                                    </tr>

                                    <?
                                }
                            }
                            ?>
                            </tbody>
                            <?php
                            $i++;
                        }
                    }
                }
                ?>



            </table>
        </div>
    </div>
    <br/>
</div>
</body>
