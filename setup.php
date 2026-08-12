<?php
require_once 'config.php';
require_once 'models.php';
$email='admin@medixon.com'; $password='Admin@12345';
$userModel=new User($db);
$existing=$userModel->findByEmail($email);
if($existing){
  $hash=password_hash($password,PASSWORD_BCRYPT);
  $h=$db->escape($hash);
  $db->executeQuery("UPDATE users SET password='$h',role='admin' WHERE email='$email'");
  echo "<h2 style='color:green;font-family:sans-serif'>✅ Admin password updated!</h2>";
} else {
  $hash=$db->escape(password_hash($password,PASSWORD_BCRYPT));
  $db->executeQuery("INSERT INTO users (name,email,phone,password,role) VALUES ('Admin','$email','01700000000','$hash','admin')");
  echo "<h2 style='color:green;font-family:sans-serif'>✅ Admin account created!</h2>";
}
echo "<p style='font-family:sans-serif'>Email: <b>$email</b><br>Password: <b>$password</b><br><a href='admin-login.html'>Go to Admin Login</a></p>";
echo "<p style='color:red;font-family:sans-serif'><b>⚠️ DELETE this file after use!</b></p>";
