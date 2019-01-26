<?php
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(isset($_POST['id'])){
	$find_rules = $db->fetch("SELECT `content` FROM `rules` WHERE `id` = ?", array($_POST['id']));
	if(!empty($find_rules)){
		echo '<div class="well">' . text::bbcode($find_rules['content'], array("bbcode" => true, "media" => false)) . '</div>';
	}
}else{
	exit;
}