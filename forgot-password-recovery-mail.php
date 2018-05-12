<?php // register_and_confirm.php
session_start();

// HANDLE THE REGISTER-AND-CONFIRM PROCESS
// NA FTIAKSOUME MIA OMORFH SELIDA GIA TO OTI ESTALEI CONFIRMATION MAIL KAI META ISWS NA KANEI REDIRECT STO MAIL PAGE

// BRING IN OUR COMMON CODE
require_once('register_and_confirm_common.php');


// PART ONE - IF THIS IS A POST-METHOD REQUEST FOR CONFIRMATION
if
(  (!empty($_GET["q"]))
&& (is_string($_GET["q"]))
&& (!empty($_SESSION['forgotMail']))
&& (is_string($_SESSION['forgotMail']))
)
{
    // PROVIDE FILTERING AND SANITY CHECKS FOR THE EXTERNAL INPUT
    $activate_code = preg_replace('/[^A-Z0-9]/i', '', $_GET["q"]);
    $activate_code = substr(trim($activate_code), 0, 32);
    $safe_activate_code = $mysqli->real_escape_string($activate_code);

    $email_address = strtolower($_SESSION['forgotMail']);
    $email_address = substr(trim($email_address), 0, 96);
    $safe_email_address = $mysqli->real_escape_string($email_address);

    // PREPATE AND RUN THE QUERY TO CONFIRM THE REGISTRATION
    $sql = "UPDATE userTable SET activated_yes = activated_yes + 1 WHERE activate_code = '$safe_activate_code' AND email_address = '$safe_email_address' LIMIT 1";
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

    // DID THE UPDATE AFFECT A ROW?
    if ( $mysqli->affected_rows ) die("THANK YOU - YOUR ACTIVATION IS COMPLETE");

    // SHOW ERROR RESPONSE
    die("SORRY - YOUR ACTIVATION CODE OR EMAIL ADDRESS WAS NOT FOUND");
}

/*
// PART TWO - IF THIS IS A GET-METHOD REQUEST FOR CONFIRMATION
if
(  (!empty($_GET["q"]))
&& (is_string($_GET["q"]))
&& (empty($_SESSION['forgotMail']))
)
{
    // PROVIDE FILTERING AND SANITY CHECKS FOR THE EXTERNAL INPUT
    $activate_code = preg_replace('/[^A-Z0-9]/i', '', $_GET["q"]);
    $activate_code = substr(trim($activate_code), 0, 32);
    $safe_activate_code = $mysqli->real_escape_string($activate_code);

    // GET THE EMAIL ADDRESS FROM THE ACTIVATION CODE
    $sql = "SELECT email_address FROM userTable WHERE activate_code = '$safe_activate_code' LIMIT 1";
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
    if ( $res->num_rows == 0 ) die("SORRY - YOUR ACTIVATION CODE WAS NOT FOUND");

    // SET UP THE EMAIL ADDRESS HINT - billy@gmail.com HINTS bill? gmail com
    $row = mysqli_fetch_assoc($res);
    $arr = explode('@', $row["email_address"]);
    $uid = $arr[0];
    $dmn = $arr[1];
    $len = strlen($dmn);
    $poz = strrpos($dmn, '.');
    $email_hint
    = substr($uid, 0, -1)
    .'?'
    . ' '
    . substr($dmn, 0, $poz)
    . ' '
    . end(explode('.', $dmn))
    ;

    // SHOW THE CONFIRMATION FORM WITH THE EMAIL ADDRESS HINT
    echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">' . PHP_EOL;
    echo 'TO CONFIRM REGISTRATION, ENTER YOUR EMAIL ADDRESS HERE:' . PHP_EOL;
    echo "<br/>HINT: IT LOOKS LIKE $email_hint" . PHP_EOL;
    echo '<input name="e" />' . PHP_EOL;
    echo '<input type="submit" />' . PHP_EOL;
    echo '</form>';
    die();
}
*/

// PART THREE - IF THE REGISTRATION FORM HAS BEEN POSTED
if
(  (!empty($_SESSION['forgotMail']))
&& (is_string($_SESSION['forgotMail']))
&& (empty($_GET))
)
{
    // VALIDATE THE EMAIL ADDRESS
    if (!check_valid_email($_SESSION['forgotMail'])) die("SORRY - THE EMAIL ADDRESS IS NOT USABLE");

    // NORMALIZE THE EMAIL ADDRESS
    $email_address = trim($_SESSION['forgotMail']);
    $email_address = strtolower($email_address);
    $safe_email_address = $mysqli->real_escape_string($email_address);

    // MAKE THE ACTIVATION CODE
    $activate_code
    = md5
    ( mt_rand()
    . time()
    . $email_address
    . $_SERVER["REMOTE_ADDR"]
    )
    ;
    $safe_activate_code = $mysqli->real_escape_string($activate_code);

    // INSERT THE EMAIL ADDRESS AND ACTIVATION CODE INTO THE DATA BASE TABLE
    $sql = "INSERT INTO userTable
    ( email_address
    , activate_code
    ) VALUES
    ( '$safe_email_address'
    , '$safe_activate_code'
    )"
    ;
    if (!$res = $mysqli->query($sql))
    {
        // IF ERROR, BUT NOT A DUPLICATE EMAIL ADDRESS
        if ( $mysqli->errno != 1062 )
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
        // IF A DUPLICATE REGISTRATION, RECOVER THE ACTIVATION CODE
        else
        {
            $sql = "SELECT activate_code FROM userTable WHERE email_address = '$safe_email_address' AND activated_yes = 1 LIMIT 1";
            $res = $mysqli->query($sql);
            $num = $res->num_rows;

            $row = $res->fetch_assoc();
            $activate_code = $row["activate_code"].$row["activate_code"];
        }
    }

    // SEND THE ACTIVATION EMAIL
    $msg = '';
    $msg .= 'TO RESET YOUR PASSWORD, PLEASE CLICK THIS LINK:' . PHP_EOL;
    $msg .= "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?q=$activate_code";
    mail
    ( $email_address
    , 'Click this link to reset your password!'
    , $msg
    )
    ;
    // TELL THE CLIENT TO CHECK HER EMAIL
    //die("PLEASE CHECK YOUR EMAIL FOR A CONFIRMATION LINK");
    echo "PLEASE CHECK YOUR EMAIL FOR A RESET PASSWORD LINK";
    header( "Refresh:3;url=index.html" );
}
