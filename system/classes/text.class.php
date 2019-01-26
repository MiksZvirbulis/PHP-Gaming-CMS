<?php

class text{

	public static $emoticons = array(
		":)" => "smile",
	":D" => "laugh",
	";P" => "blush",
	";)" => "wink",
	":(" => "sad",
		";(" => "cry",
			":B" => "cool",
			":P" => "tongue",
			":@" => "angry",
			":*" => "kiss",
			"(xmas)" => "xmas",
			"(flag_lv)" => "flag_lv",
			"(devil)" => "devil",
			"(sun)" => "sun",
			"(pool)" => "pool",
			"(giggle)" => "giggle",
			"(happy)" => "happy",
			"(run)" => "run",
			"(tree)" => "tree",
			"(tiger)" => "tiger",
			"(sik)" => "sick",
			"(punch)" => "punch",
			"(love)" => "love",
			"(hug)" => "hug",
			"(ninja)" => "ninja",
			"(halloween)" => "halloween",
			"(fool)" => "fool",
			"(cool)" => "cool",
			"(cash)" => "cash",
			"(valdis)" => "valdis",
			"(pray)" => "pray",
			"(music)" => "music",
			"(hot)" => "hot",
			"(heart)" => "heart",
			"(gaper)" => "gaper",
			"(car)" => "car",
			"(bat)" => "bat"
			);

	public static function limit($string, $characters){
		$string = strip_tags($string);
		if(mb_strlen($string, "UTF-8") <= $characters){
			return $string;
		}else{
			$string = mb_substr($string, 0, $characters, "UTF-8") . "...";
			return $string;
		}
	}

	public static function tweet($tweet){
		$tweet = strip_tags($tweet);
		$tweet = nl2br($tweet);
		$bbcodes = array(
			'/(^|\s)#(\w*[a-zA-Z_]+\w*)/',
			'/@([a-zA-Z0-9_]+)/',
			'/(?:http?:\/\/)?(?:www\.)?t\.co\/([A-Z0-9\-_]+)(?:&(.*?))?/i',
			);
		$html = array(
			'\1<a href="http://twitter.com/search?q=%23\2">#\2</a>',
			'<a href="http://twitter.com/$1">@$1</a>',
			'<a href="http://t.co/\\1">http://t.co/\\1</a>'
			);
		return preg_replace($bbcodes, $html, $tweet);
		return preg_replace("/\n/m","<br />", $tweet);
	}

	public static function seostring($string){
		$characters = array(" ", "?", "!", "~", "`", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "]", "{", "}", ":", ";", "'", '"', '\"', "|", "<", ">", ",", ".", "?", "/", "\\", "—");
		$replace = array("-", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", '', '', "", "", "", "", "", "", "", "", "");
		$string = str_replace(
			array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή'), 
			array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η'), 
			$string
			); 

		return strtolower(str_ireplace($characters, $replace, $string));
	}

	public static function stripBBCode($string){
		$string = str_replace("[", "<", $string);
		$string = str_replace("]", ">", $string);
		$string = strip_tags($string);
		$string = nl2br($string);
		return $string;
	}

	public static function returnPrice($number){
		$number = $number * 0.01;
		return number_format($number / 0.702804, 2, ".", "");
	}

	public static function bbcode($string, $settings = array()){
		global $db;
		global $c;
		$string = htmlspecialchars($string);
		$string = strip_tags($string);
		$string = nl2br($string);
		$bbcodes = array(
			'/\[p\](.*?)\[\/p\]/is',
			'/\[right\](.*?)\[\/right\]/is',
			'/\[left\](.*?)\[\/left\]/is',
			'/\[center\](.*?)\[\/center\]/is',
			'/\[b\](.*?)\[\/b]/is',
			'/\[i\](.*?)\[\/i]/is',
			'/\[u\](.*?)\[\/u]/is',
			'/\[c\](.*?)\[\/c]/is',
			'/\[info\](.*?)\[\/info]/is',
			'/\[img\](.*?)\[\/img]/is',
			'/\[color=(.*?)\](.*?)\[\/color]/is',
			'/\[url=(.*?)\](.*?)\[\/url]/is',
			'/\[font=(.*?)\](.*?)\[\/font]/is',
			'~https?://(?:[0-9A-Z-]+\.)?(?:youtu\.be/|youtube(?:-nocookie)?\.com\S*[^\w\s-])([\w-]{11})(?=[^\w-]|$)(?![?=&+%\w.-]*(?:[\'"][^<>]*>|</a>))[?=&+%\w.-]*~ix',
			'#\[video\](.+?)\[/video\]#is',
			'/\[list\](.*?)\[\/list\]/is',
			'/\[\*\]([^\[\*\]]*)/is',
			'@(^|[^"])(https?://?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@i',
			'~\[quote=(.*?)\]~is',
			'~\[quote\]~is',   
			'~\[/quote\]~is',
			);
		$html = array(
			'<p>$1</p>',
			'<p style="text-align: right;">$1</p>',
			'<p style="text-align: left;">$1</p>',
			'<center>$1</center>',
			'<b>$1</b>',
			'<i>$1</i>',
			'<u>$1</u>',
			'<center>$1</center>',
			'<p class="text-info"><span class="glyphicon glyphicon-info-sign"></span> $1</p>',
			'<img src="$1" class="img-responsive">',
			'<font color="$1">$2</font>',
			'<a href="$1" target="_blank">$2</a>',
			'<span style="font-family: $1;">$2</span>',
			(isset($settings['media']) AND $settings['media'] === false) ? 'http://youtube.com/watch?v=$1' : '<iframe type="application/x-shockwave-flash" wmode="transparent" width="500" height="300" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
			(isset($settings['media']) AND $settings['media'] === false) ? 'http://youtube.com/watch?v=$1' : '<iframe type="application/x-shockwave-flash" wmode="transparent" width="500" height="300" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
			'<ul>$1</ul>',
			'<li>$1</li>',
			'$1<a href="$2" target="_blank">$2</a>',
			'<blockquote><div id="quote2"><div class="head">$1 rakstīja:</div><div class="quote">',
			'<blockquote><div id="quote2"><div class="head">Citāts:</div><div class="quote">',
			'</div></div></blockquote>'
			);
if(isset($settings['bbcode']) AND $settings['bbcode'] === true){
	$users = $db->fetchAll("SELECT `user_id`, `display_name` FROM `users`", array());
		foreach($users as $user){
			if(strpos($string, $user['display_name']) !== false ){
				$display_name = $user['display_name'];
				$string = preg_replace("/\b$display_name\b/", user::formatName($user['user_id'], false, true, true), $string);
			}
		}
	$string = preg_replace($bbcodes, $html, $string);
}
if(isset($settings['emoticons']) AND $settings['emoticons'] === true){
	foreach(self::$emoticons as $emoticon => $image){
		$extensions = array(".png", ".gif");
		foreach($extensions as $extension){
			if(file_exists($c['dir'] . "assets/images/emoticons/" . $image . $extension)){
				$display = true;
				$return_ext = $extension;
				break;
			}else{
				$display = false;
			}
		}
		if($display === true){
			$string = str_ireplace($emoticon, "<img class='emoticon' src='" . $c['url'] . "/assets/images/emoticons/" . $image . $extension . "'>", $string);
		}
	}
}
return $string;
}

public static function secondsToTime($seconds){
	$dtF = new DateTime("@0");
	$dtT = new DateTime("@$seconds");
	return $dtF->diff($dtT)->format("%a dien. %h st. un %i min.");
}
}