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
$limit = trim($_REQUEST['limit']);
$page = trim($_REQUEST['page']);
if(empty($limit)){
 $limit = 10;
}
if(empty($page)){
 $page = 1;
}

$msg = "";

try {
    $sql = 'select count(*) as unseen from notification where for_client = 0 and staff_id = ? and driver_seen=0';
    $res = getData($con,$sql,[$user_id]);
    $unseen = $res[0]['unseen'];
    $sql2 = 'select count(*) as count from notification
            where for_client = 0 and notification.staff_id = ?';
    $sql = 'select notification.*,orders.order_no from notification inner join orders on orders.id = notification.order_id
            where for_client = 0 and notification.staff_id = ?
            order by date DESC';
    if($page != 0){
      $page = $page - 1;
    }
    $sql .= " limit ".($page * $limit).",".$limit;

    $data = getData($con,$sql,[$user_id]);
    $count = getData($con,$sql2,[$user_id]);
    $count = $count['0']['count'];
    if(!$count){$count=1;}
    $maxPage = ceil(($count/$limit));
    $success = 1;

}catch(PDOException $ex) {
    $data=["error"=>$ex];;
    $success="0";
    $msg ="Query Error";
}
ob_end_clean();
echo json_encode(['code'=>200,'message'=>$msg,'success'=>$success,"data"=>$data,'unseen'=>$unseen,'count'=>$count,'nextPage'=>($page+2),"maxPage"=>$maxPage]);
?>