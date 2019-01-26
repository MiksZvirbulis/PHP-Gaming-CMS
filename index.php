<?php
ob_start();
session_start();
define("IN_SYSTEM", true);
include "system/config.inc.php";
$path = explode("/", $_SERVER['REQUEST_URI']);

if(in_array(user::getIP(), $c['page']['banned_ip'])){
	die("fuck off");
	exit;
}

if(user::isLoggedIn()){
	$test = $db->fetch("SELECT `blocked` FROM `users` WHERE `user_id` = ?", array($_SESSION['user_id']));
	if($test['blocked'] == 1){
		die("fuck off");
		exit;
	}
}

if(empty($path[1])){
	$path[1] = $c['page']['default'];
}

if($path[1] == "acp"){
	if(!user::hasFlag("mod")){
		$path[1] = $c['page']['default'];
	}
}

if(in_array($path[1], $c['page']['user_pages'])){
	if(!user::isLoggedIn()){
		$path[1] = $c['page']['default'];
	}
}

$find_page = $db->count("SELECT `id` FROM `pages` WHERE `link` = ?", array($path[1]));
if($find_page == 0){
	if(file_exists("public/" . $path[1] . ".php")){
		include "public/" . $path[1] . ".php";
	}else{
		include "public/" . $c['page']['default'] . ".php";
	}
}else{
	$content = $db->fetch("SELECT * FROM `pages` WHERE `link` = ?", array($path[1]));
	$page = new page($content['title'], array(), ($content['side'] == 1) ? true : false);
	print($content['content']);
}