<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: text/plain');
if(!isLoggedIn()){echo 'UNAUTHORIZED';exit();}
if($_SERVER['REQUEST_METHOD']!=='POST'){echo 'INVALID_METHOD';exit();}
$uid    = getCurrentUserId();
$medId  = (int)($_POST['medicineId'] ?? 0);
$msg    = trim($_POST['message'] ?? '');
if($medId<=0){echo 'INVALID';exit();}
$medModel = new Medicine($db);
$med      = $medModel->findById($medId);
if(!$med){echo 'NOT_FOUND';exit();}
if($med['user_id']==$uid){echo 'OWN_MEDICINE';exit();}
$reqModel = new MedicineRequest($db);
$result   = $reqModel->create(['medicine_id'=>$medId,'requester_id'=>$uid,'owner_id'=>$med['user_id'],'message'=>$msg,'status'=>'Pending']);
echo ($result==='DUPLICATE')?'DUPLICATE':($result?'SUCCESS':'FAILED');
