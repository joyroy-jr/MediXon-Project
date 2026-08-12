<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
header('Content-Type: application/json');
if(!isLoggedIn()){echo json_encode([]);exit();}
$uid=(int)getCurrentUserId();
$sql="SELECT DISTINCT 
  CASE WHEN sender_id=$uid THEN receiver_id ELSE sender_id END as id,
  u.name, u.profile_pic
FROM messages m
LEFT JOIN users u ON (CASE WHEN m.sender_id=$uid THEN m.receiver_id ELSE m.sender_id END=u.id)
WHERE sender_id=$uid OR receiver_id=$uid
ORDER BY m.created_at DESC";
$result=$db->query($sql);
$out=[];
if($result) while($row=$result->fetch_assoc()) $out[]=['id'=>$row['id'],'name'=>$row['name'],'profile_pic'=>$row['profile_pic']??null];
echo json_encode($out);
