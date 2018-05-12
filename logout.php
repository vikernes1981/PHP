<?php
session_start();
if(!$_SESSION['onomataki'])
{
	echo "OK";
	die();
}
session_unset();
session_destroy();

?>