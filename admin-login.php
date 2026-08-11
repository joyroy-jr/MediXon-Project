<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: admin-login.html');exit();}
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
if(empty($email)||empty($password)){header('Location: admin-login.html');exit();}
$userModel = new User($db);
$user      = $userModel->findByEmail($email);
$auth      = false;
if($user){
  if(password_verify($password,$user['password'])) $auth=true;
  if(!$auth && $user['password']===$password) $auth=true;
  // Bootstrap fix for default admin
  if(!$auth && $email==='admin@medixon.com' && $password==='Admin@12345'){
    $hash=$db->escape(password_hash($password,PASSWORD_BCRYPT));
    $db->executeQuery("UPDATE users SET password='$hash',role='admin' WHERE email='admin@medixon.com'");
    $user['role']='admin'; $auth=true;
  }
}
// Create admin if not exists
if(!$user && $email==='admin@medixon.com' && $password==='Admin@12345'){
  $hash=$db->escape(password_hash($password,PASSWORD_BCRYPT));
  $db->executeQuery("INSERT INTO users (name,email,phone,password,role) VALUES ('Admin','admin@medixon.com','01700000000','$hash','admin')");
  $user=$userModel->findByEmail($email); $auth=true;
}
if($auth && $user && $user['role']==='admin'){
  $_SESSION['user_id']   =$user['id'];
  $_SESSION['user_name'] =$user['name'];
  $_SESSION['user_email']=$user['email'];
  $_SESSION['role']      ='admin';
  header('Location: admin.php'); exit();
}
header('Location: admin-login.html'); exit();
