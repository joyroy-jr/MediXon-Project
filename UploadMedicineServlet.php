<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once 'config.php';
require_once 'models.php';
if(!isLoggedIn()) redirect('login.html');
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('uploadMedicine.html');
$uid         = getCurrentUserId();
$name        = trim($_POST['name']         ?? '');
$generic     = trim($_POST['generic_name'] ?? '');
$category    = trim($_POST['category']     ?? '');
$dosage_form = trim($_POST['dosage_form']  ?? 'Tablet');
$strength    = trim($_POST['strength']     ?? '');
$manufacturer= trim($_POST['manufacturer'] ?? '');
$quantity    = max(1,(int)($_POST['quantity'] ?? 1));
$unit        = trim($_POST['unit']         ?? 'pcs');
$condition   = trim($_POST['condition_type']?? 'Sealed');
$mode        = trim($_POST['mode']         ?? 'Donate');
$price       = !empty($_POST['price']) ? (float)$_POST['price'] : null;
$expiry      = trim($_POST['expiry_date']  ?? '');
$location    = trim($_POST['location']     ?? '');
$description = trim($_POST['description'] ?? '');
if(empty($name)||empty($category)||empty($location)){
  $_SESSION['error']='Please fill all required fields.';
  redirect('uploadMedicine.html');
}
$photoPath=null;
if(isset($_FILES['photo'])&&$_FILES['photo']['error']===0){
  $dir='uploads/medicine/';
  if(!file_exists($dir)) mkdir($dir,0777,true);
  $ext=strtolower(pathinfo($_FILES['photo']['name'],PATHINFO_EXTENSION));
  $allowed=['jpg','jpeg','png','gif','webp'];
  if(in_array($ext,$allowed)){
    $fname=uniqid().'_'.time().'.'.$ext;
    if(move_uploaded_file($_FILES['photo']['tmp_name'],$dir.$fname)) $photoPath=$dir.$fname;
  }
}
$data=['user_id'=>$uid,'name'=>$name,'generic_name'=>$generic,'category'=>$category,
  'dosage_form'=>$dosage_form,'strength'=>$strength,'manufacturer'=>$manufacturer,
  'quantity'=>$quantity,'unit'=>$unit,'condition_type'=>$condition,'mode'=>$mode,
  'location'=>$location,'description'=>$description];
if($expiry)  $data['expiry_date']=$expiry;
if($price!==null) $data['price']=$price;
if($photoPath) $data['photo']=$photoPath;
$model=new Medicine($db);
$id=$model->create($data);
if($id){$_SESSION['success']='Medicine uploaded successfully!'; redirect('dashboard.html');}
else   {$_SESSION['error']='Upload failed. Please try again.'; redirect('uploadMedicine.html');}
