<?php
header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    /*$file = "master_bins.txt";
    $fh = fopen($file, 'r') or die("can't open file");
    $content = fread($fh,filesize($file));
    $content_array = explode(",",$content);*/
    
     // exec('sync_files_tmp', $output);
      //shell_exec('sync_files_tmp');
      //echo "<pre>"; print_r($output); echo "</pre>";
    //echo "<hr/>".$returnval;
      //echo "<pre>"; print_r($output); echo "</pre>";
    /*foreach($content_array as $key=>$vals){
        $command="";
        echo $command = "rsync -avz /var/www/html/narrowcasting/$vals/ /Volumes/files/$vals";
        exec($command ,$output);
        echo "<pre>"; print_r($output); echo "</pre>";
    }*/
   // $command = 'rsync -av --files-from=/path/to/files.txt / /Volumes/files/';
    //shell_exec($command);
    //echo "<pre>"; print_r($output); echo "</pre>";
    //fclose($fh);
    $command = "rsync -avx --progress /var/www/html/narrowcasting/images /Volumes/files/test";
    exec($command ,$output);
    
    echo "<pre>"; print_r($command); echo "</pre>";
    
?>