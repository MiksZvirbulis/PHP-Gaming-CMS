<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::isLoggedIn() AND isset($_POST['description'])){
	$errors = array();

	if(empty($_POST['description'])){
		$errors[] = "Tu aizmirsi ievadīt kļūdas aprakstu!";
	}

	if(count($errors) > 0){
		foreach($errors as $error){
			page::alert($error, "danger");
		}
	}else{
		$db->insert("INSERT INTO `reports` (`author_id`, `date`, `description`) VALUES (?, ?, ?)", array(
			$_SESSION['user_id'],
			time(),
			$_POST['description']
			));
		echo "success";
	}
}else{
	exit;
}