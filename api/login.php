<?php
ob_start();
session_start();
header("Access-Control-Allow-Origin: *");
header('Content-type:application/json');
error_reporting(0);
session_start();
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
//die(json_encode(['message'=>"Access Deny"])); 
if(empty($username) || empty($password)){
  $msg = "جميع الحقول مطلوبة";
  $code = 300;
}else{
  require_once("../php/dbconnection.php");
  $sql = "select * from staff where phone = ?  and role_id=9";
  $result = getData($con,$sql,[$username]);
  if(count($result) != 1 || !password_verify($password,$result[0]['password']) ){
    $msg = "اسم المستخدم او كلمة المرور غير صحيحة";
    $code = 300;
  }else{
    $msg = 1;
    $code = 200;
    $data['login']=1;
    $data['phone']=$result[0]['phone'];
    $data['userid']=$result[0]['id'];
    $data['name']=$result[0]['name'];
    $token=$result[0]['api_token'];
    if($result[0]['api_token'] == "" || $result[0]['api_token'] == 0 || empty($result[0]['api_token']) || $result[0]['api_token'] = null){
        $token = uniqid().uniqid().uniqid().uniqid();
        $sql = "update staff set api_token = ? where id=?";
        $res1 = setData($con,$sql,[$token,$result[0]['id']]);
        if(!$res1){
          $token = "";
        }
    }

  }
}
ob_end_clean();
echo json_encode(['data'=>$data,'token'=>$token,'code'=>$code,'message'=>$msg]);
?>