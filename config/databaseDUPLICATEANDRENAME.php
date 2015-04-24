<?php

//Config CSV/XML dropdown
$dropdown_label1 = "line1";
$dropdown_label2 = "line2";

date_default_timezone_set('Europe/Amsterdam');
class Database {

    var $Host = "localhost"; // Hostname of our MySQL server.
    var $Database = "narrowcasting"; // Logical database name on that server.
    var $User = "narrow"; // User and Password for login.
    var $Password = "RRffTTmW";
    var $Link_ID_PREV = 0;  // Result of mysql_connect().
    var $Query_ID = 0;  // Result of most recent mysql_query().
    var $Host_log = "localhost"; // Hostname of our MySQL server.
    //var $Host_log = "78.46.128.189"; // Hostname of our MySQL server.
    var $Database_Log = "narrowcasting_logs"; // Logical database name on that server.
    var $User_log = "narrow_logs"; // User and Password for login.
    var $Password_log = "RRffTTmW";
    var $Link_ID_NEW_LOG = 0;  // Result of mysql_connect().
    // var $Query_ID = 0;  // Result of most recent mysql_query().
    var $Record = array();  // current mysql_fetch_array()-result.
    var $Row;           // current row number.
    var $LoginError = "";
    var $Errno = 0;  // error state of query...
    var $Error = "";
	
	// Master carrousel credentials
    var $remote_db_host="localhost";
    var $remote_db_user="narrow";
    var $remote_db_password="RRffTTmW";
    var $remote_db_database="narrowcasting";
	
	//Config
	var $sort_by_date="0";
    var $enable_fade = "1";
    var $fade_in_time = "700";
    var $fade_out_time = "300";
    var $total_fade_time = "1000";
    var $is_html5 = 0;
    var $ffmpeg_path = "/home/pandora/bin/ffmpeg";
    var $master_ip ="10.100.0.41";
    var $rootpath="/var/www/html/narrowcasting/";

//-------------------------------------------
//    Connects to the database
//-------------------------------------------
    function connect() {
        if (0 == $this->Link_ID_PREV) {
            $this->Link_ID_PREV = mysql_connect($this->Host, $this->User, $this->Password);
        }
        if (0 == $this->Link_ID_NEW_LOG) {
            $this->Link_ID_NEW_LOG = mysql_connect($this->Host_log, $this->User_log, $this->Password_log, true);
        }
        if (!$this->Link_ID_PREV)
            $this->halt("Link-ID == false, database host connect failed");
        if (!$this->Link_ID_NEW_LOG)
            $this->halt("Link-ID == false, database host log connect failed");
        if (!mysql_query(sprintf("use %s", $this->Database), $this->Link_ID_PREV))
            $this->halt("cannot use database " . $this->Database);
        if (!mysql_query(sprintf("use %s", $this->Database_Log), $this->Link_ID_NEW_LOG))
            $this->halt("cannot use database " . $this->Database_Log);
    }

// end function connect
//-------------------------------------------
//    Queries the database
//-------------------------------------------
    function query($Query_String) {
        $this->connect();
        $this->Query_ID = mysql_query($Query_String, $this->Link_ID_PREV);
        $this->Row = 0;
        $this->Errno = mysql_errno();
        $this->Error = mysql_error();
        if (!$this->Query_ID)
            $this->halt("Invalid SQL: " . $Query_String);
        return $this->Query_ID;
    }

// end function query
//-------------------------------------------
//    If error, halts the program
//-------------------------------------------
    function halt($msg) {
        printf("</td></tr></table><b>Database error:</b> %s<br>n", $msg);
        printf("<b>MySQL Error</b>: %s (%s)<br>n", $this->Errno, $this->Error);
        die("Session halted.");
    }

// end function halt
//-------------------------------------------
//    Retrieves the next record in a recordset
//-------------------------------------------
    function nextRecord() {
        @ $this->Record = mysql_fetch_array($this->Query_ID);
        $this->Row += 1;
        $this->Errno = mysql_errno();
        $this->Error = mysql_error();
        $stat = is_array($this->Record);
        if (!$stat) {
            @ mysql_free_result($this->Query_ID);
            $this->Query_ID = 0;
        }
        return $stat;
    }

// end function nextRecord
//-------------------------------------------
//    Retrieves a single record
//-------------------------------------------
    function singleRecord() {
        $this->Record = mysql_fetch_array($this->Query_ID);
        $stat = is_array($this->Record);
        return $stat;
    }

// end function singleRecord
//-------------------------------------------
//    Returns the number of rows  in a recordset
//-------------------------------------------
    function numRows() {
        return mysql_num_rows($this->Query_ID);
    }

// end function numRows
    // end class Database
//------------ function for log write ---------------
    function writeLog($carrouselName) {
        $fileName = date('Y-m-d') . "-CP.txt";
        //$fHandle=fopen("logs/".$fileName ,'a+');
        //chmod("logs/".$fileName, 0777); 
        $fHandle = fopen("./logs/" . $fileName, 'a+');
        chmod("./logs/" . $fileName, 0777);
        $sizecheck = filesize("./logs/" . $fileName);
        $dateTime = date('m-d-Y H') . " uur";
        if ($sizecheck != 0) {
            $string = " End time : " . "$dateTime" . "\n";
        }
        $string.= "\n\n Start time : " . "$dateTime" . "\n";
        $string.= " Carrousel Name : " . "$carrouselName" . "\n";
        fwrite($fHandle, $string);
        fclose($fHandle);
    }

    /*
      @Created By : Arti Grover
      @Created On : 29th march,2013
      @Params :--id of carrousel whose end time and start time are present
      @Disc :sub function for calculation
      @Return :--
     */

    function calculation($carrouselId = null) {
        if (!empty($carrouselId)) {
            $sql_retrieve = "Select id,carrousel_item_id,start_time,end_time from carrousel_log as cl where cl.id='$carrouselId'";
            $results = mysql_fetch_array(mysql_query($sql_retrieve, $this->Link_ID_NEW_LOG)) or die(mysql_error());
            $end_time = $results['end_time'];
            $citem_id = $results['carrousel_item_id'];
            $id_to_be_updated = $results['id'];
            $start_time = $results['start_time'];
            $difference = $end_time - $start_time;
            $carrousel_sub_items_listing = "Select id,name,duration from carrousel as cl where cl.c_id='$citem_id' order by cl.record_listing_id asc";
            $results_sub_items = mysql_query($carrousel_sub_items_listing, $this->Link_ID_PREV) or die(mysql_error());
            while ($result1 = mysql_fetch_array($results_sub_items)) {
                $name = $result1['name'];

                $sub_carrousel_extension_array = explode(".", $name);
                $sub_carrousel_extension = $sub_carrousel_extension_array[1];

                /* retrieving bin names */
                $sub_carrousel_bin_array = explode("/", $name);
                $sub_carrousel_bin = $sub_carrousel_bin_array[0];
                $sub_carrousel_bin_item = $sub_carrousel_bin_array[1];

                if ($sub_carrousel_extension == "jpg" || $sub_carrousel_extension == "png" || $sub_carrousel_extension == "gif") {
                    $duration = $result1['duration'];
                } else {//finding duration in case of video files dynamically 
                    ob_start();
                    passthru("sudo $ffmpeg_path -i /var/www/html/narrowcasting/$name 2>&1");
                    $video_duration = ob_get_contents();
                    ob_end_clean();
                    preg_match('/Duration: (.*?),/', $video_duration, $matches);
                    $duration_array = $matches[1];
                    $duration_array_explode = split(':', $duration_array);
                    $duration = $duration_array_explode[1] * 60 + $duration_array_explode[2];
                }
                $array_sub_items[$result1['id']] = $duration; //array of all subitems
                $array_sub_items_bin_names[$result1['id']]['bin'] = $sub_carrousel_bin;
                $array_sub_items_bin_names[$result1['id']]['item_name'] = $sub_carrousel_bin_item;
            }
            $array_sub_items_to_be_saved = $this->sub_carrousel_count($array_sub_items, $difference); //this function will find the count of each sub image or video
            $this->save_sub_items_count($id_to_be_updated, $array_sub_items_to_be_saved, $array_sub_items, $array_sub_items_bin_names);
        }
    }

    /*
      @Created By : Arti Grover
      @Created On : 22nd march,2013
      @Params :--array of carrousel sub items,difference
      @Disc : count retrieval
      @Return :array with counts
     */

    function sub_carrousel_count($array_sub_items = array(), $difference_endtime_starttime = '') {
        $duration_sum = array_sum($array_sub_items);
        $max_outer_loop_first_value = floor($difference_endtime_starttime / $duration_sum);
        $max_outer_loop_second_value = $max_outer_loop_first_value + 1;
        $array_initialized_count_values = array(); //array which initailzes keys of $array_sub_items(ids of all subitems) to 0
        $sum_sub_items = 0;
        foreach ($array_sub_items as $key => $value) {
            $array_initialized_count_values[$key] = 0;
        }
        for ($i = 0; $i <= $max_outer_loop_second_value; $i++) {
            foreach ($array_sub_items as $key => $value) {
                if ($sum_sub_items > $difference_endtime_starttime) {
                    break;
                }
                $sum_sub_items+=$value;
                $array_initialized_count_values[$key] = $array_initialized_count_values[$key] + 1; //increment count of subitems visit every time sum is less than difference
            }
        }
        return $array_initialized_count_values;
    }

    /*
      @Created By : Arti Grover
      @Created On : 22nd march,2013
      @Params :--id to be inserted,array containg count of subitems,array for determining runtime length in case of video files
      @Disc : saving subitems count to db
      @Return :--
     */

    function save_sub_items_count($carrousel_log_id = '', $array = array(), $array_duration = array(), $bin_names = array()) {
        foreach ($array as $key => $count) {
            if (in_array($key, array_keys($array_duration))) {
                $duration = $array_duration[$key];
            }
            if (in_array($key, array_keys($bin_names))) {
                $bin_name = $bin_names[$key]['bin'];
                $item_name = $bin_names[$key]['item_name'];
            }
            $sql = "Insert into carrousel_sub_log (carrousel_log_id,carrousel_sub_item_id,duration,count,bin_name,item_name)
VALUES ($carrousel_log_id,$key,$duration,$count,'$bin_name','$item_name')";
            mysql_query($sql, $this->Link_ID_NEW_LOG) or die(mysql_error());
        }
    }

    /*
      @Created By : Arti Grover
      @Created On : 21st march,2013
      @Params :--
      @Disc : carrousel database logs
      @Return :--
     */

    function writeDbLog() {
        $ip_zero = array();
        $carrousels_sleeping_sql = "Select id from carrousel_listing as cl where cl.status=1";
        $output_carrousels_sleeping = mysql_query($carrousels_sleeping_sql, $this->Link_ID_PREV) or die(mysql_error());
        while ($results_sleeping = mysql_fetch_array($output_carrousels_sleeping)) {
            $id = $results_sleeping['id'];
            $query_end_time = "Select id from carrousel_log as cl where cl.carrousel_item_id=$id and cl.end_time IS NULL";
            $endtime_carrousels_sleeping = mysql_query($query_end_time, $this->Link_ID_NEW_LOG) or die(mysql_error());
            //echo $id;
            if (!empty($endtime_carrousels_sleeping)) {
                while ($endtime_carrousels_sleeping_array = mysql_fetch_array($endtime_carrousels_sleeping)) {
                    if (!empty($endtime_carrousels_sleeping_array['id']))
                        $sleeping_carrousels_endtime[] = $endtime_carrousels_sleeping_array['id']; //retrieve all those carrousel ids whose end time is null 
                }
                // print_r($endtime_carrousels_sleeping_array);
            }
        }
        // $array_carrousels=array_flip($sleeping_carrousels_endtime);
        if (!empty($sleeping_carrousels_endtime)) {
            $sleeping_carrousels_implode = implode(',', $sleeping_carrousels_endtime);
            $sql_update = "Update carrousel_log Set end_time=UNIX_TIMESTAMP() where id in ($sleeping_carrousels_implode)";
            mysql_query($sql_update, $this->Link_ID_NEW_LOG) or die(mysql_error());
            for ($i = 0; $i < count($sleeping_carrousels_endtime); $i++) {
                $this->calculation($sleeping_carrousels_endtime[$i]);
            }
        }

        $status_theatre = "Select id from theatre where status=1";
        $status_array = mysql_query($status_theatre, $this->Link_ID_PREV) or die(mysql_error());
        $status_array_id = mysql_fetch_assoc($status_array);
        $theatre_active = $status_array_id['id'];

        $carrousels_running_sql = "Select id,ipaddress,multiscreen,name from carrousel_listing as cl where cl.status=0";
        $output_carrousels_running = mysql_query($carrousels_running_sql, $this->Link_ID_PREV) or die(mysql_error());

        /* multiple insertions in case of ipaddress=0 */
        while ($results = mysql_fetch_array($output_carrousels_running)) {
            $id_new = $results['id'];
            $ip_address = $results['ipaddress'];
            $multiscreen = $results['multiscreen'];
	    $carrousel_name = $results['name'];
            //retrieves ip addresses comma seperated values
            if ($ip_address != '') {
                $ip_address_zero_theatre = "Select id from carrousel_log where carrousel_item_id=$id_new and end_time IS NULL";
                $ipaddress_zero_array = mysql_query($ip_address_zero_theatre, $this->Link_ID_NEW_LOG) or die(mysql_error());
                $ip_zero = mysql_fetch_array($ipaddress_zero_array);
                if ($ip_address == 0) {
                    $IpAddressesAfterCalculation = $this->insertIpAddresses(0, $theatre_active);
                } else {
                    $IpAddressesAfterCalculation = $this->insertIpAddresses($ip_address, $theatre_active);
                }
            }
            $theatre_ips_group = $IpAddressesAfterCalculation['theatreids'];
            $theatre_labels_group = $IpAddressesAfterCalculation['theatrelabels'];

            //retrieves multi screen comma seperated values
            if (!empty($multiscreen)) {
                if ($multiscreen == 0) {
                    $MultiScreensAfterCalculation = $this->insertMultiScreens(0);
                } else {
                    $multi_explode_array = explode(',', $multiscreen);
                    $MultiScreensAfterCalculation = $this->insertMultiScreens($multi_explode_array, $theatre_active);
                }
            }
            $multiscreen_name_group = $MultiScreensAfterCalculation['multiname'];
            $multiscreen_labels_group = $MultiScreensAfterCalculation['multilabels'];
            //retrieves theatre id of active theatre
            if ($ip_address != '') {
                $theatre_ips_afterexplode = explode(',', $IpAddressesAfterCalculation['theatreids']);
                $theatre_labels_afterexplode = explode(',', $IpAddressesAfterCalculation['theatrelabels']);
            }
            if (empty($ip_zero)) {
                if (count($theatre_ips_afterexplode) > 1) {
                    $j = count($theatre_ips_afterexplode);
                    for ($k = 0; $k < $j; $k++) {
                        $sql_update = "Insert into carrousel_log (carrousel_name,carrousel_item_id,start_time,theatre_id,theatre_label,theatre_ip,multiscreen_name,multiscreen_label,created,modified)
      VALUES ('$carrousel_name',$id_new,UNIX_TIMESTAMP(),$theatre_active,'All','$theatre_ips_afterexplode[$k]','$multiscreen_name_group','$multiscreen_labels_group',now(),now())";
                        mysql_query($sql_update, $this->Link_ID_NEW_LOG) or die(mysql_error());
                    }
                } else {
                    $sql_update = "Insert into carrousel_log (carrousel_name,carrousel_item_id,start_time,theatre_id,theatre_label,theatre_ip,multiscreen_name,multiscreen_label,created,modified)
      VALUES ('$carrousel_name',$id_new,UNIX_TIMESTAMP(),$theatre_active,'$theatre_labels_group','$theatre_ips_group','$multiscreen_name_group','$multiscreen_labels_group',now(),now())";
                    mysql_query($sql_update, $this->Link_ID_NEW_LOG) or die(mysql_error());
                }
            }
        }
    }

    /*
      @Created By : Arti Grover
      @Created On : 31stMarch,2013
      @Params :--
      @Disc : view for showing listing of cms records
      @Return :--
     */

    private function insertIpAddresses($ipaddress_id = '', $active_theatre = '') {
        $theatre_id_group = array();
        $theatre_label_group = array();
        $IpaddressFinalarray = array();

        if ($ipaddress_id == 0) {
            $carrousels_theatres = "Select theatre_ip,theatre_label from theatre_eppc where theatre_id=$active_theatre";
        } else {
            $carrousels_theatres = "Select theatre_ip,theatre_label from theatre_eppc where id=$ipaddress_id and theatre_id=$active_theatre";
        }

        $output_carrousels_theatres = mysql_query($carrousels_theatres, $this->Link_ID_PREV) or die(mysql_error());

        while ($result_theatres = mysql_fetch_array($output_carrousels_theatres)) {
            if (!empty($result_theatres['theatre_ip']))
                $theatre_id_group[] = $result_theatres['theatre_ip'];
            if (!empty($result_theatres['theatre_label']))
                $theatre_label_group[] = $result_theatres['theatre_label'];
        }
        if (!empty($theatre_id_group))
            $theatreids_after_implode = implode(',', $theatre_id_group);

        if (!empty($theatre_label_group))
            $theatrelabels_after_implode = implode(',', $theatre_label_group);

        $IpaddressFinalarray['theatreids'] = $theatreids_after_implode;
        $IpaddressFinalarray['theatrelabels'] = $theatrelabels_after_implode;
        return $IpaddressFinalarray;
    }

    /*
      @Created By : Arti Grover
      @Created On : 31st March,2013
      @Params :--
      @Disc : view for showing listing of cms records
      @Return :--
     */

    private function insertMultiScreens($multiscreensarray = array(), $active_theatre = '') {

        if ($multiscreensarray != 0) {
            for ($j = 0; $j < count($multiscreensarray); $j++) {
                $carrousels_theatres = "Select label,workspace_name from theatre_multiscreen where id=$multiscreensarray[$j] and theatre_id=$active_theatre";
                $output_carrousels_multiscreens = mysql_query($carrousels_theatres, $this->Link_ID_PREV) or die(mysql_error());
                while ($result_theatres = mysql_fetch_array($output_carrousels_multiscreens)) {
                    if (!empty($result_theatres['workspace_name']))
                        $multi_workspace_group[] = $result_theatres['workspace_name'];
                    if (!empty($result_theatres['label']))
                        $multi_label_group[] = $result_theatres['label'];
                }
            }
            if (!empty($multi_label_group))
                $multilabels_after_implode = implode(',', $multi_label_group);
            if (!empty($multi_workspace_group))
                $multinames_after_implode = implode(',', $multi_workspace_group);

            $MultiScreenFinalarray['multiname'] = $multinames_after_implode;
            $MultiScreenFinalarray['multilabels'] = $multilabels_after_implode;
        }else {
            $MultiScreenFinalarray['multiname'] = 'All';
            $MultiScreenFinalarray['multilabels'] = 'All';
        }
        return $MultiScreenFinalarray;
    }

}


