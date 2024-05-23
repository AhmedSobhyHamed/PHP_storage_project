<?php

require_once '../athentication/session.php';
require_once '../DB/user.php';

session_settings();
session_start();
// if there is any malicious data go to login
if(!session_rotien()) {
    session_remove();
    header('location: ../login.php');
    exit;
}
// if there is coockies
if(isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    $_POST['em'] = $_COOKIE['email'];
    $_POST['pw'] = $_COOKIE['password'];
    setcookie('email',$_POST['em'],time()+(3600*24*7),'/activities_storage');
    setcookie('password',$_POST['pw'],time()+(3600*24*7),'/activities_storage');
}
// if there is a user go to home or wall
router('../wall.php',null);
// if there is no session and there is no malicious data and data is entered correct
if(isset($_POST['em']) && isset($_POST['pw'])) {
    try {
        $user = new User(null,$_POST['em'],$_POST['pw']);
        // session_setup('sanitize',$user->get_connection());
        session_setup();
        // setup cookies
        if(isset($_POST['remmber'])) {
            setcookie('email',$_POST['em'],time()+(3600*24*7),'/activities_storage');
            setcookie('password',$_POST['pw'],time()+(3600*24*7),'/activities_storage');
        }
        header('location: ../wall.php');
        exit;
    }
    catch (Exception $e) {
        setcookie('email','',time()-(3600*24*30),'/activities_storage');
        setcookie('password','',time()-(3600*24*30),'/activities_storage');
        session_remove();
        header('location: ../login.php');
        exit;
    }
}
else {
    header('location: ../login.php');
    exit;
}