<?php

require_once 'htmlh/HTML_HANDLER.php';

$page   = new HTML_TEMPLATE();
$body   = new HTML_TEMPLATE();
$navbar = new HTML_TEMPLATE();
$orderlist = new HTML_TEMPLATE();
$sidebar = new HTML_TEMPLATE();
$maincontent = new HTML_TEMPLATE();
$pagefooter = new HTML_TEMPLATE();

$page->get_file('htmlh/main/index.html');
$body->get_file('htmlh/wall/wall.html');
$navbar->get_file('htmlh/navbar/nav.html');
$orderlist->get_file('htmlh/orderlist/orderlist.html');
$sidebar->get_file('htmlh/sidebar/sidebar.html');
$maincontent->get_file('htmlh/maincontent/maincontent.html');
$pagefooter->get_file('htmlh/footer/footer.html');
#head data
$page->add_data('description','the main page on activities stopage website page or the gallary of cards, activities storage can store notes, daily notes, images, vedios and  watchies information');
$page->add_data('keywords','gallary cards wall main home activities storage notes note manga anime movies movie video image videos images');
$page->add_data('ogimage','htmlh/main/resources/storageicon.png');
$page->add_data('title','main wall');
$page->add_data('titleicon','htmlh/main/resources/storageicon.png');
$page->add_data('script','htmlh/wall/develop.js');
$page->add_data('style','htmlh/wall/style.css');
#section data
#nav
$navbar->add_data('logoimg','htmlh/main/resources/logoimg.png');
#order list
#body
$body->add_data('navbar',$navbar->get_page());
$body->add_data('orderlist',$orderlist->get_page());
$body->add_data('sidebar',$sidebar->get_page());
$body->add_data('mainpagecontent',$maincontent->get_page());
$body->add_data('pagefooter',$pagefooter->get_page());
#include section
$page->add_data('thebody',$body->get_page());

return $page;
