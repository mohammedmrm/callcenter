<?php
ob_start(); 
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");
require_once("../php/_crpt.php");

use Violin\Violin;
require_once('../validator/autoload.php');
$v = new Violin;
$success = 0;
$error = [];
$id        = $userid;
$token = $_REQUEST['notify_token'];
$msg = "";

$v->addRuleMessage('isPhoneNumber', ' رقم هاتف غير صحيح  ');



$v->validate([
    'id'      => [$id,      'required|int'],
    'token'    => [$token,    'required|min(3)|max(200)'],
]);

if($v->passes()) {
try{
   $sql = 'update clients set token = ? where id=?';
   $result = setData($con,$sql,[$token,$id]);

  if($result > 0){
    $success = 1;
  }
}catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg ="Query Error";

}
}else{
  $error = [
           'id'=> implode($v->errors()->get('id')),
           'notify_token'=> implode($v->errors()->get('token')),
           ];
  $msg ="Request Error";
}
ob_end_clean();
echo json_encode(['code'=>200,'message'=>$msg,'success'=>$success, 'error'=>$error],JSON_PRETTY_PRINT);
?>