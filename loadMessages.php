<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode([]);exit();}
$uid=(int)getCurrentUserId();
$otherId=(int)($_GET['userId']??0);
if($otherId<=0){echo json_encode([]);exit();}
$model=new Message($db);
$msgs=$model->getConversation($uid,$otherId);
$out=[];
foreach($msgs as $m){
  $out[]=['id'=>$m['id'],'text'=>$m['message'],'me'=>((int)$m['sender_id']===$uid),
    'attachment'=>$m['attachment']??null,'attachType'=>$m['attach_type']??null,'timestamp'=>$m['created_at']];
}
echo json_encode($out);
