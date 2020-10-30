<?php
ob_start(); 
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require("_apiAccess.php");
access();
require_once("../php/dbconnection.php");
$city = $_REQUEST['city'];
if(empty($city)){
  $city =1;
}
$msg ="";
try{
  $query = "select * from towns where city_id=".$city;
  $data = getData($con,$query);
  $success="1";
} catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg ="Query Error";
}
ob_end_clean();
echo (json_encode(array('code'=>200,'message'=>$msg,"success"=>$success,"data"=>$data,'city'=>$city),JSON_PRETTY_PRINT));
?>