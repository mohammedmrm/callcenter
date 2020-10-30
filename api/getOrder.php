<?php
ob_start();
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("_apiAccess.php");
access();
error_reporting(0);
require_once("../php/dbconnection.php");
require_once("../config.php");
$id = $_REQUEST['orderid'];
$Nid = $_REQUEST['notification_id'];
$msg="";
if($id > 0){
try{
  $query = "select orders.*,
           if(order_status_id = 9,
               0,
               if(to_city = 1,
                     if(client_dev_price.price is null,(".$config['dev_b']." - discount),(client_dev_price.price - discount)),
                     if(client_dev_price.price is null,(".$config['dev_o']." - discount),(client_dev_price.price - discount))
                )
            )as dev_price,
            new_price -
              (
                 if(order_status_id = 9,
                     0,
                     if(to_city = 1,
                           if(client_dev_price.price is null,(".$config['dev_b']." - discount),(client_dev_price.price - discount)),
                           if(client_dev_price.price is null,(".$config['dev_o']." - discount),(client_dev_price.price - discount))
                      )
                  )
               ) as client_price,
            clients.name as client_name,clients.phone as client_phone,
            cites.name as city,towns.name as town,branches.name as branch_name,
            order_status.status as order_status,stores.name as store_name,
            if(staff.name is null,'غير معروف',staff.name) as driver_name,
            if(staff.phone is null,'غير معروف',staff.phone) as driver_phone
            from orders left join
            clients on clients.id = orders.client_id
            left join cites on  cites.id = orders.to_city
            left join towns on  towns.id = orders.to_town
            left join stores on  stores.id = orders.store_id
            left join branches on  branches.id = orders.to_branch
            left join order_status on  order_status.id = orders.order_status_id
            left JOIN client_dev_price on client_dev_price.client_id = orders.client_id AND client_dev_price.city_id = orders.to_city
            left JOIN staff on staff.id = orders.driver_id
            where orders.id = ? and orders.confirm <> 0 and orders.driver_id =?";
  $data = getData($con,$query,[$id,$userid]);
  $success="1";
  if(count($data) > 1){
     $success="2";
  }else{
      $query = "select tracking.*,status,DATE_FORMAT(date,'%Y-%m-%d') as date,DATE_FORMAT(date,'%H:%i') as hour from tracking
      left join order_status on tracking.order_status_id = order_status.id
      where order_id=".$id." order by tracking.id DESC";
      $data[0]['tracking'] = getData($con,$query);
  }
  if($Nid > 0){
    $sql = "update notification set client_seen = 1 where id=? and order_id=?";
    setData($con,$sql,[$Nid,$id]);
  }
} catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg ="Query Error";

}
}else{
   $data="orderid is required";
   $success="0";
   $msg ="Query Error";
}
ob_end_clean();
echo json_encode(array('code'=>200,'message'=>$msg,"success"=>$success,"data"=>$data),JSON_PRETTY_PRINT);
?>