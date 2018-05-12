<?php
session_start();

require_once('register_and_confirm_common.php');

if(!$_POST)
{
	die("Bye Motherfucker!!");
}

$Password =(string)$_POST['password']; // passPhrase
$mail = (string)$_POST['email']; // email


$mailCheckFirstPart = preg_match('#[0-9a-zA-Z]#', $mail);
$mailCheckAt = preg_match('#[@.]#', $mail);
$mailCheckThirdPart = preg_match('#[0-9a-zA-Z]#', $mail);
$mailCheckDot = preg_match('#[\.]#', $mail);
$mailCheckEnd = preg_match('#[a-zA-Z]{2,4}#', $mail);
$passUpperCase = preg_match('#[A-Z]#', $Password);
$passLowerCase = preg_match('#[a-z]#', $Password);
$passNumber    = preg_match('#[0-9]#', $Password);
$passSpecial   = preg_match('#[\W]{2,}#', $Password); 
$passLength    = strlen($Password) >= 8;

if (!$mailCheckFirstPart || !$mailCheckAt || !$mailCheckThirdPart || !$mailCheckDot || !$mailCheckEnd)
{
	die("Wrong Username biatch<br>");
}
if(!$passUpperCase || !$passLowerCase || !$passNumber || !$passSpecial || !$passLength)
{
	die("Wrong Password biatch<br>");
}
$encryptPass = md5($Password);
$dbcon = new mysqli($server,$user,$pass,$db);
if($dbcon->connect_error)
{
	die("Error".$dbcon->connect_error);
}

$select = "SELECT * FROM register WHERE email = '$mail' AND passPhrase = '$encryptPass'";

$result = $dbcon -> query($select);

$row = $result -> fetch_assoc();

if($row['registerID'])
{
	$_SESSION['onomataki'] = $mail;	
	header( "Refresh:1;url=mainda.html" );
}
else
{
	echo "You are not registered";
	header("Refresh:1;url=index.html");
}
//handle not existing users either with php or js