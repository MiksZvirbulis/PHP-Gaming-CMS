<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::isLoggedIn() AND isset($_POST['shout'])){
	$errors = array();
	$shout = text::stripBBCode($_POST['shout']);
	if(empty($shout)){
		$errors[] = "Tu aizmirsi ievadīt ziņas tekstu!";
	}else{
		$find_shout = $db->fetch("SELECT `time` FROM `shoutbox` WHERE `user_id` = ? ORDER BY `time` DESC", array($_SESSION['user_id']));
		$sinceLastShout = time() - $find_shout['time'];
		if($sinceLastShout <= 5 AND !user::hasFlag("mod")){
			$errors[] = "Atslābsti. Raksti lēnāk, čata ziņu intervāls ir 5 sekundes!";
		}
		if(strlen($shout) > 500){
			$errors[] = "Ierobežo sevi. Čata ziņa nedrīkst būt garāka par 500 rakastzīmēm!";
		}
	}

	if(count($errors) > 0){
		foreach($errors as $error){
			echo $error . "<br />";
		}
	}else{
		$db->insert("INSERT INTO `shoutbox`
			(
				`user_id`,
				`ip_address`,
				`shout`, 
				`time`
				) VALUES (?, ?, ?, ?)
		", array(
			$_SESSION['user_id'],
			user::getIP(),
			$shout,
			time()
			));
		print("success");
	}
}else{
	exit;
}