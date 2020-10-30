<?php
ob_start(); 
session_start();
error_reporting(0);
header("Access-Control-Allow-Origin: *");  
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
$msg="";
require_once("../php/dbconnection.php");
try{
  $query = "select *, status as name from order_status";
  $data = getData($con,$query);
  $success="1";
} catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg = "Query Error";
}
ob_end_clean();
echo (json_encode(array('code'=>200,'message'=>$msg,"success"=>$success,"data"=>$data),JSON_PRETTY_PRINT));
?>