<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()||!isAdmin()){echo json_encode(['error'=>'Unauthorized']);exit();}
$section = $_GET['section'] ?? 'overview';
switch($section){
  case 'overview':
    $u=new User($db); $e=new Equipment($db); $r=new Request($db);
    $f=new Feedback($db); $m=new Medicine($db); $mr=new MedicineRequest($db);
    echo json_encode([
      'users'=>$u->count(),'equip'=>$e->count(),'meds'=>$m->count(),
      'reqs'=>$r->count(),'med_reqs'=>$mr->count(),'fbs'=>$f->count()
    ]);
    break;
  case 'users':       $obj=new User($db);          echo json_encode($obj->getAll()); break;
  case 'equipment':   $obj=new Equipment($db);      echo json_encode($obj->getAll()); break;
  case 'eq_requests': $obj=new Request($db);        echo json_encode($obj->getAll()); break;
  case 'medicines':   $obj=new Medicine($db);       echo json_encode($obj->getAll()); break;
  case 'med_requests':$obj=new MedicineRequest($db);echo json_encode($obj->getAll()); break;
  case 'messages':    $obj=new Message($db);        echo json_encode($obj->getAll()); break;
  case 'feedback':    $obj=new Feedback($db);       echo json_encode($obj->getAll()); break;
  default: echo json_encode([]);
}
