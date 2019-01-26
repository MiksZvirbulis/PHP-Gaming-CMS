<?php
ob_start();
if(!defined("IN_SYSTEM")) die("Leave, please!");
ini_set("date.timezone", "Europe/Riga");

# Iesākam skaitīt ielādes laiku
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

$c = array(); # Konfigurācija kā masīvs

# Iestatījumi un Informācija
$c['page']['debug'] = true;
$c['page']['title'] = "ETERNAL.LV";
$c['page']['default'] = "news";
$c['page']['user_pages'] = array("settings", "messages", "logout", "vshop");
$c['page']['banned_ip'] = array("");
$c['page']['shouter_id'] = 0; # Lietotāja ID no kura tiks ievadīti čata ieraksti no publiskajām darbībām
$c['twitter_name'] = "poise_lv";
$c['tweet_count'] = 5;

# Automatizēti iestatījumi - NEAIZTIKT
$c['dir'] = $_SERVER['DOCUMENT_ROOT'] . "/";
$c['url'] = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'];
$c['full_url'] = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

# Datubāzes Informācija
$c['sql']['host'] = "localhost";
$c['sql']['username'] = "";
$c['sql']['password'] = "";
$c['sql']['database'] = "";

# Debug, Kļūdu Uzrādīšana
if($c['page']['debug'] === true){
	error_reporting(E_ALL | E_STRICT);
	ini_set("display_errors", 1);
}else{
	error_reporting(0);
	ini_set("display_errors", 0);
}

$servers = array(
	'dd2' => array(
		"type" => "cs16",
		"ip" => "dd2.eternal.lv",
		"port" => "27015" 
	),
	'zm' => array(
		"type" => "cs16",
		"ip" => "zm.eternal.lv",
		"port" => "27015" 
	),
	'zm2' => array(
		"type" => "cs16",
		"ip" => "zm2.eternal.lv",
		"port" => "27015" 
	),
	'war3' => array(
		"type" => "cs16",
		"ip" => "war3.eternal.lv",
		"port" => "27015" 
	),
	'cs' => array(
		"type" => "cs16",
		"ip" => "cs.eternal.lv",
		"port" => "27015" 
	),
	'csgo' => array(
		"type" => "csgo",
		"ip" => "csgo.eternal.lv",
		"port" => "27015" 
	)
);

# Twitter Iestatījumi
$twitter_settings = array(
	"twitter_name" => "poise_lv",
	"oauth_access_token" => "3062292519-S40nGVgicNVsyCzm5aUQOBvLyFc7JmJsDliwgiM",
	"oauth_access_token_secret" => "FnGrm7vGWL8OpnzwYh8oEuEcAGf8SkyfsfdrV4UtI8WEO",
	"consumer_key" => "kazPDFItLHfvBKKUwtj5Riu8s",
	"consumer_secret" => "UQVF3OmrnGU99KoNvKF2zMMLDya62TEjt5VF4mWo769r38UFIj"
	);

# Klases
require $c['dir'] . "/system/classes/db.class.php";
require $c['dir'] . "/system/classes/geo.class.php";
require $c['dir'] . "/system/classes/suncore.class.php";
require $c['dir'] . "/system/classes/gameq.class.php";
require $c['dir'] . "/system/classes/twitter.class.php";
require $c['dir'] . "/system/classes/user.class.php";
require $c['dir'] . "/system/classes/forum.class.php";
require $c['dir'] . "/system/classes/text.class.php";
require $c['dir'] . "/system/classes/addons.class.php";
require $c['dir'] . "/system/classes/page.class.php";

# Klašu Mainīgie
$db = new db($c['sql']['host'], $c['sql']['username'], $c['sql']['password'], $c['sql']['database']);
$geo = new geo();
$userD = new user();