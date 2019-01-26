<?php
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::isLoggedIn() AND isset($_POST['position']) AND isset($_POST['direction']) AND user::hasFlag("mod")){
	
}else{
	exit;
}