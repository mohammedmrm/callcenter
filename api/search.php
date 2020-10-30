<?php
ob_start();
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");
$search = $_REQUEST['search'];
$start = trim($_REQUEST['start']);
$end = trim($_REQUEST['end']);
$city = trim($_REQUEST['city']);
$status = trim($_REQUEST['status']);
$limit = trim($_REQUEST['limit']);
$page = trim($_REQUEST['page']);
$orders = 0;
if(empty($limit)){
 $limit = 10;
}
if(empty($page)){
 $page = 1;
}

if(empty($end)) {
  $end = date('Y-m-d h:i:s', strtotime($end. ' + 1 day'));
}else{
   $end .=" 23:59:59";
}
$start .=" 00:00:00";
try{
  $count = "select count(*) as count from orders
            left join (
              select max(id) as last_id,order_id from tracking group by order_id
            ) a on a.order_id = orders.id
            left join tracking on a.last_id = tracking.id";
  $query = "select orders.*,
            clients.name as client_name,clients.phone as client_phone,
            cites.name as city,towns.name as town,branches.name as branch_name,
            if(staff.phone is null,'07721397505',staff.phone) as driver_phone,
            stores.name as store_name,tracking.note as t_note ,order_status.status as status_name
            from orders left join
            clients on clients.id = orders.client_id
            left join cites on  cites.id = orders.to_city
            left join towns on  towns.id = orders.to_town
            left join staff on  orders.driver_id = staff.id
            left join stores on  stores.id = orders.store_id
            left join branches on  branches.id = orders.to_branch
            left join order_status on  order_status.id = orders.order_status_id
            left join (
              select max(id) as last_id,order_id from tracking group by order_id
            ) a on a.order_id = orders.id
            left join tracking on a.last_id = tracking.id
            ";
  $where = "where";
  $filter = "orders.driver_id =".$userid." and (orders.confirm=1) ";
  if(!empty($search)){
   $filter .= " and (order_no like '%".$search."%'
                     or customer_name like  '%".$search."%'
                     or customer_phone like '%".$search."%'
                     or tracking.note like  '%".$search."%'
                     )
                    ";
  }
  function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
  if(validateDate($start) && validateDate($end)){
      $filter .= " and date between '".$start."' AND '".$end."'";
     }
  if($city > 0){
   $filter .= " and to_city =".$city;
  }
  if($status > 0){
   $filter .= " and orders.order_status_id =".$status;
  }
  if($filter != ""){
    $filter = preg_replace('/^ and/', '', $filter);
    $filter = $where." ".$filter;
    $count .= " ".$filter;
    $query .= " ".$filter;
  }
  if($page != 0){
    $page = $page - 1;
  }
  $query .= " limit ".($page * $limit).",".$limit;
  $data = getData($con,$query);
  $ps = getData($con,$count);
  $orders = $ps[0]['count'];
  $pages= ceil($ps[0]['count']/$limit);
  $success="1";
} catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg ="Query Error";
}
ob_end_clean();
echo (json_encode(['code'=>200,'message'=>$msg,'orders'=>$orders,"success"=>$success,"data"=>$data,'pages'=>$pages,'nextPage'=>$page+2],JSON_PRETTY_PRINT));
?>