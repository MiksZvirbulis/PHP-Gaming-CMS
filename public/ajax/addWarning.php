<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(user::hasFlag("mod") AND isset($_POST['user_id']) AND isset($_POST['points']) AND isset($_POST['reason'])){
	$errors = array();
	if(!empty($_POST['user_id'])){
		$find_user = $db->count("SELECT `user_id` FROM `users` WHERE `user_id` = ? AND `user_id` != ?", array($_POST['user_id'], $_SESSION['user_id']));
		if($find_user == 0){
			$errors[] = "Lietotājs netika atrasts vai arī Tu mēģini brīdināt pats sevi!";
		}
	}

	if($_POST['points'] == ""){
		$errors[] = "Tu aizmirsi ievadīt brīdinājumu punktu skaitu!";
	}else{
		$points = $db->fetch("SELECT SUM(`points`) AS `total_warnings` FROM `warning_points` WHERE `user_id` = ?", array($_POST['user_id']));
		$new_points = $points['total_warnings'] + $_POST['points'];
		if($_POST['points'] > 10){
			$errors[] = "Punktu skaits nedrīkst pārsniegt 10!";
		}elseif($_POST['points'] == "0"){
			$errors[] = "Punktu skaitam ir jābūt vai nu virs vai zem 0!";
		}elseif($_POST['points'] > 10){
			$errors[] = "Punktu skaits nedrīkst pārsniegt -10!";
		}elseif(!is_numeric($_POST['points'])){
			$errors[] = "Punktu skaitam jābūt ciparam!";
		}elseif($new_points > 10){
			$errors[] = "Brīdinājumu līmenis nedrīkst pārsniegt 10!";
		}elseif($new_points < 0){
			$errors[] = "Brīdinājumu līmenis nedrīkst būt zemāks par 0!";
		}
	}

	if(empty($_POST['reason'])){
		$errors[] = "Tu aizmirsi ievadīt iemeslu!";
	}

	if(count($errors) > 0){
		foreach($errors as $error){
			page::alert($error, "danger");
		}
	}else{
		$db->insert("INSERT INTO `warning_points` (`points`, `author_id`, `user_id`, `reason`, `date`) VALUES (?, ?, ?, ?, ?)", array(
			$_POST['points'],
			$_SESSION['user_id'],
			$_POST['user_id'],
			$_POST['reason'],
			time()
			));
		echo "success";
	}
}else{
	exit;
}