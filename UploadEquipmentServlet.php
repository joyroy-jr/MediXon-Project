<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
if(!isLoggedIn()) redirect('login.html');
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('uploadEquipment.html');
$uid=$_SESSION['user_id'];
$name=trim($_POST['name']??''); $category=trim($_POST['category']??'');
$company=trim($_POST['company']??''); $quantity=max(1,(int)($_POST['quantity']??1));
$expiry=$_POST['expiry']??null; $condition=$_POST['condition']??'New';
$mode=$_POST['mode']??'Donate';
$price=!empty($_POST['price'])?(float)$_POST['price']:null;
$rent=!empty($_POST['rent_per_day'])?(float)$_POST['rent_per_day']:null;
$location=trim($_POST['location']??''); $description=trim($_POST['description']??'');
if(empty($name)||empty($category)||empty($location)){$_SESSION['error']='Please fill all required fields.';redirect('uploadEquipment.html');}
$photoPath=null;
if(isset($_FILES['photo'])&&$_FILES['photo']['error']===0){
  $dir='uploads/equipment/';
  if(!file_exists($dir)) mkdir($dir,0777,true);
  $ext=strtolower(pathinfo($_FILES['photo']['name'],PATHINFO_EXTENSION));
  if(in_array($ext,['jpg','jpeg','png','gif','webp'])){
    $fname=uniqid().'_'.time().'.'.$ext;
    if(move_uploaded_file($_FILES['photo']['tmp_name'],$dir.$fname)) $photoPath=$dir.$fname;
  }
}
$data=['user_id'=>$uid,'name'=>$name,'category'=>$category,'company'=>$company,
  'quantity'=>$quantity,'condition_type'=>$condition,'mode'=>$mode,
  'location'=>$location,'description'=>$description];
if($expiry) $data['expiry']=$expiry;
if($price!==null) $data['price']=$price;
if($rent!==null)  $data['rent_per_day']=$rent;
if($photoPath) $data['photo']=$photoPath;
$model=new Equipment($db);
$id=$model->create($data);
if($id){$_SESSION['success']='Equipment uploaded successfully!';redirect('dashboard.html');}
else{$_SESSION['error']='Upload failed. Please try again.';redirect('uploadEquipment.html');}
