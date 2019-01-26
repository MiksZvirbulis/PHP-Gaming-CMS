<?php
class page{
	var $page;
	var $side;

	public $_breadcrumbs = array();

	# Constructing

	public function __construct($page_name, $breadcrumbs = array(), $show_side = true){
		global $c;
		global $db;
		global $path;
		global $userD;
		$this->page = $page_name;
		$this->side = $show_side;
		foreach($breadcrumbs as $title){
			$this->_breadcrumbs[] = array($title);
		}
		/* Palaižam sesiju testu! */
		user::sessionExists();
		/* Palaižam sesiju testu! */

		/* Palaižam statistikas atjaunošanu */
		$this->updateStatistics();
		/* Palaižam statistikas atjaunošanu */
		if(user::isLoggedIn()){
			$db->update("UPDATE `users` SET `last_seen_date` = ? WHERE `user_id` = ?", array(time(), $_SESSION['user_id']));
		}

		include $c['dir'] . "/system/template/header.php";
	}

	public function __destruct(){
		global $c;
		global $db;
		global $servers;
		global $start;
		if($this->side === true){
			include $c['dir'] . "/system/template/side.php";
		}
		include $c['dir'] . "/system/template/footer.php";
	}

	# Starting Functions

	public static function sort_array(&$arr, $col, $dir = SORT_DESC){
		$sort_col = array();
		foreach($arr as $key=> $row){
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}

	public static function formatTime($endtime){
		$starttime = time() + 1;
		$timediff =	$starttime - $endtime;
		$days     =	intval($timediff/86400);
		$remain   =	$timediff%86400;
		$hours 	  =	intval($remain/3600);
		$remain   =	$remain%3600;
		$mins     =	intval($remain/60);
		$secs     =	$remain%60;
		$weeks    = intval($days/7);
		$months   = intval($days/30);
		$years   = intval($days/365);
		$gs = intval($years/100);
		$pluraldays 	= ($days == 1) ? " dienas" : " dienām";
		$pluralweeks	= ($weeks == 1) ? " nedēļas" : " nedēļām";
		$pluralhours 	= ($hours == 1) ? " stundas" : " stundām";
		$pluralmins 	= ($mins == 1) ? " min." : " min.";
		$pluralsecs 	= ($secs == 1) ? " sek." : " sek.";
		$pluralmonths	= ($months == 1) ? " mēn." : " mēn.";
		$pluralyears	= ($years == 1) ? " g." : " g.";
		$pluralgs	= ($gs == 1) ? " gs." : " gs.";
		$hourcount	= ($hours == 0) ? 1 : 0;
		$minscount	= ($mins == 0) ? 1 : 0;
		$secscount	= ($secs == 0) ? 1 : 0;
		if($mins == 0  and $days == 0 and $hours == 0){
			$timediff = "pirms $secs$pluralsecs";		
		}elseif($mins >= 1 and $hours == 0 and $days == 0){
			$timediff = "pirms $mins$pluralmins";		
		}elseif($hours >= 1 and $days == 0) {
			$timediff = "pirms $hours$pluralhours";		
		}elseif($days >= 1 and $weeks == 0){
			$timediff = "pirms $days$pluraldays";		
		}elseif($weeks >= 1 and $months == 0){
			$timediff = "pirms $weeks$pluralweeks";
		}elseif($months >= 1 and $years == 0){
			$timediff = "pirms $months$pluralmonths";
		}elseif($years >= 1 and $gs == 0){
			$timediff = "pirms $years$pluralyears";
		}elseif($gs >= 1){
			$timediff = "pirms $gs$pluralgs";
		}
		return $timediff;
	}

	public static function alert($message, $type, $size = "11px"){
		echo '<div class="alert alert-' . $type . '" style="font-size: ' . $size . ';">' . $message . '</div>';
	}

	public static function createSlug($string){
		$characters = array(" ", "?", "!", "~", "`", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "]", "{", "}", ":", ";", "'", '"', '\"', "|", "<", ">", ",", ".", "?", "/");
		$replace = array("-", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", '', '', "", "", "", "", "", "", "");
		$string = str_replace(
			array("ā", "Ā", "č", "Č", "ē", "Ē", "ģ", "Ģ", "ķ", "Ķ", "ļ", "Ļ", "ī", "Ī", "ū", "Ū", "š", "Š", "ņ", "Ņ", "ž", "Ž"), 
			array("a", "a", "c", "c", "e", "e", "g", "g", "k", "k", "l", "l", "i", "i", "u", "u", "s", "s", "n", "n", "z", "z"), 
			$string
			); 
		return strtolower(str_ireplace($characters, $replace, $string));
	}

	public static function validateDate($date, $format = "d/m/Y"){
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	public static function redirectTo($url, $settings = array()){
		global $c;
		if(isset($settings['external']) AND $settings['external'] === false){
			if(isset($settings['time']) AND $settings['time'] === false){
				header("Location: " . $c['url'] . "/" . $url);
			}else{
				header("refresh: " . $settings['time'] . "; url=" . $c['url'] . "/" . $url);
			}
		}else{
			if(isset($settings['time']) AND $settings['time'] === false){
				header("Location: $url");
			}else{
				header("refresh: " . $settings['time'] . "; url=$url");
			}
		}
	}

	function updateStatistics(){
		global $db;
		$date = date("d/m/Y");
		$time = date("H:i");
		$ip_address = user::getIP();
		$user_id = (user::isLoggedIn() === false) ? 0 : $_SESSION['user_id'];
		// Pačekojam vai eksistē
		$find = $db->count("SELECT `id` FROM `statistics` WHERE `date` = ? AND (`ip_address` = ? OR `user_id` = ?)", array($date, $ip_address, $user_id));
		if($find == 0){
			// Izveidojam jaunu
			$db->insert("INSERT INTO `statistics` (`date`, `first_time`, `last_time`, `ip_address`, `user_id`) VALUES (?, ?, ?, ?, ?)", array($date, $time, $time, $ip_address, $user_id));
		}else{
			// Atjaunojam eksistējošo
			$fetch = $db->fetch("SELECT `user_id` FROM `statistics` WHERE `date` = ? AND `ip_address` = ?", array($date, $ip_address));
			if($fetch['user_id'] == 0){
				$db->update("UPDATE `statistics` SET `last_time` = ?, `user_id` = ? WHERE `date` = ? AND `ip_address` = ?", array($time, $user_id, $date, $ip_address));
			}else{
				$db->update("UPDATE `statistics` SET `last_time` = ? WHERE `date` = ? AND `user_id` = ?", array($time, $date, $user_id));
			}
		}
	}

	public static function cronRequest($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	public static function pagination($rpp, $count, $href, $pageNumber, $minus = 0, $opts = array()){
		global $path;
		$pages = ceil($count / $rpp);
		if(!isset($opts["lastpagedefault"])) $pagedefault = 0;
		else{
			$pagedefault = floor(($count - 1) / $rpp);
			if ($pagedefault < 0) $pagedefault = 0;
		}
		if(isset($path[$pageNumber])){
			$page = $path[$pageNumber] - 1;
			if($page < 0)
				$page = $pagedefault;
		}else
		$page = $pagedefault;
		$pager2 = "";
		$lastpage = $page;
		if($lastpage > 0){
			$previous = '<li><a href="' . $href . ($lastpage) . '">&laquo;</a></li>';
		}
		if($lastpage + 2 <= $pages){
			$next = '<li><a href="' . $href . ($lastpage + 2) . '">&raquo;</a></li>';
		}else{
			$next = "";
		}
		if($count){
			$pagerarr = array();
			$dotted = 0;
			$dotspace = 4;
			$dotend = $pages+1 - $dotspace;
			$curdotend = $page +1 - $dotspace;
			$curdotstart = $page +1 + $dotspace;
			for($i = 1; $i <= $pages; $i++){
				if(($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)){
					if(!$dotted)
						$pagerarr[] = "";
					$dotted = 1;
					continue;
				}
				$dotted = 0;
				$start = $i * $rpp;
				$end = $start + $rpp - 1;
				if($end > $count)
					$end = $count;
				$href2 = $href. $i;
				$text = $i;
				if($i != $page +1)
					$pagerarr[] = "<li><a href=\"{$href2}\">$text</a></li>\n";
				else
					$pagerarr[] = "<li class=\"active\"><a>$text</a></li>\n";
			}
			$pagerstr = join("", $pagerarr);
			$current_page = $page + 1;
			if($current_page == 1){
				$pager_template = '<ul class="pagination  pagination-sm">' . $pagerstr . $next . '</ul>';
			}elseif($current_page < $pages){
				$pager_template = '<ul class="pagination  pagination-sm">' . $previous . $pagerstr . $next . '</ul>';
			}else{
				$pager_template = '<ul class="pagination  pagination-sm">' . $previous . $pagerstr . '</ul>';
			}
		}else{
			$pagerstr = "";
			$pager_template = "$pager$pagerstr$pager2\n";
		}
		$start = $page * $rpp;
		return array($pager_template, "LIMIT $start, $rpp");
	}

	public static function sendEmail($to, $from, $subject, $message, $headers = true, $cc = true){
		if($headers === true){
			$headers = "From: " . $from . "\r\n";
			if($cc === true){
				$headers .= "CC: " . $from . "\r\n";
			}
			$headers .= "Reply-To: " . $from . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		}
		if(mail($to, '=?utf-8?B?' . base64_encode($subject) . '?=', $message, $headers)){
			return true;
		}else{
			return false;
		}
	}

	public static function addShout($shout, $user_id = false){
		global $c;
		global $db;
		$db->insert("INSERT INTO `shoutbox` 
			(
				`user_id`, 
				`ip_address`, 
				`shout`, 
				`time`
				) VALUES (?, ?, ?, ?)
		", array(
			($user_id === false) ? $c['page']['shouter_id'] : $user_id,
			"127.0.0.1",
			$shout,
			time()
			));
	}

	public static function returnPrice($price){
		return number_format($price, 2, ".", "");
	}

	public static function logVshop($data = array()){
		global $db;
		$db->insert("INSERT INTO `vshop_logs` (`time`, `user_id`, `ip_address`, `item`, `price`, `result`) VALUES (?, ?, ?, ?, ?, ?)", array(
			time(),
			$_SESSION['user_id'],
			user::getIP(),
			$data['item'],
			$data['price'],
			($data['result'] === true) ? 1 : 0
		));
	}
}