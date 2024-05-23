<?php

require_once 'athentication/session.php';
require_once 'DB/user.php';
$page = require_once 'htmlh/profile/profile.php';

session_settings();
session_start();
// if there is any malicious data or no session user go to login
router(null,'login.php');
// if there is session
try {
    $user = new User(null,$_SESSION['useremail'],$_SESSION['userpaswd']);
    // echo $user->image();
    // echo $user->name();
    $page->add_data('username',$user->name());
    $page->add_data('userimg',$user->image());
    $page->add_data('mainpage','wall.php');
}
catch (Exception $e) {
    session_remove();
    header('location: ../login.php');
    exit;
}


echo $page->get_page();