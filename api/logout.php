<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../athentication/session.php';
session_settings();
session_start();
session_remove();
setcookie('email','',time()-(3600*24*30),'/activities_storage');
setcookie('password','',time()-(3600*24*30),'/activities_storage');
header('location: ../login.php');
echo 'please wait ....';
exit;