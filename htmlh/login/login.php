<?php

// require_once '../HTML_HANDLER.php';
require_once 'htmlh/HTML_HANDLER.php';

$page = new HTML_TEMPLATE();
$body = new HTML_TEMPLATE();

// $page->get_file('../main/index.html');
$page->get_file('htmlh/main/index.html');
// $body->get_file('login.html');
$body->get_file('htmlh/login/login.html');
#head data
$page->add_data('description','login to activities stopage website page , activities storage can store notes, daily notes, images, vedios and  watchies information');
$page->add_data('keywords','login activities storage notes note manga anime movies movie video image videos images');
$page->add_data('ogimage','htmlh/main/resources/storageicon.png');
$page->add_data('title','LogIn');
$page->add_data('titleicon','htmlh/main/resources/storageicon.png');
$page->add_data('script','htmlh/login/develop.js');
$page->add_data('style','htmlh/login/style.css');
#section data
// $body->add_data('data',[['name'=>'hello'],['name'=>'world'],['name'=>'hello'],['name'=>'world']]);
$body->add_data('signuppage','signup.php');
$body->add_data('formtarget','api/login.php');
#include section
$page->add_data('thebody',$body->get_page());

return $page;
