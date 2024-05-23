<?php

require_once '../athentication/session.php';
require_once '../files_management/FILE_UPLOADED.php';
require_once '../DB/user.php';

session_settings();
session_start();
// if there is any malicious data or no session user go to login
router(null,'../login.php');
// if there is session
try {
    $user = new User(null,$_SESSION['useremail'],$_SESSION['userpaswd']);
    if(count($_POST) == 3 && isset($_POST['req']) && $_POST['req'] == 'user' &&
    isset($_POST['type']) && $_POST['type'] == 'change') {
        if(isset($_POST['un'])) {
            $user->update(User::NAME,$_POST['un']);
        }
        if(isset($_POST['img']) && isset($_FILES[$_POST['img']])) {
            $fh = new FILE_UPLOADED('../resourses/profile_imgs',2000000,"image/gif",
                        "image/jpeg","image/jpg","image/png");
                    $fh->get_file($_POST['img']);
                    $fh->generate_name(null);
                    $fh->validate();
                    $delfile = $user->image();
                    if($delfile) {
                        $delfile = '../'.$delfile;
                        $delfile = realpath($delfile);
                        if(file_exists($delfile)) {
                            if(is_writable($delfile)) {
                                unlink($delfile);
                            }
                        }
                    }
                    $user->update(User::PHOTO,substr($fh->create_file($_POST['img']),3));
        }
        $user2 = new User($user->get_connection(),$_SESSION['useremail'],$_SESSION['userpaswd']);
        echo 'type<:>profile<n>name<:>'.$user2->name().'<,>img<:>'.$user2->image();
    }
    if(count($_POST) == 3 && isset($_POST['req']) && $_POST['req'] == 'user' &&
    isset($_POST['type']) && $_POST['type'] == 'delete' && isset($_POST['pwd'])) {
        if($_POST['pwd'] === $_SESSION['userpaswd']) {
            $delfiles = $user->get_resourses();
            if(is_array($delfiles)) {
                array_push($delfiles ,$user->image());
                foreach($delfiles as $delfile) {
                    if($delfile) {
                        $delfile = '../'.$delfile;
                        $delfile = realpath($delfile);
                        if(file_exists($delfile)) {
                            if(is_writable($delfile)) {
                                unlink($delfile);
                            }
                        }
                    }
                }
            }
            $user->deleteThisUser();
            throw new Exception('go to log in page');
        }
    }
}
catch (Exception $e) {
    if($e->getMessage() == 'url not valid') {
        echo 'type<:>reply<n>action<:>fail<,>cause<:>enter a valid web URL';
    }
    else {
        session_remove();
        // echo '||'.$e->getMessage().'||';
        header('location: ../login.php');
        exit;
    }
}