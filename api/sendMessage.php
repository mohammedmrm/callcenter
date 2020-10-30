<?php
ob_start();
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");
require_once("_sendNoti.php");


use Violin\Violin;
require_once('../validator/autoload.php');
$v = new Violin;


$success = 0;
$error = [];
$message  = $_REQUEST['message'];
$order_id = $_REQUEST['orderid'];


$v->addRuleMessages([
    'required' => 'الحقل مطلوب',
    'int'      => 'فقط الارقام مسموع بها',
    'min'      => 'قصير جداً',
    'max'      => 'مسموح ب {value} رمز كحد اعلى ',
]);

$v->validate([
    'message'    => [$message,    'required|min(1)|max(500)'],
    'order_id'   => [$order_id,    'required|int'],
]);

if($v->passes()) {
  try{
  $sql = 'insert into message (message,order_id,from_id,is_client) values (?,?,?,?)';
  $result = setData($con,$sql,[$message,$order_id,$userid,1]);
  if($result > 0){
    $sql = "select staff.token as s_token, clients.token as c_token from orders inner join staff
            on
            staff.id = orders.manager_id
            or
            staff.id = orders.driver_id
            inner join clients on clients.id = orders.client_id
            where orders.id = ?";
    $res =getData($con,$sql,[$order_id]);
    $f= sendNotification([$res[0]['s_token'],$res[0]['c_token']],[$order_id],'رساله جديد ',$message,"../orderDetails.php?o=".$order_id);
    $success = 1;
    }
}catch(PDOException $ex) {
       $data=["error"=>$ex];
       $success="0";
       $msg ="Query Error";
}
}else{
  $error = [
           'message'=> implode($v->errors()->get('message')),
           'orderid'=>implode($v->errors()->get('order_id')),
           ];
  $msg ="Request Error";
}
ob_end_clean();
echo json_encode(['code'=>200,'message'=>$msg,'success'=>$success,'error'=>$error,$f]);
?>