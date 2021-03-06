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
  $query = "select name as label, id as value from cites where cites.id in (select city_id from callcenter_cities where callcenter_id=".$userid.")";
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