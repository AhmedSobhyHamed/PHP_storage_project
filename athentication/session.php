<?php
// function session_begin() {
//     session_start();
// }

// setup a session when post request is sent with user email and password
// it require a sanitizer function name as string and the connection to the sql
function session_setup (/*string $santizer,mysqli $connection*/) {
    if (!isset($_SESSION['useremail']) &&
                !isset($_SESSION['userpaswd'])) {
        if(isset($_POST['em']) &&
            isset($_POST['pw'])) {
                // if(function_exists($santizer)) {
                    // $_SESSION['useremail'] = call_user_func_array($santizer,[$connection ,$_POST['em']]);
                    // $_SESSION['userpaswd'] = call_user_func_array($santizer,[$connection,$_POST['pw']]);
                    $_SESSION['useremail'] = $_POST['em'];
                    $_SESSION['userpaswd'] = $_POST['pw'];
                    $_SESSION['userhashkey'] = hash('ripemd128',
                            $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
                // }
            }
    }
}
// setup a session settings for the next request
function session_settings() {
    ini_set('session.gc_maxlifetime',900);
    ini_set('session.gc_probability',1);
    ini_set('session.gc_divisor',1);
    ini_set('session.use_only_cookies',1);
}
// prevent session fixation by fixing it
// prevent session hajaking by return false
function session_rotien(): bool {
    if(!isset($_SESSION['fixation'])) {
        session_regenerate_id();
        session_start();
        $_SESSION['fixation']=1;
    }
    if(isset($_SESSION['userhashkey']) &&
        $_SESSION['userhashkey']!== hash('ripemd128',
            $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) {
        // header('location:login.php');
        return false;
    }
    return true;
}
// remove the session
function session_remove() {
    $_SESSION = Array();
    session_unset();
    setcookie(session_name(),'',time()-157000000,'/');
    session_destroy();
}
// see if the session for a user is setup
function is_user_session(): bool {
    if (!isset($_SESSION['useremail']) &&
                !isset($_SESSION['userpaswd'])) {
        return false;
    }
    return true;
}
// is there is a user (yes,no) // stay or redirect
function router(?string $yes, ?string $no) {
    if((session_rotien() && is_user_session()) && !is_null($yes)){
        header("location: {$yes}");
        exit;
    }
    if((!session_rotien() || !is_user_session()) && !is_null($no)) {
        session_remove();
        header("location: {$no}");
        exit;
    }
}