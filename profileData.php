<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode(['error'=>'Not logged in']);exit();}
$model=new User($db);
$user=$model->findById(getCurrentUserId());
if($user){unset($user['password']);echo json_encode($user);}
else echo json_encode(['error'=>'Not found']);
