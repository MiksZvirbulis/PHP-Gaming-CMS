<?php
class user{

	protected $user_id;

	public function __get($key){
		global $db;
		$user_id = (isset($this->user_id)) ? $this->user_id : $_SESSION['user_id'];
		$request = $db->fetch("SELECT `$key` FROM `users` WHERE `user_id` = ?", array($user_id));
		return $request[$key];
	}

	public function setUserID($user_id){
		$this->user_id = $user_id;
	}

	/*
	SALT Ģenerēšana
	$length = SALT garums
	$strength = SALT stiprums - vājākais 0, spēcīgākais virs 8
	*/
	public static function generateSalt($length = 9, $strength = 0){
		$vowels = "aeuy";
		$consonants = "bdghjmnpqrstvz";
		if($strength >= 1){
			$consonants .= "BDGHJLMNPQRSTVWXZ";
		}
		if($strength >= 2){
			$vowels .= "AEUY";
		}
		if($strength >= 4){
			$consonants .= "23456789";
		}
		if($strength >= 8){
			$consonants .= "@#$%:[];";
		}
		$password = "";
		$alt = time() % 2;
		for($i = 0; $i < $length; $i++){
			if($alt == 1){
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			}else{
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

	/*
	Paroles apstrādāšana un kriptēšana
	$password = ievadītā parole
	$new = true/false - jauna parole/pastāvoša parole
	$current - vērtība/false - pastāvošs SALT/SALT neeksistē
	*/
	public static function hashPassword($password, $new = false, $current = false){
		if($new){
			$salt = self::generateSalt(10, 8);
		}else{
			$salt = $current;
		}
		$pHash = md5(md5(md5($password) . $salt) . $salt);
		return array("hash" => $pHash, "salt" => $salt);
	}

	/*
	Pārbaude vai klients ir autorizēts
	Izvadīs - true vai false
	*/
	public static function isLoggedIn(){
		global $db;
		if(isset($_SESSION['user_id']) AND isset($_SESSION['password'])){
			if((int)$_SESSION['user_id'] > 0 AND strlen($_SESSION['password']) == 32){
				$user_id = $_SESSION['user_id'];
				$result = $db->fetch("SELECT `password` FROM `users` WHERE `user_id` = ? AND `blocked` = 0", array($user_id));
				$return = $result['password'] == $_SESSION['password'] ? true : false;
			}else{
				$return = false;
			}
		}else{
			$return = false;
		}
		return $return;
	}

	/*
	E-pasta adreses validācija
	$email = e-pasta adrese
	*/
	public static function emailIsValid($email){
		return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email);
	}

	/*
	Lietotājvārda validācija
	$email = lietotājvārds
	*/
	public static function usernameIsValid($username){
		return preg_match("/^[a-zA-Z0-9\_]*$/", $username);
	}

	/*
	Dzimuma uzrādīšana
	$user_id = lietotāja ID, kura dzimums jāuzrāda
	*/
	public static function returnGender($user_id){
		global $db;
		$result = $db->fetch("SELECT `gender` FROM `users` WHERE `user_id` = ?", array($user_id));
		switch($result['gender']){
			case "v":
			$gender = "Vīrietis";
			break;
			case "s":
			$gender = "Sieviete";
			break;
			default:
			$gender = "Nav norādīts";
			break;
		}
		return $gender;
	}

	/*
	Lietotāja informācijas iegūšana
	$user_id = lietotāja ID, kura informācija tiek pieprasīta
	*/
	public static function data($user_id){
		global $db;
		return $db->fetch("SELECT * FROM `users` WHERE `user_id` = ?", array($user_id));
	}

	/*
	Lietotāja grupas noteikšana
	$user_id = lietotāja ID, kura grupa tiks noteikta. Pēc noklusējuma, tiks noteikts autorizētā lietotāja ID
	*/
	public static function isAdmin($user_id = user_id){
		if(self::isLoggedIn()){
			global $db;
			$find_admin = $db->count("SELECT `user_id` FROM `users` WHERE `user_id` = ? AND `administrator` = ?", array($user_id, 1));
			if($find_admin == 0){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	
	public static function returnAvatar($user_id, $fancybox = false, $height = 200, $width = 200, $middle = true){
		global $db;
		global $c;
		$user = self::data($user_id);
		$avatar_type = $user['avatar_type'];
		switch($avatar_type){
			case "default":
			$avatar = "default-avatar.png";
			$avatar_url = $c['url'] . "/uploads/profile/";
			break;
			case "custom":
			$avatar = $user['custom_avatar'];
			$avatar_url = $c['url'] . "/uploads/profile/";
			break;
			case "gravatar":
			$avatar = "http://gravatar.com/avatar/" . md5($user['email']) . "?s=" . $height;
			$avatar_url = "";
			break;
			default:
			$avatar = "default-avatar.png";
			$avatar_url = $c['url'] . "/uploads/profile/";
			break;
		}
		if($fancybox === true AND $middle === true){
			return '<a class="fancybox" href="' . $avatar_url . $avatar . '"><img class="avatar" src="' . $avatar_url . $avatar . '" height="' . $height . 'px" width="' . $width . 'px" class="middle"></a>';
		}elseif($fancybox === false AND $middle === true){
			return '<img class="avatar" src="' . $avatar_url . $avatar . '" height="' . $height . 'px" width="' . $width . 'px" class="middle">';
		}else{
			return '<img class="avatar" src="' . $avatar_url . $avatar . '" height="' . $height . 'px" width="' . $width . 'px">';
		}
	}

	public static function generateSessionID(){
		return md5($_SERVER['REMOTE_ADDR'] . time());
	}

	public static function sessionExists(){
		global $db;
		// Pārbaudam vai sesija eksistē!
		if(isset($_SESSION['session_id']) AND strlen($_SESSION['session_id']) == 32){
			$session_id = $_SESSION['session_id'];
			$find_session = $db->count("SELECT `session_id` FROM `sessions` WHERE `session_id` = ?", array($session_id));
			if($find_session == 0){
				self::createSession($session_id);
			}else{
				self::updateSession($session_id);
			}
		}else{
			self::createSession();
		}
		$sessions = $db->fetchAll("SELECT `session_id`, `session_start_time`, `last_action_time`, `user_id` FROM `sessions`");
		foreach($sessions as $session){
			$inactive = time() - $session['last_action_time'];
			if($inactive >= (15 * 60)){
				if($session['user_id'] > 0){
					$interval = abs($session['session_start_time'] - $session['last_action_time']);
					$online_time = round($interval / 60);
					$db->update("UPDATE `users` SET `online_time` = `online_time` + ? WHERE `user_id` = ?", array($online_time, $session['user_id']));
				}
				$db->delete("DELETE FROM `sessions` WHERE `session_id` = ?", array($session['session_id']));
			}
		}
	}

	public static function updateSession($session_id){
		global $path;
		global $db;
		// Atjaunojam sesijas informāciju, dzēšam, ja lietotājs nav aktīvs
		if(self::isLoggedIn()){
			$user_id = $_SESSION['user_id'];
		}else{
			$user_id = 0;
		}
		$path_1 = $path[1];
		$path_2 = isset($path[2]) ? $path[2] : "";
		$path_3 = isset($path[3]) ? $path[3] : "";
		$path_4 = isset($path[4]) ? $path[4] : "";
		$path_5 = isset($path[5]) ? $path[5] : "";
		$db->update("UPDATE `sessions` SET `user_id` = ?, `ip_address` = ?, `last_action_time` = ?, `path_1` = ?, `path_2` = ?, `path_3` = ?, `path_4` = ?, `path_5` = ? WHERE `session_id` = ?", array(
			$user_id,
			self::getIP(),
			time(),
			$path_1,
			$path_2,
			$path_3,
			$path_4,
			$path_5,
			$session_id
			));
	}

	public static function createSession($session_id = false){
		global $db;
		// Izveidojam sesiju
		if($session_id === false){
			// Izveidojam jaunu sesijas ID
			$session_id = self::generateSessionID();
			$_SESSION['session_id'] = $session_id;
		}else{
			// Izmantojam norādīto sesijas ID
			$session_id = $session_id;
		}
		if(self::isLoggedIn()){
			$user_id = $_SESSION['user_id'];
		}else{
			$user_id = 0;
		}
		if($user_id == 0){
			$find_ip_address = $db->count("SELECT `session_id` FROM `sessions` WHERE `ip_address` = ?", array(self::getIP()));
			if($find_ip_address == 0){
				$insert = true;
			}
		}else{
			$find_user = $db->count("SELECT `session_id` FROM `sessions` WHERE `user_id` = ?", array($user_id));
			if($find_user == 0){
				$insert = true;
			}
		}
		if(isset($insert) AND $insert === true){
			$db->insert("INSERT INTO `sessions` (`session_id`, `user_id`, `session_start_time`, `last_action_time`) VALUES (?, ?, ?, ?)", array(
				$session_id,
				$user_id,
				time(),
				time()
				));
		}
		self::updateSession($session_id);
	}

	public static function isOnline($user_id){
		global $db;
		$find_user = $db->count("SELECT `session_id` FROM `sessions` WHERE `user_id` = ?", array($user_id));
		if($find_user == 0){
			return false;
		}else{
			return true;
		}
	}

	public static function getIP(){
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		if(filter_var($client, FILTER_VALIDATE_IP)){
			$ip_address = $client;
		}elseif(filter_var($forward, FILTER_VALIDATE_IP)){
			$ip_address = $forward;
		}else{
			$ip_address = $remote;
		}
		return $ip_address;
	}

	public static function formatName($user_id, $format = true, $link = false, $colours = true, $limit = false){
		global $db;
		global $c;
		$user = $db->fetch("SELECT `prefix`, `suffix`, `seo_name`, `display_name`, `icon`, `group_id` FROM `users` WHERE `user_id` = ?", array($user_id));
		$display_name = ($limit === false) ? $user['display_name'] : text::limit($user['display_name'], $limit);
		if($format === true){
			$group = $db->fetch("SELECT `prefix`, `suffix` FROM `groups` WHERE `group_id` = ?", array($user['group_id']));
			$name = $group['prefix'] . $user['prefix'] . $display_name . $user['suffix'] . $group['suffix'];
			if(!empty($user['icon']) AND $colours === true){
				$icon = $c['url'] . "/assets/images/icons/user/" . $user['icon'];
				$name = '<img class="icon" src="' . $icon . '">' .  $group['prefix'] . $user['prefix'] . $display_name . $user['suffix'] . $group['suffix'];
			}elseif(!empty($user['icon']) AND $colours === false){
				$icon = $c['url'] . "/assets/images/icons/user/" . $user['icon'];
				$name = '<img class="icon" src="' . $icon . '">' .  $display_name;
			}elseif(empty($user['icon']) AND $colours === true){
				$name = $group['prefix'] . $user['prefix'] . $display_name . $user['suffix'] . $group['suffix'];
			}else{
				$name = $display_name;
			}
		}else{
			$name = $display_name;
		}
		if($link === true){
			$user_link = $c['url'] . "/user/" . $user_id . "-" . $user['seo_name'] . "/";
			$format = '<a href="' . $user_link . '">' . $name . '</a>';
		}else{
			$format = $name;
		}
		return $format;
	}

	public static function returnAge($user_id){
		$user = self::data($user_id);
		$date = new DateTime(str_replace("/", "-", $user['birthday']));
		$now = new DateTime();
		$interval = $now->diff($date);
		return $interval->y;
	}

	public static function returnGroup($user_id){
		global $db;
		$user = self::data($user_id);
		$group = $db->fetch("SELECT `prefix`, `description`, `suffix` FROM `groups` WHERE `group_id` = ?", array($user['group_id']));
		return $group['prefix'] . $group['description'] . $group['suffix'];
	}

	public static function hasFlag($flag){
		if(self::isLoggedIn()){
			global $db;
			$user = self::data($_SESSION['user_id']);
			$group = $db->fetch("SELECT `$flag` FROM `groups` WHERE `group_id` = ?", array($user['group_id']));
			return ($group[$flag] == 1) ? true : false;
		}else{
			return false;
		}
	}

	public static function returnStatistics($user_id, $type){
		global $db;
		$total = 0;
		if($type == "posts"){
			$topics = $db->count("SELECT `tid` FROM `topics` WHERE `author_id` = ? AND `approved` = 1", array($user_id));
			$posts = $db->count("SELECT `pid` FROM `posts` WHERE `author_id` = ? AND `approved` = 1", array($user_id));
			$comments = $db->count("SELECT `id` FROM `comments` WHERE `author_id` = ?", array($user_id));
			$total = $topics + $posts + $comments;
		}
		return $total;
	}

	public static function getMoney($user_id){
		global $db;
		$fetch = $db->fetch("SELECT `money` FROM `users` WHERE `user_id` = ?", array($user_id));
		return $fetch['money'];
	}

	public static function addMoney($user_id, $amount){
		global $db;
		$db->update("UPDATE `users` SET `money` = `money` + ? WHERE `user_id` = ?", array($amount, $user_id));
	}

	public static function deductMoney($user_id, $amount){
		global $db;
		$db->update("UPDATE `users` SET `money` = `money` - ? WHERE `user_id` = ?", array($amount, $user_id));
	}

	public static function checkMoney($user_id, $amount){
		if(self::getMoney($user_id) < $amount){
			return false;
		}else{
			return true;
		}
	}

	public static function hasUnreadMessages(){
		if(self::isLoggedIn()){
			global $db;
			$conversations = $db->fetchAll("SELECT `id` FROM `conversations` WHERE `author_id` = ? OR `participants` = ? ORDER BY `last_activity` DESC", array($_SESSION['user_id'], $_SESSION['user_id']));
			$count = 0;
			foreach($conversations as $conversation){
				$count += $db->count("SELECT `message_id` FROM `messages` WHERE `conversation_id` = ? AND `author_id` != ? AND `read_by_receiver` = 0", array($conversation['id'], $_SESSION['user_id']));
			}
			return ($count > 0) ? true : false;
		}else{
			return false;
		}
	}

	public static function returnAction($user_id){
		global $db;
		global $c;
		$session = $db->fetch("SELECT * FROM `sessions` WHERE `user_id` = ?", array($user_id));
		if($session['path_1'] == "messages"){
			$url = false;
			$action = "Lasa vēstules";
		}elseif($session['path_1'] == "forum" AND empty($session['path_2'])){
			$url = "forum";
			$action = "Apskata forumu";
		}elseif($session['path_1'] == "forum" AND $session['path_2'] == "topic" AND !empty($session['path_3'])){
			$url = "forum/topic/" . $session['path_3'];
			$action = "Lasa tēmu";
		}elseif($session['path_1'] == "news" AND empty($session['path_2'])){
			$url = "news";
			$action = "Apskata sākumlapu";
		}elseif($session['path_1'] == "news" AND !empty($session['path_2'])){
			$url = "news/" . $session['path_2'];
			$action = "Lasa jaunumus";
		}elseif($session['path_1'] == "users"){
			$url = "users";
			$action = "Apskata lietotājus";
		}elseif($session['path_1'] == "user" AND !empty($session['path_2'])){
			$url = "user/" . $session['path_2'];
			$action = "Apskata lietotāju";
		}elseif($session['path_1'] == "shop"){
			$url = "shop";
			$action = "Iepērkas veikalā";
		}elseif($session['path_1'] == "videos" AND empty($session['path_2'])){
			$url = "videos";
			$action = "Apskata video sarakstu";
		}elseif($session['path_1'] == "videos" AND $session['path_2'] == "view" AND !empty($session['path_3'])){
			$url = "videos/view/" . $session['path_3'];
			$action = "Apskata video";
		}elseif($session['path_1'] == "amx"){
			$url = "amx";
			$action = "Darbojas ar AMX";
		}elseif($session['path_1'] == "about"){
			$url = "about";
			$action = "Lasa par POISE";
		}elseif($session['path_1'] == "rules"){
			$url = "rules";
			$action = "Lasa noteikumus";
		}elseif($session['path_1'] == "settings"){
			$url = false;
			$action = "Konfigurē savu profilu";
		}else{
			$url = false;
			$action = "Atpūtina pirkstus";
		}

		if($url === false){
			return $action;
		}else{
			return '<a href="' . $c['url'] . '/' . $url . '">' . $action . '</a>';
		}
	}

	public static function returnWarning($user_id){
		global $db;
		$total = $db->fetch("SELECT SUM(`points`) AS `total_warnings` FROM `warning_points` WHERE `user_id` = ?", array($user_id));
		$unwarned = 10 - $total['total_warnings'];
		$output = "";
		if(self::hasFlag("mod") OR user::isLoggedIn() AND ($user_id == $_SESSION['user_id'])){
			$output = '<span id="warnings" data-user="' . $user_id . '">';
		}
		for($i = 0; $i < $total['total_warnings']; $i++){
			$output .= '<span class="warn warned"></span>';
		}
		for($i = 0; $i < $unwarned; $i++){
			$output .= '<span class="warn"></span>';
		}
		if(self::hasFlag("mod")){
			$output .= '</span>';
		}
		return $output;
	}

	public static function hasForumMod($forum_id){
		global $db;
		if(self::isLoggedIn()){
			$group = $db->fetch("SELECT `group_id` FROM `users` WHERE `user_id` = ?", array($_SESSION['user_id']));
			$forum = $db->fetch("SELECT `mod_groups` FROM `forums` WHERE `id` = ?", array($forum_id));
			$mod_groups = explode(",", $forum['mod_groups']);
			if(in_array($group['group_id'], $mod_groups)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}