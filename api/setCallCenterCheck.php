<?php
ob_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");
$id= $_REQUEST['orderid'];
$success = 0;
$msg="";
require_once("dbconnection.php");
use Violin\Violin;
require_once('../validator/autoload.php');
$v = new Violin;

$v->validate([
    'orderid'    => [$id,'required|int']
    ]);

if($v->passes()){
  try{
        $sql = "update orders set callcenter_id =? where id = ?";
        $result = setData($con,$sql,[$userid,$id]);
        $success = 1;
  } catch(PDOException $ex) {
     $data=["error"=>$ex];
     $success="0";
     $msg = "Query Error";
  }
}else{
  $msg = "فشل التاكيد";
  $success = 0;
}
$code = 200;
ob_end_clean();
echo json_encode(['code'=>$code,'message'=>$msg,'success'=>$success]);
?>