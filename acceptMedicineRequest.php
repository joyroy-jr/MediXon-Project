<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode(['error'=>'Unauthorized']);exit();}
if($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['error'=>'Invalid method']);exit();}
$reqId = (int)($_POST['requestId'] ?? 0);
if($reqId<=0){echo json_encode(['error'=>'Invalid ID']);exit();}
$reqModel = new MedicineRequest($db);
$result   = $reqModel->update($reqId,['status'=>'Accepted']);
if($result){
  $req = $reqModel->findById($reqId);
  if($req){
    $msgModel = new Message($db);
    $msgModel->create([
      'sender_id'  =>$req['owner_id'],
      'receiver_id'=>$req['requester_id'],
      'message'    =>"Your request for medicine '{$req['medicine_name']}' has been accepted! You can now chat to arrange the exchange."
    ]);
  }
  echo json_encode(['success'=>true]);
} else echo json_encode(['error'=>'Failed to update request']);
