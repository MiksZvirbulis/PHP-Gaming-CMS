<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::hasUnreadMessages() == true){
	echo "yes";
}