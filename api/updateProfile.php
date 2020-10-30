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
$name      = $_REQUEST['name'];
$email     = $_REQUEST['email'];
$phone     = $_REQUEST['phone'];
$password  = $_REQUEST['password'];
$msg = "";

$v->addRuleMessage('isPhoneNumber', ' رقم هاتف غير صحيح  ');

$v->addRule('isPhoneNumber', function($value, $input, $args) {
    return   (bool) preg_match("/^[0-9]{10,15}$/",$value);
});
$v->addRuleMessage('unique', 'القيمة المدخلة مستخدمة بالفعل ');

$v->addRule('unique', function($value, $input, $args) {
    $exists = getData($GLOBALS['con'],"SELECT * FROM clients WHERE phone ='".$value."' and id !='".$GLOBALS['id']."'");
    return  ! (bool) count($exists);
});
$v->addRuleMessages([
    'required' => 'الحقل مطلوب',
    'int'      => 'فقط الارقام مسموع بها',
    'regex'      => 'فقط الارقام مسموح بها',
    'min'      => 'قصير جداً',
    'max'      => 'قيمة كبيرة جداً',
    'email'      => 'البريد الالكتروني غيز صحيح',
]);

$v->validate([
    'id'      => [$id,      'required|int'],
    'name'    => [$name,    'required|min(3)|max(200)'],
    'email'   => [$email,   'email'],
    'phone'   => [$phone,   "required|unique|isPhoneNumber"],
    'password'=> [$password,"min(6)|max(20)"],
]);

if($v->passes()) {
try{
   if(empty($password)){
   $sql = 'update staff set name = ?, email=?,phone=? where id=?';
   $result = setData($con,$sql,[$name,$email,$phone,$id]);
   }else{
   $password= hashPass($password);
   $sql = 'update staff set password=?,name = ?, email=?,phone=? where id=?';
   $result = setData($con,$sql,[$password,$name,$email,$phone,$id]);
   }
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
           'name'=> implode($v->errors()->get('name')),
           'email'=>implode($v->errors()->get('email')),
           'phone'=>implode($v->errors()->get('phone')),
           'password'=>implode($v->errors()->get('password'))
           ];
  $msg ="Request Error";
}
ob_end_clean();
echo json_encode(['code'=>200,'message'=>$msg,'success'=>$success, 'error'=>$error],JSON_PRETTY_PRINT);
?>