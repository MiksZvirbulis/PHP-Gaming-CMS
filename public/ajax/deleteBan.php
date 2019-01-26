<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::isLoggedIn() AND isset($_POST['bid']) AND user::hasFlag("other")){
	$find_ban = $db->fetch("SELECT `bid`, `ban_length` FROM `amx_bans` WHERE `bid` = ?", array($_POST['bid']));
	if(empty($find_ban)){
		echo "Bans netika atrasts!";
	}else{
		if($find_ban['ban_length'] < 0){
			echo "Bans jau ir noņemts!";
		}else{
			$db->update("UPDATE `amx_bans` SET `ban_length` = ? WHERE `bid` = ?", array(-1, $_POST['bid']));
			echo "success";
		}
	}
}else{
	exit;
}