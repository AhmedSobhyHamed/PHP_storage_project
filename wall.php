<?php

require_once 'athentication/session.php';
require_once 'DB/user.php';
$page = require_once 'htmlh/wall/wall.php';

session_settings();
session_start();
// if there is any malicious data or no session user go to login
router(null,'login.php');
// if there is session
try {
    $user = new User(null,$_SESSION['useremail'],$_SESSION['userpaswd']);
    $page->add_data('profileimg',$user->image());
    $page->add_data('profilename',$user->name());
    $page->add_data('profilepageurl','profile.php');
}
catch (Exception $e) {
    session_remove();
    header('location: ../login.php');
    exit;
}


echo $page->get_page();