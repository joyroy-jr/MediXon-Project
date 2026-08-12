<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode([]);exit();}
$model = new MedicineRequest($db);
echo json_encode($model->getByOwnerId(getCurrentUserId()));
