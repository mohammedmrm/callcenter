<?php
ob_start();
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");

$success = 0;
$error = [];
$user_id = $userid;
if(empty($limit)){
 $limit = 10;
}
if(empty($page)){
 $page = 1;
}

try {
    $count = "select count(*) as count from orders
            inner join (
             select max(id) as msg_id ,max(order_id) as order_id from message
             GROUP by message.order_id

            ) a on a.order_id = orders.id

            inner join message on a.msg_id = message.id
            where orders.client_id = ?   and orders.confirm = 1 and invoice_id = 0";

    $sql = "select message.client_seen,orders.order_no,orders.id,message.id as msg_id,message.message as message,message.date from orders
            inner join (
             select max(id) as msg_id ,max(order_id) as order_id from message
             GROUP by message.order_id

            ) a on a.order_id = orders.id

            inner join message on a.msg_id = message.id
            where orders.client_id = ? and orders.confirm = 1  and invoice_id = 0
            order by message.date";
    $lim = " limit ".(($page-1) * $limit).",".$limit;
    $sql .= $lim;
    $count = getData($con,$count,[$userid]);
    $pages= ceil($count[0]['count'] / $limit);
    $data = getData($con,$sql,[$userid]);
    $success="1";
}catch(PDOException $ex) {
    $data=["error"=>$ex];
    $success="0";
    $msg ="Query Error";
}
ob_end_clean();
echo json_encode(['code'=>200,'message'=>$msg,'success'=>$success,"data"=>$data,"page"=>$page,'pages'=>$pages,'count'=>$count[0]["count"]]);
?>