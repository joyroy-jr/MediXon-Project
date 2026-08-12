<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('feedback.html');}
$name=trim($_POST['name']??'Anonymous'); $email=trim($_POST['email']??'');
$rating=(int)($_POST['rating']??5); $message=trim($_POST['message']??'');
if(empty($message)){$_SESSION['error']='Message is required.';redirect('feedback.html');}
$data=['name'=>$name?:'Anonymous','rating'=>max(1,min(5,$rating)),'message'=>$message];
if($email) $data['email']=$email;
if(isLoggedIn()) $data['user_id']=getCurrentUserId();
$model=new Feedback($db);
if($model->create($data)){$_SESSION['success']='Thank you for your feedback!';redirect('feedback.html');}
else{$_SESSION['error']='Submit failed.';redirect('feedback.html');}
