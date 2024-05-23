<?php

require_once 'athentication/session.php';
require_once 'DB/user.php';
$page = require_once 'htmlh/signup/signup.php';

session_settings();
session_start();
// if there is any malicious data go to login
if(!session_rotien()) {
    session_remove();
    header('location: ../login.php');
    exit;
}
// if there is a user go to home or wall
router('wall.php',null);
// if there is no malicious data and there is no uwser
echo $page->get_page();



// $connection = new mysqli($GLOBALS['hnm'],$GLOBALS['unm'],$GLOBALS['pwd'],$GLOBALS['dbs']);

// if(is_user_session()) {
//     try {
//         $theuser = new User($connection,$_SESSION['useremail'],$_SESSION['userpaswd']);
//         header('location: wall.php');
//     }
//     catch(Exception $e) {
//         if($e->getMessage() === 'email not found' || $e->getMessage() === 'password incorrect')
//         session_remove();
//         header('location: login.php');
//     }
// }