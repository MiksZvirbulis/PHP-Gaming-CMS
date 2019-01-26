<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::isLoggedIn() AND isset($_POST['shout_id']) AND user::hasFlag("mod")){
	$db->delete("DELETE FROM `shoutbox` WHERE `id` = ?", array($_POST['shout_id']));
}else{
	exit;
}