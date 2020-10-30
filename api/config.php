<?php
ob_start(); 
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once("../php/dbconnection.php");
require_once("_apiAccess.php");
access();
try{
  $msg="";
  $config = [
     "Company_name"=>"شركة النهر",
     "Company_phone"=>"0782222222",
     "wellcome_message"=>"اعلان",
     "Company_email"=>"nahar@nahar.com",
     "Company_logo"=>"img/logos/logo.png",
     "dev_b"=>5000,                 //??? ??????? ?????
     "dev_o"=>10000,                //??? ??????? ????? ?????????
     "weightPrice"=>1000,           //??? ??????? ????? ?????????
  ];
  $sql = "select * from setting";
  $setting = getData($con,$sql);
  foreach($setting as $val){
    $config[$val['control']] =  $val['value'];
  }
}catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
   $msg = "Query Error";
}
ob_end_clean();
echo json_encode(['code'=>200,'message'=>$msg,"success"=>$success,'config'=>$config]);
?>
