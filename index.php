<?php

require_once 'htmlh/HTML_HANDLER.php';
require_once 'athentication/session.php';

session_settings();
session_start();
// if there is any malicious data remove the session
if(!session_rotien()) {
    session_remove();
    header('location: index.php');
    exit;
}

$page   = new HTML_TEMPLATE();
$body   = new HTML_TEMPLATE();
$pagefooter = new HTML_TEMPLATE();

$page->get_file('htmlh/main/index.html');
$body->get_file('htmlh/interface/interface.html');
$pagefooter->get_file('htmlh/footer/footer.html');
#head data
$page->add_data('description','interface page activities stopage website page or the gallary of cards, about the website, what we can offer, activities storage can store notes, daily notes, images, vedios and  watchies information');
$page->add_data('keywords','interface main about contact us  activities storage notes note manga anime movies movie video image videos images');
$page->add_data('ogimage','htmlh/main/resources/storageicon.png');
$page->add_data('title','archive storage web site');
$page->add_data('titleicon','htmlh/main/resources/storageicon.png');
$page->add_data('script','htmlh/interface/develop.js');
$page->add_data('style','htmlh/interface/style.css');
#section data
$body->add_data('pagefooter',$pagefooter->get_page());
#include section
$page->add_data('thebody',$body->get_page());

echo $page->get_page();

