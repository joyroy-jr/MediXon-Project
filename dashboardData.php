<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode(['error'=>'Not logged in']);exit();}
$uid = getCurrentUserId();

// Equipment filters
$filters = [
  'search'   => $_GET['search']    ?? '',
  'category' => $_GET['category']  ?? '',
  'condition'=> $_GET['condition'] ?? '',
  'mode'     => $_GET['mode']      ?? ''
];

// Medicine filters
$medFilters = [
  'search'     => $_GET['medSearch']   ?? '',
  'category'   => $_GET['medCategory'] ?? '',
  'dosage_form'=> $_GET['medForm']     ?? '',
  'mode'       => $_GET['medMode']     ?? ''
];

$eqModel  = new Equipment($db);
$medModel = new Medicine($db);
$reqModel = new Request($db);
$msgModel = new Message($db);
$mrModel  = new MedicineRequest($db);

$hasEqFilter  = array_filter($filters);
$hasMedFilter = array_filter($medFilters);

$equipment = $hasEqFilter  ? $eqModel->search($filters)     : $eqModel->getAll();
$medicines = $hasMedFilter ? $medModel->search($medFilters) : $medModel->getAll();

echo json_encode([
  'equip'    => count($eqModel->getByUserId($uid)),
  'meds'     => count($medModel->getByUserId($uid)),
  'requests' => count($reqModel->getByOwnerId($uid)) + count($mrModel->getByOwnerId($uid)),
  'messages' => count($msgModel->getChatUsers($uid)),
  'equipment'=> $equipment,
  'medicines'=> $medicines
]);
