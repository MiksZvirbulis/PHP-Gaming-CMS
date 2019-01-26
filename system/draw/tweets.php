<?php
ob_start();
define("IN_SYSTEM", true);
include_once("../config.inc.php");
header("Content-Type: text/html; charset=utf-8");
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
$getfield = "?screen_name=" . $c['twitter_name'];
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($twitter_settings);
$response = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();
$tweets = json_decode($response, true);
$i = 0;
$ii = 1
?>
<div id="tweets">
	<?php
	foreach($tweets as $tweet){
		$show_tweet = text::tweet($tweet['text']);
		$tweet_created_timestamp = strtotime($tweet['created_at']);
		$tweet_created = page::formatTime($tweet_created_timestamp);
		$class = "left";
		if(++$ii % 2 === 0){
			$class = 'right';
		}
		echo '<div class="' . $class . '">';
		$user = $tweet['user'];
		echo '<a href="http://twitter.com/' . $c['twitter_name'] . '"><div id="image"><img src="' . $user['profile_image_url'] . '"></div></a>';
		echo '<div id="text"><a href="http://twitter.com/' . $c['twitter_name'] . '"><strong>@' . $c['twitter_name'] . '</strong></a> <span style="color: #666;font-size: 9px;">(' . $tweet_created . ')</span> : ' . $show_tweet .  "</div><br />\n";
		echo '</div>';
		if(++$i == $c['tweet_count']) break;
	}
	?>
</div>