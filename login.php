<?php

require_once 'athentication/session.php';
require_once 'DB/user.php';
$page = require_once 'htmlh/login/login.php';

session_settings();
session_start();
// if there is any malicious data remove the session
if(!session_rotien()) {
    session_remove();
    header('location: login.php');
    exit;
}
// if there is a user go to home or wall
router('wall.php',null);
// if there is coockies
if(isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    header('location: api/login.php');
}
// if there is no malicious data and no user session 
echo $page->get_page();


