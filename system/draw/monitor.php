<?php
define("IN_SYSTEM", true);
include_once("../config.inc.php");
if(!empty($_GET['server']) && array_key_exists($_GET['server'], $servers)){
	$server = $servers[$_GET['server']];
	$server_id = $_GET['server'];
}else{
	exit;
}
?>

<?php if($server['type'] == "ts3"): ?>
	<?php
	$mapes_bilde = $c['url'] . "/assets/images/maps/teamspeak.jpg";

	$url = "https://api.planetteamspeak.com/serverstatus/" . $server['ip'] . ":" . $server['port'];
	echo $url;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	$results = curl_exec($ch);
	curl_close($ch);
	$results = json_decode($results, true);
	$results = $results['result'];
	?>
	<div class="game">
		<div class="title"><img src="<?php echo $c['url']; ?>/assets/images/icons/teamspeak.png"> <?php echo $results['name']; ?></div>
	</div>
	<div id="servermon">
		<img src="<?=$mapes_bilde?>">
		<div class="players"><span class="glyphicon glyphicon-user" style="font-size: 9px;"></span> <span style="font-size: 12px;"><?php echo $results['users']; ?></span><span style="font-size: 8px;">/<?php echo $results['slots']; ?></span></div>
		<div class="info">
			<div class="ipadress"><a href="ts3server://<?php echo $server['ip']; ?>?port=<?php echo $server['port']; ?>"><span class="glyphicon glyphicon-globe" style="text-shadow: none; padding-right: 10px;"></span> <?php echo $server['ip'] . ":" . $server['port']; ?></a></div>
		</div>
	</div>
<?php else: ?>
	<?php
	$gq = new GameQ();
	$gq->addServer(
		array(
			"id" => $server_id,
			"type" => $server['type'],
			"host" => $server['ip'] . ":" . $server['port']
			)
		);

	$results = $gq->requestData();
	$results = $results[$server_id];
	$players = isset($results['players']) ? $results['players'] : array();

	if(isset($_GET['debug'])){
		print_r($gq->requestData());
	}

	if(!file_exists($_SERVER['DOCUMENT_ROOT'] . "/assets/images/maps/" . strtolower($results['map']) . ".jpg")){
		$mapes_bilde = $c['url'] . "/assets/images/maps/no_map_photo.png";
	}else{
		$mapes_bilde = $c['url'] . "/assets/images/maps/" . strtolower($results['map']) . ".jpg";
	}
	?>
	<script>
		$("#showplayers_<?php echo $server_id; ?>").click(function(){
			if($("#playerlist_<?php echo $server_id; ?>").is(":visible")){
				$("#showplayers_<?php echo $server_id; ?> b").removeClass().addClass("caret");
			}else{
				$("#showplayers_<?php echo $server_id; ?> b").removeClass().addClass("caret-up");
			}
			$("#playerlist_<?php echo $server_id; ?>").slideToggle("slow");
		});
	</script>
	<div class="game">
		<div class="title"><img src="<?php echo $c['url']; ?>/assets/images/cs16.gif"> <?php echo $results['hostname']; ?></div>
	</div>
	<div id="servermon">
		<img src="<?=$mapes_bilde?>">
		<div class="players"><span class="glyphicon glyphicon-user" style="font-size: 9px;"></span> <span style="font-size: 12px;"><?php echo $results['num_players']; ?></span><span style="font-size: 8px;">/<?php echo $results['max_players']; ?></span></div>
		<div class="info">
			<div class="ipadress"><a href="<?php echo $results['gq_joinlink']; ?>"><span class="glyphicon glyphicon-globe" style="text-shadow: none; padding-right: 10px;"></span> <?php echo $server['ip']; ?></a></div>
			<div id="showplayers_<?php echo $server_id; ?>" class="showplayers pointer">spēlētāju saraksts <b class="caret"></b></div>
		</div>
	</div>
	<div id="playerlist_<?=$server_id?>" class="playerlist">
		<?php
		if(!$players){
			echo '<div id="player">Neviens spēlētājs nav tiešsaistē!</div>';
		}else{
			foreach($players as $player){
				$player_name = $player['name'];
				$if_exists = "SELECT `user_id` FROM `users` WHERE `display_name` = ?";
				if($db->count($if_exists, array($player_name)) == 0){
					$name = htmlspecialchars($player['name']);
				}else{
					$user = $db->fetch($if_exists, array($player_name));
					$name = user::formatName($user['user_id'], true, true, false);
				}
				$admin_list = "SELECT `access` FROM `amx_amxadmins` WHERE `username` = ?";
				if(!$db->count($admin_list, array($player_name)) == 0){
					$admin = $db->fetch($admin_list, array($player_name));
					$access = $admin['access'];
					$vip = (strpos($access, "t") !== false) ? "<span class='orange'>VIP</span>" : "";
					$admin = (strpos($access, "z") !== false) ? "" : "<span class='green'>ADMIN</span>";
				}else{
					$vip = "";
					$admin = "";
				}
				?>
				<div id="player">
					<span style="text-align: left;"><?php echo $name; ?> <?php echo $vip; ?> <?php echo $admin; ?></span>
					<span style="float: right;padding-right: 5px;">(<?php echo $player['score']; ?>)</span>
				</div>
				<?php
			}
		}
		?>
	</div>
<?php endif; ?>