<?php // register_and_confirm_admin.php


// ADMINISTRATOR SCRIPT FOR A VIEW OF THE REGISTER-AND-CONFIRM PROCESS


// BRING IN OUR COMMON CODE
require_once('register_and_confirm_common.php');

// SET THE URL OF THE CONFIRMATION PAGE HERE:
$confirm_page = $_SERVER["HTTP_HOST"] . '/register_and_confirm.php';


// GATHER INFORMATION ABOUT THE SUCCESSFUL REGISTRATIONS
$sql = "SELECT email_address FROM userTable WHERE activated_yes > 0 ORDER BY email_address";
if (!$res = $mysqli->query($sql))
{
    $err
    = "QUERY FAIL: "
    . $sql
    . ' ERRNO: '
    . $mysqli->errno
    . ' ERROR: '
    . $mysqli->error
    ;
    trigger_error($err, E_USER_ERROR);
}

// HOW MANY ARE CONFIRMED
$num = $res->num_rows;
echo "<br/>$num CONFIRMED REGISTRATIONS:" . PHP_EOL;

// SHOW WHO IS CONFIRMED
while ($row = $res->fetch_assoc($res))
{
    $email_address = $row["email_address"];
    $uri
    = '<a href="mailto:'
    . $email_address
    . '?subject=THANK YOU FOR YOUR REGISTRATION'
    . '">'
    . $email_address
    . '</a>'
    ;
    echo "<br>$uri" . PHP_EOL;
}


// GATHER INFORMATION ABOUT THE PENDING REGISTRATIONS
$sql = "SELECT email_address, activate_code FROM userTable WHERE activated_yes = 0 ORDER BY email_address";
if (!$res = $mysqli->query($sql))
{
    $err
    = "QUERY FAIL: "
    . $sql
    . ' ERRNO: '
    . $mysqli->errno
    . ' ERROR: '
    . $mysqli->error
    ;
    trigger_error($err, E_USER_ERROR);
}

// HOW MANY ARE NOT CONFIRMED
$num = $res->num_rows;
echo "<br/>$num REGISTRATIONS NOT YET CONFIRMED:" . PHP_EOL;

// SHOW WHO IS NOT CONFIRMED
while ($row = $res->fetch_assoc())
{
    $email_address = $row["email_address"];
    $activate_code = $row["activate_code"];

    // CONSTRUCT A CLICKABLE LINK TO RE-SEND THE ACTIVATION CODE
    $msg = '';
    $msg .= 'WE SEE YOU HAVE NOT CONFIRMED YOUR REGISTRATION YET.  TO CONFIRM, PLEASE CLICK THIS LINK: ' . PHP_EOL;
    $msg .= "http://" . $confirm_page . "?q=$activate_code";
    $msg .= PHP_EOL;
    $uri
    = '<a href="mailto:'
    . $email_address
    . '?subject=PLEASE CONFIRM REGISTRATION'
    . '&body='
    . $msg
    . '">'
    . $email_address
    . '</a>'
    ;
    echo "<br/>$uri" . PHP_EOL;
}
