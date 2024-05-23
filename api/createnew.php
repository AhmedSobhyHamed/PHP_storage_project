<?php

require_once '../athentication/session.php';
require_once '../files_management/FILE_UPLOADED.php';
require_once '../DB/user.php';
require_once '../DB/media.php';
require_once '../DB/manga.php';
require_once '../DB/notes.php';

session_settings();
session_start();
// if there is any malicious data or no session user go to login
router(null,'../login.php');
// if there is session
try {
    $user = new User(null,$_SESSION['useremail'],$_SESSION['userpaswd']);
}
catch (Exception $e) {
    session_remove();
    header('location: ../login.php');
    exit;
}
// receive requests
function request_newwadget (User $user) {
    if(isset($_POST) && count($_POST) > 2 &&
    isset($_POST['req']) && isset($_POST['type']) &&
    $_POST['req'] == 'new') {
        // $manga_id = 0;
        // $media_id = 0;
        // $note_id  = 0;
        try {
            if($_POST['type'] == 'media' && count($_POST) >= 4){
                if(isset($_POST['name']) && isset($_POST['url'])) {
                    $user->add(User::MEDIA,$_POST['name'],$_POST['url']);
                    $media_id = $user->get_connection()->query('SELECT LAST_INSERT_ID()')
                    ->fetch_all()[0][0];
                    if(isset($_POST['file']) || isset($_POST['img']) || isset($_POST['tags'])) {
                        $media = new Media($media_id,$user->id(),$user->get_connection());
                    }
                    if(isset($_POST['file'])) {
                        $fh = new FILE_UPLOADED('../resourses/media',500000000,"audio/mpeg",
                        "audio/x-wav","audio/wav","audio/mpeg","image/gif","image/jpeg",
                        "image/jpg","image/png","video/mpeg","video/mp4","video/x-msvideo");
                        $fh->get_file($_POST['file']);
                        $fh->generate_name(null);
                        $fh->validate();
                        $media->add(Media::MEDIA_LOCALURL,substr($fh->create_file($_POST['file']),3));
                    }
                    if(isset($_POST['img'])) {
                        $fh = new FILE_UPLOADED('../resourses/imgs',2000000,"image/gif",
                        "image/jpeg","image/jpg","image/png");
                        $fh->get_file($_POST['img']);
                        $fh->generate_name(null);
                        $fh->validate();
                        $media->add(Media::MEDIA_IMG,substr($fh->create_file($_POST['img']),3));
                    }
                    if(isset($_POST['tags'])) {
                        foreach($_POST['tags'] as $tag) {
                            $media->add(Media::MEDIA_TAG_ADD,$tag);
                        }
                    }
                }
            }
            else if($_POST['type'] == 'manga' && count($_POST) >= 4) {
                if(isset($_POST['name']) && isset($_POST['url'])) {
                    $user->add(User::MANGA,$_POST['name'],$_POST['url']);
                    $manga_id = $user->get_connection()->query('SELECT LAST_INSERT_ID()')
                    ->fetch_all()[0][0];
                    if(isset($_POST['img']) || isset($_POST['description']) || isset($_POST['chapter'])) {
                        $manga = new Manga($manga_id,$user->id(),$user->get_connection());
                    }
                    if(isset($_POST['img'])) {
                        $fh = new FILE_UPLOADED('../resourses/imgs',2000000,"image/gif",
                        "image/jpeg","image/jpg","image/png");
                        $fh->get_file($_POST['img']);
                        $fh->generate_name(null);
                        $fh->validate();
                        $manga->add(Manga::MANGA_IMG,substr($fh->create_file($_POST['img']),3));
                    }
                    if(isset($_POST['description'])) {
                        $manga->add(Manga::MANGA_DSC,$_POST['description']);
                    }
                    if(isset($_POST['chapter'])) {
                        $manga->add(Manga::MANGA_chp,$_POST['chapter']);
                    }
                }
            }
            else if($_POST['type'] == 'note' && count($_POST) >= 3) {
                if(isset($_POST['name'])) {
                    $user->add(User::NOTES,$_POST['name']);
                    $note_id = $user->get_connection()->query('SELECT LAST_INSERT_ID()')
                    ->fetch_all()[0][0];
                    if(isset($_POST['snippet'])) {
                        $note = new Notes($note_id,$user->id(),$user->get_connection());
                        $note->add(Notes::SNIPPET,$_POST['snippet']);
                    }
                }
            }
            echo 'type<:>reply<n>action<:>success';
        }
        catch (Exception $e) {
            if($_POST['type'] == 'media' && isset($media_id)) {
                $user->delete(User::MEDIA,$media_id);
            }
            if($_POST['type'] == 'manga' && isset($manga_id)) {
                $user->delete(User::MANGA,$manga_id);
            }
            if($_POST['type'] == 'note' && isset($note_id)) {
                $user->delete(User::NOTES,$note_id);
            }
            if(preg_match('/Duplicate entry.*/',$e->getMessage())){
                echo 'type<:>reply<n>action<:>fail<,>cause<:>already exist';
            }
            else if($e->getMessage() == 'number not valid') {
                echo 'type<:>reply<n>action<:>fail<,>cause<:>enter a number';
            }
            else if($e->getMessage() == 'url not valid') {
                echo 'type<:>reply<n>action<:>fail<,>cause<:>enter a valid web URL';
            }
            else {
                session_remove();
                // echo '||'.$e->getMessage().'||';
                header('location: ../login.php');
                exit;
            }
        }
    }
}
request_newwadget($user);

/**
 * type = {manga | media | note}
 * response(
 * feild name:type(lable text , label)(input name , input type),
 * )
 * name =....
 * desc = ...
 * img  = ...
 * ...
 * response(
 * insert the data
 * {success| fail | already found}
 * )
 * 
 */