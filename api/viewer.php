<?php

require_once '../athentication/session.php';
require_once '../files_management/FILE_UPLOADED.php';
require_once '../DB/user.php';
require_once '../DB/manga.php';
require_once '../DB/media.php';
require_once '../DB/notes.php';

session_settings();
session_start();
// if there is any malicious data or no session user go to login
router(null,'login.php');
// if there is session
function view (User $user) {
    if(count($_POST) == 3 && isset($_POST['req']) && $_POST['req'] == 'view' &&
    isset($_POST['type']) && isset($_POST['id'])) {
        if($_POST['type'] == 'media') {
            $pr = $user->get_previous_child_data(User::MEDIA,$_POST['id']);
            $nx = $user->get_next_child_data(User::MEDIA,$_POST['id']);
            validate_number($_POST['id']);
            $md = new Media(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            $nw = $md->show();
            echo 'type<:>media<n>prev<:>'.$pr.'<,>next<:>'.$nx.'<n>';
            $mdtxt = '';
            foreach($nw as $k => $v) {
                $mdtxt.= $k.'<:>'.$v.'<,>';
            }
            echo substr($mdtxt,0,-3);
        }
        if($_POST['type'] == 'manga') {
            $pr = $user->get_previous_child_data(User::MANGA,$_POST['id']);
            $nx = $user->get_next_child_data(User::MANGA,$_POST['id']);
            validate_number($_POST['id']);
            $mg = new Manga(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            $nw = $mg->show();
            echo 'type<:>manga<n>prev<:>'.$pr.'<,>next<:>'.$nx.'<n>';
            $mdtxt = '';
            foreach($nw as $k => $v) {
                $mdtxt.= $k.'<:>'.$v.'<,>';
            }
            echo substr($mdtxt,0,-3);
        }
        if($_POST['type'] == 'notes') {
            $pr = $user->get_previous_child_data(User::NOTES,$_POST['id']);
            $nx = $user->get_next_child_data(User::NOTES,$_POST['id']);
            validate_number($_POST['id']);
            $nt = new Notes(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            $nw = $nt->show();
            echo 'type<:>notes<n>prev<:>'.$pr.'<,>next<:>'.$nx.'<n>';
            $mdtxt = '';
            foreach($nw as $k => $v) {
                $mdtxt.= $k.'<:>'.$v.'<,>';
            }
            echo substr($mdtxt,0,-3);
        }
    }
}
function remove (User $user) {
    if(count($_POST) == 4 && isset($_POST['req']) && $_POST['req'] == 'remove' &&
    isset($_POST['type']) && isset($_POST['id']) && isset($_POST['key'])) {
        if($_POST['type'] == 'media') {
            validate_number($_POST['id']);
            $md = new Media(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            switch ($_POST['key']) {
                case 'name':
                    $md->delete(Media::MEDIA_NAME);
                    break;
                // case 'weburl':
                //     $md->delete(Media::MEDIA_GLOBALURL);
                //     break;
                case 'localurl':
                    $delfile = '../'.$md->show()['localurl'];
                    if($delfile == '../') $delfile = 'abcdefg';
                    $delfile = realpath($delfile);
                    if(file_exists($delfile)) {
                        if(is_writable($delfile)) {
                            unlink($delfile);
                        }
                    }
                    $md->delete(Media::MEDIA_LOCALURL);
                    break;
                case 'img':
                    $delfile = '../'.$md->show()['img'];
                    if($delfile == '../') $delfile = 'abcdefg';
                    $delfile = realpath($delfile);
                    if(file_exists($delfile)) {
                        if(is_writable($delfile)) {
                            unlink($delfile);
                        }
                    }
                    $md->delete(Media::MEDIA_IMG);
                    break;
                default :
                    if(preg_match('/^tag-.+/',$_POST['key'])) {
                        $md->update(Media::MEDIA_TAG_REMOVE,preg_replace('/^tag-/','',$_POST['key']));
                    }
            }
        }
        if($_POST['type'] == 'manga') {
            validate_number($_POST['id']);
            $mg = new Manga(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            switch ($_POST['key']) {
                // case 'url':
                //     $mg->delete(Manga::MANGA_URL);
                //     break;
                case 'description':
                    $mg->delete(Manga::MANGA_DSC);
                    break;
                // case 'chapter':
                //     $mg->delete(Manga::MANGA_chp);
                //     break;
                case 'img':
                    $delfile = '../'.$mg->show()['img'];
                    if($delfile == '../') $delfile = 'abcdefg';
                    $delfile = realpath($delfile);
                    if(file_exists($delfile)) {
                        if(is_writable($delfile)) {
                            unlink($delfile);
                        }
                    }
                    $mg->delete(Manga::MANGA_IMG);
            }
        }
        if($_POST['type'] == 'notes') {
            validate_number($_POST['id']);
            $nt = new Notes(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            $nt->delete($_POST['key']);
        }
        unset($_POST['key']);
        $_POST['req'] = 'view';
        $GLOBALS['editmessage'] = '<|>type<:>reply<n>action<:>success';
    }
}
function edit (User $user) {
    if(count($_POST) == 5 && isset($_POST['req']) && $_POST['req'] == 'edit' &&
    isset($_POST['type']) && isset($_POST['id']) && isset($_POST['key']) && isset($_POST['val'])) {
        if($_POST['type'] == 'media') {
            validate_number($_POST['id']);
            $md = new Media(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            switch ($_POST['key']) {
                case 'name':
                    $md->update(Media::MEDIA_NAME,$_POST['val']);
                    break;
                case 'weburl':
                    $md->update(Media::MEDIA_GLOBALURL,$_POST['val']);
                    break;
                case 'localurl':
                    $fh = new FILE_UPLOADED('../resourses/media',500000000,"audio/mpeg",
                    "audio/x-wav","audio/wav","audio/mpeg","image/gif","image/jpeg",
                    "image/jpg","image/png","video/mpeg","video/mp4","video/x-msvideo");
                    $fh->get_file($_POST['val']);
                    $fh->generate_name(null);
                    $fh->validate();
                    $delfile = $md->show()['localurl'];
                    if($delfile) {
                        $delfile = '../'.$delfile;
                        $delfile = realpath($delfile);
                        if(file_exists($delfile)) {
                            if(is_writable($delfile)) {
                                // unlink($delfile);
                            }
                        }
                    }
                    $md->update(Media::MEDIA_LOCALURL,substr($fh->create_file($_POST['val']),3));
                    break;
                case 'img':
                    $fh = new FILE_UPLOADED('../resourses/imgs',2000000,"image/gif",
                        "image/jpeg","image/jpg","image/png");
                    $fh->get_file($_POST['val']);
                    $fh->generate_name(null);
                    $fh->validate();
                    $delfile = $md->show()['img'];
                    if($delfile) {
                        $delfile = '../'.$delfile;
                        $delfile = realpath($delfile);
                        if(file_exists($delfile)) {
                            if(is_writable($delfile)) {
                                unlink($delfile);
                            }
                        }
                    }
                    $md->update(Media::MEDIA_IMG,substr($fh->create_file($_POST['val']),3));
                    break;
                case 'tags':
                    $md->update(Media::MEDIA_TAG_ADD,$_POST['val']);
                    break;
            }
        }
        if($_POST['type'] == 'manga') {
            validate_number($_POST['id']);
            $mg = new Manga(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            switch ($_POST['key']) {
                case 'url':
                    $mg->update(Manga::MANGA_URL,$_POST['val']);
                    break;
                case 'description':
                    $mg->update(Manga::MANGA_DSC,$_POST['val']);
                    break;
                case 'chapter':
                    $mg->update(Manga::MANGA_chp,$_POST['val']);
                    break;
                case 'img':
                    $fh = new FILE_UPLOADED('../resourses/imgs',2000000,"image/gif",
                        "image/jpeg","image/jpg","image/png");
                    $fh->get_file($_POST['val']);
                    $fh->generate_name(null);
                    $fh->validate();
                    $delfile = '../'.$mg->show()['img'];
                    if($delfile) {
                        $delfile = '../'.$delfile;
                        $delfile = realpath($delfile);
                        if(file_exists($delfile)) {
                            if(is_writable($delfile)) {
                                unlink($delfile);
                            }
                        }
                    }
                    $mg->update(Manga::MANGA_IMG,substr($fh->create_file($_POST['val']),3));
            }
        }
        if($_POST['type'] == 'notes') {
            validate_number($_POST['id']);
            $nt = new Notes(sanitize($user->get_connection(),$_POST['id']),
            $user->id(),$user->get_connection());
            switch ($_POST['key']) {
                case 'name':
                    $nt->update(Notes::NOTE_NAME,$_POST['val']);
                    break;
                case 'new':
                    $nt->add(Notes::SNIPPET,$_POST['val']);
                    break;
                default :
                    $nt->update(Notes::SNIPPET,$_POST['val'],$_POST['key']);
            } 
        }
        unset($_POST['key']);
        unset($_POST['val']);
        $_POST['req'] = 'view';
        $GLOBALS['editmessage'] = '<|>type<:>reply<n>action<:>success';
    }
}
try {
    $user = new User(null,$_SESSION['useremail'],$_SESSION['userpaswd']);
    remove($user);
    edit($user);
    view($user);
    if(isset($GLOBALS['editmessage'])) {
        echo $GLOBALS['editmessage'];
    }
}
catch (Exception $e) {
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