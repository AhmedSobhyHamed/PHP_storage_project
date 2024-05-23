<?php

require_once 'athentication/session.php';
require_once 'DB/user.php';
require_once 'DB/manga.php';
require_once 'DB/media.php';
require_once 'DB/notes.php';
$page = require_once 'htmlh/viewer/viewer.php';

session_settings();
session_start();
// if there is any malicious data or no session user go to login
router(null,'login.php');
// if there is session
try {
    if(count($_POST) == 3 && isset($_POST['req']) && $_POST['req'] == 'view' &&
    isset($_POST['type']) && isset($_POST['id'])) {
        if($_POST['type'] == 'media' || $_POST['type'] == 'manga' || $_POST['type'] == 'notes') {
            $page->add_data('startupdata',
            '<div class="visually-hidden" id="startuptype">'.$_POST['type'].'</div>'
            .'<div class="visually-hidden" id="startupid">'.$_POST['id'].'</div>');
        }
    }
    else {
    header('location: wall.php');
    }
}
catch (Exception $e) {
    session_remove();
    header('location: login.php');
    exit;
}


echo $page->get_page();
