<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: login.html');exit();}
$email=trim($_POST['email']??''); $password=trim($_POST['password']??'');
if(empty($email)||empty($password)){header('Location: login.html');exit();}
$userModel=new User($db);
$user=$userModel->authenticate($email,$password);
if($user){
  $_SESSION['user_id']   =$user['id'];
  $_SESSION['user_name'] =$user['name'];
  $_SESSION['user_email']=$user['email'];
  $_SESSION['role']      =$user['role'];
  header('Location: '.($user['role']==='admin'?'admin.php':'dashboard.html'));exit();
}
$_SESSION['error']='Invalid email or password.';
header('Location: login.html');exit();
