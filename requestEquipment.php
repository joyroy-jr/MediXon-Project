<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: text/plain');
if(!isLoggedIn()){echo 'UNAUTHORIZED';exit();}
if($_SERVER['REQUEST_METHOD']!=='POST'){echo 'INVALID_METHOD';exit();}
$uid=(int)$_SESSION['user_id'];
$eqId=(int)($_POST['equipmentId']??0); $message=trim($_POST['message']??'');
if($eqId<=0){echo 'INVALID';exit();}
$eqModel=new Equipment($db); $eq=$eqModel->findById($eqId);
if(!$eq){echo 'NOT_FOUND';exit();}
if((int)$eq['user_id']===$uid){echo 'OWN_EQUIPMENT';exit();}
$reqModel=new Request($db);
$result=$reqModel->create(['equipment_id'=>$eqId,'requester_id'=>$uid,'owner_id'=>$eq['user_id'],'message'=>$message,'status'=>'Pending']);
echo ($result==='DUPLICATE')?'DUPLICATE':($result?'SUCCESS':'FAILED');
