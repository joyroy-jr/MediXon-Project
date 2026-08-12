<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode(['error'=>'Not logged in']);exit();}
$filters=['search'=>$_GET['search']??'','category'=>$_GET['category']??'','condition'=>$_GET['condition']??'','mode'=>$_GET['mode']??''];
$model=new Equipment($db);
echo json_encode($model->search($filters));
