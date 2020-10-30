<?php
ob_start(); 
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");

access();
$msg = "";
try{
  $query = "select id,name,phone,email from staff where id = ?";
  $data = getData($con,$query,[$userid]);
  $success="1";
}catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg ="Query Error";

}
ob_end_clean();
echo (json_encode(array('code'=>200,'message'=>$msg,"success"=>$success,"data"=>$data),JSON_PRETTY_PRINT));
?>