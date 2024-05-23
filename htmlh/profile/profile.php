<?php

require_once 'htmlh/HTML_HANDLER.php';

$page   = new HTML_TEMPLATE();
$body   = new HTML_TEMPLATE();
$pagefooter = new HTML_TEMPLATE();

$page->get_file('htmlh/main/index.html');
$body->get_file('htmlh/profile/profile.html');
$pagefooter->get_file('htmlh/footer/footer.html');
#head data
$page->add_data('description','the personal page of a user profile on activities stopage website page , activities storage can store notes, daily notes, images, vedios and  watchies information');
$page->add_data('keywords','profile activities storage notes note manga anime movies movie video image videos images');
$page->add_data('ogimage','htmlh/main/resources/storageicon.png');
$page->add_data('title','profile page');
$page->add_data('titleicon','htmlh/main/resources/storageicon.png');
$page->add_data('script','htmlh/profile/develop.js');
$page->add_data('style','htmlh/profile/style.css');
#body
$body->add_data('pagefooter',$pagefooter->get_page());
#include section
$page->add_data('thebody',$body->get_page());

return $page;
