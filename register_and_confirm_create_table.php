<?php // register_and_confirm_create_table.php


// SET UP THE DATA BASE TABLE FOR THE REGISTER-AND-CONFIRM PROCESS


// BRING IN OUR COMMON CODE
require_once('register_and_confirm_common.php');

// REMOVE THE OLD VERSION OF userTable
$res = $mysqli->query('DROP TABLE userTable');

// CREATING THE userTable
$sql
=
"
CREATE TABLE userTable
( email_address  VARCHAR(96) NOT NULL DEFAULT '' UNIQUE
, activate_code  VARCHAR(32) NOT NULL DEFAULT ''
, activated_yes  INT         NOT NULL DEFAULT 0
)
"
;
$res = $mysqli->query($sql);

// IF mysqli::query() RETURNS FALSE, LOG AND SHOW THE ERROR
if (!$res)
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

// ALL DONE - SHOW WHAT WE CREATED
$sql = "SHOW CREATE TABLE userTable";
$res = $mysqli->query($sql);
$row = $res->fetch_object($res);
echo "<pre>";
var_dump($row);
