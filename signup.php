<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('signup.html');}
$name=trim($_POST['name']??''); $email=trim($_POST['email']??'');
$phone=trim($_POST['phone']??''); $location=trim($_POST['location']??'');
$password=$_POST['password']??'';
if(empty($name)||empty($email)||empty($phone)||empty($password)){$_SESSION['error']='All required fields must be filled.';redirect('signup.html');}
if(!filter_var($email,FILTER_VALIDATE_EMAIL)){$_SESSION['error']='Invalid email address.';redirect('signup.html');}
$userModel=new User($db);
if($userModel->findByEmail($email)){$_SESSION['error']='This email is already registered.';redirect('signup.html');}
$id=$userModel->create(['name'=>$name,'email'=>$email,'phone'=>$phone,'location'=>$location,'password'=>$password]);
if($id){$_SESSION['success']='Account created! Please sign in.';redirect('login.html');}
else{$_SESSION['error']='Registration failed. Please try again.';redirect('signup.html');}
