<?php
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";

$information = $db->fetchAll("SELECT * FROM `information` ORDER BY RAND() LIMIT 1");

foreach($information as $info){
	$return[$info['id']]['Text'] = text::bbcode($info['text'], array("bbcode" => true, "emoticons" => true));
	$return[$info['id']]['Author'] = user::formatName($info['author_id'], false, true);
	$return[$info['id']]['Updated'] = page::formatTime($info['updated']);
}

echo json_encode($return, JSON_UNESCAPED_UNICODE);