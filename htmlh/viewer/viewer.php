<?php

require_once 'htmlh/HTML_HANDLER.php';

$page   = new HTML_TEMPLATE();
$body   = new HTML_TEMPLATE();
$pagefooter = new HTML_TEMPLATE();

$page->get_file('htmlh/main/index.html');
$body->get_file('htmlh/viewer/viewer.html');
$pagefooter->get_file('htmlh/footer/footer.html');
#head data
$page->add_data('description','view card information on activities stopage website page , activities storage can store notes, daily notes, images, vedios and  watchies information');
$page->add_data('keywords','view card cardinfo activities storage notes note manga anime movies movie video image videos images');
$page->add_data('ogimage','htmlh/main/resources/storageicon.png');
$page->add_data('title','viewer');
$page->add_data('titleicon','htmlh/main/resources/storageicon.png');
$page->add_data('script','htmlh/viewer/develop.js');
$page->add_data('style','htmlh/viewer/style.css');
#body
// $body->add_data('','');
$body->add_data('pagefooter',$pagefooter->get_page());
#include section
$page->add_data('thebody',$body->get_page());

return $page;