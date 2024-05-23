<?php

require_once '../athentication/session.php';
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
function request_sortlist() {
    if(isset($_POST) && count($_POST) == 3 &&
    isset($_POST['req']) && isset($_POST['type']) && isset($_POST['sort']) && 
    $_POST['type'] == 'li') {
        if($_POST['req'] == 'main' || 
        $_POST['req'] == 'media' || 
        $_POST['req'] == 'manga' || 
        $_POST['req'] == 'notes') {
            echo 'type<:>li<n>date ascending order<:>';
            if($_POST['sort'] == 'asc') echo 'selected'; else echo 'asc';
            echo '<,>date descending order<:>';
            if($_POST['sort'] == 'desc') echo 'selected'; else echo 'desc';
            echo '<,>alphaptic ascending order<:>';
            if($_POST['sort'] == 'name') echo 'selected'; else echo 'name';
        }
        else if($_POST['req'] == 'search') {
            echo 'type<:>li<n>most related<:>';
            if($_POST['sort'] == 'rel') echo 'selected'; else echo 'rel';
            echo '<,>date ascending order<:>';
            if($_POST['sort'] == 'asc') echo 'selected'; else echo 'asc';
            echo '<,>date descending order<:>';
            if($_POST['sort'] == 'desc') echo 'selected'; else echo 'desc';
            echo '<,>alphaptic ascending order<:>';
            if($_POST['sort'] == 'name') echo 'selected'; else echo 'name';
        }
    }
}
function request_newlist() {
    if(isset($_POST) && count($_POST) == 2 &&
    isset($_POST['req']) && isset($_POST['type']) &&
    $_POST['type'] == 'new') {
        if($_POST['req'] == 'main' ||
        $_POST['req'] == 'search'){
            echo 'type<:>new<n>media<,>manga<,>note';
        }
        else if($_POST['req'] == 'media') {
            echo 'type<:>new<n>media';
        }
        else if($_POST['req'] == 'manga') {
            echo 'type<:>new<n>manga';
        }
        else if($_POST['req'] == 'notes') {
            echo 'type<:>new<n>note';
        }
    }
}
function request_cards(User $user) {
    if(isset($_POST) && count($_POST) == 4 &&
    isset($_POST['req']) && isset($_POST['type']) &&
    isset($_POST['sort']) && isset($_POST['from']) &&
    $_POST['type'] == 'cards') {
        try{
            if($_POST['req'] == 'main') {
                if($_POST['sort'] == 'asc') {
                    $data = $user->show(User::ALL,User::DATE_ASC,$_POST['from'],20);
                }
                else if($_POST['sort'] == 'desc') {
                    $data = $user->show(User::ALL,User::DATE_DSC,$_POST['from'],20);
                }
                else if($_POST['sort'] == 'name') {
                    $data = $user->show(User::ALL,User::TITLE_ASC,$_POST['from'],20);
                }
            }
            else if($_POST['req'] == 'media') {
                if($_POST['sort'] == 'asc') {
                    $data = $user->show(User::MEDIA,User::DATE_ASC,$_POST['from'],($_POST['from']+20));
                }
                else if($_POST['sort'] == 'desc') {
                    $data = $user->show(User::MEDIA,User::DATE_DSC,$_POST['from'],($_POST['from']+20));
                }
                else if($_POST['sort'] == 'name') {
                    $data = $user->show(User::MEDIA,User::TITLE_ASC,$_POST['from'],($_POST['from']+20));
                }
            }
            else if($_POST['req'] == 'manga') {
                if($_POST['sort'] == 'asc') {
                    $data = $user->show(User::MANGA,User::DATE_ASC,$_POST['from'],($_POST['from']+20));
                }
                else if($_POST['sort'] == 'desc') {
                    $data = $user->show(User::MANGA,User::DATE_DSC,$_POST['from'],($_POST['from']+20));
                }
                else if($_POST['sort'] == 'name') {
                    $data = $user->show(User::MANGA,User::TITLE_ASC,$_POST['from'],($_POST['from']+20));
                }
            }
            else if($_POST['req'] == 'notes') {
                if($_POST['sort'] == 'asc') {
                    $data = $user->show(User::NOTES,User::DATE_ASC,$_POST['from'],($_POST['from']+20));
                }
                else if($_POST['sort'] == 'desc') {
                    $data = $user->show(User::NOTES,User::DATE_DSC,$_POST['from'],($_POST['from']+20));
                }
                else if($_POST['sort'] == 'name') {
                    $data = $user->show(User::NOTES,User::TITLE_ASC,$_POST['from'],($_POST['from']+20));
                }
            }
        }
        catch(Exception $e) {
            session_remove();
            header('location: ../login.php');
            exit;
        }
        if($_POST['from'] === '0') {
            $output = 'type<:>cards<n>';
        }
        else {
            $output = 'type<:>cards<,>continue<:>1<n>';
        }
            foreach($data as $d) {
                foreach($d as $k=>$v) {
                    $output .= $k.'<:>'.$v;
                    $output .= '<,>';
                }
                $output = substr($output,0,-3);
                $output .= '<n>';
            }
            echo substr($output,0,-3);
    }
    else if(isset($_POST) && count($_POST) == 5 &&
    isset($_POST['req']) && isset($_POST['type']) &&
    isset($_POST['sort']) && isset($_POST['from']) && isset($_POST['search']) &&
    $_POST['req'] == 'search' && $_POST['type'] == 'cards') {
        try{
            if($_POST['sort'] == 'asc') {
                $data = $user->show(User::SEARCH,User::DATE_ASC,$_POST['from'],($_POST['from']+20),$_POST['search']);
            }
            else if($_POST['sort'] == 'desc') {
                $data = $user->show(User::SEARCH,User::DATE_DSC,$_POST['from'],($_POST['from']+20),$_POST['search']);
            }
            else if($_POST['sort'] == 'name') {
                $data = $user->show(User::SEARCH,User::TITLE_ASC,$_POST['from'],($_POST['from']+20),$_POST['search']);
            }
            else if($_POST['sort'] == 'rel') {
                $data = $user->show(User::SEARCH,User::MATCH_RATE,$_POST['from'],($_POST['from']+20),$_POST['search']);
            }
        }
        catch(Exception $e) {
            session_remove();
            header('location: ../login.php');
            exit;
        }
        $output = 'type<:>cards<n>';
            foreach($data as $d) {
                foreach($d as $k=>$v) {
                    $output .= $k.'<:>'.$v;
                    $output .= '<,>';
                }
                $output = substr($output,0,-3);
                $output .= '<n>';
            }
            echo substr($output,0,-3);
    }
}
function empty_request(string $req, User $u) {
    $_POST['req'] = $req;
    $_POST['type'] = 'new';
    request_newlist();
    echo '<|>';
    $_POST['type'] = 'li';
    $_POST['sort'] = 'desc';
    request_sortlist();
    echo '<|>';
    $_POST['type'] = 'cards';
    $_POST['sort'] = 'desc';
    $_POST['from'] = '0';
    request_cards($u);
}
function deletecard(User $user) {
    try {
        if(isset($_POST) && count($_POST) == 3 &&
        isset($_POST['req']) && isset($_POST['type']) &&
        isset($_POST['id'])) {
            if($_POST['type'] == 'media') {
                $md = new Media(sanitize($user->get_connection(),$_POST['id']),
                $user->id(),$user->get_connection());
                $delfile = '../'.$md->show()['localurl'];
                if($delfile == '../') $delfile = 'abcdefg';
                $delfile = realpath($delfile);
                if(file_exists($delfile)) {
                    if(is_writable($delfile)) {
                        unlink($delfile);
                    }
                }
                $delfile = '../'.$md->show()['img'];
                if($delfile == '../') $delfile = 'abcdefg';
                $delfile = realpath($delfile);
                if(file_exists($delfile)) {
                    if(is_writable($delfile)) {
                        unlink($delfile);
                    }
                }
                $user->delete(User::MEDIA,$_POST['id']);
            }
            elseif($_POST['type'] == 'manga') {
                $mg = new Manga(sanitize($user->get_connection(),$_POST['id']),
                $user->id(),$user->get_connection());
                $delfile = '../'.$mg->show()['img'];
                if($delfile == '../') $delfile = 'abcdefg';
                $delfile = realpath($delfile);
                if(file_exists($delfile)) {
                    if(is_writable($delfile)) {
                        unlink($delfile);
                    }
                }
                $user->delete(User::MANGA,$_POST['id']);
            }
            elseif($_POST['type'] == 'notes') {
                $user->delete(User::NOTES,$_POST['id']);
            }
        }
    }
    catch(Exception $e) {
        session_remove();
        header('location: ../login.php');
        exit;
    }
}
request_sortlist();
request_newlist();
request_cards($user);
deletecard($user);

if(!isset($_POST) || empty($_POST) || 
    (count($_POST) == 1 && isset($_POST['req']) && $_POST['req'] == 'main')) {
        empty_request('main',$user);
}
else if(isset($_POST) && count($_POST) == 1 && isset($_POST['req']) && $_POST['req'] == 'media') {
    empty_request('media',$user);
}
else if(isset($_POST) && count($_POST) == 1 && isset($_POST['req']) && $_POST['req'] == 'manga') {
    empty_request('manga',$user);
}
else if(isset($_POST) && count($_POST) == 1 && isset($_POST['req']) && $_POST['req'] == 'notes') {
    empty_request('notes',$user);
}

