<?php
//error_reporting(0);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
     $link = "https";
}else{
    $link = "http";
}
// Here append the common URL characters.
$link .= "://";

// Append the host(domain name, ip) to the URL.
$link .= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
$link .= $_SERVER['REQUEST_URI'];

function access($username,$password,$con){
  $sql = "select * from staff where phone = ? and status=1";
  $result = getData($con,$sql,[$username]);
  if(count($result) != 1 || !password_verify($password,$result[0]['password']) ){
    $login['msg'] = "اسم المستخدم او كلمة المرور غير صحيحة";
    $login['code'] = 300;
  }else{
    $login['msg'] = 1;
    $login['code'] = 200;
    $login['id'] = $result[0]['id'];
  }
  return $login;
}
?>