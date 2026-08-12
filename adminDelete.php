<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()||!isAdmin()){echo json_encode(['error'=>'Unauthorized']);exit();}
if($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['error'=>'Invalid method']);exit();}
$type = $_GET['type'] ?? '';
$id   = (int)($_GET['id'] ?? 0);
if($id<=0){echo json_encode(['error'=>'Invalid ID']);exit();}
$map = ['user'=>'User','equipment'=>'Equipment','medicine'=>'Medicine','feedback'=>'Feedback'];
if(!isset($map[$type])){echo json_encode(['error'=>'Invalid type']);exit();}
$cls   = $map[$type];
$model = new $cls($db);
$ok    = $model->delete($id);
echo json_encode($ok ? ['success'=>true] : ['error'=>'Delete failed']);
