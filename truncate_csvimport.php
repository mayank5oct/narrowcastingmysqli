<?php
include('config/database.php');
$db = new Database;
$query="TRUNCATE TABLE `csvimport`";
$con=$db->query($query);
if($con){
    echo "<script>window.close();</script>";
}else{
    echo "Er is iets fout gegaan. U kunt hierover contact opnemen met Pandora Producties (020 8200162).";
}

?>
