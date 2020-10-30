<?php
ob_start();
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");
use Violin\Violin;
require_once('../validator/autoload.php');
$v = new Violin;


$success = 0;
$error = [];
$order_id = $_REQUEST['orderid'];
$last = $_REQUEST['last'];
$result = "";
$msg = "";
if (empty($last)){
 $last = 0;
}
$v->addRuleMessages([
    'required' => 'الحقل مطلوب',
    'int'      => 'فقط الارقام مسموع بها',
]);

$v->validate([
    'orderid'=> [$order_id,'required|int'],
]);

if($v->passes()) {
try{
  $sql = 'select message.*, date_format(message.date," %y %b %d %h:%i %p") as date, clients.name as client_name,
          staff.name as staff_name,
          role.name as role_name
          from message
          left join clients on from_id = clients.id
          left join staff on from_id = staff.id
          left join role on role.id = staff.role_id
          where order_id = ? and message.id > ?  order by message.date DESC
          ';
  $result = getData($con,$sql,[$order_id,$last]);
  if(count($result) > 0){
    $success = 1;
    $sql = "update message set client_seen = 1 where order_id=?";
    setData($con,$sql,[$order_id]);
  }
} catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg ="Query Error";
}
}else{
  $error = [
           'orderid'=>implode($v->errors()->get('orderid')),
           ];
  $msg = "Request Error";
}
ob_end_clean();
echo json_encode(['code'=>200,'message'=>$msg,'last'=>$last,'success'=>$success,"data"=>$result,'error'=>$error],JSON_PRETTY_PRINT);
?>