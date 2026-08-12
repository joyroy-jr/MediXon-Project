<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode(['error'=>'Not logged in']);exit();}
if($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['error'=>'Invalid method']);exit();}
$uid=(int)getCurrentUserId();
$receiverId=(int)($_POST['receiver']??0);
$text=trim($_POST['text']??'');
if($receiverId<=0){echo json_encode(['error'=>'Invalid receiver']);exit();}
$attachPath=null; $attachType=null;
if(isset($_FILES['attachment'])&&$_FILES['attachment']['error']===0){
  $dir='uploads/chat/';
  if(!file_exists($dir)) mkdir($dir,0777,true);
  $ext=pathinfo($_FILES['attachment']['name'],PATHINFO_EXTENSION);
  $fname=uniqid().'_'.time().'.'.$ext;
  $attachPath=$dir.$fname;
  move_uploaded_file($_FILES['attachment']['tmp_name'],$attachPath);
  $attachType=$_FILES['attachment']['type'];
}
if(empty($text)&&!$attachPath){echo json_encode(['error'=>'Empty message']);exit();}
$data=['sender_id'=>$uid,'receiver_id'=>$receiverId];
if($text) $data['message']=$text;
if($attachPath){$data['attachment']=$attachPath;$data['attach_type']=$attachType;}
$model=new Message($db);
$id=$model->create($data);
echo json_encode($id?['success'=>true,'id'=>$id]:['error'=>'Failed to send']);
