<?php

require_once 'htmlh/HTML_HANDLER.php';

$page = new HTML_TEMPLATE();
$body = new HTML_TEMPLATE();

$page->get_file('htmlh/main/index.html');
$body->get_file('htmlh/signup/signup.html');
#head data
$page->add_data('description','signup to activities stopage website page , activities storage can store notes, daily notes, images, vedios and  watchies information');
$page->add_data('keywords','signup activities storage notes note manga anime movies movie video image videos images');
$page->add_data('ogimage','htmlh/main/resources/storageicon.png');
$page->add_data('title','SignUp');
$page->add_data('titleicon','htmlh/main/resources/storageicon.png');
$page->add_data('script','htmlh/signup/develop.js');
$page->add_data('style','htmlh/signup/style.css');
#section data
$body->add_data('loginpage','login.php');
#include section
$page->add_data('thebody',$body->get_page());

return $page;
