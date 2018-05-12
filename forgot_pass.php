<?php


require_once('register_and_confirm_common.php');

function validate_forgot() // checks if value of mail is filled or empty
{
    if(($_POST['email'] === ""))
    {
    	echo "Email is required!";
        return false;
    }
    echo "Success";
    return true;
}

if(!$_POST)
{
	die("Bye Motherfucker!!");
}

if(validate_forgot())
{
	if(!check_valid_email($_POST["email"])) // check if valid email
	{
		echo "Enter a valid E-mail!";
	}
}
//Checkarei an yparxei to mail sthn db
if(!empty($_POST["email"])){
		$conn = mysqli_connect("localhost", "root", "", "fasi");
		$condition = "";
		if(!empty($_POST["email"])) {
			if(!empty($condition)) {
				$condition = " and ";
			}
			$condition = " email = '" . $_POST["email"] . "'";
		}
		if(!empty($condition)) {
			$condition = " where " . $condition;
		}

		$sql = "Select * from register " . $condition;
		$result = mysqli_query($conn,$sql);
		$user = mysqli_fetch_array($result);
		
		if(!empty($user)) { // an yparxei steile mail
			$_SESSION['forgotMail'] = $_POST["email"];
			require_once("forgot-password-recovery-mail.php");
		} else {
			$error_message = 'No User Found';
		}
	}

?>