<?php
#this file have function for validate , hashing and sanitize strings to insert it into database 
#note that regular expression needed to review
#hash string funcion not created yet
// this function act as a part of validate functions destributions 
// it test value of a string that it get if it equal to [1|2] that provided in the first argument
// 1 mean this pattern must match , 0 mean this pattern mustn't match
function validate_test(int $reg):bool {
    if($reg === 1)
        return true;
    return false;
}
// it test if a string is formed a valid email and retern true when its valid
function validate_email(string $string):bool {
    if(!validate_test(preg_match('/^[\w\d\._\-]+@[\w\d]+\.[\w\d]{2,3}$/',$string)))
        throw new Exception('email not valid');
    return true;
}
// it test if a string is formed a valid password and retern true when its valid
// any character is valid except {, }, ', ", (, ), <, >
function validate_password(string $string):bool {
    if(!validate_test(preg_match('/^[^\'\"><(){}]{10,}$/',$string)))
        throw new Exception('password not valid');
    return true;
}
// it test if a string is formed a valid URL and retern true when its valid
function validate_url(string $string):bool {
    if(!validate_test(preg_match('/(^((http|https|ftp|ftps):\/\/)?[\w\d-]+\.[\w\d]{2,3}(\/.*)?$)|(^$)/',$string)))
        throw new Exception('url not valid');
    return true;
}
// it test if a string is formed a valid number and have not any char and retern true when its valid
function validate_number(string $string):bool {
    if(!validate_test(preg_match('/^[\d]+(\.[\d]+)?$/',$string)))
        throw new Exception('number not valid');
    return true;
}
// it test if a string is formed a valid text and retern true when its valid and allways true
function validate_text(string $text):bool {
    return true;
}
// it convert any given char in a string from a XXS potintial threat to harmless html entties
// and retern a string after convertion
function sanitize_xss(string $string):string {
    return htmlspecialchars($string);
    // return htmlentities($string);
    // return strip_tags($string);
}
// it convert any given char in a string from a SQL potintial threat to harmless SQL escaped quotes
// and retern a string after convertion and false if there is something wrong with mysqli connection object
function sanitize_sql(mysqli $connection, string $string):string|false {
    if(gettype($connection)=='object' && get_class($connection)=='mysqli')
        return $connection->real_escape_string($string);
    return false;
}
// it convert any given char in a string from a SQL potintial threat to harmless SQL escaped quotes
// and then 
// it convert any given char in a string from a XXS potintial threat to harmless html entties
// and retern a string after convertion and false if there is something wrong with mysqli connection object
function sanitize(mysqli $connection, string $string):string|false {
    $s= sanitize_sql($connection,$string);
    if($s) {
        return sanitize_xss($s);
    }
    return false;
}
// retrieve a string after remove SQL sanitization and keep XSS 
function retrieve_sanitize(?string $string):string {
    if(is_null($string))
        return '';
    return stripslashes($string);
}
// it convert a given string to a encrypted one and return it
function hash_string(string $string):string {
    return password_hash($string,PASSWORD_DEFAULT);
}
// verify if string and hash matched
function verify_hash_string(string $string, string $hash):string {
    return password_verify($string,$hash);
}
