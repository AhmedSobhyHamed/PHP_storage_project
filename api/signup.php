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
// if there is a user go to home or wall
router('../wall.php',null);
// if there is no session and there is no malicious data and data is entered correct
if(isset($_POST['fn']) && isset($_POST['ln']) && isset($_POST['em']) && isset($_POST['pw'])) {
    try {
        if(isset($_POST['mg'])) {
            $user = new User(null,$_POST['em'],$_POST['pw'],$_POST['fn'] . ' ' . $_POST['ln'],$_POST['mg']);
        }
        else {
            $user = new User(null,$_POST['em'],$_POST['pw'],$_POST['fn'] . ' ' . $_POST['ln']);
        }
        // session_setup('sanitize',$user->get_connection());
        session_setup();
        header('location: ../wall.php');
        exit;
    }
    catch (Exception $e) {
        header('location: ../signup.php');
        exit;
    }
}
else {
    header('location: ../signup.php');
    exit;
}