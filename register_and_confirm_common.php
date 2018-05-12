<?php // register_and_confirm_common.php


// COMMON CODE AVAILABLE TO ALL OUR REGISTER-AND-CONFIRM SCRIPTS


// SET ERROR REPORTING SO WE CAN DEBUG OUR SCRIPTS EASILY
error_reporting(E_ALL);

// CONNECTION AND SELECTION VARIABLES FOR THE DATABASE
//db credentials
$server = "localhost";
$user = "root";
$pass = "";
$db = "fasi";

// OPEN A CONNECTION TO THE DATA BASE SERVER AND SELECT THE DB
$mysqli = new mysqli($server, $user, $pass, $db);

// DID THE CONNECT/SELECT WORK OR FAIL?
if ($mysqli->connect_errno)
{
    $err
    = "CONNECT FAIL: "
    . $mysqli->connect_errno
    . ' '
    . $mysqli->connect_error
    ;
    trigger_error($err, E_USER_ERROR);
}


// AN EMAIL VALIDATION SCRIPT THAT RETURNS TRUE OR FALSE
function check_valid_email($email)
{
    // IF PHP 5.2 OR ABOVE, WE CAN USE THE FILTER
    // MAN PAGE: http://php.net/manual/en/intro.filter.php
    if (strnatcmp(phpversion(),'5.2') >= 0)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) return FALSE;
    }
    // IF LOWER-LEVEL PHP, WE CAN CONSTRUCT A REGULAR EXPRESSION
    else
    {
        $regex
        = '/'                       // START REGEX DELIMITER
        . '^'                       // START STRING
        . '[A-Z0-9_-]'              // AN EMAIL - SOME CHARACTER(S)
        . '[A-Z0-9._-]*'            // AN EMAIL - SOME CHARACTER(S) PERMITS DOT
        . '@'                       // A SINGLE AT-SIGN
        . '([A-Z0-9][A-Z0-9-]*\.)+' // A DOMAIN NAME PERMITS DOT, ENDS DOT
        . '[A-Z\.]'                 // A TOP-LEVEL DOMAIN PERMITS DOT
        . '{2,6}'                   // TLD LENGTH >= 2 AND =< 6
        . '$'                       // ENDOF STRING
        . '/'                       // ENDOF REGEX DELIMITER
        . 'i'                       // CASE INSENSITIVE
        ;
        if (!preg_match($regex, $email)) return FALSE;
    }

    // FILTER or PREG DOES NOT TEST IF THE DOMAIN OF THE EMAIL ADDRESS IS ROUTABLE
    $domain = explode('@', $email);
    if ( checkdnsrr($domain[1],"MX") || checkdnsrr($domain[1],"A") ) return TRUE;

    // EMAIL NOT ROUTABLE
    return FALSE;
}
