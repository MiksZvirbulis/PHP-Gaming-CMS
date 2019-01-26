<?php
# Update online time and session
$session = $db->fetch("SELECT `session_start_time`, `last_action_time` FROM `sessions` WHERE `session_id` = ?", array($_SESSION['session_id']));
$interval = abs($session['session_start_time'] - $session['last_action_time']);
$online_time = round($interval / 60);
$db->update("UPDATE `users` SET `online_time` = `online_time` + ? WHERE `user_id` = ?", array($online_time, $_SESSION['user_id']));
$db->update("UPDATE `sessions` SET `session_start_time` = ? WHERE `session_id` = ?", array(time(), $_SESSION['session_id']));

# Continue logout

unset($_SESSION['user_id']);
unset($_SESSION['password']);

if(isset($_SERVER['HTTP_REFERER'])){
	header("Location: " . $_SERVER['HTTP_REFERER']);
}else{
	header("Location: " . $c['url'] . "/" . $c['page']['default']);
}