<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(isset($_POST['admin_id']) AND isset($_POST['flag']) AND isset($_POST['reason']) AND isset($_POST['link']) AND user::hasFlag("other")){
	$errors = array();

	$check_admin = $db->fetch("SELECT `id`, `access`, `username` FROM `amx_amxadmins` WHERE `id` = ?", array((int)$_POST['admin_id']));

	if(empty($check_admin)){
		$errors[] = "Admins netika atrasts!";
	}else{
		if(empty($_POST['flag'])){
			$errors[] = "Tu aizmirsi ievadīt pieejas flagu, kuru vēlies noņemt!";
		}else{
			if(strlen($_POST['flag']) != 1){
				$errors[] = "Ievadi tikai vienu pieejas flagu!";
			}else{
				if(strpos($check_admin['access'], strtolower($_POST['flag'])) === false){
					$errors[] = "Šim adminam nav pieejas pie pieprasītā pieejas flaga!";
				}
			}
		}

		if(empty($_POST['reason'])){
			$errors[] = "Tu aizmirsi ievadīt iemeslu!";
		}

		if(empty($_POST['link'])){
			$errors[] = "Tu aizmirsi ievadīt saiti!";
		}
	}

	if(count($errors) > 0){
		foreach($errors as $error){
			echo page::alert($error, "danger");
		}
	}else{
		$db->insert("INSERT INTO `admin_edit_log` (`author_id`, `time`, `admin_id`, `admin_nickname`, `old_flags`, `flag`, `reason`, `link`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", array(
			$_SESSION['user_id'],
			time(),
			$_POST['admin_id'],
			$check_admin['username'],
			$check_admin['access'],
			strtolower($_POST['flag']),
			$_POST['reason'],
			$_POST['link']
			));
		echo page::alert("Admina rediģēšanas pieprasījums veiksmīgi izsūtīts. Gaidi administratora apstiprinājumu!", "success");
	}
}else{
	exit;
}