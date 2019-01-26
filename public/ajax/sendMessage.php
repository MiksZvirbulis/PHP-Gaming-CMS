<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::isLoggedIn() AND isset($_POST['message']) AND isset($_POST['receiver_id']) AND isset($_POST['title'])){
	$errors = array();
	if(!empty($_POST['receiver_id'])){
		$find_user = $db->count("SELECT `user_id` FROM `users` WHERE `user_id` = ? AND `user_id` != ?", array($_POST['receiver_id'], $_SESSION['user_id']));
		if($find_user == 0){
			$errors[] = "Lietotājs netika atrasts vai arī tiek mēģināts nosūtīt ziņu sev!";
		}
	}

	if(empty($_POST['title'])){
		$errors[] = "Tu aizmirsi ievadīt ziņas tematu!";
	}else{
		if(strlen($_POST['title']) < 3){
			$errors[] = "Ziņas tematam jābūt vismaz 3 rakstzīmju garumā!";
		}elseif(strlen($_POST['title']) > 55){
			$errors[] = "Ziņas temats nedrīkst būt garāks par 55 rakstzīmēm!";
		}
	}

	if(empty($_POST['message'])){
		$errors[] = "Tu aizmirsi ievadīt ziņas saturu!";
	}

	if(count($errors) > 0){
		foreach($errors as $error){
			page::alert($error, "danger");
		}
	}else{
		$db->insert("INSERT INTO `conversations` (`title`, `title_seo`, `author_id`, `date`, `last_activity`, `participants`) VALUES (?, ?, ?, ?, ?, ?)", array(
			$_POST['title'],
			text::seostring($_POST['title']),
			$_SESSION['user_id'],
			time(),
			time(),
			$_POST['receiver_id']
			));
		$conversation = $db->fetch("SELECT `id` FROM `conversations` WHERE `author_id` = ? ORDER BY `date` DESC LIMIT 1", array($_SESSION['user_id']));
		$db->insert("INSERT INTO `messages` (`conversation_id`, `author_id`, `date`, `message`) VALUES (?, ?, ?, ?)", array(
			$conversation['id'],
			$_SESSION['user_id'],
			time(),
			$_POST['message']
			));
		echo "success";
	}
}else{
	exit;
}